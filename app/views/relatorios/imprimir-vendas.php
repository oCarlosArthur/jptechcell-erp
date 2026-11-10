<?php

$vendas = $dados['vendas'] ?? [];
$resumo = $dados['resumo'] ?? [];

$dataInicio = $dados['data_inicio'] ?? date('Y-m-01');
$dataFim = $dados['data_fim'] ?? date('Y-m-d');

function moedaRelatorioImpressao(float $valor): string
{
    return 'R$ ' . number_format(
        $valor,
        2,
        ',',
        '.'
    );
}

function dataHoraRelatorioImpressao(?string $data): string
{
    if (empty($data)) {
        return '-';
    }

    return date(
        'd/m/Y H:i',
        strtotime($data)
    );
}

function formaPagamentoRelatorioImpressao(
    ?string $formaPagamento
): string {
    $formaPagamento = trim(
        (string) $formaPagamento
    );

    if ($formaPagamento === '') {
        return 'Não informado';
    }

    $formas = [
        'dinheiro' => 'Dinheiro',
        'pix' => 'Pix',
        'debito' => 'Cartão de débito',
        'credito' => 'Cartão de crédito',
        'cartao_debito' => 'Cartão de débito',
        'cartao_credito' => 'Cartão de crédito'
    ];

    return $formas[$formaPagamento]
        ?? ucfirst(
            str_replace(
                '_',
                ' ',
                $formaPagamento
            )
        );
}

$geradoEm = date('d/m/Y H:i');
?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Relatório de Vendas</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            background: #e9edf2;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
        }

        .acoes-impressao {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .botao-imprimir,
        .botao-fechar {
            padding: 11px 18px;
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .botao-imprimir {
            background: #00b85c;
        }

        .botao-fechar {
            background: #374151;
        }

        .folha {
            width: 100%;
            max-width: 1100px;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            background: #ffffff;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.14);
        }

        .cabecalho {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 25px;
            padding-bottom: 16px;
            border-bottom: 3px solid #00b85c;
        }

        .empresa {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .empresa img {
            width: 145px;
            max-height: 80px;
            object-fit: contain;
        }

        .empresa-texto h1 {
            margin: 0 0 5px;
            font-size: 22px;
            color: #111827;
        }

        .empresa-texto p {
            margin: 0;
            color: #6b7280;
            font-size: 12px;
        }

        .documento-info {
            text-align: right;
        }

        .documento-info strong {
            display: block;
            margin-bottom: 5px;
            color: #111827;
            font-size: 18px;
        }

        .documento-info span {
            display: block;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
        }

        .periodo {
            margin: 18px 0;
            padding: 12px 16px;
            border: 1px solid #d1fae5;
            border-radius: 8px;
            background: #ecfdf5;
            color: #065f46;
            font-size: 13px;
        }

        .resumo {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 22px;
        }

        .card-resumo {
            padding: 13px;
            border: 1px solid #dbe2ea;
            border-radius: 8px;
            background: #f8fafc;
        }

        .card-resumo span {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
        }

        .card-resumo strong {
            display: block;
            color: #111827;
            font-size: 17px;
        }

        .card-resumo.destaque {
            border-color: #86efac;
            background: #f0fdf4;
        }

        .card-resumo.destaque strong {
            color: #15803d;
        }

        .titulo-secao {
            margin: 22px 0 12px;
            padding-bottom: 7px;
            border-bottom: 1px solid #dbe2ea;
            color: #111827;
            font-size: 16px;
        }

        .venda {
            margin-bottom: 18px;
            border: 1px solid #dbe2ea;
            border-radius: 8px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .venda-cabecalho {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding: 12px 14px;
            background: #f8fafc;
            border-bottom: 1px solid #dbe2ea;
        }

        .venda-cabecalho h3 {
            margin: 0 0 4px;
            color: #111827;
            font-size: 14px;
        }

        .venda-cabecalho p {
            margin: 0;
            color: #64748b;
            font-size: 12px;
        }

        .venda-informacoes {
            text-align: right;
            color: #475569;
            font-size: 11px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: middle;
            font-size: 11px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .texto-direita {
            text-align: right;
        }

        .os-vinculada {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin: 10px 12px;
            padding: 10px 12px;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            background: #eff6ff;
            color: #1e3a8a;
            font-size: 11px;
        }

        .os-vinculada span {
            display: block;
            margin-top: 3px;
            color: #475569;
        }

        .venda-totais {
            display: flex;
            justify-content: flex-end;
            gap: 22px;
            padding: 11px 14px;
            border-top: 1px solid #dbe2ea;
            background: #fafafa;
        }

        .venda-totais div {
            min-width: 100px;
            text-align: right;
        }

        .venda-totais span {
            display: block;
            margin-bottom: 3px;
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
        }

        .venda-totais strong {
            color: #111827;
            font-size: 12px;
        }

        .venda-totais .total strong {
            color: #15803d;
            font-size: 15px;
        }

        .formas-pagamento {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .sem-registros {
            padding: 35px;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: #64748b;
            text-align: center;
        }

        .rodape {
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px solid #dbe2ea;
            color: #94a3b8;
            font-size: 10px;
            text-align: center;
        }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        @media print {

            body {
                padding: 0;
                background: #ffffff;
            }

            .acoes-impressao {
                display: none;
            }

            .folha {
                width: 100%;
                max-width: none;
                min-height: auto;
                margin: 0;
                padding: 5mm;
                box-shadow: none;
            }

            .venda {
                break-inside: avoid;
            }

            .formas-pagamento {
                break-inside: avoid;
            }
        }

        @media (max-width: 750px) {

            .folha {
                padding: 18px;
            }

            .cabecalho {
                flex-direction: column;
                align-items: flex-start;
            }

            .documento-info {
                text-align: left;
            }

            .resumo {
                grid-template-columns: repeat(2, 1fr);
            }

            .venda-cabecalho {
                flex-direction: column;
            }

            .venda-informacoes {
                text-align: left;
            }

            .venda-totais {
                flex-direction: column;
            }

            .venda-totais div {
                text-align: left;
            }

        }

    </style>

</head>

<body>

    <div class="acoes-impressao">

        <button
            type="button"
            class="botao-fechar"
            onclick="window.close()"
        >
            Fechar
        </button>

        <button
            type="button"
            class="botao-imprimir"
            onclick="window.print()"
        >
            Imprimir relatório
        </button>

    </div>

    <main class="folha">

        <header class="cabecalho">

            <div class="empresa">

                <img
                    src="/jptechcell/public/assets/img/logo-jptechcell.png"
                    alt="Logo JP TechCell"
                >

                <div class="empresa-texto">

                    <h1>JP TechCell</h1>

                    <p>
                        Relatório gerencial de vendas
                    </p>

                </div>

            </div>

            <div class="documento-info">

                <strong>Relatório de Vendas</strong>

                <span>
                    Gerado em <?= htmlspecialchars(
                        $geradoEm,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

        </header>

        <div class="periodo">

            Período consultado:

            <strong>
                <?= date(
                    'd/m/Y',
                    strtotime($dataInicio)
                ) ?>
            </strong>

            até

            <strong>
                <?= date(
                    'd/m/Y',
                    strtotime($dataFim)
                ) ?>
            </strong>

        </div>

        <section class="resumo">

            <div class="card-resumo">

                <span>Vendas</span>

                <strong>
                    <?= (int) (
                        $resumo['quantidade_vendas']
                        ?? 0
                    ) ?>
                </strong>

            </div>

            <div class="card-resumo">

                <span>Produtos vendidos</span>

                <strong>
                    <?= (int) (
                        $resumo['quantidade_produtos']
                        ?? 0
                    ) ?>
                </strong>

            </div>

            <div class="card-resumo">

                <span>Ordens de serviço</span>

                <strong>
                    <?= (int) (
                        $resumo[
                            'quantidade_ordens_servico'
                        ]
                        ?? 0
                    ) ?>
                </strong>

            </div>

            <div class="card-resumo destaque">

                <span>Total recebido</span>

                <strong>
                    <?= moedaRelatorioImpressao(
                        (float) (
                            $resumo['total_recebido']
                            ?? 0
                        )
                    ) ?>
                </strong>

            </div>

            <div class="card-resumo">

                <span>Subtotal</span>

                <strong>
                    <?= moedaRelatorioImpressao(
                        (float) (
                            $resumo['subtotal']
                            ?? 0
                        )
                    ) ?>
                </strong>

            </div>

            <div class="card-resumo">

                <span>Descontos</span>

                <strong>
                    <?= moedaRelatorioImpressao(
                        (float) (
                            $resumo['descontos']
                            ?? 0
                        )
                    ) ?>
                </strong>

            </div>

            <div class="card-resumo">

                <span>Total em produtos</span>

                <strong>
                    <?= moedaRelatorioImpressao(
                        (float) (
                            $resumo['total_produtos']
                            ?? 0
                        )
                    ) ?>
                </strong>

            </div>

            <div class="card-resumo">

                <span>Total em serviços</span>

                <strong>
                    <?= moedaRelatorioImpressao(
                        (float) (
                            $resumo['total_ordens_servico']
                            ?? 0
                        )
                    ) ?>
                </strong>

            </div>

        </section>

        <h2 class="titulo-secao">
            Vendas do período
        </h2>

        <?php if (empty($vendas)): ?>

            <div class="sem-registros">

                Nenhuma venda concluída foi encontrada
                no período informado.

            </div>

        <?php else: ?>

            <?php foreach ($vendas as $venda): ?>

                <article class="venda">

                    <div class="venda-cabecalho">

                        <div>

                            <h3>
                                Venda
                                <?= htmlspecialchars(
                                    (string) (
                                        $venda['numero_venda']
                                        ?? $venda['id']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </h3>

                            <p>
                                Cliente:
                                <strong>
                                    <?= htmlspecialchars(
                                        $venda['cliente_nome']
                                        ?? 'Cliente não informado',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>
                            </p>

                        </div>

                        <div class="venda-informacoes">

                            <div>
                                <?= dataHoraRelatorioImpressao(
                                    $venda['criado_em']
                                    ?? null
                                ) ?>
                            </div>

                            <div>
                                <?= htmlspecialchars(
                                    formaPagamentoRelatorioImpressao(
                                        $venda['forma_pagamento']
                                        ?? null
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        </div>

                    </div>

                    <?php if (!empty($venda['itens'])): ?>

                        <table>

                            <thead>

                                <tr>
                                    <th>Produto</th>
                                    <th>Código</th>
                                    <th>Qtd.</th>
                                    <th class="texto-direita">
                                        Valor unitário
                                    </th>
                                    <th class="texto-direita">
                                        Total
                                    </th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach (
                                    $venda['itens']
                                    as $item
                                ): ?>

                                    <tr>

                                        <td>
                                            <?= htmlspecialchars(
                                                $item['produto_nome']
                                                ?? 'Produto',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                            <?php if (
                                                !empty(
                                                    $item['produto_marca']
                                                )
                                            ): ?>

                                                <br>

                                                <small>
                                                    <?= htmlspecialchars(
                                                        $item[
                                                            'produto_marca'
                                                        ],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </small>

                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $item['codigo_barras']
                                                ?? '-',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= (int) (
                                                $item['quantidade']
                                                ?? 0
                                            ) ?>
                                        </td>

                                        <td class="texto-direita">
                                            <?= moedaRelatorioImpressao(
                                                (float) (
                                                    $item[
                                                        'preco_unitario'
                                                    ]
                                                    ?? 0
                                                )
                                            ) ?>
                                        </td>

                                        <td class="texto-direita">
                                            <strong>
                                                <?= moedaRelatorioImpressao(
                                                    (float) (
                                                        $item['total']
                                                        ?? 0
                                                    )
                                                ) ?>
                                            </strong>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    <?php endif; ?>

                    <?php if (
                        !empty(
                            $venda['ordem_servico']
                        )
                    ): ?>

                        <div class="os-vinculada">

                            <div>

                                <strong>
                                    Ordem de Serviço
                                    <?= htmlspecialchars(
                                        (string) (
                                            $venda[
                                                'ordem_servico'
                                            ]['numero_os']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $venda[
                                            'ordem_servico'
                                        ]['aparelho']
                                        ?? 'Aparelho não informado',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            </div>

                            <strong>
                                <?= moedaRelatorioImpressao(
                                    (float) (
                                        $venda[
                                            'ordem_servico'
                                        ]['valor']
                                        ?? 0
                                    )
                                ) ?>
                            </strong>

                        </div>

                    <?php endif; ?>

                    <div class="venda-totais">

                        <div>

                            <span>Subtotal</span>

                            <strong>
                                <?= moedaRelatorioImpressao(
                                    (float) (
                                        $venda['subtotal']
                                        ?? 0
                                    )
                                ) ?>
                            </strong>

                        </div>

                        <div>

                            <span>Desconto</span>

                            <strong>
                                <?= moedaRelatorioImpressao(
                                    (float) (
                                        $venda['desconto']
                                        ?? 0
                                    )
                                ) ?>
                            </strong>

                        </div>

                        <div class="total">

                            <span>Total recebido</span>

                            <strong>
                                <?= moedaRelatorioImpressao(
                                    (float) (
                                        $venda['total']
                                        ?? 0
                                    )
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

        <?php if (
            !empty(
                $resumo['formas_pagamento']
            )
        ): ?>

            <section class="formas-pagamento">

                <h2 class="titulo-secao">
                    Formas de pagamento
                </h2>

                <table>

                    <thead>

                        <tr>
                            <th>Forma de pagamento</th>
                            <th>Vendas</th>
                            <th class="texto-direita">
                                Valor recebido
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach (
                            $resumo['formas_pagamento']
                            as $forma => $informacoes
                        ): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars(
                                        formaPagamentoRelatorioImpressao(
                                            $forma
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= (int) (
                                        $informacoes['quantidade']
                                        ?? 0
                                    ) ?>
                                </td>

                                <td class="texto-direita">

                                    <strong>
                                        <?= moedaRelatorioImpressao(
                                            (float) (
                                                $informacoes['total']
                                                ?? 0
                                            )
                                        ) ?>
                                    </strong>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </section>

        <?php endif; ?>

        <footer class="rodape">

            JP TechCell ERP • Relatório gerado automaticamente
            em <?= htmlspecialchars(
                $geradoEm,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </footer>

    </main>

</body>

</html>