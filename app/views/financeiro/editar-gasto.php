<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$erroFinanceiro =
    $_SESSION['erro_financeiro'] ?? null;

unset($_SESSION['erro_financeiro']);

$descricao =
    $gasto['descricao'] ?? '';

$categoriaSelecionada =
    $gasto['categoria'] ?? '';

$valor = isset($gasto['valor'])
    ? number_format(
        (float) $gasto['valor'],
        2,
        ',',
        '.'
    )
    : '';

$dataGasto =
    $gasto['data_gasto'] ?? date('Y-m-d');

$observacoes =
    $gasto['observacoes'] ?? '';

?>

<main class="conteudo">

    <header class="topo">

        <div>
            <h1>Editar gasto</h1>

            <p>
                Atualize as informações da despesa cadastrada.
            </p>
        </div>

        <a
            class="botao-cancelar"
            href="index.php?rota=financeiro&mes=<?= (int) $mes ?>&ano=<?= (int) $ano ?>"
        >
            Voltar
        </a>

    </header>

    <?php if ($erroFinanceiro): ?>

        <div class="mensagem erro">

            <?= htmlspecialchars(
                $erroFinanceiro,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

    <?php endif; ?>

    <section class="painel-formulario">

        <div class="aviso-codigo">

            <strong>Edição de despesa</strong>

            <p>
                As alterações serão aplicadas ao resultado
                financeiro do mês correspondente à data informada.
            </p>

        </div>

        <form
            class="form-gasto"
            action="index.php?rota=gasto-atualizar"
            method="POST"
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

            <div class="campo campo-total">

                <label for="descricao">
                    Descrição do gasto *
                </label>

                <input
                    type="text"
                    id="descricao"
                    name="descricao"
                    maxlength="150"
                    value="<?= htmlspecialchars(
                        (string) $descricao,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    placeholder="Ex.: Conta de energia"
                    required
                    autofocus
                >

            </div>

            <div class="campo">

                <label for="categoria">
                    Categoria *
                </label>

                <select
                    id="categoria"
                    name="categoria"
                    required
                >

                    <option value="">
                        Selecione uma categoria
                    </option>

                    <?php foreach ($categorias as $categoria): ?>

                        <option
                            value="<?= htmlspecialchars(
                                $categoria,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            <?= $categoriaSelecionada === $categoria
                                ? 'selected'
                                : '' ?>
                        >
                            <?= htmlspecialchars(
                                $categoria,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="campo">

                <label for="valor">
                    Valor *
                </label>

                <input
                    type="text"
                    id="valor"
                    name="valor"
                    inputmode="decimal"
                    value="<?= htmlspecialchars(
                        (string) $valor,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    placeholder="Ex.: 150,00"
                    required
                >

                <small>
                    Digite somente o valor. Exemplo: 150,00
                </small>

            </div>

            <div class="campo">

                <label for="data_gasto">
                    Data do gasto *
                </label>

                <input
                    type="date"
                    id="data_gasto"
                    name="data_gasto"
                    value="<?= htmlspecialchars(
                        (string) $dataGasto,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

            </div>

            <div class="campo campo-total">

                <label for="observacoes">
                    Observações
                </label>

                <textarea
                    id="observacoes"
                    name="observacoes"
                    placeholder="Informações adicionais sobre o gasto"
                ><?= htmlspecialchars(
                    (string) $observacoes,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>

            </div>

            <div class="acoes-formulario">

                <a
                    class="botao-cancelar"
                    href="index.php?rota=financeiro&mes=<?= (int) $mes ?>&ano=<?= (int) $ano ?>"
                >
                    Cancelar
                </a>

                <button
                    class="botao-salvar"
                    type="submit"
                >
                    Salvar alterações
                </button>

            </div>

        </form>

    </section>

</main>

</div>

</body>
</html>