<?php

require_once __DIR__ . '/../core/Database.php';

class Venda
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Database::conectar();
    }

    public function listarClientes(): array
    {
        $sql = "
            SELECT
                id,
                nome,
                cpf_cnpj
            FROM clientes
            WHERE ativo = 1
            ORDER BY nome ASC
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarProdutos(): array
    {
        $sql = "
            SELECT
                id,
                codigo_barras,
                nome,
                marca,
                preco_custo,
                preco_venda,
                quantidade_estoque
            FROM produtos
            WHERE ativo = 1
            ORDER BY nome ASC
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarOrdensServicoProntas(): array
{
    $sql = "
        SELECT
            os.id,
            os.numero_os,
            os.cliente_id,
            os.aparelho_marca,
            os.aparelho_modelo,
            os.valor_total,
            clientes.nome AS cliente_nome,
            clientes.cpf_cnpj AS cliente_documento
        FROM ordens_servico AS os

        INNER JOIN clientes
            ON clientes.id = os.cliente_id

        WHERE
            os.status = 'pronta'
            AND os.venda_id IS NULL

        ORDER BY os.id DESC
    ";

    $stmt = $this->conexao->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function gerarNumeroVenda(): string
    {
        $sql = "
            SELECT MAX(id) AS ultimo_id
            FROM vendas
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        $proximoNumero = ((int) ($resultado['ultimo_id'] ?? 0)) + 1;

        return 'VEN-' . str_pad(
            (string) $proximoNumero,
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    public function listar(string $pesquisa = ''): array
        {
            $sql = "
                SELECT
                    vendas.id,
                    vendas.numero_venda,
                    vendas.subtotal,
                    vendas.desconto,
                    vendas.total,
                    vendas.forma_pagamento,
                    vendas.status,
                    vendas.criado_em,
                    clientes.nome AS cliente_nome,
                    COUNT(itens_venda.id) AS quantidade_itens
                FROM vendas

                LEFT JOIN clientes
                    ON clientes.id = vendas.cliente_id

                LEFT JOIN itens_venda
                    ON itens_venda.venda_id = vendas.id

                WHERE 1 = 1
            ";

            $parametros = [];

            if ($pesquisa !== '') {
                $sql .= "
                    AND (
                        vendas.numero_venda LIKE :pesquisa
                        OR clientes.nome LIKE :pesquisa
                        OR vendas.forma_pagamento LIKE :pesquisa
                        OR vendas.status LIKE :pesquisa
                    )
                ";

                $parametros[':pesquisa'] =
                    '%' . $pesquisa . '%';
            }

            $sql .= "
                GROUP BY
                    vendas.id,
                    vendas.numero_venda,
                    vendas.subtotal,
                    vendas.desconto,
                    vendas.total,
                    vendas.forma_pagamento,
                    vendas.status,
                    vendas.criado_em,
                    clientes.nome

                ORDER BY vendas.id DESC
            ";

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute($parametros);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    public function buscarVendaPorId(int $vendaId): ?array
    {
        $sql = "
            SELECT
                vendas.*,
                clientes.nome AS cliente_nome,
                clientes.cpf_cnpj AS cliente_documento,
                clientes.telefone AS cliente_telefone
            FROM vendas
            LEFT JOIN clientes
                ON clientes.id = vendas.cliente_id
            WHERE vendas.id = :id
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':id' => $vendaId
        ]);

        $venda = $stmt->fetch(PDO::FETCH_ASSOC);

        return $venda ?: null;
    }

    public function buscarItensVenda(int $vendaId): array
    {
        $sql = "
            SELECT
                itens_venda.id,
                itens_venda.produto_id,
                itens_venda.quantidade,
                itens_venda.preco_unitario,
                itens_venda.custo_unitario,
                itens_venda.total,
                produtos.nome AS produto_nome,
                produtos.codigo_barras
            FROM itens_venda
            INNER JOIN produtos
                ON produtos.id = itens_venda.produto_id
            WHERE itens_venda.venda_id = :venda_id
            ORDER BY itens_venda.id ASC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':venda_id' => $vendaId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvar(array $dados): int
    {
        try {
            $this->conexao->beginTransaction();

            $clienteId = $dados['cliente_id'] ?? null;

            $formaPagamento = trim(
                (string) ($dados['forma_pagamento'] ?? '')
            );

            $observacoes = trim(
                (string) ($dados['observacoes'] ?? '')
            );

            $desconto = (float) ($dados['desconto'] ?? 0);

            $itensRecebidos = $dados['itens'] ?? [];

            $ordemServicoId = (int) (
                $dados['ordem_servico_id'] ?? 0
            );

            if ($formaPagamento === '') {
                throw new Exception(
                    'Informe a forma de pagamento.'
                );
            }

            if (!is_array($itensRecebidos)) {
                $itensRecebidos = [];
            }

            /*
            * A venda precisa possuir pelo menos:
            * - um produto;
            * ou
            * - uma ordem de serviço.
            */
            if (
                count($itensRecebidos) === 0
                && $ordemServicoId <= 0
            ) {
                throw new Exception(
                    'Adicione pelo menos um produto ou uma ordem de serviço.'
                );
            }

            if ($clienteId !== null && $clienteId !== '') {
                $clienteId = (int) $clienteId;

                $this->validarCliente($clienteId);
            } else {
                $clienteId = null;
            }

            /*
            * Busca e bloqueia a OS durante a venda.
            */
            $ordemServico = null;
            $valorOrdemServico = 0;

            if ($ordemServicoId > 0) {
                $ordemServico = $this->buscarOrdemServicoParaVenda(
                    $ordemServicoId
                );

                if (!$ordemServico) {
                    throw new Exception(
                        'A ordem de serviço não foi encontrada.'
                    );
                }

                if ($ordemServico['status'] !== 'pronta') {
                    throw new Exception(
                        'Somente uma ordem de serviço pronta pode ser vendida.'
                    );
                }

                if (!empty($ordemServico['venda_id'])) {
                    throw new Exception(
                        'Esta ordem de serviço já está vinculada a uma venda.'
                    );
                }

                if ($clienteId === null) {
                    throw new Exception(
                        'A venda da ordem de serviço precisa estar vinculada ao cliente.'
                    );
                }

                if (
                    (int) $ordemServico['cliente_id']
                    !== $clienteId
                ) {
                    throw new Exception(
                        'O cliente da venda é diferente do cliente da ordem de serviço.'
                    );
                }

                $valorOrdemServico =
                    (float) $ordemServico['valor_total'];

                if ($valorOrdemServico <= 0) {
                    throw new Exception(
                        'A ordem de serviço não possui um valor válido.'
                    );
                }
            }

            $itensProcessados = [];

            /*
            * O subtotal começa com o valor da OS.
            */
            $subtotal = $valorOrdemServico;

            foreach ($itensRecebidos as $itemRecebido) {
                $produtoId = (int) (
                    $itemRecebido['produto_id'] ?? 0
                );

                $quantidade = (int) (
                    $itemRecebido['quantidade'] ?? 0
                );

                if ($produtoId <= 0 || $quantidade <= 0) {
                    throw new Exception(
                        'Um dos itens da venda é inválido.'
                    );
                }

                $produto = $this->buscarProdutoParaVenda(
                    $produtoId
                );

                if (!$produto) {
                    throw new Exception(
                        'Um dos produtos não foi encontrado.'
                    );
                }

                if ((int) $produto['ativo'] !== 1) {
                    throw new Exception(
                        'O produto "'
                        . $produto['nome']
                        . '" está inativo.'
                    );
                }

                if (
                    $quantidade
                    > (int) $produto['quantidade_estoque']
                ) {
                    throw new Exception(
                        'Estoque insuficiente para o produto "'
                        . $produto['nome']
                        . '". Disponível: '
                        . $produto['quantidade_estoque']
                        . '.'
                    );
                }

                $precoUnitario =
                    (float) $produto['preco_venda'];

                $custoUnitario =
                    (float) $produto['preco_custo'];

                $totalItem =
                    $precoUnitario * $quantidade;

                $subtotal += $totalItem;

                $itensProcessados[] = [
                    'produto_id' => $produtoId,
                    'nome' => $produto['nome'],
                    'quantidade' => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'custo_unitario' => $custoUnitario,
                    'total' => $totalItem
                ];
            }

            if ($desconto < 0) {
                $desconto = 0;
            }

            if ($desconto > $subtotal) {
                throw new Exception(
                    'O desconto não pode ser maior que o subtotal.'
                );
            }

            $total = $subtotal - $desconto;

            $numeroVenda =
                $this->gerarNumeroVenda();

            /*
            * Inclui a informação da OS nas observações.
            */
            if ($ordemServico) {
                $textoOrdemServico =
                    'Ordem de serviço vinculada: '
                    . $ordemServico['numero_os'];

                if ($observacoes !== '') {
                    $observacoes .= PHP_EOL;
                }

                $observacoes .= $textoOrdemServico;
            }

            $sqlVenda = "
                INSERT INTO vendas (
                    cliente_id,
                    numero_venda,
                    subtotal,
                    desconto,
                    total,
                    forma_pagamento,
                    observacoes,
                    status,
                    criado_em
                ) VALUES (
                    :cliente_id,
                    :numero_venda,
                    :subtotal,
                    :desconto,
                    :total,
                    :forma_pagamento,
                    :observacoes,
                    'concluida',
                    NOW()
                )
            ";

            $stmtVenda = $this->conexao->prepare(
                $sqlVenda
            );

            $stmtVenda->execute([
                ':cliente_id' => $clienteId,
                ':numero_venda' => $numeroVenda,
                ':subtotal' => $subtotal,
                ':desconto' => $desconto,
                ':total' => $total,
                ':forma_pagamento' => $formaPagamento,
                ':observacoes' => $observacoes
            ]);

            $vendaId =
                (int) $this->conexao->lastInsertId();

            /*
            * Salva somente os produtos em itens_venda.
            * A OS fica vinculada por ordens_servico.venda_id.
            */
            if (count($itensProcessados) > 0) {
                $sqlItem = "
                    INSERT INTO itens_venda (
                        venda_id,
                        produto_id,
                        quantidade,
                        preco_unitario,
                        custo_unitario,
                        total
                    ) VALUES (
                        :venda_id,
                        :produto_id,
                        :quantidade,
                        :preco_unitario,
                        :custo_unitario,
                        :total
                    )
                ";

                $stmtItem = $this->conexao->prepare(
                    $sqlItem
                );

                $sqlBaixaEstoque = "
                    UPDATE produtos
                    SET
                        quantidade_estoque =
                            quantidade_estoque - :quantidade,
                        atualizado_em = NOW()
                    WHERE
                        id = :produto_id
                        AND quantidade_estoque >= :quantidade
                ";

                $stmtBaixaEstoque =
                    $this->conexao->prepare(
                        $sqlBaixaEstoque
                    );

                foreach ($itensProcessados as $item) {
                    $stmtItem->execute([
                        ':venda_id' => $vendaId,
                        ':produto_id' =>
                            $item['produto_id'],
                        ':quantidade' =>
                            $item['quantidade'],
                        ':preco_unitario' =>
                            $item['preco_unitario'],
                        ':custo_unitario' =>
                            $item['custo_unitario'],
                        ':total' =>
                            $item['total']
                    ]);

                    $stmtBaixaEstoque->execute([
                        ':quantidade' =>
                            $item['quantidade'],
                        ':produto_id' =>
                            $item['produto_id']
                    ]);

                    if (
                        $stmtBaixaEstoque->rowCount()
                        !== 1
                    ) {
                        throw new Exception(
                            'O estoque do produto "'
                            . $item['nome']
                            . '" foi alterado durante a venda. Tente novamente.'
                        );
                    }

                    $estoqueAtual =
                        $this->buscarQuantidadeEstoque(
                            (int) $item['produto_id']
                        );

                    $estoqueAnterior =
                        $estoqueAtual
                        + (int) $item['quantidade'];

                    $this->registrarMovimentacaoEstoque(
                        (int) $item['produto_id'],
                        'saida',
                        (int) $item['quantidade'],
                        $estoqueAnterior,
                        $estoqueAtual,
                        'Venda ' . $numeroVenda
                    );
                }
            }

            /*
            * Vincula a OS à venda e marca como entregue.
            */
            if ($ordemServico) {
                $sqlVincularOrdem = "
                    UPDATE ordens_servico
                    SET
                        venda_id = :venda_id,
                        status = 'entregue',
                        data_conclusao =
                            COALESCE(data_conclusao, NOW()),
                        data_entrega = NOW()
                    WHERE
                        id = :ordem_id
                        AND status = 'pronta'
                        AND venda_id IS NULL
                ";

                $stmtVincularOrdem =
                    $this->conexao->prepare(
                        $sqlVincularOrdem
                    );

                $stmtVincularOrdem->execute([
                    ':venda_id' => $vendaId,
                    ':ordem_id' => $ordemServicoId
                ]);

                if (
                    $stmtVincularOrdem->rowCount()
                    !== 1
                ) {
                    throw new Exception(
                        'Não foi possível vincular a ordem de serviço à venda.'
                    );
                }
            }

            $this->conexao->commit();

            return $vendaId;
        } catch (Throwable $erro) {
            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw $erro;
        }
    }

public function cancelar(int $vendaId): void
{
    try {
        $this->conexao->beginTransaction();

        /*
         * Busca e bloqueia a venda.
         */
        $sqlVenda = "
            SELECT
                id,
                numero_venda,
                status
            FROM vendas
            WHERE id = :id
            LIMIT 1
            FOR UPDATE
        ";

        $stmtVenda = $this->conexao->prepare(
            $sqlVenda
        );

        $stmtVenda->execute([
            ':id' => $vendaId
        ]);

        $venda = $stmtVenda->fetch(
            PDO::FETCH_ASSOC
        );

        if (!$venda) {
            throw new Exception(
                'Venda não encontrada.'
            );
        }

        if ($venda['status'] === 'cancelada') {
            throw new Exception(
                'Essa venda já está cancelada.'
            );
        }

        /*
         * Busca os produtos da venda.
         *
         * Uma venda que possui apenas OS pode não ter
         * nenhum produto, e isso não será um problema.
         */
        $itens = $this->buscarItensVenda(
            $vendaId
        );

        $sqlDevolverEstoque = "
            UPDATE produtos
            SET
                quantidade_estoque =
                    quantidade_estoque + :quantidade,
                atualizado_em = NOW()
            WHERE id = :produto_id
        ";

        $stmtDevolver =
            $this->conexao->prepare(
                $sqlDevolverEstoque
            );

        foreach ($itens as $item) {
            $produtoId =
                (int) $item['produto_id'];

            $quantidade =
                (int) $item['quantidade'];

            $estoqueAnterior =
                $this->buscarQuantidadeEstoque(
                    $produtoId,
                    true
                );

            $stmtDevolver->execute([
                ':quantidade' => $quantidade,
                ':produto_id' => $produtoId
            ]);

            if (
                $stmtDevolver->rowCount()
                !== 1
            ) {
                throw new Exception(
                    'Não foi possível devolver o produto ao estoque.'
                );
            }

            $estoqueAtual =
                $estoqueAnterior + $quantidade;

            $this->registrarMovimentacaoEstoque(
                $produtoId,
                'entrada',
                $quantidade,
                $estoqueAnterior,
                $estoqueAtual,
                'Cancelamento da venda '
                    . $venda['numero_venda']
            );
        }

        /*
         * Procura uma OS vinculada à venda.
         */
        $sqlOrdemServico = "
            SELECT
                id,
                numero_os,
                status
            FROM ordens_servico
            WHERE venda_id = :venda_id
            LIMIT 1
            FOR UPDATE
        ";

        $stmtOrdemServico =
            $this->conexao->prepare(
                $sqlOrdemServico
            );

        $stmtOrdemServico->execute([
            ':venda_id' => $vendaId
        ]);

        $ordemServico =
            $stmtOrdemServico->fetch(
                PDO::FETCH_ASSOC
            );

        /*
         * Se houver uma OS vinculada, ela volta para pronta
         * e fica novamente disponível para outra venda.
         */
        if ($ordemServico) {
            $sqlLiberarOrdem = "
                UPDATE ordens_servico
                SET
                    venda_id = NULL,
                    status = 'pronta',
                    data_entrega = NULL
                WHERE
                    id = :ordem_id
                    AND venda_id = :venda_id
            ";

            $stmtLiberarOrdem =
                $this->conexao->prepare(
                    $sqlLiberarOrdem
                );

            $stmtLiberarOrdem->execute([
                ':ordem_id' =>
                    (int) $ordemServico['id'],

                ':venda_id' =>
                    $vendaId
            ]);

            if (
                $stmtLiberarOrdem->rowCount()
                !== 1
            ) {
                throw new Exception(
                    'Não foi possível liberar a ordem de serviço vinculada.'
                );
            }
        }

        /*
         * Cancela a venda.
         */
        $sqlCancelar = "
            UPDATE vendas
            SET status = 'cancelada'
            WHERE
                id = :id
                AND status <> 'cancelada'
        ";

        $stmtCancelar =
            $this->conexao->prepare(
                $sqlCancelar
            );

        $stmtCancelar->execute([
            ':id' => $vendaId
        ]);

        if ($stmtCancelar->rowCount() !== 1) {
            throw new Exception(
                'Não foi possível cancelar a venda.'
            );
        }

        $this->conexao->commit();
    } catch (Throwable $erro) {
        if ($this->conexao->inTransaction()) {
            $this->conexao->rollBack();
        }

        throw $erro;
    }
}

    private function buscarOrdemServicoParaVenda(
    int $ordemServicoId
): ?array {
    $sql = "
        SELECT
            id,
            numero_os,
            venda_id,
            cliente_id,
            valor_total,
            status
        FROM ordens_servico
        WHERE id = :id
        LIMIT 1
        FOR UPDATE
    ";

    $stmt = $this->conexao->prepare($sql);

    $stmt->execute([
        ':id' => $ordemServicoId
    ]);

    $ordemServico =
        $stmt->fetch(PDO::FETCH_ASSOC);

    return $ordemServico ?: null;
}

    private function validarCliente(int $clienteId): void
    {
        $sql = "
            SELECT id
            FROM clientes
            WHERE
                id = :id
                AND ativo = 1
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':id' => $clienteId
        ]);

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception(
                'O cliente selecionado não foi encontrado.'
            );
        }
    }

    private function buscarProdutoParaVenda(
        int $produtoId
    ): ?array {
        $sql = "
            SELECT
                id,
                nome,
                preco_custo,
                preco_venda,
                quantidade_estoque,
                ativo
            FROM produtos
            WHERE id = :id
            LIMIT 1
            FOR UPDATE
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':id' => $produtoId
        ]);

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        return $produto ?: null;
    }

    private function buscarQuantidadeEstoque(
    int $produtoId,
    bool $bloquearRegistro = false
): int {
    $sql = "
        SELECT quantidade_estoque
        FROM produtos
        WHERE id = :produto_id
        LIMIT 1
    ";

    if ($bloquearRegistro) {
        $sql .= " FOR UPDATE";
    }

    $stmt = $this->conexao->prepare($sql);

    $stmt->execute([
        ':produto_id' => $produtoId
    ]);

    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        throw new Exception(
            'Produto não encontrado ao atualizar o estoque.'
        );
    }

    return (int) $produto['quantidade_estoque'];
}

private function registrarMovimentacaoEstoque(
    int $produtoId,
    string $tipo,
    int $quantidade,
    int $quantidadeAnterior,
    int $quantidadeAtual,
    string $motivo
): void {
    $tiposPermitidos = [
        'entrada',
        'saida',
        'ajuste'
    ];

    if (!in_array($tipo, $tiposPermitidos, true)) {
        throw new Exception(
            'Tipo de movimentação de estoque inválido.'
        );
    }

    $sql = "
        INSERT INTO movimentacoes_estoque (
            produto_id,
            tipo,
            quantidade,
            quantidade_anterior,
            quantidade_atual,
            motivo
        ) VALUES (
            :produto_id,
            :tipo,
            :quantidade,
            :quantidade_anterior,
            :quantidade_atual,
            :motivo
        )
    ";

    $stmt = $this->conexao->prepare($sql);

    $stmt->execute([
        ':produto_id' => $produtoId,
        ':tipo' => $tipo,
        ':quantidade' => $quantidade,
        ':quantidade_anterior' => $quantidadeAnterior,
        ':quantidade_atual' => $quantidadeAtual,
        ':motivo' => $motivo
    ]);

    if ($stmt->rowCount() !== 1) {
        throw new Exception(
            'Não foi possível registrar o histórico do estoque.'
        );
    }
}
}