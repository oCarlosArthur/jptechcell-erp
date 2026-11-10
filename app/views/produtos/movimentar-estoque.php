<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

?>

<main class="conteudo">

    <header class="topo">
        <div>
            <h1>Movimentação de Estoque</h1>
            <p>Entrada e ajuste de estoque do produto.</p>
        </div>

        <a
            class="botao-cancelar"
            href="index.php?rota=produto-editar&id=<?= (int)$produto['id'] ?>"
        >
            Voltar
        </a>
    </header>

    <section class="painel-formulario">

        <div class="resumo-produto">

            <div class="info-produto">
                <span>Produto</span>
                <strong><?= htmlspecialchars($produto['nome']) ?></strong>
            </div>

            <div class="info-produto">
                <span>Código</span>
                <strong><?= htmlspecialchars($produto['codigo_barras']) ?></strong>
            </div>

            <div class="info-produto">
                <span>Estoque Atual</span>
                <strong><?= (int)$produto['quantidade_estoque'] ?></strong>
            </div>

        </div>

        <form
            class="form-produto"
            method="POST"
            action="index.php?rota=produto-movimentar"
        >

            <input
                type="hidden"
                name="produto_id"
                value="<?= (int)$produto['id'] ?>"
            >

            <?php if (!empty($_SESSION['erro_movimentacao'])): ?>

                <div class="mensagem-erro">
                    <?= htmlspecialchars($_SESSION['erro_movimentacao']) ?>
                </div>

                <?php unset($_SESSION['erro_movimentacao']); ?>

            <?php endif; ?>

            <div class="campo">
                <label for="tipoMovimentacao">
                    Tipo de movimentação
                </label>

                <select
                    id="tipoMovimentacao"
                    name="tipo"
                    required
                >
                    <option value="entrada">
                        Entrada de estoque
                    </option>

                    <option value="saida">
                        Saída de estoque
                    </option>

                    <option value="ajuste">
                        Ajuste de estoque
                    </option>
                </select>
            </div>

            <div class="campo">

                <label
                    id="labelQuantidade"
                    for="quantidadeMovimentacao"
                >
                    Quantidade que entrou
                </label>

                <input
                    id="quantidadeMovimentacao"
                    type="number"
                    name="quantidade"
                    value="1"
                    min="1"
                    onfocus="this.select()"
                    required
                >

            </div>

            <div class="campo campo-total">

                <label for="motivoMovimentacao">
                    Motivo
                </label>

                <textarea
                    id="motivoMovimentacao"
                    name="motivo"
                    rows="4"
                    placeholder="Ex.: Compra de fornecedor..."
                    required
                ></textarea>

            </div>

            <div class="acoes-formulario campo-total">

                <a
                    class="botao-cancelar"
                    href="index.php?rota=produto-editar&id=<?= (int)$produto['id'] ?>"
                >
                    Cancelar
                </a>

                <button
                    class="botao-salvar"
                    type="submit"
                >
                    Salvar movimentação
                </button>

            </div>

        </form>

    </section>

</main>

<script>

const tipo = document.getElementById('tipoMovimentacao');
const label = document.getElementById('labelQuantidade');

tipo.addEventListener('change', function(){

    switch(this.value){

        case 'entrada':
            label.innerText =
                'Quantidade que entrou';
            break;

        case 'saida':
            label.innerText =
                'Quantidade que saiu';
            break;

        case 'ajuste':
            label.innerText =
                'Novo estoque correto';
            break;

    }

});

</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>