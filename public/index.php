<?php

session_start();

date_default_timezone_set('America/Sao_Paulo');

$rota = $_GET['rota'] ?? 'login';

switch ($rota) {

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    case 'login':

        require_once __DIR__
            . '/../app/views/login.php';

        break;


    case 'autenticar':

        require_once __DIR__
            . '/../app/controllers/LoginController.php';

        break;


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    case 'dashboard':

        require_once __DIR__
            . '/../app/controllers/DashboardController.php';

        $dashboardController =
            new DashboardController();

        $dashboardController->index();

        break;


    /*
    |--------------------------------------------------------------------------
    | PRODUTOS E ESTOQUE
    |--------------------------------------------------------------------------
    */

    case 'produtos':
    case 'estoque':
    case 'produto-novo':
    case 'produto-salvar':
    case 'produto-editar':
    case 'produto-atualizar':
    case 'produto-excluir':
    case 'produto-movimentar':

        require_once __DIR__
            . '/../app/controllers/ProdutoController.php';

        break;


    /*
    |--------------------------------------------------------------------------
    | CLIENTES
    |--------------------------------------------------------------------------
    */

    case 'clientes':
    case 'cliente-novo':
    case 'cliente-salvar':
    case 'cliente-editar':
    case 'cliente-atualizar':
    case 'cliente-excluir':

        require_once __DIR__
            . '/../app/controllers/ClienteController.php';

        break;


    /*
    |--------------------------------------------------------------------------
    | VENDAS
    |--------------------------------------------------------------------------
    */

    case 'vendas':
    case 'venda-nova':
    case 'venda-salvar':
    case 'venda-visualizar':
    case 'venda-cancelar':

        require_once __DIR__
            . '/../app/controllers/VendaController.php';

        $vendaController =
            new VendaController();

        switch ($rota) {

            case 'vendas':

                $vendaController->index();

                break;


            case 'venda-nova':

                $vendaController->nova();

                break;


            case 'venda-salvar':

                $vendaController->salvar();

                break;


            case 'venda-visualizar':

                $vendaController->visualizar();

                break;


            case 'venda-cancelar':

                $vendaController->cancelar();

                break;
        }

        break;


    /*
    |--------------------------------------------------------------------------
    | ORDENS DE SERVIÇO
    |--------------------------------------------------------------------------
    */

    case 'ordens-servico':
    case 'ordem-servico-nova':
    case 'ordem-servico-salvar':
    case 'ordem-servico-visualizar':
    case 'ordem-servico-editar':
    case 'ordem-servico-atualizar':
    case 'ordem-servico-imprimir':
    case 'ordem-servico-status':

        require_once __DIR__
            . '/../app/controllers/OrdemServicoController.php';

        $ordemServicoController =
            new OrdemServicoController();

        switch ($rota) {

            case 'ordens-servico':

                $ordemServicoController->index();

                break;


            case 'ordem-servico-nova':

                $ordemServicoController->nova();

                break;


            case 'ordem-servico-salvar':

                $ordemServicoController->salvar();

                break;


            case 'ordem-servico-visualizar':

                $ordemServicoController->visualizar();

                break;


            case 'ordem-servico-editar':

                $ordemServicoController->editar();

                break;


            case 'ordem-servico-atualizar':

                $ordemServicoController->atualizar();

                break;


            case 'ordem-servico-imprimir':

                $ordemServicoController->imprimir();

                break;


            case 'ordem-servico-status':

                $ordemServicoController
                    ->alterarStatus();

                break;
        }

        break;


    /*
    |--------------------------------------------------------------------------
    | FINANCEIRO E GASTOS
    |--------------------------------------------------------------------------
    */

    case 'financeiro':
    case 'gasto-novo':
    case 'gasto-salvar':
    case 'gasto-editar':
    case 'gasto-atualizar':
    case 'gasto-excluir':

        require_once __DIR__
            . '/../app/controllers/FinanceiroController.php';

        $financeiroController =
            new FinanceiroController();

        switch ($rota) {

            case 'financeiro':

                $financeiroController->index();

                break;


            case 'gasto-novo':

                $financeiroController->novoGasto();

                break;


            case 'gasto-salvar':

                $financeiroController->salvarGasto();

                break;


            case 'gasto-editar':

                $financeiroController->editarGasto();

                break;


            case 'gasto-atualizar':

                $financeiroController
                    ->atualizarGasto();

                break;


            case 'gasto-excluir':

                $financeiroController->excluirGasto();

                break;
        }

        break;


    /*
    |--------------------------------------------------------------------------
    | RELATÓRIOS
    |--------------------------------------------------------------------------
    */

    case 'relatorios':
    case 'relatorios-vendas':
    case 'relatorios-imprimir-vendas':
    case 'relatorios-produtos':
    case 'relatorios-imprimir-produtos':
    case 'relatorios-estoque':
    case 'relatorios-imprimir-estoque':
    case 'relatorios-clientes':
    case 'relatorios-imprimir-clientes':
    case 'relatorios-ordens-servico':
    case 'relatorios-imprimir-ordens-servico':
    case 'relatorios-financeiro':
    case 'relatorios-imprimir-financeiro':

        require_once __DIR__
            . '/../app/controllers/RelatorioController.php';

        $relatorioController =
            new RelatorioController();

        switch ($rota) {

            case 'relatorios':

                $relatorioController->index();

                break;


            case 'relatorios-vendas':

                $relatorioController->vendas();

                break;


            case 'relatorios-imprimir-vendas':

                $relatorioController
                    ->imprimirVendas();

                break;


            case 'relatorios-produtos':

                $relatorioController->produtos();

                break;


            case 'relatorios-imprimir-produtos':

                $relatorioController
                    ->imprimirProdutos();

                break;


            case 'relatorios-estoque':

                $relatorioController->estoque();

                break;


            case 'relatorios-imprimir-estoque':

                $relatorioController
                    ->imprimirEstoque();

                break;


            case 'relatorios-clientes':

                $relatorioController->clientes();

                break;


            case 'relatorios-imprimir-clientes':

                $relatorioController
                    ->imprimirClientes();

                break;


            case 'relatorios-ordens-servico':

                $relatorioController
                    ->ordensServico();

                break;


            case 'relatorios-imprimir-ordens-servico':

                $relatorioController
                    ->imprimirOrdensServico();

                break;


            case 'relatorios-financeiro':

                $relatorioController->financeiro();

                break;


            case 'relatorios-imprimir-financeiro':

                $relatorioController
                    ->imprimirFinanceiro();

                break;
        }

        break;


    /*
    |--------------------------------------------------------------------------
    | CONFIGURAÇÕES
    |--------------------------------------------------------------------------
    */

    case 'configuracoes':
    case 'configuracoes-salvar':
    case 'configuracoes-backup':
    case 'configuracoes-restaurar':
    case 'configuracoes-limpar':

        require_once __DIR__
            . '/../app/controllers/ConfiguracaoController.php';

        $configuracaoController =
            new ConfiguracaoController();

        switch ($rota) {

            case 'configuracoes':

                $configuracaoController->index();

                break;


            case 'configuracoes-salvar':

                $configuracaoController->salvar();

                break;


            case 'configuracoes-backup':

                $configuracaoController->backup();

                break;


            case 'configuracoes-restaurar':

                $configuracaoController->restaurar();

                break;


            case 'configuracoes-limpar':

                $configuracaoController->limpar();

                break;
        }

        break;


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    case 'logout':

        session_destroy();

        header('Location: index.php');

        exit;


    /*
    |--------------------------------------------------------------------------
    | ROTA NÃO ENCONTRADA
    |--------------------------------------------------------------------------
    */

    default:

        http_response_code(404);

        echo 'Página não encontrada.';

        break;
}