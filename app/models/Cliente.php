<?php

require_once __DIR__ . '/../core/Database.php';

class Cliente
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Database::conectar();
    }

    public function listarTodos(string $pesquisa = ''): array
    {
        $sql = "
            SELECT *
            FROM clientes
            WHERE ativo = 1
        ";

        $parametros = [];

        if ($pesquisa !== '') {
            $sql .= "
                AND (
                    nome LIKE :pesquisa
                    OR cpf_cnpj LIKE :pesquisa
                    OR telefone LIKE :pesquisa
                    OR whatsapp LIKE :pesquisa
                )
            ";

            $parametros[':pesquisa'] = '%' . $pesquisa . '%';
        }

        $sql .= "
            ORDER BY nome ASC
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }

    public function cadastrar(array $dados): int
    {
        $sql = "
            INSERT INTO clientes (
                tipo,
                nome,
                cpf_cnpj,
                telefone,
                whatsapp,
                email,
                data_nascimento,
                cep,
                endereco,
                numero,
                bairro,
                cidade,
                estado,
                observacoes
            ) VALUES (
                :tipo,
                :nome,
                :cpf_cnpj,
                :telefone,
                :whatsapp,
                :email,
                :data_nascimento,
                :cep,
                :endereco,
                :numero,
                :bairro,
                :cidade,
                :estado,
                :observacoes
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':tipo' => $dados['tipo'],
            ':nome' => $dados['nome'],
            ':cpf_cnpj' => $dados['cpf_cnpj'],
            ':telefone' => $dados['telefone'],
            ':whatsapp' => $dados['whatsapp'],
            ':email' => $dados['email'],
            ':data_nascimento' => $dados['data_nascimento'],
            ':cep' => $dados['cep'],
            ':endereco' => $dados['endereco'],
            ':numero' => $dados['numero'],
            ':bairro' => $dados['bairro'],
            ':cidade' => $dados['cidade'],
            ':estado' => $dados['estado'],
            ':observacoes' => $dados['observacoes']
        ]);

        return (int) $this->conexao->lastInsertId();
    }

    public function buscarPorId(int $id): array|false
    {
        $sql = "
            SELECT *
            FROM clientes
            WHERE id = :id
              AND ativo = 1
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $sql = "
            UPDATE clientes
            SET
                tipo = :tipo,
                nome = :nome,
                cpf_cnpj = :cpf_cnpj,
                telefone = :telefone,
                whatsapp = :whatsapp,
                email = :email,
                data_nascimento = :data_nascimento,
                cep = :cep,
                endereco = :endereco,
                numero = :numero,
                bairro = :bairro,
                cidade = :cidade,
                estado = :estado,
                observacoes = :observacoes
            WHERE id = :id
              AND ativo = 1
        ";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':tipo' => $dados['tipo'],
            ':nome' => $dados['nome'],
            ':cpf_cnpj' => $dados['cpf_cnpj'],
            ':telefone' => $dados['telefone'],
            ':whatsapp' => $dados['whatsapp'],
            ':email' => $dados['email'],
            ':data_nascimento' => $dados['data_nascimento'],
            ':cep' => $dados['cep'],
            ':endereco' => $dados['endereco'],
            ':numero' => $dados['numero'],
            ':bairro' => $dados['bairro'],
            ':cidade' => $dados['cidade'],
            ':estado' => $dados['estado'],
            ':observacoes' => $dados['observacoes']
        ]);
    }

    public function desativar(int $id): bool
    {
        $sql = "
            UPDATE clientes
            SET ativo = 0
            WHERE id = :id
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    public function buscarPorCpfCnpj(
    string $cpfCnpj,
    ?int $ignorarId = null
): array|false {

    $sql = "
        SELECT *
        FROM clientes
        WHERE cpf_cnpj = :cpf_cnpj
    ";

    $parametros = [
        ':cpf_cnpj' => $cpfCnpj
    ];

    if ($ignorarId !== null) {
        $sql .= "
            AND id != :ignorar_id
        ";

        $parametros[':ignorar_id'] = $ignorarId;
    }

    $sql .= "
        LIMIT 1
    ";

    $stmt = $this->conexao->prepare($sql);
    $stmt->execute($parametros);

    return $stmt->fetch();
}


public function reativarEAtualizar(
    int $id,
    array $dados
): bool {

    $sql = "
        UPDATE clientes
        SET
            tipo = :tipo,
            nome = :nome,
            cpf_cnpj = :cpf_cnpj,
            telefone = :telefone,
            whatsapp = :whatsapp,
            email = :email,
            data_nascimento = :data_nascimento,
            cep = :cep,
            endereco = :endereco,
            numero = :numero,
            bairro = :bairro,
            cidade = :cidade,
            estado = :estado,
            observacoes = :observacoes,
            ativo = 1
        WHERE id = :id
    ";

    $stmt = $this->conexao->prepare($sql);

    return $stmt->execute([
        ':id' => $id,
        ':tipo' => $dados['tipo'],
        ':nome' => $dados['nome'],
        ':cpf_cnpj' => $dados['cpf_cnpj'],
        ':telefone' => $dados['telefone'],
        ':whatsapp' => $dados['whatsapp'],
        ':email' => $dados['email'],
        ':data_nascimento' => $dados['data_nascimento'],
        ':cep' => $dados['cep'],
        ':endereco' => $dados['endereco'],
        ':numero' => $dados['numero'],
        ':bairro' => $dados['bairro'],
        ':cidade' => $dados['cidade'],
        ':estado' => $dados['estado'],
        ':observacoes' => $dados['observacoes']
    ]);
}
}