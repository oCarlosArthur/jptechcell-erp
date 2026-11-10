<?php

require_once __DIR__ . '/../core/Database.php';

class Configuracao
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::conectar();
    }

    public function buscar(): array|null
    {
        $sql = "SELECT * FROM configuracoes LIMIT 1";

        $stmt = $this->pdo->query($sql);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        return $dados ?: null;
    }

    public function atualizar(array $dados): bool
    {
        $sql = "
            UPDATE configuracoes SET

                nome_loja = :nome_loja,
                responsavel = :responsavel,
                cpf_cnpj = :cpf_cnpj,
                telefone = :telefone,
                whatsapp = :whatsapp,
                email = :email,
                instagram = :instagram,
                cep = :cep,
                endereco = :endereco,
                numero = :numero,
                bairro = :bairro,
                cidade = :cidade,
                estado = :estado,
                cor_principal = :cor_principal

            WHERE id = 1
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':nome_loja' => $dados['nome_loja'],
            ':responsavel' => $dados['responsavel'],
            ':cpf_cnpj' => $dados['cpf_cnpj'],
            ':telefone' => $dados['telefone'],
            ':whatsapp' => $dados['whatsapp'],
            ':email' => $dados['email'],
            ':instagram' => $dados['instagram'],
            ':cep' => $dados['cep'],
            ':endereco' => $dados['endereco'],
            ':numero' => $dados['numero'],
            ':bairro' => $dados['bairro'],
            ':cidade' => $dados['cidade'],
            ':estado' => $dados['estado'],
            ':cor_principal' => $dados['cor_principal']
        ]);
    }

    public function atualizarLogo(string $logo): bool
    {
        $sql = "
            UPDATE configuracoes
            SET logo = :logo
            WHERE id = 1
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':logo' => $logo
        ]);
    }
}