<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sucessoVenda = $_SESSION['sucesso_venda'] ?? null;
$erroVenda = $_SESSION['erro_venda'] ?? null;

unset($_SESSION['sucesso_venda']);
unset($_SESSION['erro_venda']);

function moedaVenda(float $valor): string
{
    return 'R$ ' . number_format(
        $valor,
        2,
        ',',
        '.'
    );
}

$formasPagamento = [
    'pix' => 'PIX',
    'dinheiro' => 'Dinheiro',
    'debito' => 'Cartão de débito',
    'credito' => 'Cartão de crédito',
    'parcelado' => 'Parcelado'
];

?>

<style>
    .cabecalho-detalhe-venda {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 25px;
    }

    .cabecalho-detalhe-venda h1 {
        margin: 0 0 5px;
        color: #ffffff;
    }

    .cabecalho-detalhe-venda p {
        margin: 0;
        color: #9fb0c4;
    }

    .acoes-detalhe-venda {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .botao-detalhe {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 43px;
        padding: 0 17px;
        border-radius: 9px;
        color: #ffffff;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .botao-detalhe.voltar {
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: transparent;
    }

    .botao-detalhe.nova {
        border: none;
        background: #159ee8;
    }

    .painel-detalhe-venda {
        margin-bottom: 22px;
        padding: 24px;
        border-radius: 16px;
        background: #19263a;
    }

    .grade-detalhe-venda {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .item-detalhe-venda span {
        display: block;
        margin-bottom: 5px;
        color: #8fa1b6;
        font-size: 12px;
        text-transform: uppercase;
    }

    .item-detalhe-venda strong {
        color: #ffffff;
    }

    .status-venda {
        display: inline-flex;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .status-venda.concluida {
        background: rgba(32, 229, 143, 0.12);
        color: #20e58f;
    }

    .status-venda.cancelada {
        background: rgba(255, 91, 91, 0.12);
        color: #ff7777;
    }

    .tabela-detalhe {
        width: 100%;
        border-collapse: collapse;
    }

    .tabela-detalhe th,
    .tabela-detalhe td {
        padding: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        color: #dbe5f0;
        text-align: left;
    }

    .resumo-detalhe {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .resumo-detalhe-caixa {
        width: 100%;
        max-width: 360px;
    }

    .linha-detalhe-total {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        color: #c7d3e2;
    }

    .linha-detalhe-total.total {
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: #20e58f;
        font-size: 21px;
        font-weight: 900;
    }

    .area-cancelar-venda {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .botao-cancelar-venda {
        min-height: 43px;
        padding: 0 17px;
        border: 1px solid rgba(255, 91, 91, 0.3);
        border-radius: 9px;
        background: rgba(255, 91, 91, 0.08);
        color: #ff7777;
        font-weight: 800;
        cursor: pointer;
    }

    .mensagem-venda {
        margin-bottom: 20px;
        padding: 14px;
        border-radius: 9px;
        font-weight: 700;
    }

    .mensagem-venda.sucesso {
        background: rgba(32, 229, 143, 0.1);
        color: #20e58f;
    }

    .mensagem-venda.erro {
        background: rgba(255, 91, 91, 0.1);
        color: #ff7777;
    }

    @media (max-width: 800px) {
        .cabecalho-detalhe-venda {
            align-items: stretch;
            flex-direction: column;
        }

        .grade-detalhe-venda {
            grid-template-columns: 1fr;
        }

        .painel-detalhe-venda {
            padding: 16px;
        }
    }
</style>

<main class="conteudo">

    <header class="cabecalho-detalhe-venda">

        <div>
            <h1>
                <?= htmlspecialchars($venda['numero_venda']) ?>
            </h1>

            <p>Detalhes da venda realizada.</p>
        </div>

        <div class="acoes-detalhe-venda">
            <a
                href="index.php?rota=vendas"
                class="botao-detalhe voltar"
            >
                Voltar
            </a>

            <a
                href="index.php?rota=venda-nova"
                class="botao-detalhe nova"
            >
                Nova venda
            </a>
        </div>

    </header>

    <?php if ($sucessoVenda): ?>
        <div class="mensagem-venda sucesso">
            <?= htmlspecialchars($sucessoVenda) ?>
        </div>
    <?php endif; ?>

    <?php if ($erroVenda): ?>
        <div class="mensagem-venda erro">
            <?= htmlspecialchars($erroVenda) ?>
        </div>
    <?php endif; ?>

    <section class="painel-detalhe-venda">

        <div class="grade-detalhe-venda">

            <div class="item-detalhe-venda">
                <span>Cliente</span>

                <strong>
                    <?= htmlspecialchars(
                        $venda['cliente_nome']
                        ?: 'Consumidor final'
                    ) ?>
                </strong>
            </div>

            <div class="item-detalhe-venda">
                <span>Forma de pagamento</span>

                <strong>
                    <?= htmlspecialchars(
                        $formasPagamento[
                            $venda['forma_pagamento']
                        ] ?? $venda['forma_pagamento']
                    ) ?>
                </strong>
            </div>

            <div class="item-detalhe-venda">
                <span>Status</span>

                <strong
                    class="status-venda <?= htmlspecialchars(
                        $venda['status']
                    ) ?>"
                >
                    <?= $venda['status'] === 'cancelada'
                        ? 'Cancelada'
                        : 'Concluída' ?>
                </strong>
            </div>

            <div class="item-detalhe-venda">
                <span>Data</span>

                <strong>
                    <?= date(
                        'd/m/Y H:i',
                        strtotime($venda['criado_em'])
                    ) ?>
                </strong>
            </div>

            <div class="item-detalhe-venda">
                <span>Observações</span>

                <strong>
                    <?= htmlspecialchars(
                        $venda['observacoes']
                        ?: 'Nenhuma'
                    ) ?>
                </strong>
            </div>

        </div>

    </section>

    <section class="painel-detalhe-venda">

        <table class="tabela-detalhe">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço unitário</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td>
                            <strong>
                                <?= htmlspecialchars(
                                    $item['produto_nome']
                                ) ?>
                            </strong>

                            <br>

                            <small>
                                <?= htmlspecialchars(
                                    $item['codigo_barras']
                                ) ?>
                            </small>
                        </td>

                        <td>
                            <?= (int) $item['quantidade'] ?>
                        </td>

                        <td>
                            <?= moedaVenda(
                                (float) $item['preco_unitario']
                            ) ?>
                        </td>

                        <td>
                            <?= moedaVenda(
                                (float) $item['total']
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="resumo-detalhe">
            <div class="resumo-detalhe-caixa">

                <div class="linha-detalhe-total">
                    <span>Subtotal</span>

                    <strong>
                        <?= moedaVenda(
                            (float) $venda['subtotal']
                        ) ?>
                    </strong>
                </div>

                <div class="linha-detalhe-total">
                    <span>Desconto</span>

                    <strong>
                        <?= moedaVenda(
                            (float) $venda['desconto']
                        ) ?>
                    </strong>
                </div>

                <div class="linha-detalhe-total total">
                    <span>Total</span>

                    <strong>
                        <?= moedaVenda(
                            (float) $venda['total']
                        ) ?>
                    </strong>
                </div>

            </div>
        </div>

        <?php if ($venda['status'] !== 'cancelada'): ?>

            <div class="area-cancelar-venda">

                <form
                    action="index.php?rota=venda-cancelar"
                    method="POST"
                    onsubmit="return confirm(
                        'Deseja realmente cancelar esta venda? O estoque dos produtos será devolvido.'
                    );"
                >
                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $venda['id'] ?>"
                    >

                    <button
                        type="submit"
                        class="botao-cancelar-venda"
                    >
                        Cancelar venda
                    </button>
                </form>

            </div>

        <?php endif; ?>

    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>