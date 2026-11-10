<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$erroProduto = $_SESSION['erro_produto'] ?? null;
unset($_SESSION['erro_produto']);

?>

<main class="conteudo">

    <header class="topo">
        <div>
            <h1>Editar produto</h1>
            <p>Atualize as informações do produto.</p>
        </div>

        <a
            class="botao-secundario"
            href="index.php?rota=produtos"
        >
            Voltar
        </a>
    </header>

    <?php if ($erroProduto): ?>
        <div class="mensagem erro">
            <?= htmlspecialchars($erroProduto) ?>
        </div>
    <?php endif; ?>

    <section class="painel-formulario">

        <div class="aviso-codigo">
            Código do produto:
            <strong>
                <?= htmlspecialchars($produto['codigo_barras']) ?>
            </strong>
        </div>

        <div class="resumo-produto">
            <div class="info-produto">
                <span>Código</span>
                <strong><?= htmlspecialchars($produto['codigo_barras']) ?></strong>
            </div>
            <div class="info-produto">
                <span>Estoque Atual</span>
                <strong><?= (int)$produto['quantidade_estoque'] ?></strong>
            </div>
            <div class="info-produto">
                <span>Estoque Mínimo</span>
                <strong><?= (int)$produto['estoque_minimo'] ?></strong>
            </div>
            <div class="info-produto">
                <span>Status</span>
                <?php
                    $baixo =
                        $produto['quantidade_estoque']
                        <=
                        $produto['estoque_minimo'];
                ?>
                <strong class="<?= $baixo ? 'status-baixo' : 'status-ok' ?>">
                    <?= $baixo ? '🔴 Estoque Baixo' : '🟢 Normal' ?>
                </strong>
            </div>
        </div>

        <form
            class="form-produto"
            action="index.php?rota=produto-atualizar"
            method="POST"
        >

            <input
                type="hidden"
                name="id"
                value="<?= (int) $produto['id'] ?>"
            >

            <div class="campo campo-grande">
                <label for="nome">Produto *</label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="180"
                    value="<?= htmlspecialchars($produto['nome']) ?>"
                    required
                >
            </div>

            <div class="campo campo-grande">
                <label for="marca">Marca</label>

                <input
                    type="text"
                    id="marca"
                    name="marca"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $produto['marca'] ?? ''
                    ) ?>"
                >
            </div>

            <div class="campo">
                <label for="preco_custo">
                    Preço de custo *
                </label>

                <input
                    type="number"
                    id="preco_custo"
                    name="preco_custo"
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars(
                        $produto['preco_custo']
                    ) ?>"
                    onfocus="this.select()"
                    required
                >
            </div>

            <div class="campo">
                <label for="preco_venda">
                    Preço de venda *
                </label>

                <input
                    type="number"
                    id="preco_venda"
                    name="preco_venda"
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars(
                        $produto['preco_venda']
                    ) ?>"
                    onfocus="this.select()"
                    required
                >
            </div>

            <div class="campo">
                <label>Quantidade atual</label>

                <input
                    type="number"
                    value="<?= (int) $produto[
                        'quantidade_estoque'
                    ] ?>"
                    disabled
                >

                <small>
                    O estoque será alterado pela movimentação de estoque.
                </small>
            </div>

            <div class="campo">
                <label for="estoque_minimo">
                    Avisar quando chegar em *
                </label>

                <input
                    type="number"
                    id="estoque_minimo"
                    name="estoque_minimo"
                    min="0"
                    value="<?= (int) $produto['estoque_minimo'] ?>"
                    onfocus="this.select()"
                    required
                >
            </div>

            <div class="acoes-formulario campo-total">

                <a
                    class="botao-secundario"
                    href="index.php?rota=produto-movimentar&id=<?= (int) $produto['id'] ?>"
                >
                    📦 Movimentar Estoque
                </a>

                <div>

                    <a
                        class="botao-cancelar"
                        href="index.php?rota=produtos"
                    >
                        Cancelar
                    </a>

                    <button class="botao-salvar" type="submit">
                        Salvar alterações
                    </button>

                </div>

            </div>

        </form>

        <section class="painel-formulario historico-estoque">

    <div class="cabecalho-historico">
        <div>
            <h2>Histórico de movimentações</h2>
            <p>
                Entradas, saídas e ajustes realizados neste produto.
            </p>
        </div>
    </div>

    <?php if (empty($movimentacoes)): ?>

        <div class="historico-vazio">
            Nenhuma movimentação de estoque registrada.
        </div>

    <?php else: ?>

        <div class="tabela-responsiva">

            <table class="tabela-produtos">

                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Estoque anterior</th>
                        <th>Estoque atual</th>
                        <th>Motivo</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($movimentacoes as $movimentacao): ?>

                        <?php
                        $tipoMovimentacao =
                            $movimentacao['tipo'];

                        $quantidade =
                            (int) $movimentacao['quantidade'];

                        $textoQuantidade = (string) $quantidade;

                        if ($tipoMovimentacao === 'entrada') {
                            $textoQuantidade = '+' . $quantidade;
                        }

                        if ($tipoMovimentacao === 'saida') {
                            $textoQuantidade = '-' . $quantidade;
                        }

                        $nomeTipo = match ($tipoMovimentacao) {
                            'entrada' => 'Entrada',
                            'saida' => 'Saída',
                            'ajuste' => 'Ajuste',
                            default => ucfirst($tipoMovimentacao)
                        };
                        ?>

                        <tr>

                            <td>
                                <?= date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $movimentacao['criado_em']
                                    )
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($nomeTipo) ?>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars(
                                        $textoQuantidade
                                    ) ?>
                                </strong>
                            </td>

                            <td>
                                <?= (int) $movimentacao[
                                    'quantidade_anterior'
                                ] ?>
                            </td>

                            <td>
                                <?= (int) $movimentacao[
                                    'quantidade_atual'
                                ] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $movimentacao['motivo']
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</section>

        <form
            class="form-excluir-produto"
            action="index.php?rota=produto-excluir"
            method="POST"
            onsubmit="return confirm('Tem certeza que deseja excluir este produto?');"
        >
            <input
                type="hidden"
                name="id"
                value="<?= (int) $produto['id'] ?>"
            >

            <button
                class="botao-excluir"
                type="submit"
            >
                Excluir produto
            </button>
        </form>

    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>