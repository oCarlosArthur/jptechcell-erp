<?php

/**
 * Conexao PDO do JPTechCell.
 * Credenciais sao configuradas fora do controle de versao.
 * Copie app/config/database.local.example.php para database.local.php
 * e configure com suas credenciais locais.
 */
class Database
{
    private static ?PDO $conexao = null;

    public static function conectar(): PDO
    {
        if (self::$conexao !== null) {
            return self::$conexao;
        }

        $arquivoLocal = __DIR__ . '/../config/database.local.php';
        if (!is_file($arquivoLocal)) {
            throw new RuntimeException(
                'Configuracao do banco nao encontrada. ' .
                'Crie app/config/database.local.php a partir do arquivo de exemplo.'
            );
        }

        $config = require $arquivoLocal;
        if (!is_array($config)) {
            throw new RuntimeException('Configuracao local do banco invalida.');
        }

        $host = trim((string) ($config['host'] ?? ''));
        $banco = trim((string) ($config['database'] ?? ''));
        $usuario = (string) ($config['username'] ?? '');
        $senha = (string) ($config['password'] ?? '');

        if ($host === '' || $banco === '' || $usuario === '') {
            throw new RuntimeException('Configure host, database e username no arquivo local.');
        }

        try {
            self::$conexao = new PDO(
                "mysql:host={$host};dbname={$banco};charset=utf8mb4",
                $usuario,
                $senha,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $erro) {
            error_log('JPTechCell: erro na conexao com o banco: ' . $erro->getMessage());
            http_response_code(500);
            exit('Falha interna de conexao com o banco de dados. Verifique a configuracao local.');
        }

        return self::$conexao;
    }
}
