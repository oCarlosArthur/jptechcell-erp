<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$mensagemErro = $_SESSION['erro'] ?? null;
unset($_SESSION['erro']);

$dataInicio = $dataInicio ?? date('Y-m-01');
$dataFim = $dataFim ?? date('Y-m-d');

$vendas = $dados['vendas'] ?? [];
$resumo = $dados['resumo'] ?? [];

function formatarMoedaRelatorio(float $valor): string
{
    return 'R$ ' . number_format(
        $valor,
        2,
        ',',
        '.'
    );
}

function formatarDataHoraRelatorio(
    ?string $data
): string {
    if (empty($data)) {
        return '-';
    }

    return date(
        'd/m/Y H:i',
        strtotime($data)
    );
}

function formatarFormaPagamentoRelatorio(
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
?>

<main class="conteudo-principal">

    <div class="cabecalho-pagina">
        <div>
            <h1>Relatório de Vendas</h1>

            <p>
                Consulte as vendas concluídas em um período
                e imprima o relatório com todos os valores.
            </p>
        </div>

        <a
            href="index.php?rota=relatorios"
            class="botao botao-secundario"
        >
            Voltar
        </a>
    </div>

    <?php if (!empty($mensagemErro)): ?>
        <div class="alerta alerta-erro">
            <?= htmlspecialchars(
                $mensagemErro,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>
    <?php endif; ?>

    <section class="secao-filtro-relatorio">

        <form
            action="index.php"
            method="GET"
            class="formulario-filtro-relatorio"
        >
            <input
                type="hidden"
                name="rota"
                value="relatorios-vendas"
            >

            <div class="campo-formulario">
                <label for="data_inicio">
                    Data inicial
                </label>

                <input
                    type="date"
                    id="data_inicio"
                    name="data_inicio"
                    value="<?= htmlspecialchars(
                        $dataInicio,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >
            </div>

            <div class="campo-formulario">
                <label for="data_fim">
                    Data final
                </label>

                <input
                    type="date"
                    id="data_fim"
                    name="data_fim"
                    value="<?= htmlspecialchars(
                        $dataFim,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >
            </div>

            <div class="acoes-filtro-relatorio">
                <button
                    type="submit"
                    class="botao botao-primario"
                >
                    Gerar relatório
                </button>

                <a
                    href="index.php?rota=relatorios-imprimir-vendas&data_inicio=<?= urlencode(
                        $dataInicio
                    ) ?>&data_fim=<?= urlencode(
                        $dataFim
                    ) ?>"
                    class="botao botao-secundario"
                    target="_blank"
                >
                    Imprimir relatório
                </a>

            </div>
        </form>

    </section>

    <section class="resumo-relatorio">

        <div class="cartao-resumo-relatorio">
            <span>Vendas concluídas</span>

            <strong>
                <?= (int) (
                    $resumo['quantidade_vendas']
                    ?? 0
                ) ?>
            </strong>
        </div>

        <div class="cartao-resumo-relatorio">
            <span>Produtos vendidos</span>

            <strong>
                <?= (int) (
                    $resumo['quantidade_produtos']
                    ?? 0
                ) ?>
            </strong>
        </div>

        <div class="cartao-resumo-relatorio">
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

        <div class="cartao-resumo-relatorio">
            <span>Subtotal</span>

            <strong>
                <?= formatarMoedaRelatorio(
                    (float) (
                        $resumo['subtotal']
                        ?? 0
                    )
                ) ?>
            </strong>
        </div>

        <div class="cartao-resumo-relatorio">
            <span>Descontos</span>

            <strong>
                <?= formatarMoedaRelatorio(
                    (float) (
                        $resumo['descontos']
                        ?? 0
                    )
                ) ?>
            </strong>
        </div>

        <div class="cartao-resumo-relatorio destaque">
            <span>Total recebido</span>

            <strong>
                <?= formatarMoedaRelatorio(
                    (float) (
                        $resumo['total_recebido']
                        ?? 0
                    )
                ) ?>
            </strong>
        </div>

    </section>

    <section class="secao-relatorio-detalhado">

        <div class="titulo-secao-relatorio">
            <div>
                <h2>Vendas do período</h2>

                <p>
                    De
                    <?= date(
                        'd/m/Y',
                        strtotime($dataInicio)
                    ) ?>
                    até
                    <?= date(
                        'd/m/Y',
                        strtotime($dataFim)
                    ) ?>.
                </p>
            </div>
        </div>

        <?php if (empty($vendas)): ?>

            <div class="estado-vazio">
                <h3>Nenhuma venda encontrada</h3>

                <p>
                    Não existem vendas concluídas
                    no período informado.
                </p>
            </div>

        <?php else: ?>

            <div class="lista-vendas-relatorio">

                <?php foreach ($vendas as $venda): ?>

                    <article class="venda-relatorio">

                        <div class="venda-relatorio-cabecalho">

                            <div>
                                <h3>
                                    Venda
                                    <?= htmlspecialchars(
                                        (string) (
                                            $venda['numero_venda']
                                            ?? $venda['id']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h3>

                                <p>
                                    <?= htmlspecialchars(
                                        $venda['cliente_nome']
                                        ?? 'Cliente não informado',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>
                            </div>

                            <div class="venda-relatorio-informacoes">

                                <span>
                                    <?= formatarDataHoraRelatorio(
                                        $venda['criado_em']
                                        ?? null
                                    ) ?>
                                </span>

                                <span>
                                    <?= htmlspecialchars(
                                        formatarFormaPagamentoRelatorio(
                                            $venda[
                                                'forma_pagamento'
                                            ]
                                            ?? null
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            </div>

                        </div>

                        <?php if (!empty($venda['itens'])): ?>

                            <div class="tabela-responsiva">

                                <table class="tabela-relatorio">

                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Código</th>
                                            <th>Quantidade</th>
                                            <th>Valor unitário</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php foreach (
                                            $venda['itens']
                                            as $item
                                        ): ?>

                                            <tr>
                                                <td>
                                                    <strong>
                                                        <?= htmlspecialchars(
                                                            $item[
                                                                'produto_nome'
                                                            ]
                                                            ?? 'Produto',
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>
                                                    </strong>

                                                    <?php if (
                                                        !empty(
                                                            $item[
                                                                'produto_marca'
                                                            ]
                                                        )
                                                    ): ?>
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
                                                        $item[
                                                            'codigo_barras'
                                                        ]
                                                        ?? '-',
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?= (int) (
                                                        $item[
                                                            'quantidade'
                                                        ]
                                                        ?? 0
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?= formatarMoedaRelatorio(
                                                        (float) (
                                                            $item[
                                                                'preco_unitario'
                                                            ]
                                                            ?? 0
                                                        )
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?= formatarMoedaRelatorio(
                                                        (float) (
                                                            $item['total']
                                                            ?? 0
                                                        )
                                                    ) ?>
                                                </td>
                                            </tr>

                                        <?php endforeach; ?>

                                    </tbody>

                                </table>

                            </div>

                        <?php endif; ?>

                        <?php if (
                            !empty(
                                $venda['ordem_servico']
                            )
                        ): ?>

                            <div class="ordem-servico-relatorio">

                                <div>
                                    <strong>
                                        Ordem de Serviço
                                        <?= htmlspecialchars(
                                            $venda[
                                                'ordem_servico'
                                            ]['numero_os']
                                            ?? '',
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
                                    <?= formatarMoedaRelatorio(
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

                        <div class="venda-relatorio-totais">

                            <div>
                                <span>Subtotal</span>

                                <strong>
                                    <?= formatarMoedaRelatorio(
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
                                    <?= formatarMoedaRelatorio(
                                        (float) (
                                            $venda['desconto']
                                            ?? 0
                                        )
                                    ) ?>
                                </strong>
                            </div>

                            <div class="total-venda-relatorio">
                                <span>Total recebido</span>

                                <strong>
                                    <?= formatarMoedaRelatorio(
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

            </div>

        <?php endif; ?>

    </section>

    <?php if (
        !empty(
            $resumo['formas_pagamento']
        )
    ): ?>

        <section class="secao-formas-pagamento-relatorio">

            <div class="titulo-secao-relatorio">
                <div>
                    <h2>Formas de pagamento</h2>

                    <p>
                        Valores recebidos no período
                        separados por forma de pagamento.
                    </p>
                </div>
            </div>

            <div class="tabela-responsiva">

                <table class="tabela-relatorio">

                    <thead>
                        <tr>
                            <th>Forma de pagamento</th>
                            <th>Quantidade de vendas</th>
                            <th>Valor recebido</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach (
                            $resumo['formas_pagamento']
                            as $formaPagamento
                                => $informacoes
                        ): ?>

                            <tr>
                                <td>
                                    <?= htmlspecialchars(
                                        formatarFormaPagamentoRelatorio(
                                            $formaPagamento
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= (int) (
                                        $informacoes[
                                            'quantidade'
                                        ]
                                        ?? 0
                                    ) ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= formatarMoedaRelatorio(
                                            (float) (
                                                $informacoes[
                                                    'total'
                                                ]
                                                ?? 0
                                            )
                                        ) ?>
                                    </strong>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

    <?php endif; ?>

</main>

</div>

</body>
</html>