<?php

require_once __DIR__ . '/../models/Relatorio.php';

class RelatorioController
{
    private Relatorio $relatorioModel;

    public function __construct()
    {
        $this->relatorioModel = new Relatorio();
    }

    /*
    |--------------------------------------------------------------------------
    | TELA INICIAL DOS RELATÓRIOS
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        require_once __DIR__ . '/../views/relatorios/index.php';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO DE VENDAS
    |--------------------------------------------------------------------------
    */

    public function vendas(): void
    {
        $dados = [];

        $dataInicio = $_GET['data_inicio']
            ?? date('Y-m-01');

        $dataFim = $_GET['data_fim']
            ?? date('Y-m-d');

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioVendas(
                    $dataInicio,
                    $dataFim
                );
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
        }

        require_once __DIR__
            . '/../views/relatorios/vendas.php';
    }

    public function imprimirVendas(): void
    {
        $dataInicio = $_GET['data_inicio']
            ?? date('Y-m-01');

        $dataFim = $_GET['data_fim']
            ?? date('Y-m-d');

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioVendas(
                    $dataInicio,
                    $dataFim
                );

            require_once __DIR__
                . '/../views/relatorios/imprimir-vendas.php';
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();

            header(
                'Location: index.php?rota=relatorios-vendas'
            );

            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO DE PRODUTOS
    |--------------------------------------------------------------------------
    */

    public function produtos(): void
    {
        $dados = [];

        $dataInicio = $_GET['data_inicio']
            ?? date('Y-m-01');

        $dataFim = $_GET['data_fim']
            ?? date('Y-m-d');

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioProdutos(
                    $dataInicio,
                    $dataFim
                );
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
        }

        require_once __DIR__
            . '/../views/relatorios/produtos.php';
    }

    public function imprimirProdutos(): void
    {
        $dataInicio = $_GET['data_inicio']
            ?? date('Y-m-01');

        $dataFim = $_GET['data_fim']
            ?? date('Y-m-d');

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioProdutos(
                    $dataInicio,
                    $dataFim
                );

            require_once __DIR__
                . '/../views/relatorios/imprimir-produtos.php';
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();

            header(
                'Location: index.php?rota=relatorios-produtos'
            );

            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO DE ESTOQUE
    |--------------------------------------------------------------------------
    */

    public function estoque(): void
    {
        $dados = [];

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioEstoque();
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
        }

        require_once __DIR__
            . '/../views/relatorios/estoque.php';
    }

    public function imprimirEstoque(): void
    {
        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioEstoque();

            require_once __DIR__
                . '/../views/relatorios/imprimir-estoque.php';
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();

            header(
                'Location: index.php?rota=relatorios-estoque'
            );

            exit;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO DE CLIENTES
    |--------------------------------------------------------------------------
    */

    public function clientes(): void
    {
        $dados = [];
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-d');

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioClientes($dataInicio, $dataFim);
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
        }

        require_once __DIR__
            . '/../views/relatorios/clientes.php';
    }

    public function imprimirClientes(): void
    {
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-d');

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioClientes($dataInicio, $dataFim);

            require_once __DIR__
                . '/../views/relatorios/imprimir-clientes.php';
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();

            header(
                'Location: index.php?rota=relatorios-clientes'
            );

            exit;
        }
    }



    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO DE ORDENS DE SERVIÇO
    |--------------------------------------------------------------------------
    */

    public function ordensServico(): void
    {
        $dados = [];

        $dataInicio = $_GET['data_inicio']
            ?? date('Y-m-01');

        $dataFim = $_GET['data_fim']
            ?? date('Y-m-d');

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioOrdensServico(
                    $dataInicio,
                    $dataFim
                );
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
        }

        require_once __DIR__
            . '/../views/relatorios/ordens-servico.php';
    }

    public function imprimirOrdensServico(): void
    {
        $dataInicio = $_GET['data_inicio']
            ?? date('Y-m-01');

        $dataFim = $_GET['data_fim']
            ?? date('Y-m-d');

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioOrdensServico(
                    $dataInicio,
                    $dataFim
                );

            require_once __DIR__
                . '/../views/relatorios/imprimir-ordens-servico.php';
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();

            header(
                'Location: index.php?rota=relatorios-ordens-servico'
            );

            exit;
        }
    }



    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO FINANCEIRO
    |--------------------------------------------------------------------------
    */

    public function financeiro(): void
    {
        $dados = [];

        $mes = isset($_GET['mes'])
            ? (int) $_GET['mes']
            : (int) date('m');

        $ano = isset($_GET['ano'])
            ? (int) $_GET['ano']
            : (int) date('Y');

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioFinanceiro(
                    $mes,
                    $ano
                );
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
        }

        require_once __DIR__
            . '/../views/relatorios/financeiro.php';
    }

    public function imprimirFinanceiro(): void
    {
        $mes = isset($_GET['mes'])
            ? (int) $_GET['mes']
            : (int) date('m');

        $ano = isset($_GET['ano'])
            ? (int) $_GET['ano']
            : (int) date('Y');

        try {
            $dados = $this->relatorioModel
                ->gerarRelatorioFinanceiro(
                    $mes,
                    $ano
                );

            require_once __DIR__
                . '/../views/relatorios/imprimir-financeiro.php';
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();

            header(
                'Location: index.php?rota=relatorios-financeiro'
            );

            exit;
        }
    }

}