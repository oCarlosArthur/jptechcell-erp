<?php

require_once __DIR__ . '/../models/Cliente.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$clienteModel = new Cliente();

$rota = $_GET['rota'] ?? 'clientes';

switch ($rota) {

    case 'clientes':

        $pesquisa = trim($_GET['pesquisa'] ?? '');

        $clientes = $clienteModel->listarTodos($pesquisa);

        require_once __DIR__
            . '/../views/clientes/index.php';

        break;


    case 'cliente-novo':

        require_once __DIR__
            . '/../views/clientes/novo.php';

        break;


    case 'cliente-salvar':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?rota=clientes');
            exit;
        }

        $tipo = trim($_POST['tipo'] ?? 'F');
        $nome = trim($_POST['nome'] ?? '');
        $cpfCnpj = trim($_POST['cpf_cnpj'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');

        $email = trim($_POST['email'] ?? '');

        $dataNascimento = trim(
            $_POST['data_nascimento'] ?? ''
        );

        $cep = trim($_POST['cep'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $numero = trim($_POST['numero'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');

        $estado = strtoupper(
            trim($_POST['estado'] ?? '')
        );

        $observacoes = trim(
            $_POST['observacoes'] ?? ''
        );

        $_SESSION['dados_cliente'] = [
            'tipo' => $tipo,
            'nome' => $nome,
            'cpf_cnpj' => $cpfCnpj,
            'telefone' => $telefone,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'data_nascimento' => $dataNascimento,
            'cep' => $cep,
            'endereco' => $endereco,
            'numero' => $numero,
            'bairro' => $bairro,
            'cidade' => $cidade,
            'estado' => $estado,
            'observacoes' => $observacoes
        ];

        if (!in_array($tipo, ['F', 'J'], true)) {
            $_SESSION['erro_cliente'] =
                'Selecione um tipo de cliente válido.';

            header('Location: index.php?rota=cliente-novo');
            exit;
        }

        if ($nome === '') {
            $_SESSION['erro_cliente'] =
                'Informe o nome do cliente.';

            header('Location: index.php?rota=cliente-novo');
            exit;
        }

        if ($cpfCnpj === '') {
            $_SESSION['erro_cliente'] =
                'Informe o CPF ou CNPJ.';

            header('Location: index.php?rota=cliente-novo');
            exit;
        }

        if ($telefone === '') {
            $_SESSION['erro_cliente'] =
                'Informe o telefone.';

            header('Location: index.php?rota=cliente-novo');
            exit;
        }

        if ($whatsapp === '') {
            $_SESSION['erro_cliente'] =
                'Informe o WhatsApp.';

            header('Location: index.php?rota=cliente-novo');
            exit;
        }

        $dadosCliente = [
            'tipo' => $tipo,
            'nome' => $nome,
            'cpf_cnpj' => $cpfCnpj,
            'telefone' => $telefone,
            'whatsapp' => $whatsapp,
            'email' => $email !== '' ? $email : null,
            'data_nascimento' =>
                $dataNascimento !== ''
                    ? $dataNascimento
                    : null,
            'cep' => $cep !== '' ? $cep : null,
            'endereco' =>
                $endereco !== '' ? $endereco : null,
            'numero' =>
                $numero !== '' ? $numero : null,
            'bairro' =>
                $bairro !== '' ? $bairro : null,
            'cidade' =>
                $cidade !== '' ? $cidade : null,
            'estado' =>
                $estado !== '' ? $estado : null,
            'observacoes' =>
                $observacoes !== ''
                    ? $observacoes
                    : null
        ];

        try {

            $clienteExistente =
                $clienteModel->buscarPorCpfCnpj($cpfCnpj);

            if ($clienteExistente) {

                if ((int) $clienteExistente['ativo'] === 1) {
                    $_SESSION['erro_cliente'] =
                        'Já existe um cliente ativo cadastrado com este CPF ou CNPJ.';

                    header(
                        'Location: index.php?rota=cliente-novo'
                    );
                    exit;
                }

                $clienteModel->reativarEAtualizar(
                    (int) $clienteExistente['id'],
                    $dadosCliente
                );

                unset($_SESSION['dados_cliente']);

                $_SESSION['sucesso_cliente'] =
                    'O cliente já existia e estava inativo. O cadastro foi reativado e atualizado com sucesso!';

                header('Location: index.php?rota=clientes');
                exit;
            }

            $clienteModel->cadastrar($dadosCliente);

            unset($_SESSION['dados_cliente']);

            $_SESSION['sucesso_cliente'] =
                'Cliente cadastrado com sucesso!';

            header('Location: index.php?rota=clientes');
            exit;

        } catch (Throwable $erro) {

            $_SESSION['erro_cliente'] =
                'Não foi possível cadastrar o cliente.';

            header('Location: index.php?rota=cliente-novo');
            exit;
        }


    case 'cliente-editar':

        $clienteId = (int) ($_GET['id'] ?? 0);

        if ($clienteId <= 0) {
            header('Location: index.php?rota=clientes');
            exit;
        }

        $cliente = $clienteModel->buscarPorId($clienteId);

        if (!$cliente) {
            $_SESSION['erro_lista_cliente'] =
                'Cliente não encontrado.';

            header('Location: index.php?rota=clientes');
            exit;
        }

        require_once __DIR__
            . '/../views/clientes/editar.php';

        break;


    case 'cliente-atualizar':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?rota=clientes');
            exit;
        }

        $clienteId = (int) ($_POST['id'] ?? 0);

        $tipo = trim($_POST['tipo'] ?? 'F');
        $nome = trim($_POST['nome'] ?? '');
        $cpfCnpj = trim($_POST['cpf_cnpj'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');

        $email = trim($_POST['email'] ?? '');

        $dataNascimento = trim(
            $_POST['data_nascimento'] ?? ''
        );

        $cep = trim($_POST['cep'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $numero = trim($_POST['numero'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');

        $estado = strtoupper(
            trim($_POST['estado'] ?? '')
        );

        $observacoes = trim(
            $_POST['observacoes'] ?? ''
        );

        if (
            $clienteId <= 0 ||
            $nome === '' ||
            $cpfCnpj === '' ||
            $telefone === '' ||
            $whatsapp === ''
        ) {
            $_SESSION['erro_cliente'] =
                'Preencha corretamente os dados obrigatórios.';

            header(
                'Location: index.php?rota=cliente-editar&id='
                . $clienteId
            );
            exit;
        }

        if (!in_array($tipo, ['F', 'J'], true)) {
            $_SESSION['erro_cliente'] =
                'Selecione um tipo de cliente válido.';

            header(
                'Location: index.php?rota=cliente-editar&id='
                . $clienteId
            );
            exit;
        }

        $dadosCliente = [
            'tipo' => $tipo,
            'nome' => $nome,
            'cpf_cnpj' => $cpfCnpj,
            'telefone' => $telefone,
            'whatsapp' => $whatsapp,
            'email' => $email !== '' ? $email : null,
            'data_nascimento' =>
                $dataNascimento !== ''
                    ? $dataNascimento
                    : null,
            'cep' => $cep !== '' ? $cep : null,
            'endereco' =>
                $endereco !== '' ? $endereco : null,
            'numero' =>
                $numero !== '' ? $numero : null,
            'bairro' =>
                $bairro !== '' ? $bairro : null,
            'cidade' =>
                $cidade !== '' ? $cidade : null,
            'estado' =>
                $estado !== '' ? $estado : null,
            'observacoes' =>
                $observacoes !== ''
                    ? $observacoes
                    : null
        ];

        try {

            $clienteComMesmoDocumento =
                $clienteModel->buscarPorCpfCnpj(
                    $cpfCnpj,
                    $clienteId
                );

            if ($clienteComMesmoDocumento) {
                $_SESSION['erro_cliente'] =
                    'Este CPF ou CNPJ já pertence a outro cliente cadastrado.';

                header(
                    'Location: index.php?rota=cliente-editar&id='
                    . $clienteId
                );
                exit;
            }

            $clienteModel->atualizar(
                $clienteId,
                $dadosCliente
            );

            $_SESSION['sucesso_cliente'] =
                'Cliente atualizado com sucesso!';

            header('Location: index.php?rota=clientes');
            exit;

        } catch (Throwable $erro) {

            $_SESSION['erro_cliente'] =
                'Não foi possível atualizar o cliente.';

            header(
                'Location: index.php?rota=cliente-editar&id='
                . $clienteId
            );
            exit;
        }


    case 'cliente-excluir':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?rota=clientes');
            exit;
        }

        $clienteId = (int) ($_POST['id'] ?? 0);

        if ($clienteId <= 0) {
            $_SESSION['erro_lista_cliente'] =
                'Cliente inválido.';

            header('Location: index.php?rota=clientes');
            exit;
        }

        try {

            $clienteModel->desativar($clienteId);

            $_SESSION['sucesso_cliente'] =
                'Cliente excluído com sucesso!';

            header('Location: index.php?rota=clientes');
            exit;

        } catch (Throwable $erro) {

            $_SESSION['erro_lista_cliente'] =
                'Não foi possível excluir o cliente.';

            header('Location: index.php?rota=clientes');
            exit;
        }


    default:

        http_response_code(404);

        echo 'Página de clientes não encontrada.';

        break;
}