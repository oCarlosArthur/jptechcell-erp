<?php

require_once __DIR__ . '/../core/Database.php';

class MovimentacaoEstoque
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Database::conectar();
    }

    public function movimentar(
        int $produtoId,
        string $tipo,
        int $quantidade,
        string $motivo = ''
    ): bool
    {
        try {

            $this->conexao->beginTransaction();

            $sql = "
                SELECT quantidade_estoque
                FROM produtos
                WHERE id = :id
                LIMIT 1
            ";

            $comando = $this->conexao->prepare($sql);

            $comando->execute([
                ':id' => $produtoId
            ]);

            $produto = $comando->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                throw new Exception('Produto não encontrado.');
            }

            $estoqueAnterior = (int) $produto['quantidade_estoque'];

            switch ($tipo) {

                case 'entrada':
                    $estoqueAtual = $estoqueAnterior + $quantidade;
                    break;

                case 'saida':

                    if ($quantidade > $estoqueAnterior) {
                        throw new Exception(
                            'Estoque insuficiente.'
                        );
                    }

                    $estoqueAtual = $estoqueAnterior - $quantidade;
                    break;

                case 'ajuste':
                    $estoqueAtual = $quantidade;
                    break;

                default:
                    throw new Exception(
                        'Tipo de movimentação inválido.'
                    );
            }

            $sql = "
                UPDATE produtos
                SET quantidade_estoque = :estoque
                WHERE id = :id
            ";

            $comando = $this->conexao->prepare($sql);

            $comando->execute([
                ':estoque' => $estoqueAtual,
                ':id' => $produtoId
            ]);

            $sql = "
                INSERT INTO movimentacoes_estoque
                (
                    produto_id,
                    tipo,
                    quantidade,
                    quantidade_anterior,
                    quantidade_atual,
                    motivo
                )
                VALUES
                (
                    :produto,
                    :tipo,
                    :quantidade,
                    :anterior,
                    :atual,
                    :motivo
                )
            ";

            $comando = $this->conexao->prepare($sql);

            $comando->execute([
                ':produto' => $produtoId,
                ':tipo' => $tipo,
                ':quantidade' => $quantidade,
                ':anterior' => $estoqueAnterior,
                ':atual' => $estoqueAtual,
                ':motivo' => $motivo
            ]);

            $this->conexao->commit();

            return true;

        } catch (Throwable $erro) {

            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw $erro;
        }
    }

    public function listarPorProduto(int $produtoId): array
{
    $sql = "
        SELECT *
        FROM movimentacoes_estoque
        WHERE produto_id = :produto_id
        ORDER BY criado_em DESC, id DESC
    ";

    $stmt = $this->conexao->prepare($sql);

    $stmt->bindValue(
        ':produto_id',
        $produtoId,
        PDO::PARAM_INT
    );

    $stmt->execute();

    return $stmt->fetchAll();
}
}