<?php

require_once __DIR__ . '/../core/Database.php';

class Financeiro
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Database::conectar();
    }

    /*
    |--------------------------------------------------------------------------
    | DADOS COMPLETOS DO FINANCEIRO
    |--------------------------------------------------------------------------
    */

    public function obterDadosFinanceiros(
        int $mes,
        int $ano
    ): array {
        $periodo = $this->montarPeriodo($mes, $ano);

        $vendas = $this->buscarVendasDoPeriodo(
            $periodo['inicio'],
            $periodo['fim']
        );

        $produtosVendidos =
            $this->calcularProdutosVendidos($vendas);

        $ordensServico =
            $this->calcularOrdensServico($vendas);

        $gastos = $this->listarGastos(
            $mes,
            $ano
        );

        $totalRecebido = 0;
        $totalProdutos = 0;
        $custoProdutos = 0;
        $lucroProdutos = 0;
        $totalOrdensServico = 0;
        $quantidadeItens = 0;
        $quantidadeVendas = 0;

        $formasPagamento = [];

        foreach ($vendas as $venda) {
            $totalVenda = (float) $venda['total'];

            $totalRecebido += $totalVenda;

            $quantidadeVendas++;

            $formaPagamento =
                (string) $venda['forma_pagamento'];

            if (!isset($formasPagamento[$formaPagamento])) {
                $formasPagamento[$formaPagamento] = 0;
            }

            $formasPagamento[$formaPagamento] +=
                $totalVenda;
        }

        foreach ($produtosVendidos as $produto) {
            $totalProdutos +=
                (float) $produto['total_venda'];

            $custoProdutos +=
                (float) $produto['total_custo'];

            $lucroProdutos +=
                (float) $produto['lucro'];

            $quantidadeItens +=
                (int) $produto['quantidade'];
        }

        foreach ($ordensServico as $ordemServico) {
            $totalOrdensServico +=
                (float) $ordemServico['valor_recebido'];
        }

        $totalGastos = 0;

        foreach ($gastos as $gasto) {
            $totalGastos +=
                (float) $gasto['valor'];
        }

        /*
         * O resultado calculado aqui considera:
         *
         * lucro dos produtos
         * + valor recebido em ordens de serviço
         * - gastos cadastrados
         *
         * O custo interno das ordens de serviço não é
         * controlado pelo sistema.
         */
        $resultadoMes =
            $lucroProdutos
            + $totalOrdensServico
            - $totalGastos;

        ksort($formasPagamento);

        return [
            'mes' => $mes,
            'ano' => $ano,

            'periodo' => $periodo,

            'resumo' => [
                'total_recebido' =>
                    round($totalRecebido, 2),

                'total_produtos' =>
                    round($totalProdutos, 2),

                'custo_produtos' =>
                    round($custoProdutos, 2),

                'lucro_produtos' =>
                    round($lucroProdutos, 2),

                'total_ordens_servico' =>
                    round($totalOrdensServico, 2),

                'quantidade_ordens_servico' =>
                    count($ordensServico),

                'quantidade_itens' =>
                    $quantidadeItens,

                'quantidade_vendas' =>
                    $quantidadeVendas,

                'total_gastos' =>
                    round($totalGastos, 2),

                'resultado_mes' =>
                    round($resultadoMes, 2)
            ],

            'formas_pagamento' =>
                $formasPagamento,

            'produtos_vendidos' =>
                $produtosVendidos,

            'ordens_servico' =>
                $ordensServico,

            'gastos' =>
                $gastos
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | VENDAS DO PERÍODO
    |--------------------------------------------------------------------------
    */

    private function buscarVendasDoPeriodo(
        string $dataInicio,
        string $dataFim
    ): array {
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

                ordens_servico.id
                    AS ordem_servico_id,

                ordens_servico.numero_os,

                ordens_servico.aparelho_marca,

                ordens_servico.aparelho_modelo,

                ordens_servico.valor_total
                    AS valor_ordem_servico

            FROM vendas

            LEFT JOIN clientes
                ON clientes.id = vendas.cliente_id

            LEFT JOIN ordens_servico
                ON ordens_servico.venda_id = vendas.id

            WHERE
                vendas.status = 'concluida'

                AND vendas.criado_em >= :data_inicio

                AND vendas.criado_em < :data_fim

            ORDER BY vendas.criado_em ASC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':data_inicio' => $dataInicio,
            ':data_fim' => $dataFim
        ]);

        $vendas = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

        foreach ($vendas as &$venda) {
            $venda['itens'] =
                $this->buscarItensDaVenda(
                    (int) $venda['id']
                );
        }

        unset($venda);

        return $vendas;
    }

    private function buscarItensDaVenda(
        int $vendaId
    ): array {
        $sql = "
            SELECT
                itens_venda.id,

                itens_venda.produto_id,

                itens_venda.quantidade,

                itens_venda.preco_unitario,

                itens_venda.custo_unitario,

                itens_venda.total,

                produtos.nome AS produto_nome,

                produtos.marca AS produto_marca,

                produtos.codigo_barras

            FROM itens_venda

            INNER JOIN produtos
                ON produtos.id =
                    itens_venda.produto_id

            WHERE itens_venda.venda_id =
                :venda_id

            ORDER BY produtos.nome ASC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':venda_id' => $vendaId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CÁLCULO DOS PRODUTOS VENDIDOS
    |--------------------------------------------------------------------------
    */

    private function calcularProdutosVendidos(
        array $vendas
    ): array {
        $produtosAgrupados = [];

        foreach ($vendas as $venda) {
            $subtotalVenda =
                (float) $venda['subtotal'];

            $descontoVenda =
                (float) $venda['desconto'];

            $totalBrutoProdutos = 0;

            foreach ($venda['itens'] as $item) {
                $totalBrutoProdutos +=
                    (float) $item['total'];
            }

            /*
             * Divide proporcionalmente o desconto da venda
             * entre os produtos e a ordem de serviço.
             *
             * Assim, o relatório não apresenta um valor maior
             * do que o dinheiro realmente recebido.
             */
            $descontoProdutos = 0;

            if (
                $subtotalVenda > 0
                && $totalBrutoProdutos > 0
                && $descontoVenda > 0
            ) {
                $descontoProdutos =
                    $descontoVenda
                    * (
                        $totalBrutoProdutos
                        / $subtotalVenda
                    );
            }

            foreach ($venda['itens'] as $item) {
                $produtoId =
                    (int) $item['produto_id'];

                $quantidade =
                    (int) $item['quantidade'];

                $totalBrutoItem =
                    (float) $item['total'];

                $custoTotalItem =
                    (float) $item['custo_unitario']
                    * $quantidade;

                $descontoItem = 0;

                if (
                    $totalBrutoProdutos > 0
                    && $descontoProdutos > 0
                ) {
                    $descontoItem =
                        $descontoProdutos
                        * (
                            $totalBrutoItem
                            / $totalBrutoProdutos
                        );
                }

                $totalLiquidoItem =
                    $totalBrutoItem
                    - $descontoItem;

                $lucroItem =
                    $totalLiquidoItem
                    - $custoTotalItem;

                if (
                    !isset(
                        $produtosAgrupados[$produtoId]
                    )
                ) {
                    $produtosAgrupados[$produtoId] = [
                        'produto_id' =>
                            $produtoId,

                        'produto_nome' =>
                            $item['produto_nome'],

                        'produto_marca' =>
                            $item['produto_marca'],

                        'codigo_barras' =>
                            $item['codigo_barras'],

                        'quantidade' => 0,

                        'total_bruto' => 0,

                        'desconto' => 0,

                        'total_venda' => 0,

                        'total_custo' => 0,

                        'lucro' => 0
                    ];
                }

                $produtosAgrupados[$produtoId]
                    ['quantidade'] +=
                        $quantidade;

                $produtosAgrupados[$produtoId]
                    ['total_bruto'] +=
                        $totalBrutoItem;

                $produtosAgrupados[$produtoId]
                    ['desconto'] +=
                        $descontoItem;

                $produtosAgrupados[$produtoId]
                    ['total_venda'] +=
                        $totalLiquidoItem;

                $produtosAgrupados[$produtoId]
                    ['total_custo'] +=
                        $custoTotalItem;

                $produtosAgrupados[$produtoId]
                    ['lucro'] +=
                        $lucroItem;
            }
        }

        foreach ($produtosAgrupados as &$produto) {
            $produto['total_bruto'] =
                round(
                    (float) $produto['total_bruto'],
                    2
                );

            $produto['desconto'] =
                round(
                    (float) $produto['desconto'],
                    2
                );

            $produto['total_venda'] =
                round(
                    (float) $produto['total_venda'],
                    2
                );

            $produto['total_custo'] =
                round(
                    (float) $produto['total_custo'],
                    2
                );

            $produto['lucro'] =
                round(
                    (float) $produto['lucro'],
                    2
                );
        }

        unset($produto);

        usort(
            $produtosAgrupados,
            function (
                array $produtoA,
                array $produtoB
            ): int {
                return $produtoB['quantidade']
                    <=> $produtoA['quantidade'];
            }
        );

        return array_values(
            $produtosAgrupados
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CÁLCULO DAS ORDENS DE SERVIÇO
    |--------------------------------------------------------------------------
    */

    private function calcularOrdensServico(
        array $vendas
    ): array {
        $ordensServico = [];

        foreach ($vendas as $venda) {
            if (
                empty($venda['ordem_servico_id'])
            ) {
                continue;
            }

            $subtotalVenda =
                (float) $venda['subtotal'];

            $descontoVenda =
                (float) $venda['desconto'];

            $valorBrutoOrdem =
                (float) $venda[
                    'valor_ordem_servico'
                ];

            $descontoOrdem = 0;

            if (
                $subtotalVenda > 0
                && $valorBrutoOrdem > 0
                && $descontoVenda > 0
            ) {
                $descontoOrdem =
                    $descontoVenda
                    * (
                        $valorBrutoOrdem
                        / $subtotalVenda
                    );
            }

            $valorRecebido =
                $valorBrutoOrdem
                - $descontoOrdem;

            $aparelho = trim(
                (string) (
                    $venda['aparelho_marca']
                    . ' '
                    . $venda['aparelho_modelo']
                )
            );

            $ordensServico[] = [
                'id' =>
                    (int) $venda[
                        'ordem_servico_id'
                    ],

                'numero_os' =>
                    $venda['numero_os'],

                'cliente_nome' =>
                    $venda['cliente_nome']
                    ?: 'Cliente não informado',

                'aparelho' =>
                    $aparelho !== ''
                        ? $aparelho
                        : 'Não informado',

                'numero_venda' =>
                    $venda['numero_venda'],

                'forma_pagamento' =>
                    $venda['forma_pagamento'],

                'data_venda' =>
                    $venda['criado_em'],

                'valor_bruto' =>
                    round(
                        $valorBrutoOrdem,
                        2
                    ),

                'desconto' =>
                    round(
                        $descontoOrdem,
                        2
                    ),

                'valor_recebido' =>
                    round(
                        $valorRecebido,
                        2
                    )
            ];
        }

        usort(
            $ordensServico,
            function (
                array $ordemA,
                array $ordemB
            ): int {
                return strtotime(
                    $ordemB['data_venda']
                ) <=> strtotime(
                    $ordemA['data_venda']
                );
            }
        );

        return $ordensServico;
    }

    /*
    |--------------------------------------------------------------------------
    | GASTOS
    |--------------------------------------------------------------------------
    */

    public function listarGastos(
        int $mes,
        int $ano
    ): array {
        $periodo = $this->montarPeriodo(
            $mes,
            $ano
        );

        $sql = "
            SELECT
                id,
                descricao,
                categoria,
                valor,
                data_gasto,
                observacoes,
                criado_em,
                atualizado_em

            FROM gastos

            WHERE
                data_gasto >= :data_inicio

                AND data_gasto < :data_fim

            ORDER BY
                data_gasto DESC,
                id DESC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':data_inicio' =>
                substr(
                    $periodo['inicio'],
                    0,
                    10
                ),

            ':data_fim' =>
                substr(
                    $periodo['fim'],
                    0,
                    10
                )
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function buscarGastoPorId(
        int $gastoId
    ): ?array {
        $sql = "
            SELECT
                id,
                descricao,
                categoria,
                valor,
                data_gasto,
                observacoes,
                criado_em,
                atualizado_em

            FROM gastos

            WHERE id = :id

            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':id' => $gastoId
        ]);

        $gasto = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        return $gasto ?: null;
    }

    public function salvarGasto(
        array $dados
    ): int {
        $dadosValidados =
            $this->validarDadosGasto($dados);

        $sql = "
            INSERT INTO gastos (
                descricao,
                categoria,
                valor,
                data_gasto,
                observacoes,
                criado_em,
                atualizado_em
            ) VALUES (
                :descricao,
                :categoria,
                :valor,
                :data_gasto,
                :observacoes,
                NOW(),
                NOW()
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':descricao' =>
                $dadosValidados['descricao'],

            ':categoria' =>
                $dadosValidados['categoria'],

            ':valor' =>
                $dadosValidados['valor'],

            ':data_gasto' =>
                $dadosValidados['data_gasto'],

            ':observacoes' =>
                $dadosValidados['observacoes']
        ]);

        return (int)
            $this->conexao->lastInsertId();
    }

    public function atualizarGasto(
        int $gastoId,
        array $dados
    ): void {
        if (!$this->buscarGastoPorId($gastoId)) {
            throw new Exception(
                'Gasto não encontrado.'
            );
        }

        $dadosValidados =
            $this->validarDadosGasto($dados);

        $sql = "
            UPDATE gastos

            SET
                descricao = :descricao,

                categoria = :categoria,

                valor = :valor,

                data_gasto = :data_gasto,

                observacoes = :observacoes,

                atualizado_em = NOW()

            WHERE id = :id
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':descricao' =>
                $dadosValidados['descricao'],

            ':categoria' =>
                $dadosValidados['categoria'],

            ':valor' =>
                $dadosValidados['valor'],

            ':data_gasto' =>
                $dadosValidados['data_gasto'],

            ':observacoes' =>
                $dadosValidados['observacoes'],

            ':id' => $gastoId
        ]);
    }

    public function excluirGasto(
        int $gastoId
    ): void {
        if (!$this->buscarGastoPorId($gastoId)) {
            throw new Exception(
                'Gasto não encontrado.'
            );
        }

        $sql = "
            DELETE FROM gastos
            WHERE id = :id
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':id' => $gastoId
        ]);

        if ($stmt->rowCount() !== 1) {
            throw new Exception(
                'Não foi possível excluir o gasto.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÕES
    |--------------------------------------------------------------------------
    */

    private function validarDadosGasto(
        array $dados
    ): array {
        $descricao = trim(
            (string) (
                $dados['descricao'] ?? ''
            )
        );

        $categoria = trim(
            (string) (
                $dados['categoria'] ?? ''
            )
        );

        $valorRecebido =
            $dados['valor'] ?? 0;

        $dataGasto = trim(
            (string) (
                $dados['data_gasto'] ?? ''
            )
        );

        $observacoes = trim(
            (string) (
                $dados['observacoes'] ?? ''
            )
        );

        if ($descricao === '') {
            throw new Exception(
                'Informe a descrição do gasto.'
            );
        }

        if (mb_strlen($descricao) > 150) {
            throw new Exception(
                'A descrição do gasto deve possuir no máximo 150 caracteres.'
            );
        }

        if ($categoria === '') {
            throw new Exception(
                'Informe a categoria do gasto.'
            );
        }

        if (mb_strlen($categoria) > 100) {
            throw new Exception(
                'A categoria deve possuir no máximo 100 caracteres.'
            );
        }

        $valor = $this->converterValorMonetario(
            $valorRecebido
        );

        if ($valor <= 0) {
            throw new Exception(
                'O valor do gasto deve ser maior que zero.'
            );
        }

        if (!$this->dataValida($dataGasto)) {
            throw new Exception(
                'Informe uma data válida para o gasto.'
            );
        }

        return [
            'descricao' => $descricao,
            'categoria' => $categoria,
            'valor' => round($valor, 2),
            'data_gasto' => $dataGasto,
            'observacoes' =>
                $observacoes !== ''
                    ? $observacoes
                    : null
        ];
    }

    private function converterValorMonetario(
        mixed $valor
    ): float {
        if (is_int($valor) || is_float($valor)) {
            return (float) $valor;
        }

        $valor = trim(
            (string) $valor
        );

        if ($valor === '') {
            return 0;
        }

        $valor = str_replace(
            ['R$', ' '],
            '',
            $valor
        );

        /*
         * Aceita:
         * 1500.50
         * 1500,50
         * 1.500,50
         */
        if (
            str_contains($valor, ',')
        ) {
            $valor = str_replace(
                '.',
                '',
                $valor
            );

            $valor = str_replace(
                ',',
                '.',
                $valor
            );
        }

        if (!is_numeric($valor)) {
            throw new Exception(
                'Informe um valor válido para o gasto.'
            );
        }

        return (float) $valor;
    }

    private function dataValida(
        string $data
    ): bool {
        $objetoData = DateTime::createFromFormat(
            'Y-m-d',
            $data
        );

        return
            $objetoData !== false
            && $objetoData->format('Y-m-d')
                === $data;
    }

    /*
    |--------------------------------------------------------------------------
    | PERÍODO
    |--------------------------------------------------------------------------
    */

    private function montarPeriodo(
        int $mes,
        int $ano
    ): array {
        if ($mes < 1 || $mes > 12) {
            $mes = (int) date('m');
        }

        if ($ano < 2000 || $ano > 2100) {
            $ano = (int) date('Y');
        }

        $inicio = sprintf(
            '%04d-%02d-01 00:00:00',
            $ano,
            $mes
        );

        $dataInicio = new DateTime($inicio);

        $dataFim = clone $dataInicio;

        $dataFim->modify(
            'first day of next month'
        );

        return [
            'inicio' =>
                $dataInicio->format(
                    'Y-m-d H:i:s'
                ),

            'fim' =>
                $dataFim->format(
                    'Y-m-d H:i:s'
                )
        ];
    }
}