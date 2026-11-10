<?php

$ordens = $dados['ordens'] ?? [];
$resumo = $dados['resumo'] ?? [];

$dataInicio = $dados['data_inicio']
    ?? ($_GET['data_inicio'] ?? date('Y-m-01'));

$dataFim = $dados['data_fim']
    ?? ($_GET['data_fim'] ?? date('Y-m-d'));

function escaparImpressaoOs(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function moedaImpressaoOs(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function dataImpressaoOs(?string $data): string
{
    return empty($data)
        ? '-'
        : date('d/m/Y H:i', strtotime($data));
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

    <title>Relatório de Ordens de Serviço</title>

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

        .periodo {
            margin: 9px 0;
            padding: 7px 9px;
            border: 1px solid #dbe4ee;
            border-radius: 6px;
            background: #f8fafc;
        }

        .resumo {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 6px;
            margin-bottom: 9px;
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
            font-size: 6.5px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            color: #334155;
            font-size: 7px;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .os {
            width: 8%;
        }

        .cliente {
            width: 14%;
        }

        .aparelho {
            width: 13%;
        }

        .defeito {
            width: 20%;
        }

        .data {
            width: 11%;
        }

        .valor {
            width: 9%;
            text-align: right;
        }

        .status {
            width: 11%;
            text-align: center;
        }

        .cliente strong {
            display: block;
        }

        .cliente small {
            display: block;
            margin-top: 2px;
            color: #94a3b8;
            font-size: 6.2px;
        }

        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #475569;
            font-size: 5.8px;
            font-weight: 800;
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
                        src="<?= escaparImpressaoOs($logoBase64) ?>"
                        alt="Logo JP TechCell"
                        class="logo"
                    >
                <?php endif; ?>

                <div class="empresa">
                    <h1>JP TechCell</h1>
                    <p>Sistema de gestão da loja</p>
                    <p>Relatório gerencial de ordens de serviço</p>
                </div>
            </div>

            <div class="identificacao">
                <span>Relatório gerencial</span>
                <strong>Ordens de Serviço</strong>
                <small>
                    Emitido em
                    <?= escaparImpressaoOs($dataEmissao) ?>
                </small>
            </div>

        </header>

        <section class="periodo">
            <strong>Período de entrada:</strong>

            <?= escaparImpressaoOs(
                date('d/m/Y', strtotime($dataInicio))
            ) ?>

            até

            <?= escaparImpressaoOs(
                date('d/m/Y', strtotime($dataFim))
            ) ?>
        </section>

        <section class="resumo">

            <article class="card">
                <span>Total de OS</span>
                <strong>
                    <?= (int) ($resumo['total_ordens'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Em aberto</span>
                <strong>
                    <?= (int) ($resumo['ordens_em_aberto'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Finalizadas</span>
                <strong>
                    <?= (int) ($resumo['ordens_finalizadas'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Valor bruto</span>
                <strong>
                    <?= moedaImpressaoOs(
                        (float) ($resumo['valor_bruto'] ?? 0)
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Descontos</span>
                <strong>
                    <?= moedaImpressaoOs(
                        (float) ($resumo['descontos'] ?? 0)
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Valor total</span>
                <strong>
                    <?= moedaImpressaoOs(
                        (float) ($resumo['valor_total'] ?? 0)
                    ) ?>
                </strong>
            </article>

        </section>

        <?php if (empty($ordens)): ?>

            <p>Nenhuma ordem encontrada.</p>

        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th class="os">OS</th>
                        <th class="cliente">Cliente</th>
                        <th class="aparelho">Aparelho</th>
                        <th class="defeito">Defeito</th>
                        <th class="data">Entrada</th>
                        <th class="data">Prevista</th>
                        <th class="valor">Valor</th>
                        <th class="valor">Desconto</th>
                        <th class="valor">Total</th>
                        <th class="status">Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($ordens as $ordem): ?>
                        <tr>
                            <td class="os">
                                <strong>
                                    <?= escaparImpressaoOs(
                                        $ordem['numero_os'] ?? ''
                                    ) ?>
                                </strong>
                            </td>

                            <td class="cliente">
                                <strong>
                                    <?= escaparImpressaoOs(
                                        $ordem['cliente_nome'] ?? ''
                                    ) ?>
                                </strong>

                                <small>
                                    <?= escaparImpressaoOs(
                                        $ordem['cliente_whatsapp']
                                        ?: (
                                            $ordem['cliente_telefone']
                                            ?: '-'
                                        )
                                    ) ?>
                                </small>
                            </td>

                            <td class="aparelho">
                                <?= escaparImpressaoOs(
                                    $ordem['aparelho']
                                    ?? 'Não informado'
                                ) ?>
                            </td>

                            <td class="defeito">
                                <?= escaparImpressaoOs(
                                    $ordem['defeito_relatado']
                                    ?? '-'
                                ) ?>
                            </td>

                            <td class="data">
                                <?= escaparImpressaoOs(
                                    dataImpressaoOs(
                                        $ordem['data_entrada']
                                        ?? null
                                    )
                                ) ?>
                            </td>

                            <td class="data">
                                <?= escaparImpressaoOs(
                                    dataImpressaoOs(
                                        $ordem['data_prevista']
                                        ?? null
                                    )
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoOs(
                                    (float) ($ordem['valor_os'] ?? 0)
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoOs(
                                    (float) ($ordem['desconto'] ?? 0)
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoOs(
                                    (float) ($ordem['valor_total'] ?? 0)
                                ) ?>
                            </td>

                            <td class="status">
                                <span class="badge">
                                    <?= escaparImpressaoOs(
                                        $ordem['status_nome'] ?? ''
                                    ) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>

        <footer class="rodape">
            <span>
                JP TechCell — Relatório de Ordens de Serviço
            </span>

            <span>
                <?= count($ordens) ?> ordens no período
            </span>
        </footer>

    </main>

</body>
</html>