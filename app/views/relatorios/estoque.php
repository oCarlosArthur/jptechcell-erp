<?php

$produtos = $dados['produtos'] ?? [];
$resumo = $dados['resumo'] ?? [];
$estoqueBaixo = $dados['estoque_baixo'] ?? [];
$maiorValorEstoque = $dados['maior_valor_estoque'] ?? [];

function escaparRelatorioEstoque(mixed $valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

function moedaRelatorioEstoque(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
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

            <h1>Relatório de Estoque</h1>

            <p>
                Consulte quantidades, produtos em falta,
                estoque mínimo e valores armazenados.
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
            <?= escaparRelatorioEstoque($_SESSION['erro']) ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <section class="acoes-relatorio-estoque">
        <div>
            <h2>Posição atual do estoque</h2>
            <p>
                Dados atualizados conforme o cadastro dos produtos.
            </p>
        </div>

        <a
            href="index.php?rota=relatorios-imprimir-estoque"
            target="_blank"
            class="botao-imprimir-relatorio"
        >
            Imprimir relatório
        </a>
    </section>

    <section class="grade-resumo-estoque">

        <article class="card-resumo-estoque">
            <span>Produtos cadastrados</span>
            <strong>
                <?= (int) ($resumo['total_produtos'] ?? 0) ?>
            </strong>
            <small>Produtos ativos</small>
        </article>

        <article class="card-resumo-estoque">
            <span>Unidades em estoque</span>
            <strong>
                <?= (int) ($resumo['total_unidades'] ?? 0) ?>
            </strong>
            <small>Soma das quantidades</small>
        </article>

        <article class="card-resumo-estoque alerta">
            <span>Estoque baixo</span>
            <strong>
                <?= (int) (
                    $resumo['produtos_estoque_baixo']
                    ?? 0
                ) ?>
            </strong>
            <small>Inclui produtos zerados</small>
        </article>

        <article class="card-resumo-estoque perigo">
            <span>Sem estoque</span>
            <strong>
                <?= (int) (
                    $resumo['produtos_sem_estoque']
                    ?? 0
                ) ?>
            </strong>
            <small>Produtos indisponíveis</small>
        </article>

        <article class="card-resumo-estoque azul">
            <span>Valor pelo custo</span>
            <strong>
                <?= moedaRelatorioEstoque(
                    (float) (
                        $resumo['valor_estoque_custo']
                        ?? 0
                    )
                ) ?>
            </strong>
            <small>Capital investido</small>
        </article>

        <article class="card-resumo-estoque verde">
            <span>Valor pela venda</span>
            <strong>
                <?= moedaRelatorioEstoque(
                    (float) (
                        $resumo['valor_estoque_venda']
                        ?? 0
                    )
                ) ?>
            </strong>
            <small>Potencial de faturamento</small>
        </article>

        <article class="card-resumo-estoque verde">
            <span>Lucro potencial</span>
            <strong>
                <?= moedaRelatorioEstoque(
                    (float) (
                        $resumo['lucro_potencial']
                        ?? 0
                    )
                ) ?>
            </strong>
            <small>Venda de todo o estoque</small>
        </article>

    </section>

    <section class="grade-destaques-estoque">

        <article class="cartao-relatorio-estoque">
            <div class="titulo-cartao-estoque">
                <div>
                    <h2>Produtos para reposição</h2>
                    <p>
                        Estoque igual ou abaixo do mínimo.
                    </p>
                </div>

                <span class="contador-alerta-estoque">
                    <?= count($estoqueBaixo) ?>
                </span>
            </div>

            <?php if (empty($estoqueBaixo)): ?>
                <div class="estado-vazio-estoque">
                    Nenhum produto precisa de reposição.
                </div>
            <?php else: ?>
                <div class="lista-estoque">
                    <?php foreach ($estoqueBaixo as $produto): ?>
                        <div class="item-lista-estoque">
                            <div class="dados-item-estoque">
                                <strong
                                    title="<?= escaparRelatorioEstoque(
                                        $produto['nome'] ?? ''
                                    ) ?>"
                                >
                                    <?= escaparRelatorioEstoque(
                                        $produto['nome'] ?? ''
                                    ) ?>
                                </strong>

                                <small>
                                    Mínimo:
                                    <?= (int) (
                                        $produto['estoque_minimo']
                                        ?? 0
                                    ) ?>
                                </small>
                            </div>

                            <span class="badge-estoque <?= escaparRelatorioEstoque(
                                $produto['situacao_estoque']
                                ?? 'normal'
                            ) ?>">
                                <?= (int) (
                                    $produto['quantidade_estoque']
                                    ?? 0
                                ) ?>
                                unidades
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

        <article class="cartao-relatorio-estoque">
            <div class="titulo-cartao-estoque">
                <div>
                    <h2>Maior valor armazenado</h2>
                    <p>
                        Produtos com maior capital investido.
                    </p>
                </div>
            </div>

            <?php if (empty($maiorValorEstoque)): ?>
                <div class="estado-vazio-estoque">
                    Nenhum produto cadastrado.
                </div>
            <?php else: ?>
                <div class="lista-estoque">
                    <?php foreach ($maiorValorEstoque as $indice => $produto): ?>
                        <div class="item-lista-estoque">
                            <span class="posicao-estoque">
                                <?= $indice + 1 ?>
                            </span>

                            <div class="dados-produto-estoque">
                                <strong
                                    title="<?= escaparRelatorioEstoque(
                                        $produto['nome'] ?? ''
                                    ) ?>"
                                >
                                    <?= escaparRelatorioEstoque(
                                        $produto['nome'] ?? ''
                                    ) ?>
                                </strong>

                                <small>
                                    <?= (int) (
                                        $produto['quantidade_estoque']
                                        ?? 0
                                    ) ?>
                                    unidades
                                </small>
                            </div>

                            <strong class="valor-destaque-estoque">
                                <?= moedaRelatorioEstoque(
                                    (float) (
                                        $produto['valor_estoque_custo']
                                        ?? 0
                                    )
                                ) ?>
                            </strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

    </section>

    <section class="cartao-relatorio-estoque tabela-completa-estoque">

        <div class="titulo-cartao-estoque">
            <div>
                <h2>Estoque completo</h2>
                <p>
                    Relação de todos os produtos ativos.
                </p>
            </div>

            <span class="contador-neutro-estoque">
                <?= count($produtos) ?> produtos
            </span>
        </div>

        <?php if (empty($produtos)): ?>
            <div class="estado-vazio-estoque">
                Nenhum produto encontrado.
            </div>
        <?php else: ?>
            <div class="tabela-responsiva-estoque">
                <table class="tabela-estoque">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Código</th>
                            <th>Estoque</th>
                            <th>Mínimo</th>
                            <th>Custo unitário</th>
                            <th>Venda unitária</th>
                            <th>Valor em custo</th>
                            <th>Valor em venda</th>
                            <th>Situação</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td
                                    class="coluna-produto-estoque"
                                    title="<?= escaparRelatorioEstoque(
                                        $produto['nome'] ?? ''
                                    ) ?>"
                                >
                                    <strong>
                                        <?= escaparRelatorioEstoque(
                                            $produto['nome'] ?? ''
                                        ) ?>
                                    </strong>

                                    <small
                                        title="<?= escaparRelatorioEstoque(
                                            $produto['marca']
                                            ?? 'Não informada'
                                        ) ?>"
                                    >
                                        <?= escaparRelatorioEstoque(
                                            $produto['marca']
                                            ?? 'Não informada'
                                        ) ?>
                                    </small>
                                </td>

                                <td>
                                    <?= escaparRelatorioEstoque(
                                        $produto['codigo_etiqueta']
                                        ?: (
                                            $produto['codigo_barras']
                                            ?? '-'
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= (int) (
                                        $produto['quantidade_estoque']
                                        ?? 0
                                    ) ?>
                                </td>

                                <td>
                                    <?= (int) (
                                        $produto['estoque_minimo']
                                        ?? 0
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaRelatorioEstoque(
                                        (float) (
                                            $produto['preco_custo']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaRelatorioEstoque(
                                        (float) (
                                            $produto['preco_venda']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaRelatorioEstoque(
                                        (float) (
                                            $produto['valor_estoque_custo']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaRelatorioEstoque(
                                        (float) (
                                            $produto['valor_estoque_venda']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <span class="badge-estoque <?= escaparRelatorioEstoque(
                                        $produto['situacao_estoque']
                                        ?? 'normal'
                                    ) ?>">
                                        <?= escaparRelatorioEstoque(
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
            </div>
        <?php endif; ?>

    </section>

</main>

<style>
    .subtitulo-pagina {
        display: block;
        margin-bottom: 4px;
        color: #159ee8;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .acoes-relatorio-estoque,
    .cartao-relatorio-estoque {
        margin-bottom: 22px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .05);
    }

    .acoes-relatorio-estoque {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .acoes-relatorio-estoque h2,
    .titulo-cartao-estoque h2 {
        margin: 0 0 5px;
        color: #172033;
        font-size: 20px;
    }

    .acoes-relatorio-estoque p,
    .titulo-cartao-estoque p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .botao-imprimir-relatorio {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 18px;
        border-radius: 9px;
        background: #243044;
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
    }

    .grade-resumo-estoque {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 17px;
        margin-bottom: 22px;
    }

    .card-resumo-estoque {
        min-height: 125px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
    }

    .card-resumo-estoque span {
        display: block;
        margin-bottom: 11px;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .card-resumo-estoque strong {
        display: block;
        margin-bottom: 8px;
        color: #172033;
        font-size: 27px;
    }

    .card-resumo-estoque small {
        color: #64748b;
        font-size: 12px;
    }

    .card-resumo-estoque.azul {
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .card-resumo-estoque.azul strong {
        color: #0284c7;
    }

    .card-resumo-estoque.verde {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .card-resumo-estoque.verde strong {
        color: #15803d;
    }

    .card-resumo-estoque.alerta {
        border-color: #fed7aa;
        background: #fff7ed;
    }

    .card-resumo-estoque.alerta strong {
        color: #c2410c;
    }

    .card-resumo-estoque.perigo {
        border-color: #fecaca;
        background: #fef2f2;
    }

    .card-resumo-estoque.perigo strong {
        color: #b91c1c;
    }

    .grade-destaques-estoque {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .titulo-cartao-estoque {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 18px;
    }

    .lista-estoque {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .item-lista-estoque {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 64px;
        padding: 13px;
        border: 1px solid #edf2f7;
        border-radius: 11px;
        background: #f8fafc;
    }

    .item-lista-estoque strong {
        display: block;
        color: #172033;
        font-size: 14px;
    }

    .item-lista-estoque small {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
    }

    .posicao-estoque {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        border-radius: 50%;
        background: #172033;
        color: #ffffff;
        font-size: 12px;
        font-weight: 900;
    }

    .dados-produto-estoque {
        flex: 1;
    }

    .valor-destaque-estoque {
        color: #159ee8 !important;
    }

    .contador-alerta-estoque,
    .contador-neutro-estoque {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        min-height: 34px;
        padding: 0 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
    }

    .contador-alerta-estoque {
        background: #ffedd5;
        color: #c2410c;
    }

    .contador-neutro-estoque {
        background: #e2e8f0;
        color: #475569;
    }

    .badge-estoque {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 11px;
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

    .tabela-responsiva-estoque {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
    }

    .tabela-estoque {
        width: 100%;
        min-width: 1120px;
        border-collapse: collapse;
    }

    .tabela-estoque th,
    .tabela-estoque td {
        padding: 13px 11px;
        border-bottom: 1px solid #edf2f7;
        text-align: left;
        vertical-align: middle;
        font-size: 13px;
        white-space: nowrap;
    }

    .tabela-estoque th {
        background: #f1f5f9;
        color: #334155;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .tabela-estoque td small {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 11px;
    }

    .estado-vazio-estoque {
        padding: 28px;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        color: #64748b;
        text-align: center;
        font-size: 14px;
    }

    /* CORREÇÃO DA TABELA DO RELATÓRIO DE ESTOQUE */

.tabela-estoque {
    background: #ffffff;
}

.tabela-estoque tbody tr {
    background: #ffffff;
}

.tabela-estoque tbody tr:nth-child(even) {
    background: #f8fafc;
}

.tabela-estoque td {
    color: #1e293b !important;
    font-size: 13px;
    font-weight: 600;
    opacity: 1 !important;
}

.tabela-estoque td strong {
    color: #0f172a !important;
    font-size: 14px;
    font-weight: 800;
    opacity: 1 !important;
}

.tabela-estoque td small {
    color: #64748b !important;
    font-size: 12px;
    font-weight: 500;
    opacity: 1 !important;
}

.tabela-estoque th {
    color: #334155 !important;
    background: #e2e8f0 !important;
    font-weight: 900;
    opacity: 1 !important;
}

.tabela-estoque .badge-estoque.normal {
    background: #dcfce7 !important;
    color: #166534 !important;
}

.tabela-estoque .badge-estoque.estoque_baixo {
    background: #fef3c7 !important;
    color: #92400e !important;
}

.tabela-estoque .badge-estoque.sem_estoque {
    background: #fee2e2 !important;
    color: #991b1b !important;
}

    @media (max-width: 1200px) {
        .grade-resumo-estoque {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .grade-destaques-estoque {
            grid-template-columns: 1fr;
        }

        .acoes-relatorio-estoque {
            align-items: stretch;
            flex-direction: column;
        }
    }

    @media (max-width: 600px) {
        .grade-resumo-estoque {
            grid-template-columns: 1fr;
        }
    }

    /* Ajustes para nomes longos */
    .cartao-relatorio-estoque,
    .lista-estoque,
    .item-lista-estoque {
        min-width: 0;
        max-width: 100%;
    }

    .item-lista-estoque {
        overflow: hidden;
    }

    .dados-item-estoque,
    .dados-produto-estoque {
        flex: 1 1 auto;
        min-width: 0;
        overflow: hidden;
    }

    .dados-item-estoque strong,
    .dados-item-estoque small,
    .dados-produto-estoque strong,
    .dados-produto-estoque small {
        display: block;
        width: 100%;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .item-lista-estoque .badge-estoque,
    .valor-destaque-estoque,
    .posicao-estoque {
        flex: 0 0 auto;
        white-space: nowrap;
    }

    .tabela-estoque {
        table-layout: fixed;
    }

    .tabela-estoque th:first-child,
    .tabela-estoque td:first-child {
        width: 230px;
    }

    .coluna-produto-estoque {
        overflow: hidden;
    }

    .coluna-produto-estoque strong,
    .coluna-produto-estoque small {
        display: block;
        width: 100%;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>