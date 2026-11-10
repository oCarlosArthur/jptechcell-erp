<?php

require_once __DIR__ . '/../core/Database.php';

class Produto
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Database::conectar();
    }

    public function listarTodos(string $pesquisa = ''): array
    {
        $sql = "
            SELECT
                id,
                codigo_barras,
                nome,
                marca,
                preco_custo,
                preco_venda,
                quantidade_estoque,
                estoque_minimo,
                criado_em,
                atualizado_em
            FROM produtos
            WHERE ativo = 1
        ";

        $parametros = [];

        if ($pesquisa !== '') {
            $sql .= "
                AND (
                    nome LIKE :pesquisa
                    OR marca LIKE :pesquisa
                    OR codigo_barras LIKE :pesquisa
                )
            ";

            $parametros[':pesquisa'] = '%' . $pesquisa . '%';
        }

        $sql .= " ORDER BY id DESC";

        $comando = $this->conexao->prepare($sql);
        $comando->execute($parametros);

        return $comando->fetchAll(PDO::FETCH_ASSOC);
    }
    public function cadastrar(array $dados): int
    {
        try {
            $this->conexao->beginTransaction();

            $sql = "
                INSERT INTO produtos (
                    codigo_etiqueta,
                    codigo_barras,
                    nome,
                    marca,
                    preco_custo,
                    preco_venda,
                    quantidade_estoque,
                    estoque_minimo,
                    ativo
                ) VALUES (
                    NULL,
                    NULL,
                    :nome,
                    :marca,
                    :preco_custo,
                    :preco_venda,
                    :quantidade_estoque,
                    :estoque_minimo,
                    1
                )
            ";

            $comando = $this->conexao->prepare($sql);

            $comando->execute([
                ':nome' => $dados['nome'],
                ':marca' => $dados['marca'],
                ':preco_custo' => $dados['preco_custo'],
                ':preco_venda' => $dados['preco_venda'],
                ':quantidade_estoque' => $dados['quantidade_estoque'],
                ':estoque_minimo' => $dados['estoque_minimo']
            ]);

            $produtoId = (int) $this->conexao->lastInsertId();

            $codigoBarras = 'JP' . str_pad(
                (string) $produtoId,
                6,
                '0',
                STR_PAD_LEFT
            );

            $sqlCodigo = "
                UPDATE produtos
                SET codigo_barras = :codigo_barras
                WHERE id = :id
            ";

            $comandoCodigo = $this->conexao->prepare($sqlCodigo);

            $comandoCodigo->execute([
                ':codigo_barras' => $codigoBarras,
                ':id' => $produtoId
            ]);

            if ($dados['quantidade_estoque'] > 0) {
                $sqlMovimentacao = "
                    INSERT INTO movimentacoes_estoque (
                        produto_id,
                        tipo,
                        quantidade,
                        quantidade_anterior,
                        quantidade_atual,
                        motivo
                    ) VALUES (
                        :produto_id,
                        'entrada',
                        :quantidade,
                        0,
                        :quantidade_atual,
                        'Estoque inicial do produto'
                    )
                ";

                $comandoMovimentacao = $this->conexao->prepare(
                    $sqlMovimentacao
                );

                $comandoMovimentacao->execute([
                    ':produto_id' => $produtoId,
                    ':quantidade' => $dados['quantidade_estoque'],
                    ':quantidade_atual' => $dados['quantidade_estoque']
                ]);
            }

            $this->conexao->commit();

            return $produtoId;
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
            id,
            codigo_barras,
            nome,
            marca,
            preco_custo,
            preco_venda,
            quantidade_estoque,
            estoque_minimo,
            criado_em,
            atualizado_em
        FROM produtos
        WHERE id = :id
        LIMIT 1
    ";

    $comando = $this->conexao->prepare($sql);

    $comando->execute([
        ':id' => $id
    ]);

    return $comando->fetch(PDO::FETCH_ASSOC);
}

public function atualizar(int $id, array $dados): bool
{
    $sql = "
        UPDATE produtos
        SET
            nome = :nome,
            marca = :marca,
            preco_custo = :preco_custo,
            preco_venda = :preco_venda,
            estoque_minimo = :estoque_minimo
        WHERE id = :id
    ";

    $comando = $this->conexao->prepare($sql);

    return $comando->execute([
        ':nome' => $dados['nome'],
        ':marca' => $dados['marca'],
        ':preco_custo' => $dados['preco_custo'],
        ':preco_venda' => $dados['preco_venda'],
        ':estoque_minimo' => $dados['estoque_minimo'],
        ':id' => $id
    ]);
}

public function desativar(int $id): bool
{
    $sql = "
        UPDATE produtos
        SET ativo = 0
        WHERE id = :id
    ";

    $comando = $this->conexao->prepare($sql);

    return $comando->execute([
        ':id' => $id
    ]);
}
}