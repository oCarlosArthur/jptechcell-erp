<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sucesso = $_SESSION['sucesso_venda'] ?? null;
unset($_SESSION['sucesso_venda']);

$erro = $_SESSION['erro_venda'] ?? null;
unset($_SESSION['erro_venda']);

$pesquisaAtual = trim(
    (string) ($_GET['pesquisa'] ?? '')
);

$formasPagamento = [
    'pix' => 'PIX',
    'dinheiro' => 'Dinheiro',
    'debito' => 'Cartão de débito',
    'credito' => 'Cartão de crédito',
    'parcelado' => 'Parcelado'
];

?>

<style>
    .topo-vendas {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .topo-vendas h1 {
        margin: 0 0 5px;
        color: #ffffff;
        font-size: 32px;
    }

    .topo-vendas p {
        margin: 0;
        color: #aebed0;
        font-size: 15px;
    }

    .botao-nova-venda {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 20px;
        border: none;
        border-radius: 9px;
        background: #20e58f;
        color: #f8f8f8 !important;
        font-size: 14px;
        font-weight: 900;
        text-decoration: none;
        white-space: nowrap;
        transition: 0.2s;
    }

    .botao-nova-venda:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
        color: #f8f8f8 !important;
    }

    .painel-vendas {
        padding: 24px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        background: #19263a;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .form-pesquisa-vendas {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 22px;
    }

    .campo-pesquisa-vendas {
        flex: 1;
        min-width: 0;
        min-height: 46px;
        box-sizing: border-box;
        padding: 0 14px;
        border: 1px solid rgba(255, 255, 255, 0.13);
        border-radius: 9px;
        outline: none;
        background: #101b2b;
        color: #ffffff;
        font-size: 14px;
    }

    .campo-pesquisa-vendas::placeholder {
        color: #8292a7;
    }

    .campo-pesquisa-vendas:focus {
        border-color: #52d6ff;
        box-shadow: 0 0 0 3px rgba(82, 214, 255, 0.1);
    }

    .botao-pesquisar-vendas {
        min-height: 46px;
        padding: 0 20px;
        border: none;
        border-radius: 9px;
        background: #159ee8;
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.2s;
    }

    .botao-pesquisar-vendas:hover {
        filter: brightness(1.1);
    }

    .botao-limpar-pesquisa {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 18px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 9px;
        background: transparent;
        color: #c7d3e2;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: 0.2s;
    }

    .botao-limpar-pesquisa:hover {
        border-color: rgba(255, 255, 255, 0.28);
        background: rgba(255, 255, 255, 0.05);
        color: #ffffff;
    }

    .aviso-linha-clicavel {
        margin: 0 0 18px;
        color: #8fa1b6;
        font-size: 12px;
    }

    .tabela-vendas-container {
        width: 100%;
        overflow-x: auto;
    }

    .tabela-vendas {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .tabela-vendas th {
        padding: 13px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.11);
        color: #9fb0c4;
        font-size: 12px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }

    .tabela-vendas td {
        padding: 15px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        color: #ffffff;
        font-size: 14px;
        vertical-align: middle;
    }

    .linha-venda {
        cursor: pointer;
        transition: background 0.2s, transform 0.2s;
    }

    .linha-venda:hover {
        background: rgba(82, 214, 255, 0.055);
    }

    .linha-venda:focus {
        outline: 2px solid rgba(82, 214, 255, 0.7);
        outline-offset: -2px;
        background: rgba(82, 214, 255, 0.055);
    }

    .linha-venda-cancelada {
        background: rgba(255, 91, 91, 0.025);
    }

    .linha-venda-cancelada:hover {
        background: rgba(255, 91, 91, 0.07);
    }

    .numero-venda {
        color: #52d6ff;
        font-weight: 800;
        white-space: nowrap;
    }

    .cliente-venda {
        min-width: 170px;
    }

    .cliente-venda strong {
        display: block;
        color: #ffffff;
    }

    .cliente-venda small {
        display: block;
        margin-top: 4px;
        color: #8292a7;
        font-size: 11px;
    }

    .valor-venda {
        color: #20e58f;
        font-weight: 800;
        white-space: nowrap;
    }

    .quantidade-itens {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 62px;
        padding: 6px 10px;
        border: 1px solid rgba(82, 214, 255, 0.18);
        border-radius: 999px;
        background: rgba(82, 214, 255, 0.07);
        color: #9fe5ff;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-venda {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 92px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .status-venda-concluida {
        background: rgba(32, 229, 143, 0.13);
        color: #20e58f;
    }

    .status-venda-cancelada {
        background: rgba(255, 91, 91, 0.13);
        color: #ff7777;
    }

    .coluna-acoes {
        text-align: right !important;
    }

    .acoes-venda {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .botao-visualizar-venda {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 14px;
        border: 1px solid rgba(82, 214, 255, 0.24);
        border-radius: 8px;
        background: rgba(82, 214, 255, 0.08);
        color: #8ee3ff;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        transition: 0.2s;
    }

    .botao-visualizar-venda:hover {
        border-color: rgba(82, 214, 255, 0.5);
        background: rgba(82, 214, 255, 0.15);
        color: #ffffff;
    }

    .estado-vazio-vendas {
        padding: 42px 20px !important;
        color: #aebed0 !important;
        text-align: center;
    }

    .mensagem {
        margin-bottom: 20px;
        padding: 14px 16px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 700;
    }

    .mensagem.sucesso {
        border: 1px solid rgba(32, 229, 143, 0.25);
        background: rgba(32, 229, 143, 0.1);
        color: #20e58f;
    }

    .mensagem.erro {
        border: 1px solid rgba(255, 91, 91, 0.25);
        background: rgba(255, 91, 91, 0.1);
        color: #ff7777;
    }

    @media (max-width: 800px) {
        .topo-vendas {
            align-items: stretch;
            flex-direction: column;
        }

        .botao-nova-venda {
            width: 100%;
            box-sizing: border-box;
        }

        .form-pesquisa-vendas {
            align-items: stretch;
            flex-direction: column;
        }

        .botao-pesquisar-vendas,
        .botao-limpar-pesquisa {
            width: 100%;
            box-sizing: border-box;
        }

        .painel-vendas {
            padding: 16px;
        }
    }
</style>

<main class="conteudo">

    <header class="topo-vendas">

        <div>
            <h1>Vendas</h1>

            <p>
                Gerencie e consulte todas as vendas da loja.
            </p>
        </div>

        <a
            href="index.php?rota=venda-nova"
            class="botao-nova-venda"
        >
            + Nova venda
        </a>

    </header>

    <?php if ($sucesso): ?>

        <div class="mensagem sucesso">
            <?= htmlspecialchars($sucesso) ?>
        </div>

    <?php endif; ?>

    <?php if ($erro): ?>

        <div class="mensagem erro">
            <?= htmlspecialchars($erro) ?>
        </div>

    <?php endif; ?>

    <section class="painel-vendas">

        <form
            class="form-pesquisa-vendas"
            method="GET"
        >

            <input
                type="hidden"
                name="rota"
                value="vendas"
            >

            <input
                class="campo-pesquisa-vendas"
                type="text"
                name="pesquisa"
                placeholder="Pesquisar por venda, cliente, pagamento ou status..."
                value="<?= htmlspecialchars($pesquisaAtual) ?>"
            >

            <button
                class="botao-pesquisar-vendas"
                type="submit"
            >
                Pesquisar
            </button>

            <?php if ($pesquisaAtual !== ''): ?>

                <a
                    class="botao-limpar-pesquisa"
                    href="index.php?rota=vendas"
                >
                    Limpar
                </a>

            <?php endif; ?>

        </form>

        <?php if (!empty($vendas)): ?>

            <p class="aviso-linha-clicavel">
                Clique em qualquer venda para abrir os detalhes.
            </p>

        <?php endif; ?>

        <div class="tabela-vendas-container">

            <table class="tabela-vendas">

                <thead>
                    <tr>
                        <th>Nº da venda</th>
                        <th>Cliente</th>
                        <th>Itens</th>
                        <th>Total</th>
                        <th>Pagamento</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th class="coluna-acoes">Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($vendas)): ?>

                        <tr>
                            <td
                                class="estado-vazio-vendas"
                                colspan="8"
                            >
                                Nenhuma venda encontrada.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($vendas as $venda): ?>

                            <?php
                            $status =
                                $venda['status'] ?? 'concluida';

                            $cancelada =
                                $status === 'cancelada';

                            $classeStatus =
                                $cancelada
                                    ? 'status-venda-cancelada'
                                    : 'status-venda-concluida';

                            $classeLinha =
                                $cancelada
                                    ? 'linha-venda linha-venda-cancelada'
                                    : 'linha-venda';

                            $clienteNome =
                                !empty($venda['cliente_nome'])
                                    ? $venda['cliente_nome']
                                    : 'Consumidor final';

                            $tipoCliente =
                                !empty($venda['cliente_nome'])
                                    ? 'Cliente cadastrado'
                                    : 'Sem cadastro';

                            $quantidadeItens =
                                (int) (
                                    $venda['quantidade_itens']
                                    ?? 0
                                );

                            $urlVisualizar =
                                'index.php?rota=venda-visualizar&id='
                                . (int) $venda['id'];
                            ?>

                            <tr
                                class="<?= $classeLinha ?>"
                                tabindex="0"
                                role="link"
                                data-url="<?= htmlspecialchars(
                                    $urlVisualizar
                                ) ?>"
                                aria-label="Abrir venda <?= htmlspecialchars(
                                    $venda['numero_venda']
                                ) ?>"
                            >

                                <td class="numero-venda">
                                    <?= htmlspecialchars(
                                        $venda['numero_venda']
                                    ) ?>
                                </td>

                                <td class="cliente-venda">
                                    <strong>
                                        <?= htmlspecialchars(
                                            $clienteNome
                                        ) ?>
                                    </strong>

                                    <small>
                                        <?= htmlspecialchars(
                                            $tipoCliente
                                        ) ?>
                                    </small>
                                </td>

                                <td>
                                    <span class="quantidade-itens">
                                        <?= $quantidadeItens ?>

                                        <?= $quantidadeItens === 1
                                            ? 'item'
                                            : 'itens' ?>
                                    </span>
                                </td>

                                <td class="valor-venda">
                                    R$ <?= number_format(
                                        (float) $venda['total'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $formasPagamento[
                                            $venda['forma_pagamento']
                                        ]
                                        ?? ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $venda[
                                                    'forma_pagamento'
                                                ]
                                            )
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <span
                                        class="status-venda <?= $classeStatus ?>"
                                    >
                                        <?= $cancelada
                                            ? 'Cancelada'
                                            : 'Concluída' ?>
                                    </span>
                                </td>

                                <td>
                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $venda['criado_em']
                                        )
                                    ) ?>
                                </td>

                                <td class="coluna-acoes">

                                    <div class="acoes-venda">

                                        <a
                                            class="botao-visualizar-venda"
                                            href="<?= htmlspecialchars(
                                                $urlVisualizar
                                            ) ?>"
                                        >
                                            Visualizar
                                        </a>

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

<script>
document.querySelectorAll('.linha-venda').forEach(
    linha => {
        linha.addEventListener('click', evento => {
            if (
                evento.target.closest(
                    'a, button, input, select, textarea, form'
                )
            ) {
                return;
            }

            const url = linha.dataset.url;

            if (url) {
                window.location.href = url;
            }
        });

        linha.addEventListener('keydown', evento => {
            if (
                evento.key !== 'Enter'
                && evento.key !== ' '
            ) {
                return;
            }

            if (
                evento.target.closest(
                    'a, button, input, select, textarea, form'
                )
            ) {
                return;
            }

            evento.preventDefault();

            const url = linha.dataset.url;

            if (url) {
                window.location.href = url;
            }
        });
    }
);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>