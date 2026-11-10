<?php

require_once __DIR__ . '/../models/Venda.php';

class VendaController
{
    private Venda $vendaModel;

    public function __construct()
    {
        $this->vendaModel = new Venda();
    }

    public function index(): void
        {
            $pesquisa = trim(
                (string) ($_GET['pesquisa'] ?? '')
            );

            $vendas = $this->vendaModel->listar(
                $pesquisa
            );

            require_once __DIR__
                . '/../views/vendas/index.php';
        }

    public function nova(): void
    {
        $clientes = $this->vendaModel->listarClientes();

        $produtos = $this->vendaModel->listarProdutos();

        $ordensServicoProntas =
            $this->vendaModel->listarOrdensServicoProntas();

        $numeroVenda =
            $this->vendaModel->gerarNumeroVenda();

        require_once __DIR__
            . '/../views/vendas/nova.php';
    }

    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?rota=vendas');
            exit;
        }

        try {
            $tipoCliente = trim(
                (string) ($_POST['tipo_cliente'] ?? 'final')
            );

            $clienteId = null;

            if ($tipoCliente === 'cadastrado') {
                $clienteId = $_POST['cliente_id'] ?? null;

                if (empty($clienteId)) {
                    throw new Exception(
                        'Selecione um cliente cadastrado.'
                    );
                }
            }

            $dados = [
                'cliente_id' => $clienteId,

                'forma_pagamento' =>
                    $_POST['forma_pagamento'] ?? '',

                'observacoes' =>
                    $_POST['observacoes'] ?? '',

                'desconto' =>
                    $_POST['desconto'] ?? 0,

                'itens' =>
                    $_POST['itens'] ?? [],

                'ordem_servico_id' =>
                    $_POST['ordem_servico_id'] ?? null
            ];
            $vendaId = $this->vendaModel->salvar($dados);

            $_SESSION['sucesso_venda'] =
                'Venda finalizada com sucesso.';

            header(
                'Location: index.php?rota=venda-visualizar&id='
                . $vendaId
            );

            exit;
        } catch (Throwable $erro) {
            $_SESSION['erro_venda'] = $erro->getMessage();

            header('Location: index.php?rota=venda-nova');
            exit;
        }
    }

    public function visualizar(): void
    {
        $vendaId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$vendaId) {
            $_SESSION['erro_venda'] =
                'Venda inválida.';

            header('Location: index.php?rota=vendas');
            exit;
        }

        $venda = $this->vendaModel->buscarVendaPorId(
            $vendaId
        );

        if (!$venda) {
            $_SESSION['erro_venda'] =
                'Venda não encontrada.';

            header('Location: index.php?rota=vendas');
            exit;
        }

        $itens = $this->vendaModel->buscarItensVenda(
            $vendaId
        );

        require_once __DIR__
            . '/../views/vendas/visualizar.php';
    }

    public function cancelar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?rota=vendas');
            exit;
        }

        $vendaId = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$vendaId) {
            $_SESSION['erro_venda'] =
                'Venda inválida.';

            header('Location: index.php?rota=vendas');
            exit;
        }

        try {
            $this->vendaModel->cancelar($vendaId);

            $_SESSION['sucesso_venda'] =
                'Venda cancelada. O estoque foi devolvido e a ordem de serviço voltou para pronta.';
        } catch (Throwable $erro) {
            $_SESSION['erro_venda'] =
                $erro->getMessage();
        }

        header(
            'Location: index.php?rota=venda-visualizar&id='
            . $vendaId
        );

        exit;
    }
}