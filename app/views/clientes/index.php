<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sucessoCliente = $_SESSION['sucesso_cliente'] ?? null;
$erroListaCliente = $_SESSION['erro_lista_cliente'] ?? null;

unset(
    $_SESSION['sucesso_cliente'],
    $_SESSION['erro_lista_cliente']
);

?>

<style>
    .conteudo {
        min-width: 0;
        overflow-x: hidden;
    }

    .cabecalho-clientes-acoes {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .botao-novo-cliente {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 18px;
        border-radius: 9px;
        background: #20e58f;
        color: #f6f6f6;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .botao-novo-cliente:hover {
        filter: brightness(1.08);
    }

    .painel {
        min-width: 0;
        overflow: hidden;
    }

    .barra-pesquisa {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .barra-pesquisa input[type="text"] {
        min-width: 0;
    }

    .tabela-responsiva {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    .tabela-clientes {
        width: 100%;
        max-width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .tabela-clientes th,
    .tabela-clientes td {
        padding: 13px 8px;
        font-size: 13px;
        line-height: 1.35;
        vertical-align: middle;
        overflow-wrap: anywhere;
    }

    .tabela-clientes th {
        font-size: 11px;
        white-space: normal;
    }

    /*
     * Larguras das colunas
     */
    .tabela-clientes th:nth-child(1),
    .tabela-clientes td:nth-child(1) {
        width: 10%;
    }

    .tabela-clientes th:nth-child(2),
    .tabela-clientes td:nth-child(2) {
        width: 24%;
    }

    .tabela-clientes th:nth-child(3),
    .tabela-clientes td:nth-child(3) {
        width: 17%;
    }

    .tabela-clientes th:nth-child(4),
    .tabela-clientes td:nth-child(4) {
        width: 20%;
    }

    .tabela-clientes th:nth-child(5),
    .tabela-clientes td:nth-child(5) {
        width: 17%;
    }

    .tabela-clientes th:nth-child(6),
    .tabela-clientes td:nth-child(6) {
        width: 12%;
        text-align: center;
    }

    .tipo-cliente {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        max-width: 100%;
        padding: 5px 8px;
        border-radius: 20px;
        box-sizing: border-box;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.15;
        text-align: center;
        white-space: nowrap;
    }

    .tipo-fisica {
        color: #52d6ff;
        background: rgba(82, 214, 255, 0.12);
        border: 1px solid rgba(82, 214, 255, 0.35);
    }

    .tipo-juridica {
        color: #d6a7ff;
        background: rgba(214, 167, 255, 0.12);
        border: 1px solid rgba(214, 167, 255, 0.35);
    }

    .nome-cliente {
        display: inline-block;
        max-width: 100%;
        color: #20e58f;
        font-weight: 700;
        line-height: 1.3;
        text-decoration: none;
    }

    .nome-cliente:hover {
        text-decoration: underline;
    }

    .documento-cliente {
        white-space: nowrap;
    }

    .contato-cliente {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .linha-contato {
        display: flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
    }

    .linha-contato span {
        flex-shrink: 0;
        color: #aebed0;
        font-size: 11px;
        font-weight: 700;
    }

    .linha-contato strong {
        min-width: 0;
        color: #ffffff;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .linha-whatsapp strong {
        color: #20e58f;
    }

    .email-cliente {
        display: block;
        max-width: 100%;
        font-size: 12px;
        overflow-wrap: anywhere;
    }

    .acoes-cliente {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 6px;
        width: 100%;
    }

    .botao-editar-cliente,
    .botao-excluir-cliente {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-width: 0;
        padding: 7px 5px;
        border-radius: 7px;
        box-sizing: border-box;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.1;
        text-align: center;
        text-decoration: none;
    }

    .botao-editar-cliente {
        border: none;
        background: #159ee8;
       color: #ffffff !important;;
    }

    .form-excluir-cliente {
        width: 100%;
        margin: 0;
    }

    .botao-excluir-cliente {
        border: 1px solid rgba(255, 107, 107, 0.55);
        background: rgba(255, 107, 107, 0.12);
        color: #ff7f7f;
        cursor: pointer;
    }

    .botao-editar-cliente:hover,
    .botao-excluir-cliente:hover {
        filter: brightness(1.1);
    }

    .botao-editar-cliente:hover {
    color: #ffffff !important;
    }

    @media (max-width: 1050px) {
        .tabela-clientes th,
        .tabela-clientes td {
            padding: 11px 6px;
            font-size: 12px;
        }

        .tabela-clientes th {
            font-size: 10px;
        }

        /*
         * Esconde o e-mail em telas menores.
         */
        .tabela-clientes th:nth-child(5),
        .tabela-clientes td:nth-child(5) {
            display: none;
        }

        .tabela-clientes th:nth-child(1),
        .tabela-clientes td:nth-child(1) {
            width: 11%;
        }

        .tabela-clientes th:nth-child(2),
        .tabela-clientes td:nth-child(2) {
            width: 28%;
        }

        .tabela-clientes th:nth-child(3),
        .tabela-clientes td:nth-child(3) {
            width: 19%;
        }

        .tabela-clientes th:nth-child(4),
        .tabela-clientes td:nth-child(4) {
            width: 27%;
        }

        .tabela-clientes th:nth-child(6),
        .tabela-clientes td:nth-child(6) {
            width: 15%;
        }
    }

    @media (max-width: 760px) {
        .topo {
            align-items: flex-start;
            gap: 14px;
        }

        .cabecalho-clientes-acoes {
            width: 100%;
        }

        .botao-novo-cliente {
            width: 100%;
        }

        /*
         * Esconde o tipo em telas pequenas.
         */
        .tabela-clientes th:nth-child(1),
        .tabela-clientes td:nth-child(1) {
            display: none;
        }

        .tabela-clientes th:nth-child(2),
        .tabela-clientes td:nth-child(2) {
            width: 32%;
        }

        .tabela-clientes th:nth-child(3),
        .tabela-clientes td:nth-child(3) {
            width: 23%;
        }

        .tabela-clientes th:nth-child(4),
        .tabela-clientes td:nth-child(4) {
            width: 30%;
        }

        .tabela-clientes th:nth-child(6),
        .tabela-clientes td:nth-child(6) {
            width: 15%;
        }
    }
</style>

<main class="conteudo">

    <header class="topo">

        <div>
            <h1>Clientes</h1>

            <p>
                Cadastre e consulte os clientes da loja.
            </p>
        </div>

        <div class="cabecalho-clientes-acoes">

            <a
                class="botao-novo-cliente"
                href="index.php?rota=cliente-novo"
            >
                + Novo cliente
            </a>

        </div>

    </header>

    <?php if ($sucessoCliente): ?>

        <div class="mensagem sucesso">
            <?= htmlspecialchars($sucessoCliente) ?>
        </div>

    <?php endif; ?>

    <?php if ($erroListaCliente): ?>

        <div class="mensagem erro">
            <?= htmlspecialchars($erroListaCliente) ?>
        </div>

    <?php endif; ?>

    <section class="painel">

        <form
            class="barra-pesquisa"
            action="index.php"
            method="GET"
        >

            <input
                type="hidden"
                name="rota"
                value="clientes"
            >

            <input
                type="text"
                name="pesquisa"
                value="<?= htmlspecialchars($pesquisa ?? '') ?>"
                placeholder="Pesquise por nome, CPF/CNPJ, telefone ou WhatsApp"
            >

            <button type="submit">
                Pesquisar
            </button>

        </form>

        <div class="tabela-responsiva">

            <table class="tabela-produtos tabela-clientes">

                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Cliente</th>
                        <th>CPF/CNPJ</th>
                        <th>Contato</th>
                        <th>E-mail</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($clientes)): ?>

                        <tr>
                            <td
                                colspan="6"
                                class="tabela-vazia"
                            >
                                Nenhum cliente encontrado.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($clientes as $cliente): ?>

                            <?php
                            $pessoaJuridica =
                                ($cliente['tipo'] ?? 'F') === 'J';

                            $emailCliente =
                                trim((string) ($cliente['email'] ?? ''));
                            ?>

                            <tr>

                                <td>
                                    <span
                                        class="tipo-cliente <?= $pessoaJuridica
                                            ? 'tipo-juridica'
                                            : 'tipo-fisica' ?>"
                                    >
                                        <?= $pessoaJuridica
                                            ? 'Jurídica'
                                            : 'Física' ?>
                                    </span>
                                </td>

                                <td>
                                    <a
                                        class="nome-cliente"
                                        href="index.php?rota=cliente-editar&id=<?= (int) $cliente['id'] ?>"
                                    >
                                        <?= htmlspecialchars(
                                            $cliente['nome']
                                        ) ?>
                                    </a>
                                </td>

                                <td class="documento-cliente">
                                    <?= htmlspecialchars(
                                        $cliente['cpf_cnpj']
                                    ) ?>
                                </td>

                                <td>

                                    <div class="contato-cliente">

                                        <div class="linha-contato">
                                            <span>Tel.:</span>

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $cliente['telefone']
                                                ) ?>
                                            </strong>
                                        </div>

                                        <div
                                            class="linha-contato linha-whatsapp"
                                        >
                                            <span>WhatsApp:</span>

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $cliente['whatsapp']
                                                ) ?>
                                            </strong>
                                        </div>

                                    </div>

                                </td>

                                <td>
                                    <span class="email-cliente">
                                        <?= $emailCliente !== ''
                                            ? htmlspecialchars($emailCliente)
                                            : '-' ?>
                                    </span>
                                </td>

                                <td>

                                    <div class="acoes-cliente">

                                        <a
                                            class="botao-editar-cliente"
                                            href="index.php?rota=cliente-editar&id=<?= (int) $cliente['id'] ?>"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            class="form-excluir-cliente"
                                            action="index.php?rota=cliente-excluir"
                                            method="POST"
                                            onsubmit="return confirm(
                                                'Deseja realmente excluir este cliente?'
                                            );"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $cliente['id'] ?>"
                                            >

                                            <button
                                                class="botao-excluir-cliente"
                                                type="submit"
                                            >
                                                Excluir
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>