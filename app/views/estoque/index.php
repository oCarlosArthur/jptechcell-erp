<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

/*
 * Dados dos cartões de resumo.
 */
$totalProdutos = count($produtos);

$totalNormais = 0;
$totalBaixos = 0;
$totalZerados = 0;

$quantidadeTotalEstoque = 0;
$valorTotalCusto = 0;
$valorTotalVenda = 0;

foreach ($produtos as $produto) {

    $quantidade = (int) $produto['quantidade_estoque'];
    $estoqueMinimo = (int) $produto['estoque_minimo'];

    $precoCusto = (float) $produto['preco_custo'];
    $precoVenda = (float) $produto['preco_venda'];

    $quantidadeTotalEstoque += $quantidade;

    $valorTotalCusto += $precoCusto * $quantidade;
    $valorTotalVenda += $precoVenda * $quantidade;

    if ($quantidade === 0) {
        $totalZerados++;
    } elseif ($quantidade <= $estoqueMinimo) {
        $totalBaixos++;
    } else {
        $totalNormais++;
    }
}

?>

<style>
    /*
     * Impede a página de ultrapassar
     * a largura disponível.
     */
    .conteudo {
        min-width: 0;
        overflow-x: hidden;
    }

    /*
     * Mantém quatro cartões na primeira linha.
     */
    .resumo-estoque {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .cartao-estoque {
        min-width: 0;
        padding: 18px;
        border-radius: 14px;
        background: rgba(34, 60, 91, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .cartao-estoque span {
        display: block;
        margin-bottom: 7px;
        color: #b8c5d6;
        font-size: 13px;
    }

    .cartao-estoque strong {
        display: block;
        color: #ffffff;
        font-size: 25px;
    }

    .cartao-estoque small {
        display: block;
        margin-top: 5px;
        color: #aebed0;
        font-size: 12px;
        line-height: 1.3;
    }

    /*
     * Mantém dois cartões na segunda linha.
     */
    .resumo-valores {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .cartao-valor {
        min-width: 0;
        padding: 18px;
        border-radius: 14px;
        background: rgba(34, 60, 91, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .cartao-valor span {
        display: block;
        margin-bottom: 7px;
        color: #b8c5d6;
        font-size: 13px;
    }

    .cartao-valor strong {
        color: #20e58f;
        font-size: 24px;
    }

    /*
     * Painel principal da tabela.
     */
    .painel {
        min-width: 0;
        overflow: hidden;
    }

    .barra-pesquisa {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .barra-pesquisa input[type="text"] {
        min-width: 0;
    }

    /*
     * A tabela fica limitada ao espaço
     * disponível, sem rolagem horizontal.
     */
    .tabela-responsiva {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    .tabela-produtos {
        width: 100%;
        max-width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .tabela-produtos th,
    .tabela-produtos td {
        padding: 11px 6px;
        font-size: 12px;
        line-height: 1.25;
        vertical-align: middle;
        overflow: hidden;
    }

    .texto-limitado {
        display:block;
        width:100%;
        overflow:hidden;
        white-space:nowrap;
        text-overflow:ellipsis;
    }

    .tabela-produtos th {
        font-size: 11px;
        white-space: normal;
    }

    /*
     * Larguras proporcionais das colunas.
     */
    .tabela-produtos th:nth-child(1),
    .tabela-produtos td:nth-child(1) {
        width: 10%;
    }

    .tabela-produtos th:nth-child(2),
    .tabela-produtos td:nth-child(2) {
        width: 24%;
    }

    .tabela-produtos th:nth-child(3),
    .tabela-produtos td:nth-child(3) {
        width: 12%;
    }

    .tabela-produtos th:nth-child(4),
    .tabela-produtos td:nth-child(4) {
        width: 8%;
        text-align: center;
    }

    .tabela-produtos th:nth-child(5),
    .tabela-produtos td:nth-child(5) {
        width: 7%;
        text-align: center;
    }

    .tabela-produtos th:nth-child(6),
    .tabela-produtos td:nth-child(6) {
        width: 11%;
        text-align: center;
    }

    .tabela-produtos th:nth-child(7),
    .tabela-produtos td:nth-child(7) {
        width: 12%;
    }

    .tabela-produtos th:nth-child(8),
    .tabela-produtos td:nth-child(8) {
        width: 12%;
    }

    .tabela-produtos th:nth-child(9),
    .tabela-produtos td:nth-child(9) {
        width: 13%;
        text-align: center;
    }

    .link-produto {
        display: inline-block;
        max-width: 100%;
        line-height: 1.25;
    }

    .quantidade-estoque {
        font-size: 17px;
        font-weight: 700;
    }

    .quantidade-normal {
        color: #29eb92;
    }

    .quantidade-baixa {
        color: #ffcc4d;
    }

    .quantidade-zerada {
        color: #ff6b6b;
    }

    .status-estoque {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        max-width: 100%;
        padding: 5px 7px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.15;
        text-align: center;
        box-sizing: border-box;
    }

    .status-normal {
        color: #29eb92;
        background: rgba(41, 235, 146, 0.12);
        border: 1px solid rgba(41, 235, 146, 0.35);
    }

    .status-baixo {
        color: #ffcc4d;
        background: rgba(255, 204, 77, 0.12);
        border: 1px solid rgba(255, 204, 77, 0.35);
    }

    .status-zerado {
        color: #ff6b6b;
        background: rgba(255, 107, 107, 0.12);
        border: 1px solid rgba(255, 107, 107, 0.35);
    }

    .botao-movimentar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        max-width: 105px;
        min-width: 0;
        padding: 8px 5px;
        border-radius: 8px;
        box-sizing: border-box;
        background: #159ee8;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.1;
        text-align: center;
        text-decoration: none;
    }

    .botao-movimentar:hover {
        filter: brightness(1.1);
    }

    .rodape-estoque {
        margin-top: 16px;
        color: #b8c5d6;
        font-size: 13px;
    }

    .rodape-estoque strong {
        color: #ffffff;
    }

    /*
     * Ajustes para telas menores.
     */
    @media (max-width: 1200px) {
        .resumo-estoque {
            gap: 10px;
        }

        .cartao-estoque,
        .cartao-valor {
            padding: 15px;
        }

        .cartao-estoque strong {
            font-size: 22px;
        }

        .cartao-valor strong {
            font-size: 21px;
        }

        .tabela-produtos th,
        .tabela-produtos td {
            padding: 9px 4px;
            font-size: 11px;
        }

        .tabela-produtos th {
            font-size: 10px;
        }

        .status-estoque {
            padding: 4px 5px;
            font-size: 10px;
        }

        .botao-movimentar {
            padding: 7px 3px;
            font-size: 10px;
        }
    }

    @media (max-width: 900px) {
        .resumo-estoque {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        /*
         * Em telas muito pequenas, escondemos
         * marca e estoque mínimo para preservar
         * a leitura sem criar rolagem lateral.
         */
        .tabela-produtos th:nth-child(3),
        .tabela-produtos td:nth-child(3),
        .tabela-produtos th:nth-child(5),
        .tabela-produtos td:nth-child(5) {
            display: none;
        }

        .tabela-produtos th:nth-child(1),
        .tabela-produtos td:nth-child(1) {
            width: 13%;
        }

        .tabela-produtos th:nth-child(2),
        .tabela-produtos td:nth-child(2) {
            width: 23%;
        }

        .tabela-produtos th:nth-child(4),
        .tabela-produtos td:nth-child(4) {
            width: 10%;
        }

        .tabela-produtos th:nth-child(6),
        .tabela-produtos td:nth-child(6) {
            width: 14%;
        }

        .tabela-produtos th:nth-child(7),
        .tabela-produtos td:nth-child(7),
        .tabela-produtos th:nth-child(8),
        .tabela-produtos td:nth-child(8) {
            width: 15%;
        }

        .tabela-produtos th:nth-child(9),
        .tabela-produtos td:nth-child(9) {
            width: 10%;
        }
    }

    @media (max-width: 700px) {
        .resumo-estoque,
        .resumo-valores {
            grid-template-columns: 1fr;
        }
    }
</style>

<main class="conteudo">

    <header class="topo">
        <div>
            <h1>Estoque</h1>

            <p>
                Acompanhe as quantidades e a situação do estoque da loja.
            </p>
        </div>
    </header>

    <section class="resumo-estoque">

        <div class="cartao-estoque">
            <span>Produtos cadastrados</span>

            <strong>
                <?= $totalProdutos ?>
            </strong>

            <small>
                Produtos ativos no sistema
            </small>
        </div>

        <div class="cartao-estoque">
            <span>Estoque normal</span>

            <strong>
                <?= $totalNormais ?>
            </strong>

            <small>
                Acima do estoque mínimo
            </small>
        </div>

        <div class="cartao-estoque">
            <span>Estoque baixo</span>

            <strong>
                <?= $totalBaixos ?>
            </strong>

            <small>
                Precisa de atenção
            </small>
        </div>

        <div class="cartao-estoque">
            <span>Produtos zerados</span>

            <strong>
                <?= $totalZerados ?>
            </strong>

            <small>
                Sem unidades disponíveis
            </small>
        </div>

    </section>

    <section class="resumo-valores">

        <div class="cartao-valor">
            <span>Valor investido em estoque</span>

            <strong>
                R$ <?= number_format(
                    $valorTotalCusto,
                    2,
                    ',',
                    '.'
                ) ?>
            </strong>
        </div>

        <div class="cartao-valor">
            <span>Valor potencial de venda</span>

            <strong>
                R$ <?= number_format(
                    $valorTotalVenda,
                    2,
                    ',',
                    '.'
                ) ?>
            </strong>
        </div>

    </section>

    <section class="painel">

        <form
            class="barra-pesquisa"
            method="GET"
            action="index.php"
        >
            <input
                type="hidden"
                name="rota"
                value="estoque"
            >

            <input
                type="text"
                name="pesquisa"
                value="<?= htmlspecialchars($pesquisa ?? '') ?>"
                placeholder="Pesquise por produto, marca ou código"
            >

            <button type="submit">
                Pesquisar
            </button>
        </form>

        <div class="tabela-responsiva">

            <table class="tabela-produtos">

                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Produto</th>
                        <th>Marca</th>
                        <th>Qtd.</th>
                        <th>Mín.</th>
                        <th>Status</th>
                        <th>Custo total</th>
                        <th>Venda total</th>
                        <th>Ação</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($produtos)): ?>

                        <tr>
                            <td
                                colspan="9"
                                class="tabela-vazia"
                            >
                                Nenhum produto encontrado.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($produtos as $produto): ?>

                            <?php

                            $quantidade =
                                (int) $produto['quantidade_estoque'];

                            $estoqueMinimo =
                                (int) $produto['estoque_minimo'];

                            $custoTotalProduto =
                                (float) $produto['preco_custo']
                                * $quantidade;

                            $vendaTotalProduto =
                                (float) $produto['preco_venda']
                                * $quantidade;

                            if ($quantidade === 0) {

                                $nomeStatus = 'Sem estoque';
                                $classeStatus = 'status-zerado';
                                $classeQuantidade =
                                    'quantidade-zerada';

                            } elseif (
                                $quantidade <= $estoqueMinimo
                            ) {

                                $nomeStatus = 'Estoque baixo';
                                $classeStatus = 'status-baixo';
                                $classeQuantidade =
                                    'quantidade-baixa';

                            } else {

                                $nomeStatus = 'Normal';
                                $classeStatus = 'status-normal';
                                $classeQuantidade =
                                    'quantidade-normal';
                            }

                            ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars(
                                            $produto['codigo_barras']
                                        ) ?>
                                    </strong>
                                </td>

                                <td title="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                    <a
                                        class="link-produto texto-limitado"
                                        href="index.php?rota=produto-editar&id=<?= (int) $produto['id'] ?>"
                                    >
                                        <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </td>

                                <td title="<?= htmlspecialchars($produto['marca'] ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                                    <span class="texto-limitado"><?= htmlspecialchars($produto['marca'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                                </td>

                                <td>
                                    <span
                                        class="quantidade-estoque <?= $classeQuantidade ?>"
                                    >
                                        <?= $quantidade ?>
                                    </span>
                                </td>

                                <td>
                                    <?= $estoqueMinimo ?>
                                </td>

                                <td>
                                    <span
                                        class="status-estoque <?= $classeStatus ?>"
                                    >
                                        <?= htmlspecialchars(
                                            $nomeStatus
                                        ) ?>
                                    </span>
                                </td>

                                <td>
                                    R$ <?= number_format(
                                        $custoTotalProduto,
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    R$ <?= number_format(
                                        $vendaTotalProduto,
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    <a
                                        class="botao-movimentar"
                                        href="index.php?rota=produto-movimentar&id=<?= (int) $produto['id'] ?>"
                                    >
                                        Movimentar
                                    </a>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <div class="rodape-estoque">
            Total de unidades armazenadas:

            <strong>
                <?= $quantidadeTotalEstoque ?>
            </strong>
        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>