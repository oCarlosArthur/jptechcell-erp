<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$formatarMoeda = static function (
    float|int|string|null $valor
): string {
    return 'R$ ' . number_format(
        (float) $valor,
        2,
        ',',
        '.'
    );
};

$formatarData = static function (
    ?string $data
): string {
    if (empty($data)) {
        return '-';
    }

    $timestamp = strtotime($data);

    if ($timestamp === false) {
        return '-';
    }

    return date('d/m/Y', $timestamp);
};

$formatarDataHora = static function (
    ?string $data
): string {
    if (empty($data)) {
        return '-';
    }

    $timestamp = strtotime($data);

    if ($timestamp === false) {
        return '-';
    }

    return date('d/m/Y H:i', $timestamp);
};

$formatarFormaPagamento = static function (
    string $formaPagamento
): string {
    $formas = [
        'dinheiro' => 'Dinheiro',
        'pix' => 'Pix',
        'debito' => 'Cartão de débito',
        'credito' => 'Cartão de crédito',
        'cartao_debito' => 'Cartão de débito',
        'cartao_credito' => 'Cartão de crédito',
        'parcelado' => 'Parcelado',
        'outro' => 'Outro'
    ];

    return $formas[$formaPagamento]
        ?? ucfirst(
            str_replace(
                '_',
                ' ',
                $formaPagamento
            )
        );
};

$resultadoMes = (float) (
    $resumo['resultado_mes'] ?? 0
);

$classeResultado = $resultadoMes >= 0
    ? 'card-resultado-positivo'
    : 'card-resultado-negativo';

$nomeMesSelecionado =
    $meses[$mes] ?? 'Mês';

$sucessoFinanceiro =
    $_SESSION['sucesso_financeiro'] ?? null;

$erroFinanceiro =
    $_SESSION['erro_financeiro'] ?? null;

unset(
    $_SESSION['sucesso_financeiro'],
    $_SESSION['erro_financeiro']
);

?>

<main class="conteudo">

    <header class="financeiro-topo">

        <div>
            <h1>Financeiro</h1>

            <p>
                Acompanhe as vendas, os custos,
                os lucros, as ordens de serviço
                e os gastos da loja.
            </p>
        </div>

        <div class="financeiro-acoes">

            <a
                class="botao-novo"
                href="index.php?rota=gasto-novo&mes=<?= (int) $mes ?>&ano=<?= (int) $ano ?>"
            >
                + Novo gasto
            </a>

        </div>

    </header>

    <?php if ($sucessoFinanceiro): ?>

        <div class="mensagem sucesso">
            <?= htmlspecialchars(
                $sucessoFinanceiro,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    <?php endif; ?>

    <?php if ($erroFinanceiro): ?>

        <div class="mensagem erro">
            <?= htmlspecialchars(
                $erroFinanceiro,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    <?php endif; ?>

    <!--
    |--------------------------------------------------------------------------
    | FILTRO E PERÍODO
    |--------------------------------------------------------------------------
    -->

    <section class="financeiro-filtro-periodo">

        <form
            class="filtro-financeiro"
            action="index.php"
            method="GET"
        >

            <input
                type="hidden"
                name="rota"
                value="financeiro"
            >

            <div class="campo">

                <label for="mes">
                    Mês
                </label>

                <select
                    id="mes"
                    name="mes"
                >

                    <?php foreach ($meses as $numeroMes => $nomeMes): ?>

                        <option
                            value="<?= (int) $numeroMes ?>"
                            <?= (int) $numeroMes === (int) $mes
                                ? 'selected'
                                : '' ?>
                        >
                            <?= htmlspecialchars(
                                $nomeMes,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="campo">

                <label for="ano">
                    Ano
                </label>

                <select
                    id="ano"
                    name="ano"
                >

                    <?php foreach ($anos as $anoDisponivel): ?>

                        <option
                            value="<?= (int) $anoDisponivel ?>"
                            <?= (int) $anoDisponivel === (int) $ano
                                ? 'selected'
                                : '' ?>
                        >
                            <?= (int) $anoDisponivel ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <button
                class="botao-secundario"
                type="submit"
            >
                Consultar
            </button>

        </form>

        <div class="periodo-financeiro-compacto">

            <span>
                Período selecionado
            </span>

            <strong>
                <?= htmlspecialchars(
                    $nomeMesSelecionado,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
                de
                <?= (int) $ano ?>
            </strong>

        </div>

    </section>

    <!--
    |--------------------------------------------------------------------------
    | CARDS PRINCIPAIS
    |--------------------------------------------------------------------------
    -->

    <section class="cards-financeiro">

        <article class="card-financeiro card-destaque">

            <span>
                Recebimento total
            </span>

            <strong>
                <?= $formatarMoeda(
                    $resumo['total_recebido'] ?? 0
                ) ?>
            </strong>

            <small>
                Produtos e ordens de serviço
            </small>

        </article>

        <article class="card-financeiro">

            <span>
                Venda de produtos
            </span>

            <strong>
                <?= $formatarMoeda(
                    $resumo['total_produtos'] ?? 0
                ) ?>
            </strong>

            <small>
                Valor líquido dos produtos
            </small>

        </article>

        <article class="card-financeiro">

            <span>
                Custo dos produtos
            </span>

            <strong>
                <?= $formatarMoeda(
                    $resumo['custo_produtos'] ?? 0
                ) ?>
            </strong>

            <small>
                Custo dos itens vendidos
            </small>

        </article>

        <article class="card-financeiro card-destaque">

            <span>
                Lucro dos produtos
            </span>

            <strong>
                <?= $formatarMoeda(
                    $resumo['lucro_produtos'] ?? 0
                ) ?>
            </strong>

            <small>
                Venda menos o custo
            </small>

        </article>

        <article class="card-financeiro">

            <span>
                Ordens de serviço
            </span>

            <strong>
                <?= $formatarMoeda(
                    $resumo[
                        'total_ordens_servico'
                    ] ?? 0
                ) ?>
            </strong>

            <small>
                <?= (int) (
                    $resumo[
                        'quantidade_ordens_servico'
                    ] ?? 0
                ) ?>
                ordem(ns) recebida(s)
            </small>

        </article>

        <article class="card-financeiro card-gasto">

            <span>
                Gastos do mês
            </span>

            <strong>
                <?= $formatarMoeda(
                    $resumo['total_gastos'] ?? 0
                ) ?>
            </strong>

            <small>
                Gastos cadastrados no período
            </small>

        </article>

        <article class="card-financeiro <?= $classeResultado ?>">

            <span>
                Resultado do mês
            </span>

            <strong>
                <?= $formatarMoeda($resultadoMes) ?>
            </strong>

            <small>
                Lucro + OS − gastos
            </small>

        </article>

        <article class="card-financeiro">

            <span>
                Movimento do mês
            </span>

            <strong>
                <?= (int) (
                    $resumo['quantidade_vendas'] ?? 0
                ) ?>
                venda(s)
            </strong>

            <small>
                <?= (int) (
                    $resumo['quantidade_itens'] ?? 0
                ) ?>
                item(ns) vendido(s)
            </small>

        </article>

    </section>

    <!--
    |--------------------------------------------------------------------------
    | RECEBIMENTOS POR FORMA DE PAGAMENTO
    |--------------------------------------------------------------------------
    -->

    <section class="painel-financeiro painel-pagamentos">

        <div class="painel-financeiro-topo">

            <div>
                <h2>
                    Recebimentos por forma de pagamento
                </h2>

                <p>
                    Veja como os valores foram recebidos
                    nas vendas concluídas.
                </p>
            </div>

            <strong class="total-painel-financeiro">
                <?= $formatarMoeda(
                    $resumo['total_recebido'] ?? 0
                ) ?>
            </strong>

        </div>

        <?php if (empty($formasPagamento)): ?>

            <div class="financeiro-vazio-compacto">

                <strong>
                    Nenhum recebimento no período
                </strong>

                <span>
                    As formas de pagamento aparecerão
                    aqui quando houver vendas concluídas.
                </span>

            </div>

        <?php else: ?>

            <div class="grade-pagamentos-financeiro">

                <?php
                foreach (
                    $formasPagamento
                    as $formaPagamento => $valorForma
                ):
                ?>

                    <article class="card-pagamento-financeiro">

                        <span>
                            <?= htmlspecialchars(
                                $formatarFormaPagamento(
                                    (string) $formaPagamento
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                        <strong>
                            <?= $formatarMoeda($valorForma) ?>
                        </strong>

                        <?php
                        $totalRecebido = (float) (
                            $resumo['total_recebido'] ?? 0
                        );

                        $porcentagem = $totalRecebido > 0
                            ? (
                                (float) $valorForma
                                / $totalRecebido
                            ) * 100
                            : 0;
                        ?>

                        <small>
                            <?= number_format(
                                $porcentagem,
                                1,
                                ',',
                                '.'
                            ) ?>%
                            do total recebido
                        </small>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

    <!--
    |--------------------------------------------------------------------------
    | PRODUTOS VENDIDOS
    |--------------------------------------------------------------------------
    -->

    <section class="painel-financeiro painel-produtos-vendidos">

        <div class="painel-financeiro-topo">

            <div>
                <h2>
                    Produtos vendidos
                </h2>

                <p>
                    Quantidade, faturamento, custo
                    e lucro dos produtos vendidos.
                </p>
            </div>

            <div class="resumo-produtos-vendidos">

                <span>
                    <?= (int) (
                        $resumo['quantidade_itens'] ?? 0
                    ) ?>
                    item(ns)
                </span>

                <strong>
                    <?= $formatarMoeda(
                        $resumo['total_produtos'] ?? 0
                    ) ?>
                </strong>

            </div>

        </div>

        <?php if (empty($produtosVendidos)): ?>

            <div class="financeiro-vazio-compacto">

                <strong>
                    Nenhum produto vendido
                </strong>

                <span>
                    Os produtos aparecerão aqui quando
                    uma venda for concluída no período.
                </span>

            </div>

        <?php else: ?>

            <div class="tabela-financeiro-responsiva">

                <table class="tabela-financeiro">

                    <thead>

                        <tr>
                            <th>Produto</th>
                            <th>Código</th>
                            <th class="texto-centro">
                                Quantidade
                            </th>
                            <th class="texto-direita">
                                Venda bruta
                            </th>
                            <th class="texto-direita">
                                Desconto
                            </th>
                            <th class="texto-direita">
                                Venda líquida
                            </th>
                            <th class="texto-direita">
                                Custo
                            </th>
                            <th class="texto-direita">
                                Lucro
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($produtosVendidos as $produto): ?>

                            <?php
                            $lucroProduto = (float) (
                                $produto['lucro'] ?? 0
                            );
                            ?>

                            <tr>

                                <td class="produto-vendido-nome">

                                    <strong>
                                        <?= htmlspecialchars(
                                            (string) (
                                                $produto[
                                                    'produto_nome'
                                                ] ?? '-'
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>

                                    <?php
                                    if (
                                        !empty(
                                            $produto[
                                                'produto_marca'
                                            ]
                                        )
                                    ):
                                    ?>

                                        <small>
                                            <?= htmlspecialchars(
                                                (string) $produto[
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
                                        (string) (
                                            $produto[
                                                'codigo_barras'
                                            ] ?? '-'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td class="texto-centro">

                                    <span class="badge-quantidade">
                                        <?= (int) (
                                            $produto[
                                                'quantidade'
                                            ] ?? 0
                                        ) ?>
                                    </span>

                                </td>

                                <td class="texto-direita">
                                    <?= $formatarMoeda(
                                        $produto[
                                            'total_bruto'
                                        ] ?? 0
                                    ) ?>
                                </td>

                                <td class="texto-direita valor-negativo">
                                    <?= $formatarMoeda(
                                        $produto[
                                            'desconto'
                                        ] ?? 0
                                    ) ?>
                                </td>

                                <td class="texto-direita valor-recebido">
                                    <?= $formatarMoeda(
                                        $produto[
                                            'total_venda'
                                        ] ?? 0
                                    ) ?>
                                </td>

                                <td class="texto-direita">
                                    <?= $formatarMoeda(
                                        $produto[
                                            'total_custo'
                                        ] ?? 0
                                    ) ?>
                                </td>

                                <td
                                    class="texto-direita <?= $lucroProduto >= 0
                                        ? 'valor-positivo'
                                        : 'valor-negativo' ?>"
                                >

                                    <strong>
                                        <?= $formatarMoeda(
                                            $lucroProduto
                                        ) ?>
                                    </strong>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                    <tfoot>

                        <tr>

                            <td colspan="2">
                                <strong>
                                    Total
                                </strong>
                            </td>

                            <td class="texto-centro">
                                <strong>
                                    <?= (int) (
                                        $resumo[
                                            'quantidade_itens'
                                        ] ?? 0
                                    ) ?>
                                </strong>
                            </td>

                            <td></td>

                            <td></td>

                            <td class="texto-direita valor-recebido">
                                <strong>
                                    <?= $formatarMoeda(
                                        $resumo[
                                            'total_produtos'
                                        ] ?? 0
                                    ) ?>
                                </strong>
                            </td>

                            <td class="texto-direita">
                                <strong>
                                    <?= $formatarMoeda(
                                        $resumo[
                                            'custo_produtos'
                                        ] ?? 0
                                    ) ?>
                                </strong>
                            </td>

                            <td class="texto-direita valor-positivo">
                                <strong>
                                    <?= $formatarMoeda(
                                        $resumo[
                                            'lucro_produtos'
                                        ] ?? 0
                                    ) ?>
                                </strong>
                            </td>

                        </tr>

                    </tfoot>

                </table>

            </div>

        <?php endif; ?>

    </section>

    <!--
    |--------------------------------------------------------------------------
    | ORDENS DE SERVIÇO
    |--------------------------------------------------------------------------
    -->

    <section class="painel-financeiro">

        <div class="painel-financeiro-topo">

            <div>
                <h2>
                    Ordens de serviço recebidas
                </h2>

                <p>
                    Ordens de serviço vinculadas
                    às vendas concluídas.
                </p>
            </div>

            <strong class="total-painel-financeiro">
                <?= $formatarMoeda(
                    $resumo[
                        'total_ordens_servico'
                    ] ?? 0
                ) ?>
            </strong>

        </div>

        <?php if (empty($ordensServico)): ?>

            <div class="financeiro-vazio-compacto">

                <strong>
                    Nenhuma ordem de serviço recebida
                </strong>

                <span>
                    As ordens aparecerão aqui depois
                    que forem finalizadas em uma venda.
                </span>

            </div>

        <?php else: ?>

            <div class="tabela-financeiro-responsiva">

                <table class="tabela-financeiro">

                    <thead>

                        <tr>
                            <th>OS</th>
                            <th>Cliente</th>
                            <th>Aparelho</th>
                            <th>Venda</th>
                            <th>Pagamento</th>
                            <th>Data</th>
                            <th class="texto-direita">
                                Valor bruto
                            </th>
                            <th class="texto-direita">
                                Desconto
                            </th>
                            <th class="texto-direita">
                                Recebido
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($ordensServico as $ordem): ?>

                            <tr>

                                <td>

                                    <a
                                        class="link-produto"
                                        href="index.php?rota=ordem-servico-visualizar&id=<?= (int) ($ordem['id'] ?? 0) ?>"
                                    >
                                        <?= htmlspecialchars(
                                            (string) (
                                                $ordem[
                                                    'numero_os'
                                                ] ?? '-'
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </a>

                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        (string) (
                                            $ordem[
                                                'cliente_nome'
                                            ] ?? '-'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        (string) (
                                            $ordem[
                                                'aparelho'
                                            ] ?? '-'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        (string) (
                                            $ordem[
                                                'numero_venda'
                                            ] ?? '-'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $formatarFormaPagamento(
                                            (string) (
                                                $ordem[
                                                    'forma_pagamento'
                                                ] ?? ''
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= $formatarDataHora(
                                        $ordem[
                                            'data_venda'
                                        ] ?? null
                                    ) ?>
                                </td>

                                <td class="texto-direita">
                                    <?= $formatarMoeda(
                                        $ordem[
                                            'valor_bruto'
                                        ] ?? 0
                                    ) ?>
                                </td>

                                <td class="texto-direita valor-negativo">
                                    <?= $formatarMoeda(
                                        $ordem[
                                            'desconto'
                                        ] ?? 0
                                    ) ?>
                                </td>

                                <td class="texto-direita valor-positivo">

                                    <strong>
                                        <?= $formatarMoeda(
                                            $ordem[
                                                'valor_recebido'
                                            ] ?? 0
                                        ) ?>
                                    </strong>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </section>

    <!--
    |--------------------------------------------------------------------------
    | GASTOS
    |--------------------------------------------------------------------------
    -->

    <section class="painel-financeiro">

        <div class="painel-financeiro-topo">

            <div>
                <h2>
                    Gastos do mês
                </h2>

                <p>
                    Contas, taxas, materiais
                    e demais despesas da loja.
                </p>
            </div>

            <a
                class="botao-novo"
                href="index.php?rota=gasto-novo&mes=<?= (int) $mes ?>&ano=<?= (int) $ano ?>"
            >
                + Novo gasto
            </a>

        </div>

        <?php if (empty($gastos)): ?>

            <div class="financeiro-vazio-compacto">

                <strong>
                    Nenhum gasto cadastrado
                </strong>

                <span>
                    Cadastre as despesas da loja para
                    obter o resultado mensal correto.
                </span>

                <a
                    class="botao-secundario"
                    href="index.php?rota=gasto-novo&mes=<?= (int) $mes ?>&ano=<?= (int) $ano ?>"
                >
                    Cadastrar primeiro gasto
                </a>

            </div>

        <?php else: ?>

            <div class="tabela-financeiro-responsiva">

                <table class="tabela-financeiro">

                    <thead>

                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Observações</th>
                            <th class="texto-direita">
                                Valor
                            </th>
                            <th>Ações</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($gastos as $gasto): ?>

                            <tr>

                                <td>
                                    <?= $formatarData(
                                        $gasto[
                                            'data_gasto'
                                        ] ?? null
                                    ) ?>
                                </td>

                                <td class="descricao-gasto">

                                    <strong>
                                        <?= htmlspecialchars(
                                            (string) (
                                                $gasto[
                                                    'descricao'
                                                ] ?? '-'
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>

                                </td>

                                <td>

                                    <span class="badge-categoria">
                                        <?= htmlspecialchars(
                                            (string) (
                                                $gasto[
                                                    'categoria'
                                                ] ?? '-'
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                </td>

                                <td>
                                    <?= !empty(
                                        $gasto['observacoes']
                                    )
                                        ? nl2br(
                                            htmlspecialchars(
                                                (string) $gasto[
                                                    'observacoes'
                                                ],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                        )
                                        : '-' ?>
                                </td>

                                <td class="texto-direita valor-gasto">

                                    <strong>
                                        <?= $formatarMoeda(
                                            $gasto[
                                                'valor'
                                            ] ?? 0
                                        ) ?>
                                    </strong>

                                </td>

                                <td>

                                    <div class="acoes-tabela">

                                        <a
                                            class="botao-editar"
                                            href="index.php?rota=gasto-editar&id=<?= (int) ($gasto['id'] ?? 0) ?>&mes=<?= (int) $mes ?>&ano=<?= (int) $ano ?>"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            action="index.php?rota=gasto-excluir"
                                            method="POST"
                                            onsubmit="return confirm('Deseja realmente excluir este gasto?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) ($gasto['id'] ?? 0) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="mes"
                                                value="<?= (int) $mes ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="ano"
                                                value="<?= (int) $ano ?>"
                                            >

                                            <button
                                                class="botao-excluir-pequeno"
                                                type="submit"
                                            >
                                                Excluir
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                    <tfoot>

                        <tr>

                            <td colspan="4">
                                <strong>
                                    Total de gastos
                                </strong>
                            </td>

                            <td class="texto-direita valor-gasto">

                                <strong>
                                    <?= $formatarMoeda(
                                        $resumo[
                                            'total_gastos'
                                        ] ?? 0
                                    ) ?>
                                </strong>

                            </td>

                            <td></td>

                        </tr>

                    </tfoot>

                </table>

            </div>

        <?php endif; ?>

    </section>

    <!--
    |--------------------------------------------------------------------------
    | RESUMO DO CÁLCULO
    |--------------------------------------------------------------------------
    -->

    <section class="painel-financeiro">

        <div class="painel-financeiro-topo">

            <div>
                <h2>
                    Resultado mensal
                </h2>

                <p>
                    Resumo do cálculo financeiro do período.
                </p>
            </div>

        </div>

        <div class="resumo-financeiro">

            <div class="linha-resumo-financeiro">

                <span>
                    Lucro dos produtos
                </span>

                <strong class="valor-positivo">
                    <?= $formatarMoeda(
                        $resumo[
                            'lucro_produtos'
                        ] ?? 0
                    ) ?>
                </strong>

            </div>

            <div class="linha-resumo-financeiro">

                <span>
                    Ordens de serviço
                </span>

                <strong class="valor-positivo">
                    <?= $formatarMoeda(
                        $resumo[
                            'total_ordens_servico'
                        ] ?? 0
                    ) ?>
                </strong>

            </div>

            <div class="linha-resumo-financeiro">

                <span>
                    Gastos cadastrados
                </span>

                <strong class="valor-negativo">
                    − <?= $formatarMoeda(
                        $resumo[
                            'total_gastos'
                        ] ?? 0
                    ) ?>
                </strong>

            </div>

            <div
                class="linha-resumo-financeiro resultado <?= $resultadoMes < 0
                    ? 'resultado-negativo'
                    : '' ?>"
            >

                <span>
                    Resultado do mês
                </span>

                <strong class="<?= $resultadoMes >= 0
                    ? 'valor-positivo'
                    : 'valor-negativo' ?>"
                >
                    <?= $formatarMoeda($resultadoMes) ?>
                </strong>

            </div>

        </div>

    </section>

</main>

</div>

</body>
</html>