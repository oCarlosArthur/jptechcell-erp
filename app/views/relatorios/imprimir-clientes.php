<?php

$clientes = $dados['clientes'] ?? [];
$resumo = $dados['resumo'] ?? [];
$maisCompraram = $dados['mais_compraram'] ?? [];

$dataInicio = $dados['data_inicio']
    ?? ($_GET['data_inicio'] ?? date('Y-m-01'));

$dataFim = $dados['data_fim']
    ?? ($_GET['data_fim'] ?? date('Y-m-d'));

function escaparImpressaoClientes(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function moedaImpressaoClientes(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function dataImpressaoClientes(?string $data): string
{
    return empty($data)
        ? 'Sem compra'
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

    <title>Relatório de Clientes</title>

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

        .ranking {
            margin-bottom: 9px;
            padding: 7px;
            border: 1px solid #dbe4ee;
            border-radius: 6px;
        }

        .ranking h2 {
            margin: 0 0 6px;
            font-size: 10px;
        }

        .ranking-lista {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
        }

        .ranking-item {
            padding: 6px;
            border-radius: 5px;
            background: #f8fafc;
        }

        .ranking-item strong {
            display: block;
            font-size: 7.5px;
        }

        .ranking-item small {
            display: block;
            margin-top: 2px;
            color: #64748b;
            font-size: 6.5px;
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

        .cliente {
            width: 18%;
        }

        .documento {
            width: 12%;
        }

        .contato {
            width: 13%;
        }

        .localidade {
            width: 12%;
        }

        .numero {
            width: 8%;
            text-align: center;
        }

        .valor {
            width: 11%;
            text-align: right;
        }

        .data {
            width: 13%;
        }

        .cliente strong {
            display: block;
        }

        .cliente small {
            display: block;
            margin-top: 2px;
            color: #94a3b8;
            font-size: 6.4px;
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
                        src="<?= escaparImpressaoClientes($logoBase64) ?>"
                        alt="Logo JP TechCell"
                        class="logo"
                    >
                <?php endif; ?>

                <div class="empresa">
                    <h1>JP TechCell</h1>
                    <p>Sistema de gestão da loja</p>
                    <p>Relatório gerencial de clientes</p>
                </div>
            </div>

            <div class="identificacao">
                <span>Relatório gerencial</span>
                <strong>Clientes</strong>
                <small>
                    Emitido em
                    <?= escaparImpressaoClientes($dataEmissao) ?>
                </small>
            </div>

        </header>

        <section class="periodo">
            <strong>Período:</strong>

            <?= escaparImpressaoClientes(
                date('d/m/Y', strtotime($dataInicio))
            ) ?>

            até

            <?= escaparImpressaoClientes(
                date('d/m/Y', strtotime($dataFim))
            ) ?>
        </section>

        <section class="resumo">

            <article class="card">
                <span>Clientes ativos</span>
                <strong>
                    <?= (int) ($resumo['total_clientes'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Com compra</span>
                <strong>
                    <?= (int) ($resumo['clientes_com_compra'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Sem compra</span>
                <strong>
                    <?= (int) ($resumo['clientes_sem_compra'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Compras</span>
                <strong>
                    <?= (int) ($resumo['quantidade_compras'] ?? 0) ?>
                </strong>
            </article>

            <article class="card">
                <span>Total movimentado</span>
                <strong>
                    <?= moedaImpressaoClientes(
                        (float) ($resumo['total_movimentado'] ?? 0)
                    ) ?>
                </strong>
            </article>

            <article class="card">
                <span>Ticket médio</span>
                <strong>
                    <?= moedaImpressaoClientes(
                        (float) ($resumo['ticket_medio_geral'] ?? 0)
                    ) ?>
                </strong>
            </article>

        </section>

        <?php if (!empty($maisCompraram)): ?>
            <section class="ranking">
                <h2>Clientes que mais compraram</h2>

                <div class="ranking-lista">
                    <?php foreach (
                        array_slice($maisCompraram, 0, 5)
                        as $cliente
                    ): ?>
                        <div class="ranking-item">
                            <strong>
                                <?= escaparImpressaoClientes(
                                    $cliente['nome'] ?? ''
                                ) ?>
                            </strong>

                            <small>
                                <?= moedaImpressaoClientes(
                                    (float) ($cliente['total_gasto'] ?? 0)
                                ) ?>
                                ·
                                <?= (int) (
                                    $cliente['quantidade_compras']
                                    ?? 0
                                ) ?>
                                compras
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (empty($clientes)): ?>

            <p>Nenhum cliente encontrado.</p>

        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th class="cliente">Cliente</th>
                        <th class="documento">CPF/CNPJ</th>
                        <th class="contato">Contato</th>
                        <th class="localidade">Localidade</th>
                        <th class="numero">Compras</th>
                        <th class="valor">Total gasto</th>
                        <th class="valor">Ticket médio</th>
                        <th class="data">Última compra</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td class="cliente">
                                <strong>
                                    <?= escaparImpressaoClientes(
                                        $cliente['nome'] ?? ''
                                    ) ?>
                                </strong>

                                <small>
                                    <?= escaparImpressaoClientes(
                                        $cliente['tipo']
                                        ?? 'Não informado'
                                    ) ?>
                                </small>
                            </td>

                            <td class="documento">
                                <?= escaparImpressaoClientes(
                                    $cliente['cpf_cnpj'] ?: '-'
                                ) ?>
                            </td>

                            <td class="contato">
                                <?= escaparImpressaoClientes(
                                    $cliente['whatsapp']
                                    ?: ($cliente['telefone'] ?: '-')
                                ) ?>
                            </td>

                            <td class="localidade">
                                <?= escaparImpressaoClientes(
                                    $cliente['localidade']
                                    ?? 'Não informada'
                                ) ?>
                            </td>

                            <td class="numero">
                                <?= (int) (
                                    $cliente['quantidade_compras']
                                    ?? 0
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoClientes(
                                    (float) ($cliente['total_gasto'] ?? 0)
                                ) ?>
                            </td>

                            <td class="valor">
                                <?= moedaImpressaoClientes(
                                    (float) ($cliente['ticket_medio'] ?? 0)
                                ) ?>
                            </td>

                            <td class="data">
                                <?= escaparImpressaoClientes(
                                    dataImpressaoClientes(
                                        $cliente['ultima_compra']
                                        ?? null
                                    )
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>

        <footer class="rodape">
            <span>JP TechCell — Relatório de Clientes</span>
            <span><?= count($clientes) ?> clientes ativos</span>
        </footer>

    </main>

</body>
</html>