<?php

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

?>

<main class="conteudo">

    <header class="topo">

        <div>
            <h1>Dashboard</h1>

            <p>
                Bem-vindo,
                <?= htmlspecialchars(
                    $_SESSION['usuario_nome']
                ) ?>!
            </p>
        </div>

        <div class="usuario-logado">
            <?= htmlspecialchars(
                $_SESSION['usuario_login']
            ) ?>
        </div>

    </header>

    <section class="cards">

        <article class="card">

            <span>
                Produtos cadastrados
            </span>

            <strong>
                <?= (int) $totalProdutos ?>
            </strong>

        </article>

        <article class="card">

            <span>
                Estoque baixo
            </span>

            <strong>
                <?= (int) $estoqueBaixo ?>
            </strong>

        </article>

        <article class="card">

            <span>
                Ordens em andamento
            </span>

            <strong>
                <?= (int) $ordensEmAndamento ?>
            </strong>

        </article>

        <article class="card">

            <span>
                Prontas para entrega
            </span>

            <strong>
                <?= (int) $ordensProntas ?>
            </strong>

        </article>

    </section>

    <section class="cards">

    <article class="card">
        <span>Recebidas hoje</span>

        <strong>
            <?= (int) $ordensRecebidasHoje ?>
        </strong>
    </article>

    <article class="card">
        <span>Aguardando aprovação</span>

        <strong>
            <?= (int) $aguardandoAprovacao ?>
        </strong>
    </article>

    <article class="card">
        <span>Aguardando peça</span>

        <strong>
            <?= (int) $aguardandoPeca ?>
        </strong>
    </article>

    <article class="card">
        <span>Em manutenção</span>

        <strong>
            <?= (int) $emManutencao ?>
        </strong>
    </article>

</section>

<?php

$nomesStatusOs = [
    'recebida' => 'Recebida',
    'em_analise' => 'Em análise',
    'aguardando_aprovacao' => 'Aguardando aprovação',
    'aguardando_peca' => 'Aguardando peça',
    'em_manutencao' => 'Em manutenção',
    'pronta' => 'Pronta',
    'entregue' => 'Entregue',
    'cancelada' => 'Cancelada'
];

?>

<section class="painel-listas">

    <div class="lista-dashboard">

        <div class="lista-dashboard-topo">

            <div>
                <h2>Últimas Ordens de Serviço</h2>

                <p>
                    Ordens cadastradas recentemente
                </p>
            </div>

            <a
                href="index.php?rota=ordens-servico"
                class="link-ver-todos"
            >
                Ver todas
            </a>

        </div>

        <div class="tabela-dashboard-responsiva">

            <table class="tabela-dashboard">

                <thead>
                    <tr>
                        <th>OS</th>
                        <th>Cliente</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($ultimasOrdens)): ?>

                        <tr>
                            <td
                                colspan="3"
                                class="dashboard-sem-registros"
                            >
                                Nenhuma ordem de serviço cadastrada.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($ultimasOrdens as $ordem): ?>

                            <?php
                            $statusOs =
                                $ordem['status'] ?? 'recebida';

                            $nomeStatus =
                                $nomesStatusOs[$statusOs]
                                ?? ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $statusOs
                                    )
                                );
                            ?>

                            <tr>

                                <td>
                                    <a
                                        class="link-registro-dashboard"
                                        href="index.php?rota=ordem-servico-visualizar&id=<?= (int) $ordem['id'] ?>"
                                    >
                                        <?= htmlspecialchars(
                                            $ordem['numero_os']
                                        ) ?>
                                    </a>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $ordem['cliente']
                                    ) ?>
                                </td>

                                <td>
                                    <span
                                        class="badge-status status-<?= htmlspecialchars($statusOs) ?>"
                                    >
                                        <?= htmlspecialchars($nomeStatus) ?>
                                    </span>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <div class="lista-dashboard">

        <div class="lista-dashboard-topo">

            <div>
                <h2>Últimas Vendas</h2>

                <p>
                    Vendas registradas recentemente
                </p>
            </div>

            <a
                href="index.php?rota=vendas"
                class="link-ver-todos"
            >
                Ver todas
            </a>

        </div>

        <div class="tabela-dashboard-responsiva">

            <table class="tabela-dashboard">

                <thead>
                    <tr>
                        <th>Venda</th>
                        <th>Cliente</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($ultimasVendas)): ?>

                        <tr>
                            <td
                                colspan="3"
                                class="dashboard-sem-registros"
                            >
                                Nenhuma venda cadastrada.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($ultimasVendas as $venda): ?>

                            <?php
                            $statusVenda =
                                $venda['status'] ?? 'concluida';

                            $nomeStatusVenda =
                                $statusVenda === 'cancelada'
                                ? 'Cancelada'
                                : 'Concluída';
                            ?>

                            <tr>

                                <td>
                                    <a
                                        class="link-registro-dashboard"
                                        href="index.php?rota=venda-visualizar&id=<?= (int) $venda['id'] ?>"
                                    >
                                        <?= htmlspecialchars(
                                            $venda['numero_venda']
                                        ) ?>
                                    </a>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $venda['cliente']
                                        ?? 'Consumidor não identificado'
                                    ) ?>
                                </td>

                                <td>
                                    <span
                                        class="badge-status venda-<?= htmlspecialchars($statusVenda) ?>"
                                    >
                                        <?= htmlspecialchars(
                                            $nomeStatusVenda
                                        ) ?>
                                    </span>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</section>

    <section class="atalhos">

        <h2>Acesso rápido</h2>

        <div class="grade-atalhos">

            <a
                href="index.php?rota=venda-nova"
                class="atalho"
            >
                Nova venda
            </a>

            <a
                href="index.php?rota=ordem-servico-nova"
                class="atalho"
            >
                Nova ordem de serviço
            </a>

            <a
                href="index.php?rota=produto-novo"
                class="atalho"
            >
                Cadastrar produto
            </a>

            <a
                href="index.php?rota=cliente-novo"
                class="atalho"
            >
                Cadastrar cliente
            </a>

        </div>

    </section>

</main>

<?php

require_once __DIR__ . '/includes/footer.php';

?>