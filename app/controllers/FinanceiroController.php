<?php

require_once __DIR__ . '/../models/Financeiro.php';

class FinanceiroController
{
    private Financeiro $financeiroModel;

    public function __construct()
    {
        $this->financeiroModel = new Financeiro();
    }

    /*
    |--------------------------------------------------------------------------
    | TELA PRINCIPAL DO FINANCEIRO
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $mes = filter_input(
            INPUT_GET,
            'mes',
            FILTER_VALIDATE_INT
        );

        $ano = filter_input(
            INPUT_GET,
            'ano',
            FILTER_VALIDATE_INT
        );

        if (!$mes || $mes < 1 || $mes > 12) {
            $mes = (int) date('m');
        }

        if (!$ano || $ano < 2000 || $ano > 2100) {
            $ano = (int) date('Y');
        }

        try {
            $dadosFinanceiros =
                $this->financeiroModel
                    ->obterDadosFinanceiros(
                        $mes,
                        $ano
                    );

            $resumo =
                $dadosFinanceiros['resumo'];

            $formasPagamento =
                $dadosFinanceiros[
                    'formas_pagamento'
                ];

            $produtosVendidos =
                $dadosFinanceiros[
                    'produtos_vendidos'
                ];

            $ordensServico =
                $dadosFinanceiros[
                    'ordens_servico'
                ];

            $gastos =
                $dadosFinanceiros['gastos'];

        } catch (Throwable $erro) {
            $_SESSION['erro_financeiro'] =
                'Não foi possível carregar o financeiro: '
                . $erro->getMessage();

            $dadosFinanceiros = [];

            $resumo = [
                'total_recebido' => 0,
                'total_produtos' => 0,
                'custo_produtos' => 0,
                'lucro_produtos' => 0,
                'total_ordens_servico' => 0,
                'quantidade_ordens_servico' => 0,
                'quantidade_itens' => 0,
                'quantidade_vendas' => 0,
                'total_gastos' => 0,
                'resultado_mes' => 0
            ];

            $formasPagamento = [];
            $produtosVendidos = [];
            $ordensServico = [];
            $gastos = [];
        }

        $meses = $this->listarMeses();

        $anos = $this->listarAnos();

        require_once __DIR__
            . '/../views/financeiro/index.php';
    }

    /*
    |--------------------------------------------------------------------------
    | NOVO GASTO
    |--------------------------------------------------------------------------
    */

    public function novoGasto(): void
    {
        $mes = filter_input(
            INPUT_GET,
            'mes',
            FILTER_VALIDATE_INT
        );

        $ano = filter_input(
            INPUT_GET,
            'ano',
            FILTER_VALIDATE_INT
        );

        if (!$mes || $mes < 1 || $mes > 12) {
            $mes = (int) date('m');
        }

        if (!$ano || $ano < 2000 || $ano > 2100) {
            $ano = (int) date('Y');
        }

        $categorias =
            $this->listarCategoriasGastos();

        $dataPadrao = date('Y-m-d');

        require_once __DIR__
            . '/../views/financeiro/novo-gasto.php';
    }

    /*
    |--------------------------------------------------------------------------
    | SALVAR GASTO
    |--------------------------------------------------------------------------
    */

    public function salvarGasto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header(
                'Location: index.php?rota=financeiro'
            );

            exit;
        }

        $mes = filter_input(
            INPUT_POST,
            'mes',
            FILTER_VALIDATE_INT
        );

        $ano = filter_input(
            INPUT_POST,
            'ano',
            FILTER_VALIDATE_INT
        );

        if (!$mes || $mes < 1 || $mes > 12) {
            $mes = (int) date('m');
        }

        if (!$ano || $ano < 2000 || $ano > 2100) {
            $ano = (int) date('Y');
        }

        try {
            $dados = [
                'descricao' =>
                    $_POST['descricao'] ?? '',

                'categoria' =>
                    $_POST['categoria'] ?? '',

                'valor' =>
                    $_POST['valor'] ?? 0,

                'data_gasto' =>
                    $_POST['data_gasto'] ?? '',

                'observacoes' =>
                    $_POST['observacoes'] ?? ''
            ];

            $this->financeiroModel
                ->salvarGasto($dados);

            $_SESSION['sucesso_financeiro'] =
                'Gasto cadastrado com sucesso.';

            header(
                'Location: index.php?rota=financeiro'
                . '&mes=' . $mes
                . '&ano=' . $ano
            );

            exit;

        } catch (Throwable $erro) {
            $_SESSION['erro_financeiro'] =
                $erro->getMessage();

            $_SESSION['dados_gasto'] = [
                'descricao' =>
                    $_POST['descricao'] ?? '',

                'categoria' =>
                    $_POST['categoria'] ?? '',

                'valor' =>
                    $_POST['valor'] ?? '',

                'data_gasto' =>
                    $_POST['data_gasto'] ?? '',

                'observacoes' =>
                    $_POST['observacoes'] ?? ''
            ];

            header(
                'Location: index.php?rota=gasto-novo'
                . '&mes=' . $mes
                . '&ano=' . $ano
            );

            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR GASTO
    |--------------------------------------------------------------------------
    */

    public function editarGasto(): void
    {
        $gastoId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        $mes = filter_input(
            INPUT_GET,
            'mes',
            FILTER_VALIDATE_INT
        );

        $ano = filter_input(
            INPUT_GET,
            'ano',
            FILTER_VALIDATE_INT
        );

        if (!$mes || $mes < 1 || $mes > 12) {
            $mes = (int) date('m');
        }

        if (!$ano || $ano < 2000 || $ano > 2100) {
            $ano = (int) date('Y');
        }

        if (!$gastoId) {
            $_SESSION['erro_financeiro'] =
                'Gasto inválido.';

            header(
                'Location: index.php?rota=financeiro'
                . '&mes=' . $mes
                . '&ano=' . $ano
            );

            exit;
        }

        $gasto =
            $this->financeiroModel
                ->buscarGastoPorId($gastoId);

        if (!$gasto) {
            $_SESSION['erro_financeiro'] =
                'Gasto não encontrado.';

            header(
                'Location: index.php?rota=financeiro'
                . '&mes=' . $mes
                . '&ano=' . $ano
            );

            exit;
        }

        $categorias =
            $this->listarCategoriasGastos();

        require_once __DIR__
            . '/../views/financeiro/editar-gasto.php';
    }

    /*
    |--------------------------------------------------------------------------
    | ATUALIZAR GASTO
    |--------------------------------------------------------------------------
    */

    public function atualizarGasto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header(
                'Location: index.php?rota=financeiro'
            );

            exit;
        }

        $gastoId = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        $mes = filter_input(
            INPUT_POST,
            'mes',
            FILTER_VALIDATE_INT
        );

        $ano = filter_input(
            INPUT_POST,
            'ano',
            FILTER_VALIDATE_INT
        );

        if (!$mes || $mes < 1 || $mes > 12) {
            $mes = (int) date('m');
        }

        if (!$ano || $ano < 2000 || $ano > 2100) {
            $ano = (int) date('Y');
        }

        if (!$gastoId) {
            $_SESSION['erro_financeiro'] =
                'Gasto inválido.';

            header(
                'Location: index.php?rota=financeiro'
                . '&mes=' . $mes
                . '&ano=' . $ano
            );

            exit;
        }

        try {
            $dados = [
                'descricao' =>
                    $_POST['descricao'] ?? '',

                'categoria' =>
                    $_POST['categoria'] ?? '',

                'valor' =>
                    $_POST['valor'] ?? 0,

                'data_gasto' =>
                    $_POST['data_gasto'] ?? '',

                'observacoes' =>
                    $_POST['observacoes'] ?? ''
            ];

            $this->financeiroModel
                ->atualizarGasto(
                    $gastoId,
                    $dados
                );

            $_SESSION['sucesso_financeiro'] =
                'Gasto atualizado com sucesso.';

            header(
                'Location: index.php?rota=financeiro'
                . '&mes=' . $mes
                . '&ano=' . $ano
            );

            exit;

        } catch (Throwable $erro) {
            $_SESSION['erro_financeiro'] =
                $erro->getMessage();

            header(
                'Location: index.php?rota=gasto-editar'
                . '&id=' . $gastoId
                . '&mes=' . $mes
                . '&ano=' . $ano
            );

            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXCLUIR GASTO
    |--------------------------------------------------------------------------
    */

    public function excluirGasto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header(
                'Location: index.php?rota=financeiro'
            );

            exit;
        }

        $gastoId = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        $mes = filter_input(
            INPUT_POST,
            'mes',
            FILTER_VALIDATE_INT
        );

        $ano = filter_input(
            INPUT_POST,
            'ano',
            FILTER_VALIDATE_INT
        );

        if (!$mes || $mes < 1 || $mes > 12) {
            $mes = (int) date('m');
        }

        if (!$ano || $ano < 2000 || $ano > 2100) {
            $ano = (int) date('Y');
        }

        if (!$gastoId) {
            $_SESSION['erro_financeiro'] =
                'Gasto inválido.';

            header(
                'Location: index.php?rota=financeiro'
                . '&mes=' . $mes
                . '&ano=' . $ano
            );

            exit;
        }

        try {
            $this->financeiroModel
                ->excluirGasto($gastoId);

            $_SESSION['sucesso_financeiro'] =
                'Gasto excluído com sucesso.';

        } catch (Throwable $erro) {
            $_SESSION['erro_financeiro'] =
                $erro->getMessage();
        }

        header(
            'Location: index.php?rota=financeiro'
            . '&mes=' . $mes
            . '&ano=' . $ano
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAS AUXILIARES
    |--------------------------------------------------------------------------
    */

    private function listarCategoriasGastos(): array
    {
        return [
            'Aluguel',
            'Água',
            'Energia',
            'Internet',
            'Telefone',
            'Salários',
            'Impostos',
            'Taxas',
            'Maquininha de cartão',
            'Compra de materiais',
            'Manutenção',
            'Transporte',
            'Limpeza',
            'Publicidade',
            'Outros'
        ];
    }

    private function listarMeses(): array
    {
        return [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];
    }

    private function listarAnos(): array
    {
        $anoAtual = (int) date('Y');

        $anos = [];

        for (
            $ano = $anoAtual + 1;
            $ano >= $anoAtual - 10;
            $ano--
        ) {
            $anos[] = $ano;
        }

        return $anos;
    }
}