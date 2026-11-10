<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$erroProduto = $_SESSION['erro_produto'] ?? null;
$dadosProduto = $_SESSION['dados_produto'] ?? [];

unset($_SESSION['erro_produto']);

?>

<main class="conteudo">

    <header class="topo">
        <div>
            <h1>Novo produto</h1>
            <p>Cadastre um novo produto no estoque.</p>
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
            O código de barras será gerado automaticamente ao salvar.
        </div>

        <form
            class="form-produto"
            action="index.php?rota=produto-salvar"
            method="POST"
        >

            <div class="campo campo-grande">
                <label for="nome">Produto *</label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="180"
                    value="<?= htmlspecialchars(
                        $dadosProduto['nome'] ?? ''
                    ) ?>"
                    placeholder="Ex.: Carregador USB-C 20W"
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
                        $dadosProduto['marca'] ?? ''
                    ) ?>"
                    placeholder="Ex.: Kaidi"
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
                        $dadosProduto['preco_custo'] ?? '0.00'
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
                        $dadosProduto['preco_venda'] ?? '0.00'
                    ) ?>"
                    onfocus="this.select()"
                    required
                >
            </div>

            <div class="campo">
                <label for="quantidade_estoque">
                    Quantidade no estoque *
                </label>

                <input
                    type="number"
                    id="quantidade_estoque"
                    name="quantidade_estoque"
                    min="0"
                    value="<?= (int) (
                        $dadosProduto['quantidade_estoque'] ?? 0
                    ) ?>"
                    onfocus="this.select()"
                    required
                >
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
                    value="<?= (int) (
                        $dadosProduto['estoque_minimo'] ?? 0
                    ) ?>"
                    onfocus="this.select()"
                    required
                >

                <small>
                    Exemplo: informe 3 para receber o alerta quando
                    restarem 3 unidades.
                </small>
            </div>

            <div class="acoes-formulario campo-total">
                <a
                    class="botao-cancelar"
                    href="index.php?rota=produtos"
                >
                    Cancelar
                </a>

                <button class="botao-salvar" type="submit">
                    Salvar produto
                </button>
            </div>

        </form>

    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>