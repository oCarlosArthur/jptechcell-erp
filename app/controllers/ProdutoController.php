<?php

require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/MovimentacaoEstoque.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$produtoModel = new Produto();
$movimentacaoModel = new MovimentacaoEstoque();
$rota = $_GET['rota'] ?? 'produtos';

switch ($rota) {
    case 'produtos':

        $pesquisa = trim($_GET['pesquisa'] ?? '');

        $produtos = $produtoModel->listarTodos($pesquisa);

        require_once __DIR__
            . '/../views/produtos/index.php';

        break;


    case 'estoque':

        $pesquisa = trim($_GET['pesquisa'] ?? '');

        $produtos = $produtoModel->listarTodos($pesquisa);

        require_once __DIR__
            . '/../views/estoque/index.php';

        break;

    case 'produto-novo':
        require_once __DIR__ . '/../views/produtos/novo.php';
        break;

    case 'produto-salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?rota=produtos');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');
        $marca = trim($_POST['marca'] ?? '');

        $precoCusto = (float) ($_POST['preco_custo'] ?? 0);
        $precoVenda = (float) ($_POST['preco_venda'] ?? 0);

        $quantidadeEstoque = (int) (
            $_POST['quantidade_estoque'] ?? 0
        );

        $estoqueMinimo = (int) (
            $_POST['estoque_minimo'] ?? 0
        );

        $_SESSION['dados_produto'] = [
            'nome' => $nome,
            'marca' => $marca,
            'preco_custo' => $_POST['preco_custo'] ?? '',
            'preco_venda' => $_POST['preco_venda'] ?? '',
            'quantidade_estoque' => $quantidadeEstoque,
            'estoque_minimo' => $estoqueMinimo
        ];

        if ($nome === '') {
            $_SESSION['erro_produto'] =
                'Informe o nome do produto.';

            header('Location: index.php?rota=produto-novo');
            exit;
        }

        if ($precoCusto < 0 || $precoVenda < 0) {
            $_SESSION['erro_produto'] =
                'Os preços não podem ser negativos.';

            header('Location: index.php?rota=produto-novo');
            exit;
        }

        if ($quantidadeEstoque < 0 || $estoqueMinimo < 0) {
            $_SESSION['erro_produto'] =
                'As quantidades não podem ser negativas.';

            header('Location: index.php?rota=produto-novo');
            exit;
        }

        try {
            $produtoModel->cadastrar([
                'nome' => $nome,
                'marca' => $marca !== '' ? $marca : null,
                'preco_custo' => $precoCusto,
                'preco_venda' => $precoVenda,
                'quantidade_estoque' => $quantidadeEstoque,
                'estoque_minimo' => $estoqueMinimo
            ]);

            unset($_SESSION['dados_produto']);

            $_SESSION['sucesso_produto'] =
                'Produto cadastrado com sucesso!';

            header('Location: index.php?rota=produtos');
            exit;
        } catch (Throwable $erro) {
            $_SESSION['erro_produto'] =
                'Não foi possível cadastrar o produto.';

            header('Location: index.php?rota=produto-novo');
            exit;
        }

       case 'produto-editar':
            $produtoId = (int) ($_GET['id'] ?? 0);

            if ($produtoId <= 0) {
                header('Location: index.php?rota=produtos');
                exit;
            }

            $produto = $produtoModel->buscarPorId($produtoId);

            if (!$produto) {

                $_SESSION['erro_lista_produto'] =
                    'Produto não encontrado.';

                header('Location: index.php?rota=produtos');
                exit;
            }

            /*
            * Busca o histórico de movimentações
            * deste produto.
            */
            $movimentacoes = $movimentacaoModel->listarPorProduto(
                $produtoId
            );

            require_once __DIR__
                . '/../views/produtos/editar.php';

            break;

case 'produto-movimentar':

    /*
     * Quando o usuário clicar em
     * "Salvar movimentação".
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $produtoId = (int) ($_POST['produto_id'] ?? 0);
        $tipo = trim((string) ($_POST['tipo'] ?? ''));
        $quantidade = filter_var(
            $_POST['quantidade'] ?? null,
            FILTER_VALIDATE_INT
        );
        $motivo = trim((string) ($_POST['motivo'] ?? ''));

        /*
         * Verifica o produto.
         */
        if ($produtoId <= 0) {
            $_SESSION['erro_lista_produto'] =
                'Produto inválido.';

            header('Location: index.php?rota=produtos');
            exit;
        }

        $produto = $produtoModel->buscarPorId($produtoId);

        if (!$produto) {
            $_SESSION['erro_lista_produto'] =
                'Produto não encontrado.';

            header('Location: index.php?rota=produtos');
            exit;
        }

        /*
         * Valida o tipo da movimentação.
         */
        $tiposPermitidos = [
            'entrada',
            'saida',
            'ajuste'
        ];

        if (!in_array($tipo, $tiposPermitidos, true)) {
            $_SESSION['erro_movimentacao'] =
                'Selecione um tipo de movimentação válido.';

            header(
                'Location: index.php?rota=produto-movimentar&id='
                . $produtoId
            );
            exit;
        }

        /*
         * Entrada e saída precisam ser maiores que zero.
         * No ajuste, zero é permitido.
         */
        $quantidadeInvalida =
            $quantidade === false
            || ($tipo !== 'ajuste' && $quantidade <= 0)
            || ($tipo === 'ajuste' && $quantidade < 0);

        if ($quantidadeInvalida) {
            $_SESSION['erro_movimentacao'] =
                'Informe uma quantidade válida.';

            header(
                'Location: index.php?rota=produto-movimentar&id='
                . $produtoId
            );
            exit;
        }

        if ($motivo === '') {
            $_SESSION['erro_movimentacao'] =
                'Informe o motivo da movimentação.';

            header(
                'Location: index.php?rota=produto-movimentar&id='
                . $produtoId
            );
            exit;
        }

        try {

            $movimentacaoModel->movimentar(
                $produtoId,
                $tipo,
                $quantidade,
                $motivo
            );

            $_SESSION['sucesso_produto'] =
                'Movimentação de estoque registrada com sucesso.';

            header(
                'Location: index.php?rota=produto-editar&id='
                . $produtoId
            );
            exit;

        } catch (Throwable $erro) {

            $_SESSION['erro_movimentacao'] =
                'Não foi possível realizar a movimentação: '
                . $erro->getMessage();

            header(
                'Location: index.php?rota=produto-movimentar&id='
                . $produtoId
            );
            exit;
        }
    }

    /*
     * Quando o usuário apenas abre a página.
     */
    $produtoId = (int) ($_GET['id'] ?? 0);

    if ($produtoId <= 0) {
        header('Location: index.php?rota=produtos');
        exit;
    }

    $produto = $produtoModel->buscarPorId($produtoId);

    if (!$produto) {
        $_SESSION['erro_lista_produto'] =
            'Produto não encontrado.';

        header('Location: index.php?rota=produtos');
        exit;
    }

    require_once __DIR__
        . '/../views/produtos/movimentar-estoque.php';

    break;

case 'produto-atualizar':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?rota=produtos');
        exit;
    }

    $produtoId = (int) ($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $marca = trim($_POST['marca'] ?? '');

    $precoCusto = (float) ($_POST['preco_custo'] ?? 0);
    $precoVenda = (float) ($_POST['preco_venda'] ?? 0);
    $estoqueMinimo = (int) ($_POST['estoque_minimo'] ?? 0);

    if ($produtoId <= 0 || $nome === '') {
        $_SESSION['erro_produto'] =
            'Preencha corretamente os dados do produto.';

        header(
            'Location: index.php?rota=produto-editar&id='
            . $produtoId
        );
        exit;
    }

    if (
        $precoCusto < 0 ||
        $precoVenda < 0 ||
        $estoqueMinimo < 0
    ) {
        $_SESSION['erro_produto'] =
            'Valores e quantidades não podem ser negativos.';

        header(
            'Location: index.php?rota=produto-editar&id='
            . $produtoId
        );
        exit;
    }

    try {
        $produtoModel->atualizar($produtoId, [
            'nome' => $nome,
            'marca' => $marca !== '' ? $marca : null,
            'preco_custo' => $precoCusto,
            'preco_venda' => $precoVenda,
            'estoque_minimo' => $estoqueMinimo
        ]);

        $_SESSION['sucesso_produto'] =
            'Produto atualizado com sucesso!';

        header('Location: index.php?rota=produtos');
        exit;
    } catch (Throwable $erro) {
        $_SESSION['erro_produto'] =
            'Não foi possível atualizar o produto.';

        header(
            'Location: index.php?rota=produto-editar&id='
            . $produtoId
        );
        exit;
    }

    case 'produto-excluir':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?rota=produtos');
        exit;
    }

    $produtoId = (int) ($_POST['id'] ?? 0);

    if ($produtoId <= 0) {
        $_SESSION['erro_lista_produto'] =
            'Produto inválido.';

        header('Location: index.php?rota=produtos');
        exit;
    }

    try {
        $produtoModel->desativar($produtoId);

        $_SESSION['sucesso_produto'] =
            'Produto excluído com sucesso!';

        header('Location: index.php?rota=produtos');
        exit;
    } catch (Throwable $erro) {
        $_SESSION['erro_lista_produto'] =
            'Não foi possível excluir o produto.';

        header('Location: index.php?rota=produtos');
        exit;
    }

    default:
        http_response_code(404);
        echo 'Página de produtos não encontrada.';
        break;
}