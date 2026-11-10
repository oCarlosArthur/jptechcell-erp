<?php

$resumo = $dados['resumo'] ?? [];
$formasPagamento = $dados['formas_pagamento'] ?? [];
$produtosVendidos = $dados['produtos_vendidos'] ?? [];
$ordensServico = $dados['ordens_servico'] ?? [];
$gastos = $dados['gastos'] ?? [];
$categoriasGastos = $dados['categorias_gastos'] ?? [];

$mes = (int) ($dados['mes'] ?? date('m'));
$ano = (int) ($dados['ano'] ?? date('Y'));

$nomesMeses = [
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

function escaparImpressaoFinanceiro(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function moedaImpressaoFinanceiro(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function dataImpressaoFinanceiro(?string $data): string
{
    return empty($data)
        ? '-'
        : date('d/m/Y', strtotime($data));
}

function formaImpressaoFinanceiro(string $forma): string
{
    $nomes = [
        'dinheiro' => 'Dinheiro',
        'pix' => 'Pix',
        'cartao_credito' => 'Cartão de crédito',
        'cartao_debito' => 'Cartão de débito',
        'credito' => 'Cartão de crédito',
        'debito' => 'Cartão de débito',
        'boleto' => 'Boleto',
        'transferencia' => 'Transferência'
    ];

    return $nomes[$forma]
        ?? ucfirst(str_replace('_', ' ', $forma));
}

$logoArquivo = __DIR__
    . '/../../../public/assets/img/logo-jptechcell.png';

$logoBase64 = null;

if (is_file($logoArquivo)) {
    $conteudoLogo = file_get_contents($logoArquivo);

    if ($conteudoLogo !== false) {
        $logoBase64 =
            'data:image/png;base64,'
            . base64_encode($conteudoLogo);
    }
}

$resultadoMes =
    (float) ($resumo['resultado_mes'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Relatório Financeiro</title>

    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 9mm;
        }

        body {
            margin: 0;
            background: #eef2f7;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
        }

        .acoes {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            background: #172033;
        }

        .acoes button {
            min-height: 38px;
            padding: 0 18px;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 700;
        }

        .imprimir {
            background: #159ee8;
            color: #ffffff;
        }

        .fechar {
            background: #ffffff;
            color: #172033;
        }

        .pagina {
            width: 297mm;
            min-height: 210mm;
            margin: 16px auto;
            padding: 9mm;
            background: #ffffff;
            box-shadow: 0 7px 28px rgba(15, 23, 42, .12);
        }

        .cabecalho {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 9px;
            border-bottom: 3px solid #159ee8;
        }

        .marca {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 88px;
            height: 64px;
            object-fit: contain;
        }

        .empresa h1 {
            margin: 0 0 3px;
            font-size: 19px;
        }

        .empresa p {
            margin: 2px 0;
            color: #64748b;
        }

        .identificacao {
            text-align: right;
        }

        .identificacao span {
            display: block;
            color: #159ee8;
            font-weight: 900;
            text-transform: uppercase;
        }

        .identificacao strong {
            display: block;
            margin-top: 3px;
            font-size: 17px;
        }

        .periodo {
            margin: 8px 0;
            padding: 7px 9px;
            border: 1px solid #dbe4ee;
            border-radius: 6px;
            background: #f8fafc;
        }

        .resumo {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 5px;
            margin-bottom: 8px;
        }

        .card {
            padding: 6px;
            border: 1px solid #dbe4ee;
            border-radius: 6px;
        }

        .card span {
            display: block;
            margin-bottom: 3px;
            color: #64748b;
            font-size: 6px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .card strong {
            font-size: 10px;
        }

        .resultado {
            border-color: #bbf7d0;
            background: #f0fdf4;
        }

        .resultado.negativo {
            border-color: #fecaca;
            background: #fef2f2;
        }

        .secao {
            margin-top: 9px;
        }

        .secao h2 {
            margin: 0 0 5px;
            font-size: 11px;
        }

        .dupla {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        tr {
            break-inside: avoid;
        }

        th,
        td {
            padding: 4px;
            border: 1px solid #dbe4ee;
            vertical-align: middle;
        }

        th {
            background: #172033;
            color: #ffffff;
            font-size: 6px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            color: #334155;
            font-size: 6.5px;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .valor {
            text-align: right;
        }

        .positivo {
            color: #15803d;
            font-weight: 800;
        }

        .negativo {
            color: #b91c1c;
            font-weight: 800;
        }

        .rodape {
            display: flex;
            justify-content: space-between;
            margin-top: 9px;
            padding-top: 6px;
            border-top: 1px solid #dbe4ee;
            color: #94a3b8;
            font-size: 6.5px;
        }


        /* Proteção contra textos longos nas tabelas e cartões */
        .card {
            min-width: 0;
            overflow: hidden;
        }

        .card span,
        .card strong {
            display: block;
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        th,
        td {
            overflow: hidden;
        }

        .texto-limitado {
            max-width: 0;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .dupla table th:first-child,
        .dupla table td:first-child {
            width: 65%;
        }

        .dupla table th:last-child,
        .dupla table td:last-child {
            width: 35%;
        }

        .secao table td,
        .secao table th {
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        @media print {
            .card,
            th,
            td,
            .texto-limitado {
                overflow: hidden !important;
            }

            .card span,
            .card strong,
            .texto-limitado {
                white-space: nowrap !important;
                text-overflow: ellipsis !important;
            }
        }

        @media print {
            body {
                background: #ffffff;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .acoes {
                display: none;
            }

            .pagina {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

    <div class="acoes">
        <button
            type="button"
            class="imprimir"
            onclick="window.print()"
        >
            Imprimir
        </button>

        <button
            type="button"
            class="fechar"
            onclick="window.close()"
        >
            Fechar
        </button>
    </div>

    <main class="pagina">

        <header class="cabecalho">
            <div class="marca">
                <?php if ($logoBase64 !== null): ?>
                    <img
                        src="<?= escaparImpressaoFinanceiro($logoBase64) ?>"
                        alt="Logo JP TechCell"
                        class="logo"
                    >
                <?php endif; ?>

                <div class="empresa">
                    <h1>JP TechCell</h1>
                    <p>Sistema de gestão da loja</p>
                    <p>Relatório gerencial financeiro</p>
                </div>
            </div>

            <div class="identificacao">
                <span>Relatório gerencial</span>
                <strong>Financeiro</strong>
                <small>
                    Emitido em <?= date('d/m/Y H:i') ?>
                </small>
            </div>
        </header>

        <section class="periodo">
            <strong>Período:</strong>
            <?= escaparImpressaoFinanceiro(
                ($nomesMeses[$mes] ?? '')
                . ' de '
                . $ano
            ) ?>
        </section>

        <section class="resumo">
            <article class="card">
                <span>Total recebido</span>
                <strong>
                    <?= moedaImpressaoFinanceiro(
                        (float) ($resumo['total_recebido'] ?? 0)
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Venda de produtos</span>
                <strong>
                    <?= moedaImpressaoFinanceiro(
                        (float) ($resumo['total_produtos'] ?? 0)
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Custo dos produtos</span>
                <strong>
                    <?= moedaImpressaoFinanceiro(
                        (float) ($resumo['custo_produtos'] ?? 0)
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Lucro dos produtos</span>
                <strong>
                    <?= moedaImpressaoFinanceiro(
                        (float) ($resumo['lucro_produtos'] ?? 0)
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Receita de OS</span>
                <strong>
                    <?= moedaImpressaoFinanceiro(
                        (float) (
                            $resumo['total_ordens_servico']
                            ?? 0
                        )
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Gastos</span>
                <strong class="negativo">
                    <?= moedaImpressaoFinanceiro(
                        (float) ($resumo['total_gastos'] ?? 0)
                    ) ?>
                </strong>
            </article>

            <article class="card resultado <?= $resultadoMes < 0 ? 'negativo' : '' ?>">
                <span>Resultado do mês</span>
                <strong>
                    <?= moedaImpressaoFinanceiro($resultadoMes) ?>
                </strong>
            </article>

            <article class="card">
                <span>Ticket médio</span>
                <strong>
                    <?= moedaImpressaoFinanceiro(
                        (float) ($resumo['ticket_medio'] ?? 0)
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Vendas concluídas</span>
                <strong>
                    <?= (int) ($resumo['quantidade_vendas'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Itens vendidos</span>
                <strong>
                    <?= (int) ($resumo['quantidade_itens'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Ordens de serviço</span>
                <strong>
                    <?= (int) (
                        $resumo['quantidade_ordens_servico']
                        ?? 0
                    ) ?>
                </strong>
            </article>
        </section>

        <section class="dupla">
            <div class="secao">
                <h2>Formas de pagamento</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Forma</th>
                            <th class="valor">Valor</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($formasPagamento)): ?>
                            <tr>
                                <td colspan="2">
                                    Nenhum pagamento encontrado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($formasPagamento as $forma => $valor): ?>
                                <tr>
                                    <td>
                                        <?= escaparImpressaoFinanceiro(
                                            formaImpressaoFinanceiro(
                                                (string) $forma
                                            )
                                        ) ?>
                                    </td>

                                    <td class="valor">
                                        <?= moedaImpressaoFinanceiro(
                                            (float) $valor
                                        ) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="secao">
                <h2>Gastos por categoria</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Quantidade</th>
                            <th class="valor">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($categoriasGastos)): ?>
                            <tr>
                                <td colspan="3">
                                    Nenhum gasto cadastrado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categoriasGastos as $categoria): ?>
                                <tr>
                                    <td>
                                        <?= escaparImpressaoFinanceiro(
                                            $categoria['categoria']
                                            ?? ''
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= (int) (
                                            $categoria['quantidade']
                                            ?? 0
                                        ) ?>
                                    </td>

                                    <td class="valor negativo">
                                        <?= moedaImpressaoFinanceiro(
                                            (float) (
                                                $categoria['valor_total']
                                                ?? 0
                                            )
                                        ) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="secao">
            <h2>Produtos vendidos</h2>

            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Marca</th>
                        <th>Quantidade</th>
                        <th class="valor">Venda líquida</th>
                        <th class="valor">Custo</th>
                        <th class="valor">Lucro</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($produtosVendidos)): ?>
                        <tr>
                            <td colspan="6">
                                Nenhum produto vendido.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produtosVendidos as $produto): ?>
                            <tr>
                                <td
                                    class="texto-limitado"
                                    title="<?= escaparImpressaoFinanceiro(
                                        $produto['produto_nome']
                                        ?? ''
                                    ) ?>"
                                >
                                    <?= escaparImpressaoFinanceiro(
                                        $produto['produto_nome']
                                        ?? ''
                                    ) ?>
                                </td>

                                <td
                                    class="texto-limitado"
                                    title="<?= escaparImpressaoFinanceiro(
                                        $produto['produto_marca']
                                        ?: '-'
                                    ) ?>"
                                >
                                    <?= escaparImpressaoFinanceiro(
                                        $produto['produto_marca']
                                        ?: '-'
                                    ) ?>
                                </td>

                                <td>
                                    <?= (int) (
                                        $produto['quantidade']
                                        ?? 0
                                    ) ?>
                                </td>

                                <td class="valor">
                                    <?= moedaImpressaoFinanceiro(
                                        (float) (
                                            $produto['total_venda']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td class="valor">
                                    <?= moedaImpressaoFinanceiro(
                                        (float) (
                                            $produto['total_custo']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td class="valor positivo">
                                    <?= moedaImpressaoFinanceiro(
                                        (float) (
                                            $produto['lucro']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="secao">
            <h2>Ordens de serviço recebidas</h2>

            <table>
                <thead>
                    <tr>
                        <th>OS</th>
                        <th>Venda</th>
                        <th>Cliente</th>
                        <th>Aparelho</th>
                        <th>Pagamento</th>
                        <th>Data</th>
                        <th class="valor">Recebido</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($ordensServico)): ?>
                        <tr>
                            <td colspan="7">
                                Nenhuma OS recebida.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ordensServico as $ordem): ?>
                            <tr>
                                <td>
                                    <?= escaparImpressaoFinanceiro(
                                        $ordem['numero_os'] ?? ''
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparImpressaoFinanceiro(
                                        $ordem['numero_venda'] ?? ''
                                    ) ?>
                                </td>

                                <td
                                    class="texto-limitado"
                                    title="<?= escaparImpressaoFinanceiro(
                                        $ordem['cliente_nome'] ?? ''
                                    ) ?>"
                                >
                                    <?= escaparImpressaoFinanceiro(
                                        $ordem['cliente_nome'] ?? ''
                                    ) ?>
                                </td>

                                <td
                                    class="texto-limitado"
                                    title="<?= escaparImpressaoFinanceiro(
                                        $ordem['aparelho'] ?? ''
                                    ) ?>"
                                >
                                    <?= escaparImpressaoFinanceiro(
                                        $ordem['aparelho'] ?? ''
                                    ) ?>
                                </td>

                                <td
                                    class="texto-limitado"
                                    title="<?= escaparImpressaoFinanceiro(
                                        formaImpressaoFinanceiro(
                                            (string) (
                                                $ordem['forma_pagamento']
                                                ?? ''
                                            )
                                        )
                                    ) ?>"
                                >
                                    <?= escaparImpressaoFinanceiro(
                                        formaImpressaoFinanceiro(
                                            (string) (
                                                $ordem['forma_pagamento']
                                                ?? ''
                                            )
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparImpressaoFinanceiro(
                                        dataImpressaoFinanceiro(
                                            $ordem['data_venda']
                                            ?? null
                                        )
                                    ) ?>
                                </td>

                                <td class="valor positivo">
                                    <?= moedaImpressaoFinanceiro(
                                        (float) (
                                            $ordem['valor_recebido']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="secao">
            <h2>Gastos do mês</h2>

            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Observações</th>
                        <th class="valor">Valor</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($gastos)): ?>
                        <tr>
                            <td colspan="5">
                                Nenhum gasto cadastrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($gastos as $gasto): ?>
                            <tr>
                                <td>
                                    <?= escaparImpressaoFinanceiro(
                                        dataImpressaoFinanceiro(
                                            $gasto['data_gasto']
                                            ?? null
                                        )
                                    ) ?>
                                </td>

                                <td
                                    class="texto-limitado"
                                    title="<?= escaparImpressaoFinanceiro(
                                        $gasto['descricao'] ?? ''
                                    ) ?>"
                                >
                                    <?= escaparImpressaoFinanceiro(
                                        $gasto['descricao'] ?? ''
                                    ) ?>
                                </td>

                                <td
                                    class="texto-limitado"
                                    title="<?= escaparImpressaoFinanceiro(
                                        $gasto['categoria'] ?? ''
                                    ) ?>"
                                >
                                    <?= escaparImpressaoFinanceiro(
                                        $gasto['categoria'] ?? ''
                                    ) ?>
                                </td>

                                <td
                                    class="texto-limitado"
                                    title="<?= escaparImpressaoFinanceiro(
                                        $gasto['observacoes']
                                        ?: '-'
                                    ) ?>"
                                >
                                    <?= escaparImpressaoFinanceiro(
                                        $gasto['observacoes']
                                        ?: '-'
                                    ) ?>
                                </td>

                                <td class="valor negativo">
                                    <?= moedaImpressaoFinanceiro(
                                        (float) (
                                            $gasto['valor']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <footer class="rodape">
            <span>
                JP TechCell — Relatório Financeiro
            </span>

            <span>
                <?= escaparImpressaoFinanceiro(
                    ($nomesMeses[$mes] ?? '')
                    . '/'
                    . $ano
                ) ?>
            </span>
        </footer>

    </main>

</body>
</html>