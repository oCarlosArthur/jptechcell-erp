<?php

$produtos = $dados['produtos'] ?? [];
$resumo = $dados['resumo'] ?? [];
$topMaisVendidos = $dados['top_mais_vendidos'] ?? [];
$topMaisLucrativos = $dados['top_mais_lucrativos'] ?? [];
$produtosSemVenda = $dados['produtos_sem_venda'] ?? [];
$produtosEstoqueBaixo = $dados['produtos_estoque_baixo'] ?? [];

$dataInicio = $dados['data_inicio'] ?? ($_GET['data_inicio'] ?? date('Y-m-01'));
$dataFim = $dados['data_fim'] ?? ($_GET['data_fim'] ?? date('Y-m-d'));

function moedaRelatorioProdutos(float $valor): string
{
    return 'R$ ' . number_format(
        $valor,
        2,
        ',',
        '.'
    );
}

function percentualRelatorioProdutos(float $valor): string
{
    return number_format(
        $valor,
        2,
        ',',
        '.'
    ) . '%';
}

function textoRelatorioProdutos(mixed $valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="conteudo-principal">

    <section class="cabecalho-pagina">

        <div>

            <span class="subtitulo-pagina">
                Relatórios gerenciais
            </span>

            <h1>Relatório de Produtos</h1>

            <p>
                Acompanhe vendas, lucro, estoque e desempenho dos produtos.
            </p>

        </div>

        <a
            href="index.php?rota=relatorios"
            class="botao-secundario"
        >
            ← Voltar
        </a>

    </section>

    <?php if (!empty($_SESSION['erro'])): ?>

        <div class="alerta alerta-erro">

            <?= textoRelatorioProdutos($_SESSION['erro']) ?>

        </div>

        <?php unset($_SESSION['erro']); ?>

    <?php endif; ?>

    <section class="cartao-relatorio">

        <div class="cabecalho-cartao-relatorio">

            <div>

                <h2>Período do relatório</h2>

                <p>
                    Escolha as datas para analisar o desempenho dos produtos.
                </p>

            </div>

        </div>

        <form
            action="index.php"
            method="get"
            class="formulario-filtros-relatorio"
        >

            <input
                type="hidden"
                name="rota"
                value="relatorios-produtos"
            >

            <div class="campo-formulario">

                <label for="data_inicio">
                    Data inicial
                </label>

                <input
                    type="date"
                    id="data_inicio"
                    name="data_inicio"
                    value="<?= textoRelatorioProdutos($dataInicio) ?>"
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
                    value="<?= textoRelatorioProdutos($dataFim) ?>"
                    required
                >

            </div>

            <div class="acoes-filtros-relatorio">

                <button
                    type="submit"
                    class="botao-primario"
                >
                    Aplicar filtro
                </button>

                <a
                    href="index.php?rota=relatorios-imprimir-produtos&data_inicio=<?= urlencode($dataInicio) ?>&data_fim=<?= urlencode($dataFim) ?>"
                    target="_blank"
                    class="botao-imprimir-relatorio"
                >
                    Imprimir relatório
                </a>

            </div>

        </form>

    </section>

    <section class="grade-resumo-relatorio">

        <article class="cartao-resumo-relatorio">

            <span>Produtos cadastrados</span>

            <strong>
                <?= (int) ($resumo['total_cadastrados'] ?? 0) ?>
            </strong>

            <small>
                Produtos ativos no sistema
            </small>

        </article>

        <article class="cartao-resumo-relatorio">

            <span>Produtos vendidos</span>

            <strong>
                <?= (int) ($resumo['produtos_vendidos'] ?? 0) ?>
            </strong>

            <small>
                Produtos diferentes vendidos
            </small>

        </article>

        <article class="cartao-resumo-relatorio">

            <span>Unidades vendidas</span>

            <strong>
                <?= (int) ($resumo['quantidade_vendida'] ?? 0) ?>
            </strong>

            <small>
                Soma das quantidades do período
            </small>

        </article>

        <article class="cartao-resumo-relatorio destaque">

            <span>Faturamento</span>

            <strong>
                <?= moedaRelatorioProdutos(
                    (float) ($resumo['faturamento'] ?? 0)
                ) ?>
            </strong>

            <small>
                Total vendido em produtos
            </small>

        </article>

        <article class="cartao-resumo-relatorio">

            <span>Custo total</span>

            <strong>
                <?= moedaRelatorioProdutos(
                    (float) ($resumo['custo_total'] ?? 0)
                ) ?>
            </strong>

            <small>
                Custo dos produtos vendidos
            </small>

        </article>

        <article class="cartao-resumo-relatorio destaque-lucro">

            <span>Lucro</span>

            <strong>
                <?= moedaRelatorioProdutos(
                    (float) ($resumo['lucro'] ?? 0)
                ) ?>
            </strong>

            <small>
                Margem:
                <?= percentualRelatorioProdutos(
                    (float) ($resumo['margem_lucro'] ?? 0)
                ) ?>
            </small>

        </article>

        <article class="cartao-resumo-relatorio alerta">

            <span>Estoque baixo</span>

            <strong>
                <?= (int) ($resumo['produtos_estoque_baixo'] ?? 0) ?>
            </strong>

            <small>
                Inclui produtos sem estoque
            </small>

        </article>

        <article class="cartao-resumo-relatorio">

            <span>Sem venda</span>

            <strong>
                <?= (int) ($resumo['produtos_sem_venda'] ?? 0) ?>
            </strong>

            <small>
                Sem movimentação no período
            </small>

        </article>

    </section>

    <section class="grade-rankings-relatorio">

        <article class="cartao-relatorio">

            <div class="cabecalho-cartao-relatorio">

                <div>

                    <h2>Mais vendidos</h2>

                    <p>
                        Ranking por quantidade vendida.
                    </p>

                </div>

            </div>

            <?php if (empty($topMaisVendidos)): ?>

                <div class="estado-vazio-relatorio">
                    Nenhum produto foi vendido no período.
                </div>

            <?php else: ?>

                <div class="lista-ranking-relatorio">

                    <?php foreach ($topMaisVendidos as $indice => $produto): ?>

                        <div class="item-ranking-relatorio">

                            <span class="posicao-ranking">
                                <?= $indice + 1 ?>
                            </span>

                            <div class="dados-ranking">

                                <strong>
                                    <?= textoRelatorioProdutos($produto['nome'] ?? '') ?>
                                </strong>

                                <small>
                                    <?= textoRelatorioProdutos($produto['marca'] ?? 'Não informada') ?>
                                </small>

                            </div>

                            <div class="valor-ranking">

                                <strong>
                                    <?= (int) ($produto['quantidade_vendida'] ?? 0) ?>
                                </strong>

                                <small>unidades</small>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </article>

        <article class="cartao-relatorio">

            <div class="cabecalho-cartao-relatorio">

                <div>

                    <h2>Mais lucrativos</h2>

                    <p>
                        Ranking pelo lucro obtido no período.
                    </p>

                </div>

            </div>

            <?php if (empty($topMaisLucrativos)): ?>

                <div class="estado-vazio-relatorio">
                    Nenhum lucro foi registrado no período.
                </div>

            <?php else: ?>

                <div class="lista-ranking-relatorio">

                    <?php foreach ($topMaisLucrativos as $indice => $produto): ?>

                        <div class="item-ranking-relatorio">

                            <span class="posicao-ranking">
                                <?= $indice + 1 ?>
                            </span>

                            <div class="dados-ranking">

                                <strong>
                                    <?= textoRelatorioProdutos($produto['nome'] ?? '') ?>
                                </strong>

                                <small>
                                    <?= textoRelatorioProdutos($produto['marca'] ?? 'Não informada') ?>
                                </small>

                            </div>

                            <div class="valor-ranking">

                                <strong>
                                    <?= moedaRelatorioProdutos(
                                        (float) ($produto['lucro'] ?? 0)
                                    ) ?>
                                </strong>

                                <small>
                                    <?= percentualRelatorioProdutos(
                                        (float) ($produto['margem_lucro'] ?? 0)
                                    ) ?>
                                </small>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </article>

    </section>

    <section class="grade-rankings-relatorio">

        <article class="cartao-relatorio">

            <div class="cabecalho-cartao-relatorio">

                <div>

                    <h2>Estoque baixo</h2>

                    <p>
                        Produtos que precisam de reposição.
                    </p>

                </div>

                <span class="contador-alerta">
                    <?= count($produtosEstoqueBaixo) ?>
                </span>

            </div>

            <?php if (empty($produtosEstoqueBaixo)): ?>

                <div class="estado-vazio-relatorio">
                    Nenhum produto está abaixo do estoque mínimo.
                </div>

            <?php else: ?>

                <div class="lista-alertas-relatorio">

                    <?php foreach ($produtosEstoqueBaixo as $produto): ?>

                        <div class="item-alerta-relatorio">

                            <div>

                                <strong>
                                    <?= textoRelatorioProdutos($produto['nome'] ?? '') ?>
                                </strong>

                                <small>
                                    Mínimo:
                                    <?= (int) ($produto['estoque_minimo'] ?? 0) ?>
                                </small>

                            </div>

                            <span class="badge-estoque <?= textoRelatorioProdutos($produto['situacao_estoque'] ?? 'normal') ?>">

                                <?= (int) ($produto['estoque_atual'] ?? 0) ?>
                                em estoque

                            </span>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </article>

        <article class="cartao-relatorio">

            <div class="cabecalho-cartao-relatorio">

                <div>

                    <h2>Sem vendas</h2>

                    <p>
                        Produtos sem saída no período selecionado.
                    </p>

                </div>

                <span class="contador-neutro">
                    <?= count($produtosSemVenda) ?>
                </span>

            </div>

            <?php if (empty($produtosSemVenda)): ?>

                <div class="estado-vazio-relatorio">
                    Todos os produtos tiveram venda no período.
                </div>

            <?php else: ?>

                <div class="lista-alertas-relatorio">

                    <?php foreach ($produtosSemVenda as $produto): ?>

                        <div class="item-alerta-relatorio">

                            <div>

                                <strong>
                                    <?= textoRelatorioProdutos($produto['nome'] ?? '') ?>
                                </strong>

                                <small>
                                    <?= textoRelatorioProdutos($produto['marca'] ?? 'Não informada') ?>
                                </small>

                            </div>

                            <span class="badge-neutro">
                                Estoque:
                                <?= (int) ($produto['estoque_atual'] ?? 0) ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </article>

    </section>

    <section class="cartao-relatorio relatorio-desempenho-produtos">

        <div class="cabecalho-cartao-relatorio">

            <div>

                <h2>Desempenho completo</h2>

                <p>
                    Resultado individual de todos os produtos ativos.
                </p>

            </div>

            <span class="contador-neutro">
                <?= count($produtos) ?> produtos
            </span>

        </div>

        <?php if (empty($produtos)): ?>

            <div class="estado-vazio-relatorio">
                Nenhum produto foi encontrado.
            </div>

        <?php else: ?>

            <div class="tabela-responsiva-relatorio">

                <table class="tabela-relatorio">

                    <thead>

                        <tr>
                            <th>Produto</th>
                            <th>Código</th>
                            <th>Estoque</th>
                            <th>Vendidos</th>
                            <th>Faturamento</th>
                            <th>Custo</th>
                            <th>Lucro</th>
                            <th>Margem</th>
                            <th>Situação</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($produtos as $produto): ?>

                            <tr>

                                <td>

                                    <strong>
                                        <?= textoRelatorioProdutos($produto['nome'] ?? '') ?>
                                    </strong>

                                    <small class="texto-secundario-tabela">
                                        <?= textoRelatorioProdutos($produto['marca'] ?? 'Não informada') ?>
                                    </small>

                                </td>

                                <td>
                                    <?= textoRelatorioProdutos($produto['codigo_barras'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= (int) ($produto['estoque_atual'] ?? 0) ?>
                                </td>

                                <td>
                                    <?= (int) ($produto['quantidade_vendida'] ?? 0) ?>
                                </td>

                                <td>
                                    <?= moedaRelatorioProdutos(
                                        (float) ($produto['faturamento'] ?? 0)
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaRelatorioProdutos(
                                        (float) ($produto['custo_total'] ?? 0)
                                    ) ?>
                                </td>

                                <td>

                                    <strong class="<?= ((float) ($produto['lucro'] ?? 0)) >= 0 ? 'texto-positivo' : 'texto-negativo' ?>">
                                        <?= moedaRelatorioProdutos(
                                            (float) ($produto['lucro'] ?? 0)
                                        ) ?>
                                    </strong>

                                </td>

                                <td>
                                    <?= percentualRelatorioProdutos(
                                        (float) ($produto['margem_lucro'] ?? 0)
                                    ) ?>
                                </td>

                                <td>

                                    <span class="badge-estoque <?= textoRelatorioProdutos($produto['situacao_estoque'] ?? 'normal') ?>">

                                        <?= textoRelatorioProdutos(
                                            $produto['situacao_estoque_nome']
                                            ?? 'Normal'
                                        ) ?>

                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </section>

</main>

<style>
    .subtitulo-pagina {
        display: block;
        margin-bottom: 4px;
        color: #159ee8;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    .cartao-relatorio {
        margin-bottom: 22px;
        padding: 22px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .05);
    }

    .cabecalho-cartao-relatorio {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
    }

    .cabecalho-cartao-relatorio h2 {
        margin: 0 0 5px;
        color: #172033;
        font-size: 18px;
    }

    .cabecalho-cartao-relatorio p {
        margin: 0;
        color: #718096;
        font-size: 13px;
    }

    .formulario-filtros-relatorio {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        align-items: end;
        gap: 16px;
    }

    .acoes-filtros-relatorio {
        display: flex;
        gap: 10px;
    }

    .botao-imprimir-relatorio {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 8px;
        background: #243044;
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
    }

    .grade-resumo-relatorio {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .cartao-resumo-relatorio {
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(15, 23, 42, .04);
    }

    .cartao-resumo-relatorio span {
        display: block;
        margin-bottom: 8px;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .cartao-resumo-relatorio strong {
        display: block;
        margin-bottom: 6px;
        color: #172033;
        font-size: 24px;
    }

    .cartao-resumo-relatorio small {
        color: #94a3b8;
        font-size: 11px;
    }

    .cartao-resumo-relatorio.destaque {
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .cartao-resumo-relatorio.destaque strong {
        color: #0284c7;
    }

    .cartao-resumo-relatorio.destaque-lucro {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .cartao-resumo-relatorio.destaque-lucro strong {
        color: #15803d;
    }

    .cartao-resumo-relatorio.alerta {
        border-color: #fed7aa;
        background: #fff7ed;
    }

    .cartao-resumo-relatorio.alerta strong {
        color: #c2410c;
    }

    .grade-rankings-relatorio {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .lista-ranking-relatorio,
    .lista-alertas-relatorio {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .item-ranking-relatorio,
    .item-alerta-relatorio {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border: 1px solid #edf2f7;
        border-radius: 10px;
        background: #f8fafc;
    }

    .item-alerta-relatorio {
        justify-content: space-between;
    }

    .posicao-ranking {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        flex: 0 0 30px;
        border-radius: 50%;
        background: #172033;
        color: #ffffff;
        font-size: 12px;
        font-weight: 900;
    }

    .dados-ranking {
        flex: 1;
        min-width: 0;
    }

    .dados-ranking strong,
    .item-alerta-relatorio strong {
        display: block;
        color: #172033;
        font-size: 13px;
    }

    .dados-ranking small,
    .item-alerta-relatorio small {
        color: #94a3b8;
        font-size: 11px;
    }

    .valor-ranking {
        text-align: right;
    }

    .valor-ranking strong {
        display: block;
        color: #159ee8;
        font-size: 13px;
    }

    .valor-ranking small {
        color: #94a3b8;
        font-size: 10px;
    }

    .contador-alerta,
    .contador-neutro {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        min-height: 30px;
        padding: 0 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
    }

    .contador-alerta {
        background: #ffedd5;
        color: #c2410c;
    }

    .contador-neutro {
        background: #e2e8f0;
        color: #475569;
    }

    .badge-estoque,
    .badge-neutro {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .badge-estoque.normal {
        background: #dcfce7;
        color: #166534;
    }

    .badge-estoque.estoque_baixo {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-estoque.sem_estoque {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-neutro {
        background: #e2e8f0;
        color: #475569;
    }

    .tabela-responsiva-relatorio {
        width: 100%;
        overflow-x: auto;
    }

    .tabela-relatorio {
        width: 100%;
        border-collapse: collapse;
    }

    .tabela-relatorio th,
    .tabela-relatorio td {
        padding: 12px 10px;
        border-bottom: 1px solid #edf2f7;
        text-align: left;
        vertical-align: middle;
        font-size: 12px;
        white-space: nowrap;
    }

    .tabela-relatorio th {
        color: #64748b;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .texto-secundario-tabela {
        display: block;
        margin-top: 3px;
        color: #94a3b8;
    }

    .texto-positivo {
        color: #15803d;
    }

    .texto-negativo {
        color: #b91c1c;
    }

    .estado-vazio-relatorio {
        padding: 26px;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        color: #64748b;
        text-align: center;
        font-size: 13px;
    }

    @media (max-width: 1200px) {
        .grade-resumo-relatorio {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .formulario-filtros-relatorio,
        .grade-rankings-relatorio {
            grid-template-columns: 1fr;
        }

        .acoes-filtros-relatorio {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 600px) {
        .grade-resumo-relatorio {
            grid-template-columns: 1fr;
        }

        .cartao-relatorio {
            padding: 16px;
        }

        .cabecalho-cartao-relatorio {
            flex-direction: column;
        }

        .acoes-filtros-relatorio {
            flex-direction: column;
        }

        .acoes-filtros-relatorio a,
        .acoes-filtros-relatorio button {
            width: 100%;
        }
    }
</style>


<style>
    /*
    |--------------------------------------------------------------------------
    | AJUSTES DE LEGIBILIDADE — RELATÓRIO DE PRODUTOS
    |--------------------------------------------------------------------------
    */

    .conteudo-principal {
        font-size: 15px;
    }

    .cabecalho-pagina h1 {
        font-size: 30px;
        line-height: 1.15;
    }

    .cabecalho-pagina p {
        font-size: 15px;
        line-height: 1.55;
        color: #64748b;
    }

    .subtitulo-pagina {
        font-size: 13px;
    }

    .cartao-relatorio {
        padding: 26px;
        border-radius: 16px;
    }

    .cabecalho-cartao-relatorio h2 {
        font-size: 21px;
        line-height: 1.25;
    }

    .cabecalho-cartao-relatorio p {
        font-size: 14px;
        line-height: 1.5;
        color: #64748b;
    }

    .formulario-filtros-relatorio label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 14px;
        font-weight: 800;
    }

    .formulario-filtros-relatorio input[type="date"] {
        width: 100%;
        min-height: 46px;
        padding: 0 13px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #ffffff;
        color: #172033;
        font-size: 14px;
        font-weight: 700;
    }

    .acoes-filtros-relatorio .botao-primario,
    .botao-imprimir-relatorio {
        min-height: 46px;
        padding: 0 19px;
        font-size: 14px;
    }

    .grade-resumo-relatorio {
        gap: 18px;
    }

    .cartao-resumo-relatorio {
        min-height: 132px;
        padding: 21px;
        border-radius: 15px;
    }

    .cartao-resumo-relatorio span {
        margin-bottom: 12px;
        font-size: 12px;
        letter-spacing: .35px;
    }

    .cartao-resumo-relatorio strong {
        margin-bottom: 9px;
        font-size: 29px;
        line-height: 1.1;
    }

    .cartao-resumo-relatorio small {
        font-size: 13px;
        line-height: 1.4;
        color: #64748b;
    }

    .grade-rankings-relatorio {
        gap: 20px;
    }

    .item-ranking-relatorio,
    .item-alerta-relatorio {
        min-height: 66px;
        padding: 14px;
        border-radius: 12px;
    }

    .posicao-ranking {
        width: 34px;
        height: 34px;
        flex-basis: 34px;
        font-size: 13px;
    }

    .dados-ranking strong,
    .item-alerta-relatorio strong {
        font-size: 15px;
        line-height: 1.35;
    }

    .dados-ranking small,
    .item-alerta-relatorio small {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        line-height: 1.35;
        color: #64748b;
    }

    .valor-ranking strong {
        font-size: 15px;
    }

    .valor-ranking small {
        font-size: 11px;
    }

    .contador-alerta,
    .contador-neutro {
        min-width: 34px;
        min-height: 34px;
        font-size: 12px;
    }

    .badge-estoque,
    .badge-neutro {
        padding: 7px 11px;
        font-size: 11px;
    }

    .tabela-responsiva-relatorio {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
    }

    .tabela-relatorio th,
    .tabela-relatorio td {
        padding: 14px 12px;
        font-size: 13px;
        line-height: 1.35;
    }

    .tabela-relatorio th {
        background: #f1f5f9;
        color: #334155;
        font-size: 11px;
        letter-spacing: .25px;
    }

    .tabela-relatorio tbody tr:hover {
        background: #f8fafc;
    }

    .texto-secundario-tabela {
        margin-top: 5px;
        color: #64748b;
        font-size: 12px;
    }

    .estado-vazio-relatorio {
        padding: 32px;
        font-size: 14px;
        line-height: 1.5;
    }

    @media (max-width: 1100px) {
        .grade-resumo-relatorio {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grade-rankings-relatorio {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .cabecalho-pagina h1 {
            font-size: 25px;
        }

        .grade-resumo-relatorio {
            grid-template-columns: 1fr;
        }

        .cartao-resumo-relatorio {
            min-height: auto;
        }

        .cartao-relatorio {
            padding: 18px;
        }
    }
</style>


<style>
    /* Correção da tabela de desempenho completo */
    .relatorio-desempenho-produtos {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .relatorio-desempenho-produtos > .cabecalho-cartao-relatorio {
        display: flex !important;
        width: 100% !important;
        margin-bottom: 20px !important;
    }

    .relatorio-desempenho-produtos .tabela-responsiva-relatorio {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        clear: both !important;
    }

    .relatorio-desempenho-produtos .tabela-relatorio {
        width: 100% !important;
        min-width: 980px !important;
        table-layout: auto !important;
    }

    .relatorio-desempenho-produtos .tabela-relatorio th:first-child,
    .relatorio-desempenho-produtos .tabela-relatorio td:first-child {
        min-width: 190px;
        white-space: normal !important;
    }

    .relatorio-desempenho-produtos .tabela-relatorio th:nth-child(2),
    .relatorio-desempenho-produtos .tabela-relatorio td:nth-child(2) {
        min-width: 115px;
    }

    .relatorio-desempenho-produtos .tabela-relatorio th,
    .relatorio-desempenho-produtos .tabela-relatorio td {
        white-space: nowrap;
        color: #263548;
    }

    .relatorio-desempenho-produtos .tabela-relatorio th {
        color: #334155;
        font-size: 11px;
    }

    .relatorio-desempenho-produtos .tabela-relatorio td {
        font-size: 13px;
    }
</style>


<style>
/*
|--------------------------------------------------------------------------
| CORREÇÃO DOS CARDS — RELATÓRIO DE PRODUTOS
|--------------------------------------------------------------------------
*/

/* Garante que o cabeçalho fique em cima e a lista embaixo */
.grade-rankings-relatorio > .cartao-relatorio {
    display: block !important;
    width: 100% !important;
    min-width: 0 !important;
    max-width: 100% !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
}

.grade-rankings-relatorio
> .cartao-relatorio
> .cabecalho-cartao-relatorio {
    display: flex !important;
    width: 100% !important;
    min-width: 0 !important;
    margin-bottom: 18px !important;
    box-sizing: border-box !important;
}

.grade-rankings-relatorio
> .cartao-relatorio
> .lista-ranking-relatorio,
.grade-rankings-relatorio
> .cartao-relatorio
> .lista-alertas-relatorio,
.grade-rankings-relatorio
> .cartao-relatorio
> .estado-vazio-relatorio {
    display: flex !important;
    width: 100% !important;
    min-width: 0 !important;
    max-width: 100% !important;
    clear: both !important;
    box-sizing: border-box !important;
}

.grade-rankings-relatorio
> .cartao-relatorio
> .estado-vazio-relatorio {
    display: block !important;
}

/* Mais vendidos e mais lucrativos */
.item-ranking-relatorio {
    display: grid !important;
    grid-template-columns: 34px minmax(0, 1fr) auto !important;
    align-items: center !important;
    gap: 12px !important;
    width: 100% !important;
    min-width: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
}

.dados-ranking {
    width: 100% !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.dados-ranking strong,
.dados-ranking small {
    display: block !important;
    width: 100% !important;
    min-width: 0 !important;
    overflow: hidden !important;
    white-space: nowrap !important;
    text-overflow: ellipsis !important;
}

.valor-ranking {
    min-width: 70px !important;
    flex: 0 0 auto !important;
    text-align: right !important;
    white-space: nowrap !important;
}

/* Estoque baixo e sem vendas */
.item-alerta-relatorio {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) auto !important;
    align-items: center !important;
    gap: 14px !important;
    width: 100% !important;
    min-width: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
}

.item-alerta-relatorio > div:first-child {
    width: 100% !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.item-alerta-relatorio > div:first-child strong,
.item-alerta-relatorio > div:first-child small {
    display: block !important;
    width: 100% !important;
    min-width: 0 !important;
    overflow: hidden !important;
    white-space: nowrap !important;
    text-overflow: ellipsis !important;
}

.item-alerta-relatorio .badge-estoque,
.item-alerta-relatorio .badge-neutro {
    flex: 0 0 auto !important;
    align-self: center !important;
    justify-self: end !important;
    white-space: nowrap !important;
}

/* Evita que os títulos dos cards fiquem espremidos */
.grade-rankings-relatorio
.cabecalho-cartao-relatorio > div:first-child {
    flex: 1 1 auto !important;
    min-width: 0 !important;
}

.grade-rankings-relatorio
.cabecalho-cartao-relatorio h2 {
    white-space: normal !important;
}

@media (max-width: 850px) {
    .grade-rankings-relatorio {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>