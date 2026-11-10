<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sucesso = $_SESSION['sucesso_os'] ?? null;
unset($_SESSION['sucesso_os']);

$erro = $_SESSION['erro_os'] ?? null;
unset($_SESSION['erro_os']);

$pesquisaAtual = trim(
    (string) ($_GET['pesquisa'] ?? '')
);

$statusOs = [
    'recebida' => 'Recebida',
    'em_analise' => 'Em análise',
    'aguardando_aprovacao' => 'Aguardando aprovação',
    'aguardando_peca' => 'Aguardando peça',
    'em_manutencao' => 'Em manutenção',
    'pronta' => 'Pronta',
    'entregue' => 'Entregue',
    'cancelada' => 'Cancelada'
];

/**
 * Retorna a classe visual correspondente ao status.
 */
function classeStatusOs(string $status): string
{
    $classesPermitidas = [
        'recebida',
        'em_analise',
        'aguardando_aprovacao',
        'aguardando_peca',
        'em_manutencao',
        'pronta',
        'entregue',
        'cancelada'
    ];

    if (!in_array($status, $classesPermitidas, true)) {
        return 'recebida';
    }

    return $status;
}

/**
 * Formata a data da ordem.
 */
function formatarDataOs(?string $data): string
{
    if (empty($data)) {
        return '-';
    }

    $timestamp = strtotime($data);

    if ($timestamp === false) {
        return '-';
    }

    return date('d/m/Y H:i', $timestamp);
}

?>

<style>
    html,
    body {
        max-width: 100%;
        overflow-x: hidden;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    .conteudo {
        width: auto;
        max-width: 100%;
        min-width: 0;
        overflow-x: hidden;
    }

    .topo-ordens {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .topo-ordens-texto {
        min-width: 0;
    }

    .topo-ordens h1 {
        margin: 0 0 5px;
        color: #ffffff;
        font-size: 32px;
        line-height: 1.2;
    }

    .topo-ordens p {
        margin: 0;
        color: #aebed0;
        font-size: 15px;
        line-height: 1.5;
    }

    .botao-nova-ordem {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 20px;
        border: none;
        border-radius: 9px;
        background: #20e58f;
        color: #07190f !important;
        font-size: 14px;
        font-weight: 900;
        text-decoration: none;
        white-space: nowrap;
        transition:
            transform 0.2s,
            filter 0.2s;
    }

    .botao-nova-ordem:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
        color: #07190f !important;
    }

    .mensagem {
        margin-bottom: 20px;
        padding: 14px 16px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
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

    .painel-ordens {
        width: 100%;
        min-width: 0;
        padding: 24px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        background: #19263a;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .form-pesquisa-ordens {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        min-width: 0;
        margin-bottom: 22px;
    }

    .campo-pesquisa-ordens {
        flex: 1;
        width: 100%;
        min-width: 0;
        min-height: 46px;
        padding: 0 14px;
        border: 1px solid rgba(255, 255, 255, 0.13);
        border-radius: 9px;
        outline: none;
        background: #101b2b;
        color: #ffffff;
        font-size: 14px;
    }

    .campo-pesquisa-ordens::placeholder {
        color: #8292a7;
    }

    .campo-pesquisa-ordens:focus {
        border-color: #52d6ff;
        box-shadow: 0 0 0 3px rgba(82, 214, 255, 0.1);
    }

    .botao-pesquisar-ordens,
    .botao-limpar-pesquisa {
        min-height: 46px;
        padding: 0 20px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 800;
        white-space: nowrap;
        transition: 0.2s;
    }

    .botao-pesquisar-ordens {
        border: none;
        background: #159ee8;
        color: #ffffff;
        cursor: pointer;
    }

    .botao-pesquisar-ordens:hover {
        filter: brightness(1.1);
    }

    .botao-limpar-pesquisa {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: transparent;
        color: #c7d3e2;
        text-decoration: none;
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

    /*
    |--------------------------------------------------------------------------
    | TABELA PARA COMPUTADOR
    |--------------------------------------------------------------------------
    */

    .tabela-ordens-container {
        width: 100%;
        min-width: 0;
    }

    .tabela-ordens {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .tabela-ordens th {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.11);
        color: #9fb0c4;
        font-size: 11px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .tabela-ordens td {
        min-width: 0;
        padding: 15px 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        color: #ffffff;
        font-size: 13px;
        vertical-align: middle;
        overflow-wrap: anywhere;
    }

    .tabela-ordens th:nth-child(1),
    .tabela-ordens td:nth-child(1) {
        width: 11%;
    }

    .tabela-ordens th:nth-child(2),
    .tabela-ordens td:nth-child(2) {
        width: 17%;
    }

    .tabela-ordens th:nth-child(3),
    .tabela-ordens td:nth-child(3) {
        width: 16%;
    }

    .tabela-ordens th:nth-child(4),
    .tabela-ordens td:nth-child(4) {
        width: 18%;
    }

    .tabela-ordens th:nth-child(5),
    .tabela-ordens td:nth-child(5) {
        width: 10%;
    }

    .tabela-ordens th:nth-child(6),
    .tabela-ordens td:nth-child(6) {
        width: 13%;
    }

    .tabela-ordens th:nth-child(7),
    .tabela-ordens td:nth-child(7) {
        width: 15%;
    }

    .linha-ordem {
        cursor: pointer;
        transition: background 0.2s;
    }

    .linha-ordem:hover {
        background: rgba(82, 214, 255, 0.055);
    }

    .linha-ordem:focus {
        outline: 2px solid rgba(82, 214, 255, 0.7);
        outline-offset: -2px;
        background: rgba(82, 214, 255, 0.055);
    }

    .linha-ordem-cancelada {
        background: rgba(255, 91, 91, 0.025);
    }

    .linha-ordem-cancelada:hover {
        background: rgba(255, 91, 91, 0.07);
    }

    .numero-ordem {
        color: #52d6ff !important;
        font-weight: 900;
        white-space: nowrap;
    }

    .cliente-ordem strong,
    .aparelho-ordem strong {
        display: block;
        color: #ffffff;
        overflow-wrap: anywhere;
    }

    .cliente-ordem small,
    .aparelho-ordem small {
        display: block;
        margin-top: 4px;
        color: #8292a7;
        font-size: 11px;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .defeito-ordem {
        color: #c9d4e2;
        line-height: 1.45;
    }

    .valor-ordem {
        color: #20e58f !important;
        font-weight: 900;
        white-space: nowrap;
    }

    .data-ordem {
        color: #c9d4e2 !important;
        line-height: 1.45;
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    .status-ordem{
    display:flex;
    align-items:center;
    justify-content:center;

    width:120px;
    height:34px;

    margin:0 auto;

    border-radius:999px;

    font-size:12px;
    font-weight:800;

    text-align:center;
    line-height:1;
}

    .status-recebida {
        background: rgba(82, 214, 255, 0.13);
        color: #8ee3ff;
    }

    .status-em_analise {
        background: rgba(255, 193, 7, 0.13);
        color: #ffd45f;
    }

    .status-aguardando_aprovacao {
        background: rgba(171, 110, 255, 0.14);
        color: #c89aff;
    }

    .status-aguardando_peca {
        background: rgba(255, 145, 77, 0.14);
        color: #ffad78;
    }

    .status-em_manutencao {
        background: rgba(21, 158, 232, 0.15);
        color: #64c7ff;
    }

    .status-pronta {
        background: rgba(32, 229, 143, 0.13);
        color: #20e58f;
    }

    .status-entregue {
        background: rgba(32, 229, 143, 0.2);
        color: #72f0ba;
    }

    .status-cancelada {
        background: rgba(255, 91, 91, 0.13);
        color: #ff7777;
    }

    /*
    |--------------------------------------------------------------------------
    | CARTÕES PARA CELULAR E TABLET
    |--------------------------------------------------------------------------
    */

    .lista-cartoes-ordens {
        display: none;
        gap: 14px;
    }

    .cartao-ordem {
        display: block;
        width: 100%;
        min-width: 0;
        padding: 17px;
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 14px;
        background: #142136;
        color: inherit;
        text-decoration: none;
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.1);
        transition:
            transform 0.2s,
            border-color 0.2s,
            background 0.2s;
    }

    .cartao-ordem:hover {
        border-color: rgba(82, 214, 255, 0.25);
        background: #17263d;
        transform: translateY(-1px);
    }

    .cartao-ordem.cancelada {
        border-color: rgba(255, 91, 91, 0.13);
        background: rgba(255, 91, 91, 0.035);
    }

    .cartao-ordem-topo {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .cartao-numero-os {
        color: #52d6ff;
        font-size: 17px;
        font-weight: 900;
        line-height: 1.3;
    }

    .cartao-data-os {
        margin-top: 4px;
        color: #8292a7;
        font-size: 11px;
    }

    .cartao-ordem-conteudo {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
    }

    .cartao-campo {
        min-width: 0;
    }

    .cartao-campo.inteiro {
        grid-column: 1 / -1;
    }

    .cartao-campo span {
        display: block;
        margin-bottom: 5px;
        color: #8292a7;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .cartao-campo strong,
    .cartao-campo p {
        display: block;
        margin: 0;
        color: #ffffff;
        font-size: 13px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .cartao-campo p {
        color: #c9d4e2;
    }

    .cartao-telefone {
        margin-top: 3px !important;
        color: #8fa1b6 !important;
        font-size: 11px !important;
    }

    .cartao-valor-os {
        color: #20e58f !important;
        font-size: 16px !important;
        font-weight: 900 !important;
    }

    .cartao-ordem-rodape {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.07);
    }

    .texto-visualizar-cartao {
        color: #8ee3ff;
        font-size: 12px;
        font-weight: 900;
    }

    .estado-vazio-ordens {
        padding: 42px 20px;
        border: 1px dashed rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #aebed0;
        text-align: center;
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSIVIDADE
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1250px) {
        .tabela-ordens th:nth-child(4),
        .tabela-ordens td:nth-child(4) {
            display: none;
        }

        .tabela-ordens th:nth-child(2),
        .tabela-ordens td:nth-child(2) {
            width: 22%;
        }

        .tabela-ordens th:nth-child(3),
        .tabela-ordens td:nth-child(3) {
            width: 20%;
        }

        .tabela-ordens th:nth-child(6),
        .tabela-ordens td:nth-child(6) {
            width: 17%;
        }
    }

    @media (max-width: 980px) {
        .tabela-ordens-container {
            display: none;
        }

        .lista-cartoes-ordens {
            display: grid;
        }

        .aviso-linha-clicavel {
            display: none;
        }
    }

    @media (max-width: 800px) {
        .topo-ordens {
            align-items: stretch;
            flex-direction: column;
        }

        .topo-ordens h1 {
            font-size: 27px;
        }

        .botao-nova-ordem {
            width: 100%;
        }

        .form-pesquisa-ordens {
            align-items: stretch;
            flex-direction: column;
        }

        .botao-pesquisar-ordens,
        .botao-limpar-pesquisa {
            width: 100%;
        }

        .painel-ordens {
            padding: 16px;
        }
    }

    @media (max-width: 560px) {
        .topo-ordens {
            margin-bottom: 20px;
        }

        .topo-ordens h1 {
            font-size: 24px;
        }

        .painel-ordens {
            padding: 13px;
            border-radius: 13px;
        }

        .cartao-ordem {
            padding: 15px;
        }

        .cartao-ordem-topo {
            align-items: stretch;
            flex-direction: column;
        }

        .cartao-ordem-topo .status-ordem {
            align-self: flex-start;
        }

        .cartao-ordem-conteudo {
            grid-template-columns: 1fr;
        }

        .cartao-campo.inteiro {
            grid-column: 1;
        }
    }
</style>

<main class="conteudo">

    <header class="topo-ordens">

        <div class="topo-ordens-texto">

            <h1>Ordens de serviço</h1>

            <p>
                Cadastre e acompanhe os atendimentos técnicos da loja.
            </p>

        </div>

        <a
            href="index.php?rota=ordem-servico-nova"
            class="botao-nova-ordem"
        >
            + Nova ordem de serviço
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

    <section class="painel-ordens">

        <form
            class="form-pesquisa-ordens"
            method="GET"
        >

            <input
                type="hidden"
                name="rota"
                value="ordens-servico"
            >

            <input
                class="campo-pesquisa-ordens"
                type="text"
                name="pesquisa"
                placeholder="Pesquisar por OS, cliente, telefone, aparelho, IMEI, defeito ou status..."
                value="<?= htmlspecialchars($pesquisaAtual) ?>"
            >

            <button
                class="botao-pesquisar-ordens"
                type="submit"
            >
                Pesquisar
            </button>

            <?php if ($pesquisaAtual !== ''): ?>

                <a
                    class="botao-limpar-pesquisa"
                    href="index.php?rota=ordens-servico"
                >
                    Limpar
                </a>

            <?php endif; ?>

        </form>

        <?php if (!empty($ordens)): ?>

            <p class="aviso-linha-clicavel">
                Clique em qualquer ordem para abrir os detalhes.
            </p>

        <?php endif; ?>

        <!--
        |--------------------------------------------------------------------------
        | TABELA PARA COMPUTADOR
        |--------------------------------------------------------------------------
        -->

        <div class="tabela-ordens-container">

            <table class="tabela-ordens">

                <thead>
                    <tr>
                        <th>Nº da OS</th>
                        <th>Cliente</th>
                        <th>Aparelho</th>
                        <th>Defeito relatado</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Entrada</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($ordens)): ?>

                        <tr>
                            <td colspan="7">

                                <div class="estado-vazio-ordens">
                                    Nenhuma ordem de serviço encontrada.
                                </div>

                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($ordens as $ordem): ?>

                            <?php

                            $status = trim(
                                (string) (
                                    $ordem['status'] ?? 'recebida'
                                )
                            );

                            if ($status === '') {
                                $status = 'recebida';
                            }

                            $classeStatus =
                                'status-' . classeStatusOs($status);

                            $nomeStatus = $statusOs[$status]
                                ?? ucfirst(
                                    str_replace('_', ' ', $status)
                                );

                            $cancelada = $status === 'cancelada';

                            $classeLinha = $cancelada
                                ? 'linha-ordem linha-ordem-cancelada'
                                : 'linha-ordem';

                            $marca = trim(
                                (string) (
                                    $ordem['aparelho_marca'] ?? ''
                                )
                            );

                            $modelo = trim(
                                (string) (
                                    $ordem['aparelho_modelo'] ?? ''
                                )
                            );

                            $aparelho = trim(
                                $marca . ' ' . $modelo
                            );

                            if ($aparelho === '') {
                                $aparelho = 'Não informado';
                            }

                            $cor = trim(
                                (string) (
                                    $ordem['aparelho_cor'] ?? ''
                                )
                            );

                            $defeito = trim(
                                (string) (
                                    $ordem['defeito_relatado'] ?? ''
                                )
                            );

                            $urlVisualizar =
                                'index.php?rota=ordem-servico-visualizar&id='
                                . (int) $ordem['id'];

                            ?>

                            <tr
                                class="<?= htmlspecialchars($classeLinha) ?>"
                                tabindex="0"
                                role="link"
                                data-url="<?= htmlspecialchars(
                                    $urlVisualizar
                                ) ?>"
                                aria-label="Abrir ordem de serviço <?= htmlspecialchars(
                                    (string) $ordem['numero_os']
                                ) ?>"
                            >

                                <td class="numero-ordem">
                                    <?= htmlspecialchars(
                                        (string) $ordem['numero_os']
                                    ) ?>
                                </td>

                                <td class="cliente-ordem">

                                    <strong>
                                        <?= htmlspecialchars(
                                            (string) $ordem['cliente_nome']
                                        ) ?>
                                    </strong>

                                    <small>
                                        <?= !empty(
                                            $ordem['cliente_telefone']
                                        )
                                            ? htmlspecialchars(
                                                (string) $ordem[
                                                    'cliente_telefone'
                                                ]
                                            )
                                            : 'Telefone não informado' ?>
                                    </small>

                                </td>

                                <td class="aparelho-ordem">

                                    <strong>
                                        <?= htmlspecialchars($aparelho) ?>
                                    </strong>

                                    <?php if ($cor !== ''): ?>

                                        <small>
                                            Cor:
                                            <?= htmlspecialchars($cor) ?>
                                        </small>

                                    <?php endif; ?>

                                </td>

                                <td class="defeito-ordem">
                                    <?= htmlspecialchars(
                                        mb_strimwidth(
                                            $defeito,
                                            0,
                                            52,
                                            '...'
                                        )
                                    ) ?>
                                </td>

                                <td class="valor-ordem">
                                    R$ <?= number_format(
                                        (float) (
                                            $ordem['valor_total'] ?? 0
                                        ),
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>

                                    <span
                                        class="status-ordem <?= htmlspecialchars(
                                            $classeStatus
                                        ) ?>"
                                    >
                                        <?= htmlspecialchars($nomeStatus) ?>
                                    </span>

                                </td>

                                <td class="data-ordem">
                                    <?= formatarDataOs(
                                        $ordem['data_entrada'] ?? null
                                    ) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <!--
        |--------------------------------------------------------------------------
        | CARTÕES PARA CELULAR E TABLET
        |--------------------------------------------------------------------------
        -->

        <div class="lista-cartoes-ordens">

            <?php if (empty($ordens)): ?>

                <div class="estado-vazio-ordens">
                    Nenhuma ordem de serviço encontrada.
                </div>

            <?php else: ?>

                <?php foreach ($ordens as $ordem): ?>

                    <?php

                    $status = trim(
                        (string) (
                            $ordem['status'] ?? 'recebida'
                        )
                    );

                    if ($status === '') {
                        $status = 'recebida';
                    }

                    $classeStatus =
                        'status-' . classeStatusOs($status);

                    $nomeStatus = $statusOs[$status]
                        ?? ucfirst(
                            str_replace('_', ' ', $status)
                        );

                    $cancelada = $status === 'cancelada';

                    $marca = trim(
                        (string) (
                            $ordem['aparelho_marca'] ?? ''
                        )
                    );

                    $modelo = trim(
                        (string) (
                            $ordem['aparelho_modelo'] ?? ''
                        )
                    );

                    $aparelho = trim(
                        $marca . ' ' . $modelo
                    );

                    if ($aparelho === '') {
                        $aparelho = 'Não informado';
                    }

                    $cor = trim(
                        (string) (
                            $ordem['aparelho_cor'] ?? ''
                        )
                    );

                    $defeito = trim(
                        (string) (
                            $ordem['defeito_relatado'] ?? ''
                        )
                    );

                    $urlVisualizar =
                        'index.php?rota=ordem-servico-visualizar&id='
                        . (int) $ordem['id'];

                    ?>

                    <a
                        class="cartao-ordem <?= $cancelada
                            ? 'cancelada'
                            : '' ?>"
                        href="<?= htmlspecialchars($urlVisualizar) ?>"
                    >

                        <div class="cartao-ordem-topo">

                            <div>

                                <div class="cartao-numero-os">
                                    <?= htmlspecialchars(
                                        (string) $ordem['numero_os']
                                    ) ?>
                                </div>

                                <div class="cartao-data-os">
                                    Entrada:
                                    <?= formatarDataOs(
                                        $ordem['data_entrada'] ?? null
                                    ) ?>
                                </div>

                            </div>

                            <span
                                class="status-ordem <?= htmlspecialchars(
                                    $classeStatus
                                ) ?>"
                            >
                                <?= htmlspecialchars($nomeStatus) ?>
                            </span>

                        </div>

                        <div class="cartao-ordem-conteudo">

                            <div class="cartao-campo">

                                <span>Cliente</span>

                                <strong>
                                    <?= htmlspecialchars(
                                        (string) $ordem['cliente_nome']
                                    ) ?>
                                </strong>

                                <p class="cartao-telefone">
                                    <?= !empty(
                                        $ordem['cliente_telefone']
                                    )
                                        ? htmlspecialchars(
                                            (string) $ordem[
                                                'cliente_telefone'
                                            ]
                                        )
                                        : 'Telefone não informado' ?>
                                </p>

                            </div>

                            <div class="cartao-campo">

                                <span>Aparelho</span>

                                <strong>
                                    <?= htmlspecialchars($aparelho) ?>
                                </strong>

                                <?php if ($cor !== ''): ?>

                                    <p>
                                        Cor:
                                        <?= htmlspecialchars($cor) ?>
                                    </p>

                                <?php endif; ?>

                            </div>

                            <div class="cartao-campo inteiro">

                                <span>Defeito relatado</span>

                                <p>
                                    <?= $defeito !== ''
                                        ? htmlspecialchars($defeito)
                                        : 'Não informado' ?>
                                </p>

                            </div>

                            <div class="cartao-campo inteiro">

                                <span>Total</span>

                                <strong class="cartao-valor-os">
                                    R$ <?= number_format(
                                        (float) (
                                            $ordem['valor_total'] ?? 0
                                        ),
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>

                            </div>

                        </div>

                        <div class="cartao-ordem-rodape">

                            <span class="texto-visualizar-cartao">
                                Visualizar ordem →
                            </span>

                        </div>

                    </a>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </section>

</main>

<script>
document.querySelectorAll('.linha-ordem').forEach(
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