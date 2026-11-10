<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sucessoProduto = $_SESSION['sucesso_produto'] ?? null;
unset($_SESSION['sucesso_produto']);

?>

<main class="conteudo">

    <header class="topo">
        <div>
            <h1>Produtos</h1>
            <p>Controle os produtos e o estoque da loja.</p>
        </div>

        <a
            class="botao-principal"
            href="index.php?rota=produto-novo"
        >
            + Novo produto
        </a>
    </header>

    <?php if ($sucessoProduto): ?>
        <div class="mensagem sucesso">
            <?= htmlspecialchars($sucessoProduto) ?>
        </div>
    <?php endif; ?>

    <section class="painel">

        <form class="barra-pesquisa" method="GET">
            <input
                type="hidden"
                name="rota"
                value="produtos"
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
                        <th>Custo</th>
                        <th>Venda</th>
                        <th>Estoque</th>
                        <th>Alerta</th>
                        <th>Cadastrado em</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($produtos)): ?>
                        <tr>
                            <td colspan="8" class="tabela-vazia">
                                Nenhum produto encontrado.
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php foreach ($produtos as $produto): ?>

                            <?php
                            $estoqueBaixo =
                                (int) $produto['quantidade_estoque']
                                <=
                                (int) $produto['estoque_minimo'];
                            ?>

                            <tr class="<?= $estoqueBaixo
                                ? 'linha-estoque-baixo'
                                : '' ?>">

                                <td>
                                    <div class="codigo-produto">
                                        <strong>
                                            <?= htmlspecialchars($produto['codigo_barras']) ?>
                                        </strong>

                                        <button
                                            type="button"
                                            class="botao-etiqueta"
                                            onclick="abrirEtiqueta(
                                                '<?= htmlspecialchars(
                                                    $produto['codigo_barras'],
                                                    ENT_QUOTES
                                                ) ?>',
                                                '<?= htmlspecialchars(
                                                    $produto['nome'],
                                                    ENT_QUOTES
                                                ) ?>',
                                                '<?= number_format(
                                                    (float) $produto['preco_venda'],
                                                    2,
                                                    '.',
                                                    ''
                                                ) ?>'
                                            )"
                                        >
                                            Imprimir etiqueta
                                        </button>
                                    </div>
                                </td>

                                <td
                                    class="coluna-produto"
                                    title="<?= htmlspecialchars(
                                        $produto['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >
                                    <a
                                        class="link-produto texto-limitado"
                                        href="index.php?rota=produto-editar&id=<?= (int) $produto['id'] ?>"
                                    >
                                        <?= htmlspecialchars(
                                            $produto['nome'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </a>
                                </td>

                                <td
                                    class="coluna-marca"
                                    title="<?= htmlspecialchars(
                                        $produto['marca'] ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >
                                    <span class="texto-limitado">
                                        <?= htmlspecialchars(
                                            $produto['marca'] ?? '-',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </td>

                                <td>
                                    R$ <?= number_format(
                                        (float) $produto['preco_custo'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    R$ <?= number_format(
                                        (float) $produto['preco_venda'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    <?= (int) $produto[
                                        'quantidade_estoque'
                                    ] ?>
                                </td>

                                <td>
                                    <?php if ($estoqueBaixo): ?>
                                        <span class="alerta-estoque">
                                            Estoque baixo
                                        </span>
                                    <?php else: ?>
                                        Avisar em
                                        <?= (int) $produto[
                                            'estoque_minimo'
                                        ] ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $produto['criado_em']
                                        )
                                    ) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

<div id="modal-etiqueta" class="modal-etiqueta" aria-hidden="true">

    <div class="modal-etiqueta-fundo" onclick="fecharEtiqueta()"></div>

    <div class="modal-etiqueta-caixa">

        <div class="modal-etiqueta-topo">
            <div>
                <h2>Etiqueta do produto</h2>
                <p>Confira antes de imprimir.</p>
            </div>

            <button
                type="button"
                class="fechar-etiqueta"
                onclick="fecharEtiqueta()"
                aria-label="Fechar"
            >
                ×
            </button>
        </div>

        <div class="visualizacao-etiqueta">

            <div id="area-impressao-etiqueta" class="etiqueta">

                <strong class="etiqueta-loja">
                    JP TechCell
                </strong>

                <span id="etiqueta-produto"></span>

                <strong id="etiqueta-preco"></strong>

                <svg id="codigo-barras-svg"></svg>

            </div>

        </div>

        <div class="modal-etiqueta-acoes">
            <button
                type="button"
                class="botao-cancelar-modal"
                onclick="fecharEtiqueta()"
            >
                Fechar
            </button>

            <button
                type="button"
                class="botao-imprimir-etiqueta"
                onclick="imprimirEtiqueta()"
            >
                Imprimir etiqueta
            </button>
        </div>

    </div>

</div>

<style>
    .tabela-responsiva {
        width: 100%;
        overflow-x: auto;
    }

    .tabela-produtos {
        width: 100%;
        min-width: 1180px;
        table-layout: fixed;
    }

    .tabela-produtos th,
    .tabela-produtos td {
        overflow: hidden;
        vertical-align: middle;
    }

    .tabela-produtos th:nth-child(1),
    .tabela-produtos td:nth-child(1) {
        width: 145px;
    }

    .tabela-produtos th:nth-child(2),
    .tabela-produtos td:nth-child(2) {
        width: 270px;
    }

    .tabela-produtos th:nth-child(3),
    .tabela-produtos td:nth-child(3) {
        width: 150px;
    }

    .tabela-produtos th:nth-child(4),
    .tabela-produtos td:nth-child(4),
    .tabela-produtos th:nth-child(5),
    .tabela-produtos td:nth-child(5) {
        width: 105px;
        white-space: nowrap;
    }

    .tabela-produtos th:nth-child(6),
    .tabela-produtos td:nth-child(6) {
        width: 85px;
        text-align: center;
    }

    .tabela-produtos th:nth-child(7),
    .tabela-produtos td:nth-child(7) {
        width: 135px;
    }

    .tabela-produtos th:nth-child(8),
    .tabela-produtos td:nth-child(8) {
        width: 145px;
        white-space: nowrap;
    }

    .coluna-produto,
    .coluna-marca {
        min-width: 0;
    }

    .texto-limitado {
        display: block;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .link-produto.texto-limitado {
        max-width: 100%;
    }

    @media (max-width: 900px) {
        .tabela-produtos {
            min-width: 1080px;
        }
    }
</style>

<script
    src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.12.3/JsBarcode.all.min.js"
></script>

<script>
function abrirEtiqueta(codigo, produto, preco) {
    const modal = document.getElementById('modal-etiqueta');

    document.getElementById('etiqueta-produto').textContent = produto;

    document.getElementById('etiqueta-preco').textContent =
        'R$ ' + Number(preco).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

    JsBarcode('#codigo-barras-svg', codigo, {
        format: 'CODE128',
        width: 2,
        height: 55,
        displayValue: true,
        fontSize: 13,
        margin: 4
    });

    modal.classList.add('aberto');
    modal.setAttribute('aria-hidden', 'false');

    document.body.classList.add('modal-aberto');
}

function fecharEtiqueta() {
    const modal = document.getElementById('modal-etiqueta');

    modal.classList.remove('aberto');
    modal.setAttribute('aria-hidden', 'true');

    document.body.classList.remove('modal-aberto');
}

document.addEventListener('keydown', function(evento) {
    if (evento.key === 'Escape') {
        fecharEtiqueta();
    }
});

function imprimirEtiqueta() {
    const conteudo = document
        .getElementById('area-impressao-etiqueta')
        .innerHTML;

    const janela = window.open('', '_blank', 'width=500,height=500');

    if (!janela) {
        alert('O navegador bloqueou a janela de impressão.');
        return;
    }

    janela.document.write(`
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <title>Etiqueta do produto</title>

            <style>
                @page {
                    size: 50mm 30mm;
                    margin: 0;
                }

                html,
                body {
                    width: 50mm;
                    height: 30mm;
                    margin: 0;
                    padding: 0;
                }

                body {
                    font-family: Arial, sans-serif;
                }

                .etiqueta {
                    width: 50mm;
                    height: 30mm;
                    box-sizing: border-box;
                    padding: 2mm;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    gap: 1mm;
                    overflow: hidden;
                    text-align: center;
                }

                .etiqueta-loja {
                    font-size: 11px;
                }

                #etiqueta-produto {
                    max-width: 100%;
                    font-size: 9px;
                    line-height: 1.1;
                }

                #etiqueta-preco {
                    font-size: 13px;
                }

                svg {
                    width: 100%;
                    max-height: 15mm;
                }
            </style>
        </head>

        <body>
            <div class="etiqueta">
                ${conteudo}
            </div>
        </body>
        </html>
    `);

    janela.document.close();

    setTimeout(function() {
        janela.focus();
        janela.print();
    }, 400);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>