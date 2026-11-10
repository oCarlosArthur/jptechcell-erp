<?php

require_once __DIR__ . '/../core/Database.php';

class Dashboard
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Database::conectar();
    }

    public function totalProdutos(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM produtos
            WHERE ativo = 1
        ";

        return (int) $this->conexao
            ->query($sql)
            ->fetchColumn();
    }

    public function estoqueBaixo(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM produtos
            WHERE
                ativo = 1
                AND quantidade_estoque <= estoque_minimo
        ";

        return (int) $this->conexao
            ->query($sql)
            ->fetchColumn();
    }

    public function ordensEmAndamento(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM ordens_servico
            WHERE status IN (
                'recebida',
                'em_analise',
                'aguardando_aprovacao',
                'aguardando_peca',
                'em_manutencao'
            )
        ";

        return (int) $this->conexao
            ->query($sql)
            ->fetchColumn();
    }

    public function ordensProntas(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM ordens_servico
            WHERE status = 'pronta'
        ";

        return (int) $this->conexao
            ->query($sql)
            ->fetchColumn();
    }

    public function ordensRecebidasHoje(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM ordens_servico
            WHERE DATE(data_entrada) = CURDATE()
        ";

        return (int) $this->conexao
            ->query($sql)
            ->fetchColumn();
    }

    public function aguardandoAprovacao(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM ordens_servico
            WHERE status = 'aguardando_aprovacao'
        ";

        return (int) $this->conexao
            ->query($sql)
            ->fetchColumn();
    }

    public function aguardandoPeca(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM ordens_servico
            WHERE status = 'aguardando_peca'
        ";

        return (int) $this->conexao
            ->query($sql)
            ->fetchColumn();
    }

    public function emManutencao(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM ordens_servico
            WHERE status = 'em_manutencao'
        ";

        return (int) $this->conexao
            ->query($sql)
            ->fetchColumn();
    }

    public function ultimasOrdensServico(
        int $limite = 5
    ): array {
        $sql = "
            SELECT
                os.id,
                os.numero_os,
                os.status,
                os.data_entrada,
                clientes.nome AS cliente
            FROM ordens_servico AS os

            INNER JOIN clientes
                ON clientes.id = os.cliente_id

            ORDER BY os.id DESC

            LIMIT :limite
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':limite',
            $limite,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ultimasVendas(
        int $limite = 5
    ): array {
        $sql = "
            SELECT
                vendas.id,
                vendas.numero_venda,
                vendas.total,
                vendas.status,
                vendas.criado_em,
                clientes.nome AS cliente
            FROM vendas

            LEFT JOIN clientes
                ON clientes.id = vendas.cliente_id

            ORDER BY vendas.id DESC

            LIMIT :limite
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':limite',
            $limite,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}