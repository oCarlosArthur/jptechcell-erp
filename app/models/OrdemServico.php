<?php

require_once __DIR__ . '/../core/Database.php';

class OrdemServico
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Database::conectar();
    }

    public function listarTodas(string $pesquisa = ''): array
    {
        $sql = "
            SELECT
                os.id,
                os.numero_os,
                os.cliente_id,
                os.aparelho_marca,
                os.aparelho_modelo,
                os.aparelho_cor,
                os.aparelho_imei,
                os.defeito_relatado,
                os.valor_os,
                os.desconto,
                os.valor_total,
                os.status,
                os.data_entrada,
                os.data_prevista,
                os.data_conclusao,
                os.data_entrega,
                clientes.nome AS cliente_nome,
                clientes.telefone AS cliente_telefone,
                clientes.whatsapp AS cliente_whatsapp
            FROM ordens_servico AS os

            INNER JOIN clientes
                ON clientes.id = os.cliente_id

            WHERE 1 = 1
        ";

        $parametros = [];

        if ($pesquisa !== '') {
            $sql .= "
                AND (
                    os.numero_os LIKE :pesquisa
                    OR clientes.nome LIKE :pesquisa
                    OR clientes.telefone LIKE :pesquisa
                    OR clientes.whatsapp LIKE :pesquisa
                    OR os.aparelho_marca LIKE :pesquisa
                    OR os.aparelho_modelo LIKE :pesquisa
                    OR os.aparelho_imei LIKE :pesquisa
                    OR os.defeito_relatado LIKE :pesquisa
                    OR os.status LIKE :pesquisa
                )
            ";

            $parametros[':pesquisa'] = '%' . $pesquisa . '%';
        }

        $sql .= " ORDER BY os.id DESC";

        $comando = $this->conexao->prepare($sql);
        $comando->execute($parametros);

        return $comando->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cadastrar(array $dados): int
    {
        try {
            $this->conexao->beginTransaction();

            $numeroTemporario = 'TEMP-' . uniqid();

            $sql = "
                INSERT INTO ordens_servico (
                    numero_os,
                    orcamento_id,
                    venda_id,
                    cliente_id,
                    aparelho_marca,
                    aparelho_modelo,
                    aparelho_cor,
                    aparelho_imei,
                    aparelho_senha,
                    aparelho_ligado,
                    aparelho_desbloqueado,
                    acessorios_entregues,
                    defeito_relatado,
                    diagnostico,
                    servico_realizado,
                    valor_os,
                    desconto,
                    valor_total,
                    garantia_dias,
                    status,
                    data_entrada,
                    data_prevista,
                    observacoes
                ) VALUES (
                    :numero_os,
                    NULL,
                    NULL,
                    :cliente_id,
                    :aparelho_marca,
                    :aparelho_modelo,
                    :aparelho_cor,
                    :aparelho_imei,
                    :aparelho_senha,
                    :aparelho_ligado,
                    :aparelho_desbloqueado,
                    :acessorios_entregues,
                    :defeito_relatado,
                    :diagnostico,
                    :servico_realizado,
                    :valor_os,
                    :desconto,
                    :valor_total,
                    :garantia_dias,
                    :status,
                    :data_entrada,
                    :data_prevista,
                    :observacoes
                )
            ";

            $comando = $this->conexao->prepare($sql);

            $comando->execute([
                ':numero_os' => $numeroTemporario,
                ':cliente_id' => $dados['cliente_id'],
                ':aparelho_marca' => $this->valorOuNull(
                    $dados['aparelho_marca'] ?? null
                ),
                ':aparelho_modelo' => $dados['aparelho_modelo'],
                ':aparelho_cor' => $this->valorOuNull(
                    $dados['aparelho_cor'] ?? null
                ),
                ':aparelho_imei' => $this->valorOuNull(
                    $dados['aparelho_imei'] ?? null
                ),
                ':aparelho_senha' => $this->valorOuNull(
                    $dados['aparelho_senha'] ?? null
                ),
                ':aparelho_ligado' =>
                    $dados['aparelho_ligado'] ?? 0,
                ':aparelho_desbloqueado' =>
                    $dados['aparelho_desbloqueado'] ?? 0,
                ':acessorios_entregues' => $this->valorOuNull(
                    $dados['acessorios_entregues'] ?? null
                ),
                ':defeito_relatado' =>
                    $dados['defeito_relatado'],
                ':diagnostico' => $this->valorOuNull(
                    $dados['diagnostico'] ?? null
                ),
                ':servico_realizado' => $this->valorOuNull(
                    $dados['servico_realizado'] ?? null
                ),
                ':valor_os' => $dados['valor_os'],
                ':desconto' => $dados['desconto'] ?? 0,
                ':valor_total' => $dados['valor_total'],
                ':garantia_dias' =>
                    $dados['garantia_dias'] ?? 0,
                ':status' => $dados['status'] ?? 'recebida',
                ':data_entrada' => $dados['data_entrada'],
                ':data_prevista' => $this->valorOuNull(
                    $dados['data_prevista'] ?? null
                ),
                ':observacoes' => $this->valorOuNull(
                    $dados['observacoes'] ?? null
                )
            ]);

            $ordemServicoId =
                (int) $this->conexao->lastInsertId();

            $numeroOs = 'OS-' . str_pad(
                (string) $ordemServicoId,
                6,
                '0',
                STR_PAD_LEFT
            );

            $sqlNumero = "
                UPDATE ordens_servico
                SET numero_os = :numero_os
                WHERE id = :id
            ";

            $comandoNumero =
                $this->conexao->prepare($sqlNumero);

            $comandoNumero->execute([
                ':numero_os' => $numeroOs,
                ':id' => $ordemServicoId
            ]);

            $this->registrarHistorico(
                $ordemServicoId,
                'criacao',
                null,
                null,
                null,
                'Ordem de serviço criada.'
            );

            $this->conexao->commit();

            return $ordemServicoId;
        } catch (Throwable $erro) {
            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw $erro;
        }
    }

    public function buscarPorId(int $id): array|false
    {
        $sql = "
            SELECT
                os.*,
                clientes.nome AS cliente_nome,
                clientes.tipo AS cliente_tipo,
                clientes.cpf_cnpj AS cliente_cpf_cnpj,
                clientes.telefone AS cliente_telefone,
                clientes.whatsapp AS cliente_whatsapp,
                clientes.email AS cliente_email,
                clientes.cep AS cliente_cep,
                clientes.endereco AS cliente_endereco,
                clientes.numero AS cliente_numero,
                clientes.bairro AS cliente_bairro,
                clientes.cidade AS cliente_cidade,
                clientes.estado AS cliente_estado
            FROM ordens_servico AS os

            INNER JOIN clientes
                ON clientes.id = os.cliente_id

            WHERE os.id = :id
            LIMIT 1
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->execute([
            ':id' => $id
        ]);

        return $comando->fetch(PDO::FETCH_ASSOC);
    }

    public function listarHistorico(int $ordemServicoId): array
    {
        $sql = "
            SELECT
                id,
                ordem_servico_id,
                tipo,
                campo,
                valor_anterior,
                valor_novo,
                descricao,
                criado_em
            FROM historico_ordens_servico
            WHERE ordem_servico_id = :ordem_servico_id
            ORDER BY criado_em DESC, id DESC
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->execute([
            ':ordem_servico_id' => $ordemServicoId
        ]);

        return $comando->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizar(int $id, array $dados): bool
    {
        try {
            $this->conexao->beginTransaction();

            $ordemAnterior = $this->buscarPorId($id);

            if (!$ordemAnterior) {
                throw new Exception(
                    'Ordem de serviço não encontrada.'
                );
            }

            $dadosNovos = [
                'cliente_id' => (int) $dados['cliente_id'],
                'aparelho_marca' => $this->valorOuNull(
                    $dados['aparelho_marca'] ?? null
                ),
                'aparelho_modelo' =>
                    trim((string) $dados['aparelho_modelo']),
                'aparelho_cor' => $this->valorOuNull(
                    $dados['aparelho_cor'] ?? null
                ),
                'aparelho_imei' => $this->valorOuNull(
                    $dados['aparelho_imei'] ?? null
                ),
                'aparelho_senha' => $this->valorOuNull(
                    $dados['aparelho_senha'] ?? null
                ),
                'aparelho_ligado' =>
                    (int) ($dados['aparelho_ligado'] ?? 0),
                'aparelho_desbloqueado' =>
                    (int) (
                        $dados['aparelho_desbloqueado'] ?? 0
                    ),
                'acessorios_entregues' => $this->valorOuNull(
                    $dados['acessorios_entregues'] ?? null
                ),
                'defeito_relatado' =>
                    trim((string) $dados['defeito_relatado']),
                'diagnostico' => $this->valorOuNull(
                    $dados['diagnostico'] ?? null
                ),
                'servico_realizado' => $this->valorOuNull(
                    $dados['servico_realizado'] ?? null
                ),
                'valor_os' => (float) $dados['valor_os'],
                'desconto' =>
                    (float) ($dados['desconto'] ?? 0),
                'valor_total' =>
                    (float) $dados['valor_total'],
                'garantia_dias' =>
                    (int) ($dados['garantia_dias'] ?? 0),
                'data_prevista' => $this->valorOuNull(
                    $dados['data_prevista'] ?? null
                ),
                'observacoes' => $this->valorOuNull(
                    $dados['observacoes'] ?? null
                )
            ];

            $sql = "
                UPDATE ordens_servico
                SET
                    cliente_id = :cliente_id,
                    aparelho_marca = :aparelho_marca,
                    aparelho_modelo = :aparelho_modelo,
                    aparelho_cor = :aparelho_cor,
                    aparelho_imei = :aparelho_imei,
                    aparelho_senha = :aparelho_senha,
                    aparelho_ligado = :aparelho_ligado,
                    aparelho_desbloqueado =
                        :aparelho_desbloqueado,
                    acessorios_entregues =
                        :acessorios_entregues,
                    defeito_relatado = :defeito_relatado,
                    diagnostico = :diagnostico,
                    servico_realizado = :servico_realizado,
                    valor_os = :valor_os,
                    desconto = :desconto,
                    valor_total = :valor_total,
                    garantia_dias = :garantia_dias,
                    data_prevista = :data_prevista,
                    observacoes = :observacoes
                WHERE id = :id
            ";

            $comando = $this->conexao->prepare($sql);

            $resultado = $comando->execute([
                ':cliente_id' => $dadosNovos['cliente_id'],
                ':aparelho_marca' =>
                    $dadosNovos['aparelho_marca'],
                ':aparelho_modelo' =>
                    $dadosNovos['aparelho_modelo'],
                ':aparelho_cor' =>
                    $dadosNovos['aparelho_cor'],
                ':aparelho_imei' =>
                    $dadosNovos['aparelho_imei'],
                ':aparelho_senha' =>
                    $dadosNovos['aparelho_senha'],
                ':aparelho_ligado' =>
                    $dadosNovos['aparelho_ligado'],
                ':aparelho_desbloqueado' =>
                    $dadosNovos['aparelho_desbloqueado'],
                ':acessorios_entregues' =>
                    $dadosNovos['acessorios_entregues'],
                ':defeito_relatado' =>
                    $dadosNovos['defeito_relatado'],
                ':diagnostico' =>
                    $dadosNovos['diagnostico'],
                ':servico_realizado' =>
                    $dadosNovos['servico_realizado'],
                ':valor_os' => $dadosNovos['valor_os'],
                ':desconto' => $dadosNovos['desconto'],
                ':valor_total' =>
                    $dadosNovos['valor_total'],
                ':garantia_dias' =>
                    $dadosNovos['garantia_dias'],
                ':data_prevista' =>
                    $dadosNovos['data_prevista'],
                ':observacoes' =>
                    $dadosNovos['observacoes'],
                ':id' => $id
            ]);

            $nomesCampos = [
                'cliente_id' => 'Cliente',
                'aparelho_marca' => 'Marca do aparelho',
                'aparelho_modelo' => 'Modelo do aparelho',
                'aparelho_cor' => 'Cor do aparelho',
                'aparelho_imei' => 'IMEI ou número de série',
                'aparelho_senha' =>
                    'Senha ou padrão de desbloqueio',
                'aparelho_ligado' => 'Aparelho liga',
                'aparelho_desbloqueado' =>
                    'Aparelho desbloqueado',
                'acessorios_entregues' =>
                    'Acessórios entregues',
                'defeito_relatado' => 'Defeito relatado',
                'diagnostico' => 'Diagnóstico',
                'servico_realizado' => 'Serviço realizado',
                'valor_os' => 'Valor da OS',
                'desconto' => 'Desconto',
                'valor_total' => 'Total final',
                'garantia_dias' => 'Garantia',
                'data_prevista' => 'Data prevista',
                'observacoes' => 'Observações'
            ];

            foreach ($dadosNovos as $campo => $valorNovo) {
                $valorAnterior =
                    $ordemAnterior[$campo] ?? null;

                if (!$this->valoresSaoDiferentes(
                    $campo,
                    $valorAnterior,
                    $valorNovo
                )) {
                    continue;
                }

                $anteriorFormatado =
                    $this->formatarValorHistorico(
                        $campo,
                        $valorAnterior
                    );

                $novoFormatado =
                    $this->formatarValorHistorico(
                        $campo,
                        $valorNovo
                    );

                if ($campo === 'cliente_id') {
                    $anteriorFormatado =
                        (string) (
                            $ordemAnterior['cliente_nome']
                            ?? $valorAnterior
                        );

                    $novoFormatado =
                        $this->buscarNomeCliente(
                            (int) $valorNovo
                        );
                }

                $descricao =
                    ($nomesCampos[$campo] ?? $campo)
                    . ' alterado.';

                $this->registrarHistorico(
                    $id,
                    'edicao',
                    $campo,
                    $anteriorFormatado,
                    $novoFormatado,
                    $descricao
                );
            }

            $this->conexao->commit();

            return $resultado;
        } catch (Throwable $erro) {
            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw $erro;
        }
    }

    public function atualizarStatus(
        int $id,
        string $status
    ): bool {
        try {
            $this->conexao->beginTransaction();

            $ordemAnterior = $this->buscarPorId($id);

            if (!$ordemAnterior) {
                throw new Exception(
                    'Ordem de serviço não encontrada.'
                );
            }

            $statusAnterior =
                (string) ($ordemAnterior['status'] ?? '');

            if ($statusAnterior === $status) {
                $this->conexao->commit();

                return true;
            }

            if ($status === 'pronta') {
                $sql = "
                    UPDATE ordens_servico
                    SET
                        status = :status,
                        data_conclusao = NOW()
                    WHERE id = :id
                ";
            } else {
                $sql = "
                    UPDATE ordens_servico
                    SET
                        status = :status,
                        data_conclusao = NULL
                    WHERE id = :id
                ";
            }

            $comando = $this->conexao->prepare($sql);

            $resultado = $comando->execute([
                ':status' => $status,
                ':id' => $id
            ]);

            $this->registrarHistorico(
                $id,
                'status',
                'status',
                $this->nomeStatus($statusAnterior),
                $this->nomeStatus($status),
                'Status da ordem de serviço alterado.'
            );

            $this->conexao->commit();

            return $resultado;
        } catch (Throwable $erro) {
            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw $erro;
        }
    }

    private function registrarHistorico(
        int $ordemServicoId,
        string $tipo,
        ?string $campo,
        mixed $valorAnterior,
        mixed $valorNovo,
        string $descricao
    ): void {
        $sql = "
            INSERT INTO historico_ordens_servico (
                ordem_servico_id,
                tipo,
                campo,
                valor_anterior,
                valor_novo,
                descricao,
                criado_em
            ) VALUES (
                :ordem_servico_id,
                :tipo,
                :campo,
                :valor_anterior,
                :valor_novo,
                :descricao,
                NOW()
            )
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->execute([
            ':ordem_servico_id' => $ordemServicoId,
            ':tipo' => $tipo,
            ':campo' => $campo,
            ':valor_anterior' => $this->valorOuNull(
                $valorAnterior
            ),
            ':valor_novo' => $this->valorOuNull(
                $valorNovo
            ),
            ':descricao' => $descricao
        ]);
    }

    private function buscarNomeCliente(int $clienteId): string
    {
        $sql = "
            SELECT nome
            FROM clientes
            WHERE id = :id
            LIMIT 1
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->execute([
            ':id' => $clienteId
        ]);

        $nome = $comando->fetchColumn();

        return $nome !== false
            ? (string) $nome
            : 'Cliente #' . $clienteId;
    }

    private function valoresSaoDiferentes(
        string $campo,
        mixed $valorAnterior,
        mixed $valorNovo
    ): bool {
        $camposMonetarios = [
            'valor_os',
            'desconto',
            'valor_total'
        ];

        if (in_array($campo, $camposMonetarios, true)) {
            return round((float) $valorAnterior, 2)
                !== round((float) $valorNovo, 2);
        }

        $camposInteiros = [
            'cliente_id',
            'aparelho_ligado',
            'aparelho_desbloqueado',
            'garantia_dias'
        ];

        if (in_array($campo, $camposInteiros, true)) {
            return (int) $valorAnterior
                !== (int) $valorNovo;
        }

        return trim((string) ($valorAnterior ?? ''))
            !== trim((string) ($valorNovo ?? ''));
    }

    private function formatarValorHistorico(
        string $campo,
        mixed $valor
    ): string {
        if ($valor === null || $valor === '') {
            return 'Não informado';
        }

        if (in_array(
            $campo,
            ['valor_os', 'desconto', 'valor_total'],
            true
        )) {
            return 'R$ ' . number_format(
                (float) $valor,
                2,
                ',',
                '.'
            );
        }

        if (in_array(
            $campo,
            ['aparelho_ligado', 'aparelho_desbloqueado'],
            true
        )) {
            return (string) $valor === '1'
                ? 'Sim'
                : 'Não';
        }

        if ($campo === 'garantia_dias') {
            return (int) $valor . ' dias';
        }

        if ($campo === 'data_prevista') {
            $timestamp = strtotime((string) $valor);

            return $timestamp !== false
                ? date('d/m/Y', $timestamp)
                : (string) $valor;
        }

        return (string) $valor;
    }

    private function nomeStatus(string $status): string
    {
        $nomes = [
            'recebida' => 'Recebida',
            'em_analise' => 'Em análise',
            'aguardando_aprovacao' =>
                'Aguardando aprovação',
            'aguardando_peca' => 'Aguardando peça',
            'em_manutencao' => 'Em manutenção',
            'pronta' => 'Pronta',
            'entregue' => 'Entregue',
            'cancelada' => 'Cancelada'
        ];

        return $nomes[$status]
            ?? ucfirst(str_replace('_', ' ', $status));
    }

    private function valorOuNull(mixed $valor): mixed
    {
        if (is_string($valor)) {
            $valor = trim($valor);

            return $valor === '' ? null : $valor;
        }

        return $valor;
    }
}