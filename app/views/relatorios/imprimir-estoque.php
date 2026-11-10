<?php

$produtos = $dados['produtos'] ?? [];
$resumo = $dados['resumo'] ?? [];

function escaparImpressaoEstoque(mixed $valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

function moedaImpressaoEstoque(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
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

    <title>Relatório de Estoque</title>

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
            font-size: 9px;
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
            gap: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #159ee8;
        }

        .marca {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .logo {
            width: 88px;
            height: 66px;
            object-fit: contain;
        }

        .empresa h1 {
            margin: 0 0 4px;
            font-size: 19px;
        }

        .empresa p {
            margin: 2px 0;
            color: #64748b;
            font-size: 8px;
        }

        .identificacao {
            text-align: right;
        }

        .identificacao span {
            display: block;
            color: #159ee8;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .identificacao strong {
            display: block;
            margin-top: 3px;
            font-size: 17px;
        }

        .identificacao small {
            display: block;
            margin-top: 4px;
            color: #64748b;
        }

        .resumo {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
            margin: 10px 0;
        }

        .card {
            padding: 7px;
            border: 1px solid #dbe4ee;
            border-radius: 6px;
        }

        .card span {
            display: block;
            margin-bottom: 4px;
            color: #64748b;
            font-size: 6.5px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .card strong {
            font-size: 11px;
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
            font-size: 6.7px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            color: #334155;
            font-size: 7.2px;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .produto {
            width: 17%;
        }

        .codigo {
            width: 10%;
        }

        .numero {
            width: 7%;
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
            font-size: 6.4px;
        }

        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 999px;
            font-size: 6px;
            font-weight: 800;
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
            margin-top: 9px;
            padding-top: 6px;
            border-top: 1px solid #dbe4ee;
            color: #94a3b8;
            font-size: 7px;
        }


        /* Correção para nomes longos no relatório impresso */
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

        td.codigo,
        td.valor,
        td.numero,
        td.situacao {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .badge {
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        @media print {
            td.produto,
            td.codigo,
            td.valor,
            td.numero,
            td.situacao {
                overflow: hidden !important;
            }

            td.produto strong,
            td.produto small {
                overflow: hidden !important;
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
    </style>
</head>

<body>

    <div class="barra-acoes">
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
                        src="<?= escaparImpressaoEstoque($logoBase64) ?>"
                        alt="Logo JP TechCell"
                        class="logo"
                    >
                <?php endif; ?>

                <div class="empresa">
                    <h1>JP TechCell</h1>
                    <p>Sistema de gestão da loja</p>
                    <p>Posição atual do estoque</p>
                </div>
            </div>

            <div class="identificacao">
                <span>Relatório gerencial</span>
                <strong>Estoque</strong>
                <small>
                    Emitido em
                    <?= escaparImpressaoEstoque($dataEmissao) ?>
                </small>
            </div>

        </header>

        <section class="resumo">

            <article class="card">
                <span>Produtos</span>
                <strong>
                    <?= (int) ($resumo['total_produtos'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Unidades</span>
                <strong>
                    <?= (int) ($resumo['total_unidades'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Estoque baixo</span>
                <strong>
                    <?= (int) (
                        $resumo['produtos_estoque_baixo']
                        ?? 0
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Sem estoque</span>
                <strong>
                    <?= (int) (
                        $resumo['produtos_sem_estoque']
                        ?? 0
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Valor pelo custo</span>
                <strong>
                    <?= moedaImpressaoEstoque(
                        (float) (
                            $resumo['valor_estoque_custo']
                            ?? 0
                        )
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Valor pela venda</span>
                <strong>
                    <?= moedaImpressaoEstoque(
                        (float) (
                            $resumo['valor_estoque_venda']
                            ?? 0
                        )
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Lucro potencial</span>
                <strong>
                    <?= moedaImpressaoEstoque(
                        (float) (
                            $resumo['lucro_potencial']
                            ?? 0
                        )
                    ) ?>
                </strong>
            </article>

        </section>

        <?php if (empty($produtos)): ?>

            <p>Nenhum produto encontrado.</p>

        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th class="produto">Produto</th>
                        <th class="codigo">Código</th>
                        <th class="numero">Estoque</th>
                        <th class="numero">Mínimo</th>
                        <th class="valor">Custo unitário</th>
                        <th class="valor">Venda unitária</th>
                        <th class="valor">Valor em custo</th>
                        <th class="valor">Valor em venda</th>
                        <th class="valor">Lucro potencial</th>
                        <th class="situacao">Situação</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td
                                class="produto"
                                title="<?= escaparImpressaoEstoque(
                                    $produto['nome'] ?? ''
                                ) ?>"
                            >
                                <strong>
                                    <?= escaparImpressaoEstoque(
                                        $produto['nome'] ?? ''
                                    ) ?>
                                </strong>

                                <small
                                    title="<?= escaparImpressaoEstoque(
                                        $produto['marca']
                                        ?? 'Não informada'
                                    ) ?>"
                                >
                                    <?= escaparImpressaoEstoque(
                                        $produto['marca']
                                        ?? 'Não informada'
                                    ) ?>
                                </small>
                            </td>

                            <td class="codigo">
                                <?= escaparImpressaoEstoque(
                                    $produto['codigo_etiqueta']
                                    ?: (
                                        $produto['codigo_barras']
                                        ?? '-'
                                    )
                                ) ?>
                            </td>

                            <td class="numero">
                                <?= (int) (
                                    $produto['quantidade_estoque']
                                    ?? 0
                                ) ?>
                            </td>

                            <td class="numero">
                                <?= (int) (
                                    $produto['estoque_minimo']
                                    ?? 0
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoEstoque(
                                    (float) (
                                        $produto['preco_custo']
                                        ?? 0
                                    )
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoEstoque(
                                    (float) (
                                        $produto['preco_venda']
                                        ?? 0
                                    )
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoEstoque(
                                    (float) (
                                        $produto['valor_estoque_custo']
                                        ?? 0
                                    )
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoEstoque(
                                    (float) (
                                        $produto['valor_estoque_venda']
                                        ?? 0
                                    )
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoEstoque(
                                    (float) (
                                        $produto['lucro_potencial']
                                        ?? 0
                                    )
                                ) ?>
                            </td>

                            <td class="situacao">
                                <span class="badge <?= escaparImpressaoEstoque(
                                    $produto['situacao_estoque']
                                    ?? 'normal'
                                ) ?>">
                                    <?= escaparImpressaoEstoque(
                                        $produto[
                                            'situacao_estoque_nome'
                                        ]
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
            <span>JP TechCell — Relatório de Estoque</span>
            <span><?= count($produtos) ?> produtos ativos</span>
        </footer>

    </main>

</body>
</html>