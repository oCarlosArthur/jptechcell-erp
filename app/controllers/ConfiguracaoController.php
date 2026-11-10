<?php

require_once __DIR__ . '/../models/Configuracao.php';
require_once __DIR__ . '/../core/Database.php';

class ConfiguracaoController
{
    private Configuracao $configuracaoModel;

    public function __construct()
    {
        $this->configuracaoModel = new Configuracao();
    }

    public function index(): void
    {
        $configuracao = $this->configuracaoModel->buscar();

        require_once __DIR__
            . '/../views/configuracoes/index.php';
    }

    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionarConfiguracoes();
        }

        $dados = [
            'nome_loja' => trim(
                $_POST['nome_loja'] ?? ''
            ),

            'responsavel' => trim(
                $_POST['responsavel'] ?? ''
            ),

            'cpf_cnpj' => trim(
                $_POST['cpf_cnpj'] ?? ''
            ),

            'telefone' => trim(
                $_POST['telefone'] ?? ''
            ),

            'whatsapp' => trim(
                $_POST['whatsapp'] ?? ''
            ),

            'email' => trim(
                $_POST['email'] ?? ''
            ),

            'instagram' => trim(
                $_POST['instagram'] ?? ''
            ),

            'cep' => trim(
                $_POST['cep'] ?? ''
            ),

            'endereco' => trim(
                $_POST['endereco'] ?? ''
            ),

            'numero' => trim(
                $_POST['numero'] ?? ''
            ),

            'bairro' => trim(
                $_POST['bairro'] ?? ''
            ),

            'cidade' => trim(
                $_POST['cidade'] ?? ''
            ),

            'estado' => strtoupper(
                trim($_POST['estado'] ?? '')
            ),

            'cor_principal' => trim(
                $_POST['cor_principal'] ?? '#159ee8'
            )
        ];

        if ($dados['nome_loja'] === '') {
            $_SESSION['erro'] =
                'Informe o nome da empresa.';

            $_SESSION['dados_formulario'] = $dados;

            $this->redirecionarConfiguracoes();
        }

        if (
            $dados['email'] !== ''
            && !filter_var(
                $dados['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $_SESSION['erro'] =
                'Informe um e-mail válido.';

            $_SESSION['dados_formulario'] = $dados;

            $this->redirecionarConfiguracoes();
        }

        if (
            !preg_match(
                '/^#[0-9A-Fa-f]{6}$/',
                $dados['cor_principal']
            )
        ) {
            $dados['cor_principal'] = '#159ee8';
        }

        try {
            $salvou = $this->configuracaoModel
                ->atualizar($dados);

            if (!$salvou) {
                throw new Exception(
                    'Não foi possível salvar as configurações.'
                );
            }

            $this->salvarLogoEnviada();

            unset($_SESSION['dados_formulario']);

            $_SESSION['sucesso'] =
                'Configurações salvas com sucesso.';
        } catch (Throwable $erro) {
            $_SESSION['erro'] =
                $erro->getMessage();

            $_SESSION['dados_formulario'] = $dados;
        }

        $this->redirecionarConfiguracoes();
    }

    /*
    |--------------------------------------------------------------------------
    | BACKUP DO BANCO DE DADOS
    |--------------------------------------------------------------------------
    */

    public function backup(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $_SESSION['erro'] =
                'Método inválido para gerar o backup.';

            $this->redirecionarConfiguracoes();
        }

        try {
            $pdo = Database::conectar();

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $nomeBanco = (string) $pdo
                ->query('SELECT DATABASE()')
                ->fetchColumn();

            if ($nomeBanco === '') {
                throw new Exception(
                    'Não foi possível identificar o banco de dados.'
                );
            }

            $conteudoBackup =
                $this->gerarConteudoBackup(
                    $pdo,
                    $nomeBanco
                );

            $nomeArquivo =
                'backup_jptechcell_'
                . date('Y-m-d_H-i-s')
                . '.sql';

            if (ob_get_length()) {
                ob_end_clean();
            }

            header(
                'Content-Type: application/sql; charset=UTF-8'
            );

            header(
                'Content-Disposition: attachment; filename="'
                . $nomeArquivo
                . '"'
            );

            header(
                'Content-Length: '
                . strlen($conteudoBackup)
            );

            header(
                'Cache-Control: no-store, no-cache, must-revalidate'
            );

            header('Pragma: no-cache');

            echo $conteudoBackup;

            exit;
        } catch (Throwable $erro) {
            $_SESSION['erro'] =
                'Não foi possível gerar o backup: '
                . $erro->getMessage();

            $this->redirecionarConfiguracoes();
        }
    }

    private function gerarConteudoBackup(
        PDO $pdo,
        string $nomeBanco
    ): string {
        $dataBackup = date('d/m/Y H:i:s');

        $sql = '';

        $sql .= "-- JP TechCell ERP\n";
        $sql .= "-- Backup completo do banco de dados\n";
        $sql .= "-- Banco: {$nomeBanco}\n";
        $sql .= "-- Gerado em: {$dataBackup}\n\n";

        $sql .= "SET NAMES utf8mb4;\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

        $tabelas = $this->listarTabelas($pdo);

        foreach ($tabelas as $tabela) {
            $nomeSeguro =
                str_replace('`', '``', $tabela);

            $sql .= "\n";
            $sql .= "-- --------------------------------------\n";
            $sql .= "-- Estrutura da tabela `{$nomeSeguro}`\n";
            $sql .= "-- --------------------------------------\n\n";

            $resultadoCriacao = $pdo->query(
                "SHOW CREATE TABLE `{$nomeSeguro}`"
            );

            $criacao = $resultadoCriacao
                ->fetch(PDO::FETCH_ASSOC);

            if (!$criacao) {
                continue;
            }

            $comandoCriacao =
                $criacao['Create Table']
                ?? array_values($criacao)[1]
                ?? '';

            $sql .=
                "DROP TABLE IF EXISTS `{$nomeSeguro}`;\n";

            $sql .= $comandoCriacao . ";\n\n";

            $resultadoDados = $pdo->query(
                "SELECT * FROM `{$nomeSeguro}`"
            );

            $quantidadeColunas =
                $resultadoDados->columnCount();

            if ($quantidadeColunas === 0) {
                continue;
            }

            $nomesColunas = [];

            for (
                $indice = 0;
                $indice < $quantidadeColunas;
                $indice++
            ) {
                $meta = $resultadoDados
                    ->getColumnMeta($indice);

                $nomeColuna =
                    (string) ($meta['name'] ?? '');

                $nomesColunas[] =
                    '`'
                    . str_replace(
                        '`',
                        '``',
                        $nomeColuna
                    )
                    . '`';
            }

            $linhas = $resultadoDados
                ->fetchAll(PDO::FETCH_ASSOC);

            if (empty($linhas)) {
                continue;
            }

            $sql .= "-- Dados da tabela `{$nomeSeguro}`\n\n";

            foreach ($linhas as $linha) {
                $valores = [];

                foreach ($linha as $valor) {
                    if ($valor === null) {
                        $valores[] = 'NULL';

                        continue;
                    }

                    $valores[] = $pdo->quote(
                        (string) $valor
                    );
                }

                $sql .=
                    "INSERT INTO `{$nomeSeguro}` ("
                    . implode(', ', $nomesColunas)
                    . ') VALUES ('
                    . implode(', ', $valores)
                    . ");\n";
            }

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $sql .= "-- Fim do backup\n";

        return $sql;
    }

    /*
    |--------------------------------------------------------------------------
    | RESTAURAÇÃO DO BACKUP
    |--------------------------------------------------------------------------
    */

    public function restaurar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionarConfiguracoes();
        }

        if (
            empty($_FILES['arquivo_backup'])
            || !isset(
                $_FILES['arquivo_backup']['error']
            )
        ) {
            $_SESSION['erro'] =
                'Selecione um arquivo de backup.';

            $this->redirecionarConfiguracoes();
        }

        $arquivo = $_FILES['arquivo_backup'];

        if (
            $arquivo['error']
            !== UPLOAD_ERR_OK
        ) {
            $_SESSION['erro'] =
                'Ocorreu um erro ao enviar o backup.';

            $this->redirecionarConfiguracoes();
        }

        $nomeOriginal =
            (string) ($arquivo['name'] ?? '');

        $extensao = strtolower(
            pathinfo(
                $nomeOriginal,
                PATHINFO_EXTENSION
            )
        );

        if ($extensao !== 'sql') {
            $_SESSION['erro'] =
                'Selecione um arquivo com extensão .sql.';

            $this->redirecionarConfiguracoes();
        }

        $tamanhoArquivo =
            (int) ($arquivo['size'] ?? 0);

        if ($tamanhoArquivo <= 0) {
            $_SESSION['erro'] =
                'O arquivo de backup está vazio.';

            $this->redirecionarConfiguracoes();
        }

        if ($tamanhoArquivo > 50 * 1024 * 1024) {
            $_SESSION['erro'] =
                'O arquivo de backup deve ter no máximo 50 MB.';

            $this->redirecionarConfiguracoes();
        }

        $caminhoTemporario =
            (string) $arquivo['tmp_name'];

        $conteudo = file_get_contents(
            $caminhoTemporario
        );

        if (
            $conteudo === false
            || trim($conteudo) === ''
        ) {
            $_SESSION['erro'] =
                'Não foi possível ler o arquivo de backup.';

            $this->redirecionarConfiguracoes();
        }

        try {
            $pdo = Database::conectar();

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $comandos =
                $this->separarComandosSql(
                    $conteudo
                );

            if (empty($comandos)) {
                throw new Exception(
                    'Nenhum comando SQL válido foi encontrado.'
                );
            }

            $pdo->exec(
                'SET FOREIGN_KEY_CHECKS = 0'
            );

            try {
                foreach ($comandos as $comando) {
                    $comando = trim($comando);

                    if ($comando === '') {
                        continue;
                    }

                    $pdo->exec($comando);
                }
            } finally {
                $pdo->exec(
                    'SET FOREIGN_KEY_CHECKS = 1'
                );
            }

            $_SESSION['sucesso'] =
                'Backup restaurado com sucesso.';
        } catch (Throwable $erro) {
            try {
                if (isset($pdo)) {
                    $pdo->exec(
                        'SET FOREIGN_KEY_CHECKS = 1'
                    );
                }
            } catch (Throwable $erroIgnorado) {
            }

            $_SESSION['erro'] =
                'Não foi possível restaurar o backup: '
                . $erro->getMessage();
        }

        $this->redirecionarConfiguracoes();
    }

    private function separarComandosSql(
        string $conteudo
    ): array {
        $comandos = [];

        $comandoAtual = '';

        $tamanho = strlen($conteudo);

        $aspasSimples = false;
        $aspasDuplas = false;
        $crase = false;

        $comentarioLinha = false;
        $comentarioBloco = false;

        for (
            $indice = 0;
            $indice < $tamanho;
            $indice++
        ) {
            $caractere = $conteudo[$indice];

            $proximo =
                $indice + 1 < $tamanho
                ? $conteudo[$indice + 1]
                : '';

            if ($comentarioLinha) {
                if (
                    $caractere === "\n"
                    || $caractere === "\r"
                ) {
                    $comentarioLinha = false;
                    $comandoAtual .= ' ';
                }

                continue;
            }

            if ($comentarioBloco) {
                if (
                    $caractere === '*'
                    && $proximo === '/'
                ) {
                    $comentarioBloco = false;
                    $indice++;
                    $comandoAtual .= ' ';
                }

                continue;
            }

            if (
                !$aspasSimples
                && !$aspasDuplas
                && !$crase
            ) {
                if (
                    $caractere === '-'
                    && $proximo === '-'
                ) {
                    $terceiro =
                        $indice + 2 < $tamanho
                        ? $conteudo[$indice + 2]
                        : '';

                    if (
                        $terceiro === ' '
                        || $terceiro === "\t"
                        || $terceiro === "\n"
                        || $terceiro === "\r"
                    ) {
                        $comentarioLinha = true;
                        $indice++;

                        continue;
                    }
                }

                if ($caractere === '#') {
                    $comentarioLinha = true;

                    continue;
                }

                if (
                    $caractere === '/'
                    && $proximo === '*'
                ) {
                    $comentarioBloco = true;
                    $indice++;

                    continue;
                }
            }

            $escapado = false;

            if ($indice > 0) {
                $quantidadeBarras = 0;

                for (
                    $posicao = $indice - 1;
                    $posicao >= 0
                    && $conteudo[$posicao] === '\\';
                    $posicao--
                ) {
                    $quantidadeBarras++;
                }

                $escapado =
                    $quantidadeBarras % 2 === 1;
            }

            if (
                $caractere === "'"
                && !$aspasDuplas
                && !$crase
                && !$escapado
            ) {
                $aspasSimples = !$aspasSimples;
            } elseif (
                $caractere === '"'
                && !$aspasSimples
                && !$crase
                && !$escapado
            ) {
                $aspasDuplas = !$aspasDuplas;
            } elseif (
                $caractere === '`'
                && !$aspasSimples
                && !$aspasDuplas
                && !$escapado
            ) {
                $crase = !$crase;
            }

            if (
                $caractere === ';'
                && !$aspasSimples
                && !$aspasDuplas
                && !$crase
            ) {
                $comandoAtual = trim(
                    $comandoAtual
                );

                if ($comandoAtual !== '') {
                    $comandos[] =
                        $comandoAtual;
                }

                $comandoAtual = '';

                continue;
            }

            $comandoAtual .= $caractere;
        }

        $comandoAtual = trim($comandoAtual);

        if ($comandoAtual !== '') {
            $comandos[] = $comandoAtual;
        }

        return $comandos;
    }

    /*
    |--------------------------------------------------------------------------
    | LIMPEZA DOS DADOS OPERACIONAIS
    |--------------------------------------------------------------------------
    */

    public function limpar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionarConfiguracoes();
        }

        $confirmacao = strtoupper(
            trim($_POST['confirmacao'] ?? '')
        );

        if ($confirmacao !== 'CONFIRMAR') {
            $_SESSION['erro'] =
                'A confirmação está incorreta. Digite CONFIRMAR.';

            $this->redirecionarConfiguracoes();
        }

        try {
            $pdo = Database::conectar();

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $tabelas = $this->listarTabelas(
                $pdo
            );

            /*
             * Estas tabelas serão mantidas.
             *
             * configuracoes:
             * mantém os dados da empresa, logo e cor.
             *
             * usuarios:
             * mantém o acesso do administrador.
             */
            $tabelasPreservadas = [
                'configuracoes',
                'usuarios'
            ];

            $pdo->exec(
                'SET FOREIGN_KEY_CHECKS = 0'
            );

            try {
                foreach ($tabelas as $tabela) {
                    if (
                        in_array(
                            strtolower($tabela),
                            $tabelasPreservadas,
                            true
                        )
                    ) {
                        continue;
                    }

                    $nomeSeguro =
                        str_replace(
                            '`',
                            '``',
                            $tabela
                        );

                    $pdo->exec(
                        "TRUNCATE TABLE `{$nomeSeguro}`"
                    );
                }
            } finally {
                $pdo->exec(
                    'SET FOREIGN_KEY_CHECKS = 1'
                );
            }

            $_SESSION['sucesso'] =
                'Todos os dados operacionais foram removidos. '
                . 'O usuário e as configurações da empresa foram mantidos.';
        } catch (Throwable $erro) {
            try {
                if (isset($pdo)) {
                    $pdo->exec(
                        'SET FOREIGN_KEY_CHECKS = 1'
                    );
                }
            } catch (Throwable $erroIgnorado) {
            }

            $_SESSION['erro'] =
                'Não foi possível limpar o sistema: '
                . $erro->getMessage();
        }

        $this->redirecionarConfiguracoes();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS AUXILIARES
    |--------------------------------------------------------------------------
    */

    private function listarTabelas(
        PDO $pdo
    ): array {
        $resultado = $pdo->query(
            "
            SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_TYPE = 'BASE TABLE'
            ORDER BY TABLE_NAME
            "
        );

        return $resultado->fetchAll(
            PDO::FETCH_COLUMN
        );
    }

    private function redirecionarConfiguracoes(): void
    {
        header(
            'Location: index.php?rota=configuracoes'
        );

        exit;
    }

    private function salvarLogoEnviada(): void
    {
        if (
            empty($_FILES['logo'])
            || !isset($_FILES['logo']['error'])
            || $_FILES['logo']['error']
                === UPLOAD_ERR_NO_FILE
        ) {
            return;
        }

        if (
            $_FILES['logo']['error']
            !== UPLOAD_ERR_OK
        ) {
            throw new Exception(
                'Ocorreu um erro ao enviar a logo.'
            );
        }

        $arquivoTemporario =
            $_FILES['logo']['tmp_name'];

        $tamanhoArquivo =
            (int) $_FILES['logo']['size'];

        if ($tamanhoArquivo > 2 * 1024 * 1024) {
            throw new Exception(
                'A logo deve ter no máximo 2 MB.'
            );
        }

        $finfo = new finfo(
            FILEINFO_MIME_TYPE
        );

        $tipoMime = $finfo->file(
            $arquivoTemporario
        );

        $extensoesPermitidas = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (
            !isset(
                $extensoesPermitidas[$tipoMime]
            )
        ) {
            throw new Exception(
                'Envie uma logo em JPG, PNG ou WEBP.'
            );
        }

        $pastaDestino = __DIR__
            . '/../../public/uploads/logos';

        if (!is_dir($pastaDestino)) {
            $criouPasta = mkdir(
                $pastaDestino,
                0775,
                true
            );

            if (
                !$criouPasta
                && !is_dir($pastaDestino)
            ) {
                throw new Exception(
                    'Não foi possível criar a pasta da logo.'
                );
            }
        }

        $nomeArquivo =
            'logo-empresa-'
            . time()
            . '.'
            . $extensoesPermitidas[$tipoMime];

        $caminhoCompleto =
            $pastaDestino
            . DIRECTORY_SEPARATOR
            . $nomeArquivo;

        if (
            !move_uploaded_file(
                $arquivoTemporario,
                $caminhoCompleto
            )
        ) {
            throw new Exception(
                'Não foi possível salvar a logo.'
            );
        }

        $configuracaoAtual =
            $this->configuracaoModel->buscar();

        $logoAntiga =
            $configuracaoAtual['logo']
            ?? null;

        $caminhoBanco =
            'uploads/logos/'
            . $nomeArquivo;

        $this->configuracaoModel
            ->atualizarLogo($caminhoBanco);

        if (!empty($logoAntiga)) {
            $arquivoAntigo = __DIR__
                . '/../../public/'
                . ltrim(
                    str_replace(
                        ['\\', '/'],
                        DIRECTORY_SEPARATOR,
                        $logoAntiga
                    ),
                    DIRECTORY_SEPARATOR
                );

            if (
                is_file($arquivoAntigo)
                && basename($arquivoAntigo)
                    !== $nomeArquivo
            ) {
                unlink($arquivoAntigo);
            }
        }
    }
}