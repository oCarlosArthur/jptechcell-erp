<?php

$produtos = $dados['produtos'] ?? [];
$resumo = $dados['resumo'] ?? [];
$topMaisVendidos = $dados['top_mais_vendidos'] ?? [];
$topMaisLucrativos = $dados['top_mais_lucrativos'] ?? [];

$dataInicio = $dados['data_inicio'] ?? ($_GET['data_inicio'] ?? date('Y-m-01'));
$dataFim = $dados['data_fim'] ?? ($_GET['data_fim'] ?? date('Y-m-d'));

function escaparImpressaoProduto(mixed $valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

function moedaImpressaoProduto(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function percentualImpressaoProduto(float $valor): string
{
    return number_format($valor, 2, ',', '.') . '%';
}

function formatarDataImpressaoProduto(string $data): string
{
    $objeto = DateTime::createFromFormat('Y-m-d', $data);

    return $objeto
        ? $objeto->format('d/m/Y')
        : $data;
}

$logoPossiveis = [
    __DIR__ . '/../../../public/assets/img/logo-jptechcell.png',
    __DIR__ . '/../../../public/assets/img/logo.png',
    __DIR__ . '/../../../public/assets/images/logo.png',
    __DIR__ . '/../../../public/assets/img/logo-jptech.png',
    __DIR__ . '/../../../public/assets/images/logo-jptech.png'
];

$logoBase64 = null;

foreach ($logoPossiveis as $logoArquivo) {
    if (!is_file($logoArquivo)) {
        continue;
    }

    $extensao = strtolower(
        pathinfo($logoArquivo, PATHINFO_EXTENSION)
    );

    $tipo = match ($extensao) {
        'jpg', 'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        default => 'image/png'
    };

    $conteudo = file_get_contents($logoArquivo);

    if ($conteudo !== false) {
        $logoBase64 =
            'data:' . $tipo . ';base64,' . base64_encode($conteudo);
    }

    break;
}

$dataEmissao = date('d/m/Y H:i');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Relatório de Produtos</title>

    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            margin: 0;
            background: #eef2f7;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
        }

        .barra-acoes {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            background: #172033;
        }

        .barra-acoes button {
            min-height: 38px;
            padding: 0 18px;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 700;
        }

        .botao-imprimir {
            background: #159ee8;
            color: #ffffff;
        }

        .botao-fechar {
            background: #ffffff;
            color: #172033;
        }

        .pagina {
            width: 210mm;
            min-height: 297mm;
            margin: 18px auto;
            padding: 10mm;
            background: #ffffff;
            box-shadow: 0 7px 28px rgba(15, 23, 42, .12);
        }

        .cabecalho {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #159ee8;
        }

        .marca {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 88px;
            height: 70px;
            overflow: hidden;
            border-radius: 10px;
            background: #f8fafc;
            color: #159ee8;
            font-size: 18px;
            font-weight: 900;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }

        .dados-empresa h1 {
            margin: 0 0 4px;
            font-size: 19px;
        }

        .dados-empresa p {
            margin: 2px 0;
            color: #64748b;
            font-size: 9px;
        }

        .identificacao {
            text-align: right;
        }

        .identificacao span {
            display: block;
            margin-bottom: 4px;
            color: #159ee8;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .identificacao strong {
            display: block;
            font-size: 17px;
        }

        .identificacao small {
            display: block;
            margin-top: 5px;
            color: #64748b;
            font-size: 8px;
        }

        .periodo {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin: 12px 0;
            padding: 9px 11px;
            border: 1px solid #dbe4ee;
            border-radius: 7px;
            background: #f8fafc;
        }

        .periodo span {
            color: #64748b;
        }

        .grade-resumo {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 7px;
            margin-bottom: 12px;
        }

        .card {
            padding: 8px;
            border: 1px solid #dbe4ee;
            border-radius: 7px;
        }

        .card span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .card strong {
            display: block;
            font-size: 13px;
        }

        .card small {
            display: block;
            margin-top: 3px;
            color: #94a3b8;
            font-size: 7px;
        }

        .card.azul {
            border-color: #bae6fd;
            background: #f0f9ff;
        }

        .card.azul strong {
            color: #0284c7;
        }

        .card.verde {
            border-color: #bbf7d0;
            background: #f0fdf4;
        }

        .card.verde strong {
            color: #15803d;
        }

        .card.laranja {
            border-color: #fed7aa;
            background: #fff7ed;
        }

        .card.laranja strong {
            color: #c2410c;
        }

        .grade-rankings {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 9px;
            margin-bottom: 12px;
        }

        .ranking {
            border: 1px solid #dbe4ee;
            border-radius: 7px;
            overflow: hidden;
            break-inside: avoid;
        }

        .ranking h2 {
            margin: 0;
            padding: 7px 9px;
            background: #f1f5f9;
            border-bottom: 1px solid #dbe4ee;
            font-size: 10px;
        }

        .ranking-lista {
            padding: 4px 8px;
        }

        .ranking-item {
            display: grid;
            grid-template-columns: 18px 1fr auto;
            align-items: center;
            gap: 6px;
            padding: 5px 0;
            border-bottom: 1px solid #edf2f7;
        }

        .ranking-item:last-child {
            border-bottom: 0;
        }

        .posicao {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #172033;
            color: #ffffff;
            font-size: 7px;
            font-weight: 900;
        }

        .ranking-item strong {
            display: block;
            font-size: 8px;
        }

        .ranking-item small {
            display: block;
            margin-top: 2px;
            color: #94a3b8;
            font-size: 7px;
        }

        .ranking-valor {
            color: #159ee8;
            font-size: 8px;
            font-weight: 900;
            text-align: right;
        }

        .vazio {
            padding: 14px;
            color: #64748b;
            font-size: 8px;
            text-align: center;
        }

        .titulo-tabela {
            display: flex;
            justify-content: space-between;
            align-items: end;
            margin: 12px 0 6px;
        }

        .titulo-tabela h2 {
            margin: 0;
            font-size: 11px;
        }

        .titulo-tabela span {
            color: #64748b;
            font-size: 8px;
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
            padding: 5px 4px;
            border: 1px solid #dbe4ee;
            vertical-align: middle;
        }

        th {
            background: #172033;
            color: #ffffff;
            font-size: 7px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            color: #263548;
            font-size: 7.6px;
            line-height: 1.25;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .produto {
            width: 21%;
        }

        .codigo {
            width: 11%;
        }

        .numero {
            width: 8%;
            text-align: center;
        }

        .valor {
            width: 11%;
            text-align: right;
        }

        .situacao {
            width: 10%;
            text-align: center;
        }

        .produto strong {
            display: block;
        }

        .produto small {
            display: block;
            margin-top: 2px;
            color: #94a3b8;
            font-size: 6px;
        }

        .positivo {
            color: #15803d;
            font-weight: 800;
        }

        .negativo {
            color: #b91c1c;
            font-weight: 800;
        }

        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 999px;
            font-size: 5.8px;
            font-weight: 800;
            white-space: nowrap;
        }

        .badge.normal {
            background: #dcfce7;
            color: #166534;
        }

        .badge.estoque_baixo {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.sem_estoque {
            background: #fee2e2;
            color: #991b1b;
        }

        .rodape {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 12px;
            padding-top: 7px;
            border-top: 1px solid #dbe4ee;
            color: #94a3b8;
            font-size: 7px;
        }

        @media print {
            body {
                background: #ffffff;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .barra-acoes {
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

        /* Correção para nomes longos na impressão */
        .ranking-item {
            grid-template-columns: 18px minmax(0, 1fr) auto;
            overflow: hidden;
        }

        .ranking-dados {
            min-width: 0;
            overflow: hidden;
        }

        .ranking-dados strong,
        .ranking-dados small {
            display: block;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .ranking-valor {
            min-width: 48px;
            max-width: 82px;
            overflow: hidden;
            white-space: nowrap;
        }

        td.produto {
            overflow: hidden;
        }

        td.produto strong,
        td.produto small {
            display: block;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        @media print {
            .ranking-item,
            td.produto {
                overflow: hidden !important;
            }

            .ranking-dados strong,
            .ranking-dados small,
            td.produto strong,
            td.produto small {
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
        }

    </style>
</head>

<body>

    <div class="barra-acoes">
        <button
            type="button"
            class="botao-imprimir"
            onclick="window.print()"
        >
            Imprimir
        </button>

        <button
            type="button"
            class="botao-fechar"
            onclick="window.close()"
        >
            Fechar
        </button>
    </div>

    <main class="pagina">

        <header class="cabecalho">

            <div class="marca">

                <div class="logo">
                    <?php if ($logoBase64 !== null): ?>
                        <img
                            src="<?= escaparImpressaoProduto($logoBase64) ?>"
                            alt="Logo da empresa"
                        >
                    <?php else: ?>
                        JP
                    <?php endif; ?>
                </div>

                <div class="dados-empresa">
                    <h1>JP TechCell</h1>
                    <p>Sistema de gestão da loja</p>
                    <p>Relatório gerencial de produtos</p>
                </div>

            </div>

            <div class="identificacao">
                <span>Relatório gerencial</span>
                <strong>Produtos</strong>
                <small>
                    Emitido em
                    <?= escaparImpressaoProduto($dataEmissao) ?>
                </small>
            </div>

        </header>

        <section class="periodo">

            <div>
                <strong>Período:</strong>

                <span>
                    <?= escaparImpressaoProduto(
                        formatarDataImpressaoProduto($dataInicio)
                    ) ?>
                    até
                    <?= escaparImpressaoProduto(
                        formatarDataImpressaoProduto($dataFim)
                    ) ?>
                </span>
            </div>

            <div>
                <strong>Produtos ativos:</strong>

                <span>
                    <?= count($produtos) ?>
                </span>
            </div>

        </section>

        <section class="grade-resumo">

            <article class="card">
                <span>Produtos cadastrados</span>
                <strong>
                    <?= (int) ($resumo['total_cadastrados'] ?? 0) ?>
                </strong>
                <small>Produtos ativos</small>
            </article>

            <article class="card">
                <span>Unidades vendidas</span>
                <strong>
                    <?= (int) ($resumo['quantidade_vendida'] ?? 0) ?>
                </strong>
                <small>
                    <?= (int) ($resumo['produtos_vendidos'] ?? 0) ?>
                    produtos diferentes
                </small>
            </article>

            <article class="card azul">
                <span>Faturamento</span>
                <strong>
                    <?= moedaImpressaoProduto(
                        (float) ($resumo['faturamento'] ?? 0)
                    ) ?>
                </strong>
                <small>Total vendido</small>
            </article>

            <article class="card">
                <span>Custo total</span>
                <strong>
                    <?= moedaImpressaoProduto(
                        (float) ($resumo['custo_total'] ?? 0)
                    ) ?>
                </strong>
                <small>Custo dos produtos</small>
            </article>

            <article class="card verde">
                <span>Lucro</span>
                <strong>
                    <?= moedaImpressaoProduto(
                        (float) ($resumo['lucro'] ?? 0)
                    ) ?>
                </strong>
                <small>
                    Margem:
                    <?= percentualImpressaoProduto(
                        (float) ($resumo['margem_lucro'] ?? 0)
                    ) ?>
                </small>
            </article>

            <article class="card laranja">
                <span>Estoque baixo</span>
                <strong>
                    <?= (int) ($resumo['produtos_estoque_baixo'] ?? 0) ?>
                </strong>
                <small>
                    Sem estoque:
                    <?= (int) ($resumo['produtos_sem_estoque'] ?? 0) ?>
                </small>
            </article>

            <article class="card">
                <span>Estoque pelo custo</span>
                <strong>
                    <?= moedaImpressaoProduto(
                        (float) ($resumo['valor_estoque_custo'] ?? 0)
                    ) ?>
                </strong>
                <small>Capital investido</small>
            </article>

            <article class="card">
                <span>Estoque pela venda</span>
                <strong>
                    <?= moedaImpressaoProduto(
                        (float) ($resumo['valor_estoque_venda'] ?? 0)
                    ) ?>
                </strong>
                <small>Potencial de faturamento</small>
            </article>

        </section>

        <section class="grade-rankings">

            <article class="ranking">
                <h2>Top 5 mais vendidos</h2>

                <?php if (empty($topMaisVendidos)): ?>

                    <div class="vazio">
                        Nenhuma venda registrada.
                    </div>

                <?php else: ?>

                    <div class="ranking-lista">

                        <?php foreach (
                            array_slice($topMaisVendidos, 0, 5)
                            as $indice => $produto
                        ): ?>

                            <div class="ranking-item">

                                <span class="posicao">
                                    <?= $indice + 1 ?>
                                </span>

                                <div class="ranking-dados">
                                    <strong
                                        title="<?= escaparImpressaoProduto(
                                            $produto['nome'] ?? ''
                                        ) ?>"
                                    >
                                        <?= escaparImpressaoProduto(
                                            $produto['nome'] ?? ''
                                        ) ?>
                                    </strong>

                                    <small
                                        title="<?= escaparImpressaoProduto(
                                            $produto['marca']
                                            ?? 'Não informada'
                                        ) ?>"
                                    >
                                        <?= escaparImpressaoProduto(
                                            $produto['marca']
                                            ?? 'Não informada'
                                        ) ?>
                                    </small>
                                </div>

                                <div class="ranking-valor">
                                    <?= (int) (
                                        $produto['quantidade_vendida']
                                        ?? 0
                                    ) ?>
                                    un.
                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>
            </article>

            <article class="ranking">
                <h2>Top 5 mais lucrativos</h2>

                <?php if (empty($topMaisLucrativos)): ?>

                    <div class="vazio">
                        Nenhum lucro registrado.
                    </div>

                <?php else: ?>

                    <div class="ranking-lista">

                        <?php foreach (
                            array_slice($topMaisLucrativos, 0, 5)
                            as $indice => $produto
                        ): ?>

                            <div class="ranking-item">

                                <span class="posicao">
                                    <?= $indice + 1 ?>
                                </span>

                                <div class="ranking-dados">
                                    <strong
                                        title="<?= escaparImpressaoProduto(
                                            $produto['nome'] ?? ''
                                        ) ?>"
                                    >
                                        <?= escaparImpressaoProduto(
                                            $produto['nome'] ?? ''
                                        ) ?>
                                    </strong>

                                    <small>
                                        <?= percentualImpressaoProduto(
                                            (float) (
                                                $produto['margem_lucro']
                                                ?? 0
                                            )
                                        ) ?>
                                    </small>
                                </div>

                                <div class="ranking-valor">
                                    <?= moedaImpressaoProduto(
                                        (float) ($produto['lucro'] ?? 0)
                                    ) ?>
                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>
            </article>

        </section>

        <div class="titulo-tabela">
            <h2>Desempenho completo dos produtos</h2>

            <span>
                <?= count($produtos) ?> produtos
            </span>
        </div>

        <?php if (empty($produtos)): ?>

            <div class="vazio">
                Nenhum produto encontrado.
            </div>

        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th class="produto">Produto</th>
                        <th class="codigo">Código</th>
                        <th class="numero">Estoque</th>
                        <th class="numero">Vendidos</th>
                        <th class="valor">Faturamento</th>
                        <th class="valor">Custo</th>
                        <th class="valor">Lucro</th>
                        <th class="numero">Margem</th>
                        <th class="situacao">Situação</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($produtos as $produto): ?>

                        <tr>
                            <td
                                class="produto"
                                title="<?= escaparImpressaoProduto(
                                    $produto['nome'] ?? ''
                                ) ?>"
                            >
                                <strong>
                                    <?= escaparImpressaoProduto(
                                        $produto['nome'] ?? ''
                                    ) ?>
                                </strong>

                                <small
                                    title="<?= escaparImpressaoProduto(
                                        $produto['marca']
                                        ?? 'Não informada'
                                    ) ?>"
                                >
                                    <?= escaparImpressaoProduto(
                                        $produto['marca']
                                        ?? 'Não informada'
                                    ) ?>
                                </small>
                            </td>

                            <td class="codigo">
                                <?= escaparImpressaoProduto(
                                    $produto['codigo_barras'] ?? '-'
                                ) ?>
                            </td>

                            <td class="numero">
                                <?= (int) (
                                    $produto['estoque_atual'] ?? 0
                                ) ?>
                            </td>

                            <td class="numero">
                                <?= (int) (
                                    $produto['quantidade_vendida'] ?? 0
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoProduto(
                                    (float) (
                                        $produto['faturamento'] ?? 0
                                    )
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoProduto(
                                    (float) (
                                        $produto['custo_total'] ?? 0
                                    )
                                ) ?>
                            </td>

                            <td
                                class="valor <?= (
                                    (float) ($produto['lucro'] ?? 0)
                                ) >= 0 ? 'positivo' : 'negativo' ?>"
                            >
                                <?= moedaImpressaoProduto(
                                    (float) ($produto['lucro'] ?? 0)
                                ) ?>
                            </td>

                            <td class="numero">
                                <?= percentualImpressaoProduto(
                                    (float) (
                                        $produto['margem_lucro'] ?? 0
                                    )
                                ) ?>
                            </td>

                            <td class="situacao">
                                <span
                                    class="badge <?= escaparImpressaoProduto(
                                        $produto['situacao_estoque']
                                        ?? 'normal'
                                    ) ?>"
                                >
                                    <?= escaparImpressaoProduto(
                                        $produto['situacao_estoque_nome']
                                        ?? 'Normal'
                                    ) ?>
                                </span>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>

        <?php endif; ?>

        <footer class="rodape">
            <span>JP TechCell — Relatório de Produtos</span>

            <span>
                Período:
                <?= escaparImpressaoProduto(
                    formatarDataImpressaoProduto($dataInicio)
                ) ?>
                a
                <?= escaparImpressaoProduto(
                    formatarDataImpressaoProduto($dataFim)
                ) ?>
            </span>
        </footer>

    </main>

</body>
</html>