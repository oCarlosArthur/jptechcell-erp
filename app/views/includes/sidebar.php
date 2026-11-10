<?php

$rotaAtual = $_GET['rota'] ?? 'dashboard';

?>

<aside class="sidebar">

    <div class="marca">
        <img
            src="/jptechcell/public/assets/img/logo-jptechcell.png"
            alt="Logo JP TechCell"
        >
    </div>

    <nav class="menu">

        <a
            class="<?= $rotaAtual === 'dashboard' ? 'ativo' : '' ?>"
            href="index.php?rota=dashboard"
        >
            Dashboard
        </a>

        <a
            class="<?= in_array(
                $rotaAtual,
                [
                    'produtos',
                    'produto-novo',
                    'produto-editar'
                ],
                true
            ) ? 'ativo' : '' ?>"
            href="index.php?rota=produtos"
        >
            Produtos
        </a>

        <a
            class="<?= in_array(
                $rotaAtual,
                [
                    'estoque',
                    'produto-movimentar'
                ],
                true
            ) ? 'ativo' : '' ?>"
            href="index.php?rota=estoque"
        >
            Estoque
        </a>

        <a
            class="<?= in_array(
                $rotaAtual,
                [
                    'clientes',
                    'cliente-novo',
                    'cliente-editar'
                ],
                true
            ) ? 'ativo' : '' ?>"
            href="index.php?rota=clientes"
        >
            Clientes
        </a>

        <a
            class="<?= in_array(
                $rotaAtual,
                [
                    'vendas',
                    'venda-nova',
                    'venda-visualizar'
                ],
                true
            ) ? 'ativo' : '' ?>"
            href="index.php?rota=vendas"
        >
            Vendas
        </a>

        <a
            class="<?= in_array(
                $rotaAtual,
                [
                    'ordens-servico',
                    'ordem-servico-nova',
                    'ordem-servico-visualizar',
                    'ordem-servico-editar'
                ],
                true
            ) ? 'ativo' : '' ?>"
            href="index.php?rota=ordens-servico"
        >
            Ordens de serviço
        </a>

        <a
            class="<?= in_array(
                $rotaAtual,
                [
                    'financeiro',
                    'gasto-novo',
                    'gasto-editar'
                ],
                true
            ) ? 'ativo' : '' ?>"
            href="index.php?rota=financeiro"
        >
            Financeiro
        </a>

        <a
            class="<?= in_array(
                $rotaAtual,
                [
                    'relatorios',
                    'relatorios-vendas',
                    'relatorios-imprimir-vendas',
                    'relatorios-produtos',
                    'relatorios-imprimir-produtos'
                ],
                true
            ) ? 'ativo' : '' ?>"
            href="index.php?rota=relatorios"
        >
            Relatórios
        </a>

<a
    class="<?= in_array(
        $rotaAtual,
        [
            'configuracoes',
            'configuracoes-salvar',
            'configuracoes-backup',
            'configuracoes-restaurar',
            'configuracoes-limpar'
        ],
        true
    ) ? 'ativo' : '' ?>"
    href="index.php?rota=configuracoes"
>
    Configurações
</a>

    </nav>

    <a
        class="botao-sair"
        href="index.php?rota=logout"
    >
        Sair
    </a>

</aside>