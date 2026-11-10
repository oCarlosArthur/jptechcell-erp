<?php

require_once __DIR__ . '/../core/Database.php';

class Usuario
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Database::conectar();
    }

    public function buscarPorUsuario(string $usuario): array|false
    {
        $sql = "
            SELECT id, nome, usuario, senha, ativo
            FROM usuarios
            WHERE usuario = :usuario
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':usuario' => $usuario
        ]);

        return $stmt->fetch();
    }
}