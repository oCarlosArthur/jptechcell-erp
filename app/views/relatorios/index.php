<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$mensagemSucesso = $_SESSION['sucesso'] ?? null;
$mensagemErro = $_SESSION['erro'] ?? null;

unset($_SESSION['sucesso'], $_SESSION['erro']);
?>

<main class="conteudo-principal">

    <div class="cabecalho-pagina">
        <div>
            <h1>Relatórios</h1>

            <p>
                Consulte informações detalhadas da loja
                e imprima os relatórios necessários.
            </p>
        </div>
    </div>

    <?php if (!empty($mensagemSucesso)): ?>
        <div class="alerta alerta-sucesso">
            <?= htmlspecialchars(
                $mensagemSucesso,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensagemErro)): ?>
        <div class="alerta alerta-erro">
            <?= htmlspecialchars(
                $mensagemErro,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>
    <?php endif; ?>

    <section class="secao-relatorios">

        <div class="grade-relatorios">

            <a
                href="index.php?rota=relatorios-vendas"
                class="cartao-relatorio"
            >
                <div class="cartao-relatorio-icone">
                    🧾
                </div>

                <div class="cartao-relatorio-conteudo">
                    <h2>Relatório de Vendas</h2>

                    <p>
                        Consulte vendas concluídas,
                        clientes, produtos, descontos,
                        formas de pagamento e valores.
                    </p>

                    <span class="cartao-relatorio-link">
                        Abrir relatório
                    </span>
                </div>
            </a>

<a
    href="index.php?rota=relatorios-produtos"
    class="cartao-relatorio"
>

    <div class="cartao-relatorio-icone">
        📦
    </div>

    <div class="cartao-relatorio-conteudo">

        <h2>Relatório de Produtos</h2>

        <p>
            Consulte produtos vendidos,
            quantidades, faturamento,
            custos e lucro.
        </p>

        <span class="cartao-relatorio-link">
            Abrir relatório
        </span>

    </div>

</a>

<a
    href="index.php?rota=relatorios-estoque"
    class="cartao-relatorio"
>

    <div class="cartao-relatorio-icone">
        📊
    </div>

    <div class="cartao-relatorio-conteudo">

        <h2>Relatório de Estoque</h2>

        <p>
            Consulte estoque atual,
            produtos abaixo do mínimo
            e valor investido.
        </p>

        <span class="cartao-relatorio-link">
            Abrir relatório
        </span>

    </div>

</a>

<a
    href="index.php?rota=relatorios-clientes"
    class="cartao-relatorio"
>

    <div class="cartao-relatorio-icone">
        👥
    </div>

    <div class="cartao-relatorio-conteudo">

        <h2>Relatório de Clientes</h2>

        <p>
            Consulte o histórico dos clientes,
            quantidade de compras e valores movimentados.
        </p>

        <span class="cartao-relatorio-link">
            Abrir relatório
        </span>

    </div>

</a>

<a
    href="index.php?rota=relatorios-ordens-servico"
    class="cartao-relatorio"
>

    <div class="cartao-relatorio-icone">
        🔧
    </div>

    <div class="cartao-relatorio-conteudo">

        <h2>Relatório de Ordens de Serviço</h2>

        <p>
            Consulte quantidade de ordens,
            status, valores, clientes e aparelhos atendidos.
        </p>

        <span class="cartao-relatorio-link">
            Abrir relatório
        </span>

    </div>

</a>

<a
    href="index.php?rota=relatorios-financeiro"
    class="cartao-relatorio"
>

    <div class="cartao-relatorio-icone">
        💰
    </div>

    <div class="cartao-relatorio-conteudo">

        <h2>Relatório Financeiro</h2>

        <p>
            Consulte receitas, custos, gastos,
            lucros e o resultado mensal da loja.
        </p>

        <span class="cartao-relatorio-link">
            Abrir relatório
        </span>

    </div>

</a>

        </div>

    </section>

</main>

</div>

</body>
</html>