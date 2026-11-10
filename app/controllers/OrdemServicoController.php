<?php

require_once __DIR__ . '/../models/OrdemServico.php';
require_once __DIR__ . '/../models/Cliente.php';

class OrdemServicoController
{
    private OrdemServico $ordemServicoModel;

    public function __construct()
    {
        $this->ordemServicoModel = new OrdemServico();
    }

    /**
     * Lista as ordens de serviço.
     */
    public function index(): void
    {
        $pesquisa = trim(
            (string) ($_GET['pesquisa'] ?? '')
        );

        $ordens = $this->ordemServicoModel->listarTodas(
            $pesquisa
        );

        require_once __DIR__
            . '/../views/ordens-servico/index.php';
    }

    /**
     * Tela de cadastro.
     */
    public function nova(): void
    {
        $clienteModel = new Cliente();

        $clientes = $clienteModel->listarTodos();

        require_once __DIR__
            . '/../views/ordens-servico/nova.php';
    }

    /**
     * Salvar nova OS.
     */
    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?rota=ordens-servico');
            exit;
        }

        try {
            if (empty($_POST['cliente_id'])) {
                throw new Exception(
                    'Selecione um cliente.'
                );
            }

            if (empty(trim($_POST['aparelho_modelo'] ?? ''))) {
                throw new Exception(
                    'Informe o modelo do aparelho.'
                );
            }

            if (empty(trim($_POST['defeito_relatado'] ?? ''))) {
                throw new Exception(
                    'Informe o defeito relatado.'
                );
            }

            $valorOs = (float) (
                $_POST['valor_os'] ?? 0
            );

            $desconto = (float) (
                $_POST['desconto'] ?? 0
            );

            if ($valorOs <= 0) {
                throw new Exception(
                    'Informe o valor da ordem de serviço.'
                );
            }

            if ($desconto < 0) {
                throw new Exception(
                    'O desconto não pode ser negativo.'
                );
            }

            if ($desconto > $valorOs) {
                throw new Exception(
                    'O desconto não pode ser maior que o valor da OS.'
                );
            }

            $dados = $_POST;

            $dados['valor_os'] = $valorOs;
            $dados['desconto'] = $desconto;
            $dados['valor_total'] = $valorOs - $desconto;
            $dados['data_entrada'] = date('Y-m-d H:i:s');

            /*
             * Uma OS nova sempre começa como recebida.
             * Evita que seja criada como pronta ou entregue.
             */
            $dados['status'] = 'recebida';

            $osId = $this->ordemServicoModel->cadastrar(
                $dados
            );

            $_SESSION['sucesso_os'] =
                'Ordem de serviço cadastrada com sucesso.';

            header(
                'Location: index.php?rota=ordem-servico-visualizar&id='
                . $osId
            );

            exit;
        } catch (Throwable $erro) {
            $_SESSION['erro_os'] =
                $erro->getMessage();

            header(
                'Location: index.php?rota=ordem-servico-nova'
            );

            exit;
        }
    }

    /**
     * Visualizar OS.
     */
    public function visualizar(): void
    {
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id) {
            $_SESSION['erro_os'] =
                'Ordem de serviço inválida.';

            header(
                'Location: index.php?rota=ordens-servico'
            );

            exit;
        }

        $ordem =
            $this->ordemServicoModel->buscarPorId($id);

        if (!$ordem) {
            $_SESSION['erro_os'] =
                'Ordem de serviço não encontrada.';

            header(
                'Location: index.php?rota=ordens-servico'
            );

            exit;
        }

        $historico =
            $this->ordemServicoModel->listarHistorico($id);

        require_once __DIR__
            . '/../views/ordens-servico/visualizar.php';
    }

    /**
 * Tela de edição da OS.
 */
public function editar(): void
{
    $id = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$id) {
        $_SESSION['erro_os'] =
            'Ordem de serviço inválida.';

        header(
            'Location: index.php?rota=ordens-servico'
        );

        exit;
    }

    $ordem =
        $this->ordemServicoModel->buscarPorId($id);

    if (!$ordem) {
        $_SESSION['erro_os'] =
            'Ordem de serviço não encontrada.';

        header(
            'Location: index.php?rota=ordens-servico'
        );

        exit;
    }

    /*
     * Uma OS entregue não deve mais ser alterada.
     */
    if (($ordem['status'] ?? '') === 'entregue') {
        $_SESSION['erro_os'] =
            'Uma ordem entregue não pode ser editada.';

        header(
            'Location: index.php?rota=ordem-servico-visualizar&id='
            . $id
        );

        exit;
    }

    /*
     * Evita alterar valores ou cliente depois que
     * a OS já estiver vinculada a uma venda.
     */
    if (!empty($ordem['venda_id'])) {
        $_SESSION['erro_os'] =
            'Esta ordem já está vinculada a uma venda e não pode ser editada.';

        header(
            'Location: index.php?rota=ordem-servico-visualizar&id='
            . $id
        );

        exit;
    }

    $clienteModel = new Cliente();

    $clientes = $clienteModel->listarTodos();

    require_once __DIR__
        . '/../views/ordens-servico/editar.php';
}

/**
 * Atualizar os dados da OS.
 */
public function atualizar(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header(
            'Location: index.php?rota=ordens-servico'
        );

        exit;
    }

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$id) {
        $_SESSION['erro_os'] =
            'Ordem de serviço inválida.';

        header(
            'Location: index.php?rota=ordens-servico'
        );

        exit;
    }

    try {
        $ordem =
            $this->ordemServicoModel->buscarPorId($id);

        if (!$ordem) {
            throw new Exception(
                'Ordem de serviço não encontrada.'
            );
        }

        if (($ordem['status'] ?? '') === 'entregue') {
            throw new Exception(
                'Uma ordem entregue não pode ser editada.'
            );
        }

        if (!empty($ordem['venda_id'])) {
            throw new Exception(
                'Esta ordem já está vinculada a uma venda e não pode ser editada.'
            );
        }

        if (empty($_POST['cliente_id'])) {
            throw new Exception(
                'Selecione um cliente.'
            );
        }

        $modelo = trim(
            (string) ($_POST['aparelho_modelo'] ?? '')
        );

        if ($modelo === '') {
            throw new Exception(
                'Informe o modelo do aparelho.'
            );
        }

        $defeitoRelatado = trim(
            (string) ($_POST['defeito_relatado'] ?? '')
        );

        if ($defeitoRelatado === '') {
            throw new Exception(
                'Informe o defeito relatado.'
            );
        }

        $valorOs = (float) (
            $_POST['valor_os'] ?? 0
        );

        $desconto = (float) (
            $_POST['desconto'] ?? 0
        );

        if ($valorOs <= 0) {
            throw new Exception(
                'Informe um valor válido para a ordem de serviço.'
            );
        }

        if ($desconto < 0) {
            throw new Exception(
                'O desconto não pode ser negativo.'
            );
        }

        if ($desconto > $valorOs) {
            throw new Exception(
                'O desconto não pode ser maior que o valor da OS.'
            );
        }

        $garantiaDias = (int) (
            $_POST['garantia_dias'] ?? 0
        );

        if ($garantiaDias < 0) {
            throw new Exception(
                'A garantia não pode ser negativa.'
            );
        }

        $dados = $_POST;

        $dados['cliente_id'] =
            (int) $_POST['cliente_id'];

        $dados['aparelho_modelo'] =
            $modelo;

        $dados['defeito_relatado'] =
            $defeitoRelatado;

        $dados['valor_os'] =
            $valorOs;

        $dados['desconto'] =
            $desconto;

        $dados['valor_total'] =
            $valorOs - $desconto;

        $dados['garantia_dias'] =
            $garantiaDias;

        $dados['aparelho_ligado'] =
            (int) ($_POST['aparelho_ligado'] ?? 0);

        $dados['aparelho_desbloqueado'] =
            (int) (
                $_POST['aparelho_desbloqueado'] ?? 0
            );

        $this->ordemServicoModel->atualizar(
            $id,
            $dados
        );

        $_SESSION['sucesso_os'] =
            'Ordem de serviço atualizada com sucesso.';

        header(
            'Location: index.php?rota=ordem-servico-visualizar&id='
            . $id
        );

        exit;
    } catch (Throwable $erro) {
        $_SESSION['erro_os'] =
            $erro->getMessage();

        header(
            'Location: index.php?rota=ordem-servico-editar&id='
            . $id
        );

        exit;
    }
}
/**
 * Exibir a versão da OS preparada para impressão.
 */
public function imprimir(): void
{
    $id = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$id) {
        $_SESSION['erro_os'] =
            'Ordem de serviço inválida.';

        header(
            'Location: index.php?rota=ordens-servico'
        );

        exit;
    }

    $ordem =
        $this->ordemServicoModel->buscarPorId($id);

    if (!$ordem) {
        $_SESSION['erro_os'] =
            'Ordem de serviço não encontrada.';

        header(
            'Location: index.php?rota=ordens-servico'
        );

        exit;
    }

    require_once __DIR__
        . '/../views/ordens-servico/imprimir.php';
}

    /**
     * Alterar o status da OS.
     */
    public function alterarStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?rota=ordens-servico');
            exit;
        }

        $id = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        $status = trim(
            (string) ($_POST['status'] ?? '')
        );

        if (!$id) {
            $_SESSION['erro_os'] =
                'Ordem de serviço inválida.';

            header(
                'Location: index.php?rota=ordens-servico'
            );

            exit;
        }

        try {
            $ordem =
                $this->ordemServicoModel->buscarPorId($id);

            if (!$ordem) {
                throw new Exception(
                    'Ordem de serviço não encontrada.'
                );
            }

            /*
             * Entregue não aparece aqui.
             * A entrega será feita automaticamente
             * quando a venda for paga.
             */
            $statusPermitidos = [
                'recebida',
                'em_analise',
                'aguardando_aprovacao',
                'aguardando_peca',
                'em_manutencao',
                'pronta',
                'cancelada'
            ];

            if (!in_array($status, $statusPermitidos, true)) {
                throw new Exception(
                    'O status selecionado não é permitido.'
                );
            }

            /*
             * Uma OS vinculada a uma venda não poderá
             * ter seu status técnico alterado manualmente.
             */
            if (!empty($ordem['venda_id'])) {
                throw new Exception(
                    'Esta ordem já está vinculada a uma venda.'
                );
            }

            /*
             * Depois de entregue, a OS está encerrada.
             */
            if (($ordem['status'] ?? '') === 'entregue') {
                throw new Exception(
                    'Esta ordem já foi entregue e está encerrada.'
                );
            }

            $this->ordemServicoModel->atualizarStatus(
                $id,
                $status
            );

            $nomesStatus = [
                'recebida' => 'Recebida',
                'em_analise' => 'Em análise',
                'aguardando_aprovacao' =>
                    'Aguardando aprovação',
                'aguardando_peca' =>
                    'Aguardando peça',
                'em_manutencao' =>
                    'Em manutenção',
                'pronta' => 'Pronta',
                'cancelada' => 'Cancelada'
            ];

            $_SESSION['sucesso_os'] =
                'Status alterado para '
                . ($nomesStatus[$status] ?? $status)
                . '.';
        } catch (Throwable $erro) {
            $_SESSION['erro_os'] =
                $erro->getMessage();
        }

        header(
            'Location: index.php?rota=ordem-servico-visualizar&id='
            . $id
        );

        exit;
    }
}