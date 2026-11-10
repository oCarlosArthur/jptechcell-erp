<?php

$resumo = $dados['resumo'] ?? [];
$formasPagamento = $dados['formas_pagamento'] ?? [];
$produtosVendidos = $dados['produtos_vendidos'] ?? [];
$ordensServico = $dados['ordens_servico'] ?? [];
$gastos = $dados['gastos'] ?? [];
$categoriasGastos = $dados['categorias_gastos'] ?? [];
$produtosMaisLucrativos =
    $dados['produtos_mais_lucrativos'] ?? [];

$mes = (int) ($dados['mes'] ?? ($_GET['mes'] ?? date('m')));
$ano = (int) ($dados['ano'] ?? ($_GET['ano'] ?? date('Y')));

$nomesMeses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];

function escaparFinanceiro(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function moedaFinanceiro(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function dataFinanceiro(?string $data): string
{
    if (empty($data)) {
        return '-';
    }

    return date('d/m/Y', strtotime($data));
}

function nomeFormaPagamentoFinanceiro(string $forma): string
{
    $nomes = [
        'dinheiro' => 'Dinheiro',
        'pix' => 'Pix',
        'cartao_credito' => 'Cartão de crédito',
        'cartao_debito' => 'Cartão de débito',
        'credito' => 'Cartão de crédito',
        'debito' => 'Cartão de débito',
        'boleto' => 'Boleto',
        'transferencia' => 'Transferência'
    ];

    return $nomes[$forma]
        ?? ucfirst(str_replace('_', ' ', $forma));
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="conteudo-principal">

    <section class="cabecalho-pagina">
        <div>
            <span class="subtitulo-financeiro">
                Relatórios gerenciais
            </span>

            <h1>Relatório Financeiro</h1>

            <p>
                Acompanhe receitas, custos, gastos e
                resultado mensal da loja.
            </p>
        </div>

        <a
            href="index.php?rota=relatorios"
            class="botao-secundario"
        >
            ← Voltar
        </a>
    </section>

    <?php if (!empty($_SESSION['erro'])): ?>
        <div class="alerta alerta-erro">
            <?= escaparFinanceiro($_SESSION['erro']) ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <section class="cartao-financeiro">
        <div class="titulo-cartao-financeiro">
            <div>
                <h2>Período do relatório</h2>
                <p>Selecione o mês e o ano desejados.</p>
            </div>
        </div>

        <form
            action="index.php"
            method="get"
            class="filtros-financeiro"
        >
            <input
                type="hidden"
                name="rota"
                value="relatorios-financeiro"
            >

            <div>
                <label for="mes">Mês</label>

                <select id="mes" name="mes" required>
                    <?php foreach ($nomesMeses as $numero => $nome): ?>
                        <option
                            value="<?= $numero ?>"
                            <?= $mes === $numero ? 'selected' : '' ?>
                        >
                            <?= escaparFinanceiro($nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="ano">Ano</label>

                <select id="ano" name="ano" required>
                    <?php
                    $anoAtual = (int) date('Y');

                    for ($anoOpcao = $anoAtual + 1;
                        $anoOpcao >= $anoAtual - 10;
                        $anoOpcao--
                    ):
                    ?>
                        <option
                            value="<?= $anoOpcao ?>"
                            <?= $ano === $anoOpcao ? 'selected' : '' ?>
                        >
                            <?= $anoOpcao ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="acoes-filtros-financeiro">
                <button
                    type="submit"
                    class="botao-primario"
                >
                    Aplicar filtro
                </button>

                <a
                    href="index.php?rota=relatorios-imprimir-financeiro&mes=<?= $mes ?>&ano=<?= $ano ?>"
                    target="_blank"
                    class="botao-imprimir-financeiro"
                >
                    Imprimir relatório
                </a>
            </div>
        </form>
    </section>

    <section class="grade-resumo-financeiro">

        <article class="card-resumo-financeiro azul">
            <span>Total recebido</span>
            <strong>
                <?= moedaFinanceiro(
                    (float) ($resumo['total_recebido'] ?? 0)
                ) ?>
            </strong>
            <small>Receitas concluídas no mês</small>
        </article>

        <article class="card-resumo-financeiro">
            <span>Vendas de produtos</span>
            <strong>
                <?= moedaFinanceiro(
                    (float) ($resumo['total_produtos'] ?? 0)
                ) ?>
            </strong>
            <small>Total líquido dos produtos</small>
        </article>

        <article class="card-resumo-financeiro">
            <span>Custo dos produtos</span>
            <strong>
                <?= moedaFinanceiro(
                    (float) ($resumo['custo_produtos'] ?? 0)
                ) ?>
            </strong>
            <small>Custo dos itens vendidos</small>
        </article>

        <article class="card-resumo-financeiro verde">
            <span>Lucro dos produtos</span>
            <strong>
                <?= moedaFinanceiro(
                    (float) ($resumo['lucro_produtos'] ?? 0)
                ) ?>
            </strong>
            <small>Venda menos custo</small>
        </article>

        <article class="card-resumo-financeiro roxo">
            <span>Receita de OS</span>
            <strong>
                <?= moedaFinanceiro(
                    (float) (
                        $resumo['total_ordens_servico']
                        ?? 0
                    )
                ) ?>
            </strong>
            <small>Serviços recebidos no mês</small>
        </article>

        <article class="card-resumo-financeiro vermelho">
            <span>Gastos</span>
            <strong>
                <?= moedaFinanceiro(
                    (float) ($resumo['total_gastos'] ?? 0)
                ) ?>
            </strong>
            <small>Despesas cadastradas</small>
        </article>

        <?php
        $resultadoMes =
            (float) ($resumo['resultado_mes'] ?? 0);
        ?>

        <article class="card-resumo-financeiro <?= $resultadoMes >= 0 ? 'resultado-positivo' : 'resultado-negativo' ?>">
            <span>Resultado do mês</span>
            <strong>
                <?= moedaFinanceiro($resultadoMes) ?>
            </strong>
            <small>
                Lucro de produtos + OS - gastos
            </small>
        </article>

        <article class="card-resumo-financeiro">
            <span>Ticket médio</span>
            <strong>
                <?= moedaFinanceiro(
                    (float) ($resumo['ticket_medio'] ?? 0)
                ) ?>
            </strong>
            <small>Média por venda concluída</small>
        </article>

        <article class="card-resumo-financeiro">
            <span>Quantidade de vendas</span>
            <strong>
                <?= (int) ($resumo['quantidade_vendas'] ?? 0) ?>
            </strong>
            <small>
                <?= (int) ($resumo['quantidade_itens'] ?? 0) ?>
                itens vendidos
            </small>
        </article>

    </section>

    <section class="grade-dupla-financeiro">

        <article class="cartao-financeiro">
            <div class="titulo-cartao-financeiro">
                <div>
                    <h2>Formas de pagamento</h2>
                    <p>Valores recebidos em cada forma.</p>
                </div>
            </div>

            <?php if (empty($formasPagamento)): ?>
                <div class="vazio-financeiro">
                    Nenhum pagamento encontrado.
                </div>
            <?php else: ?>
                <div class="lista-financeiro">
                    <?php foreach ($formasPagamento as $forma => $valorForma): ?>
                        <div class="item-lista-financeiro">
                            <span>
                                <?= escaparFinanceiro(
                                    nomeFormaPagamentoFinanceiro(
                                        (string) $forma
                                    )
                                ) ?>
                            </span>

                            <strong>
                                <?= moedaFinanceiro(
                                    (float) $valorForma
                                ) ?>
                            </strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

        <article class="cartao-financeiro">
            <div class="titulo-cartao-financeiro">
                <div>
                    <h2>Gastos por categoria</h2>
                    <p>Categorias com maior impacto.</p>
                </div>
            </div>

            <?php if (empty($categoriasGastos)): ?>
                <div class="vazio-financeiro">
                    Nenhum gasto cadastrado.
                </div>
            <?php else: ?>
                <div class="lista-financeiro">
                    <?php foreach ($categoriasGastos as $categoria): ?>
                        <div class="item-lista-financeiro">
                            <div>
                                <span>
                                    <?= escaparFinanceiro(
                                        $categoria['categoria'] ?? ''
                                    ) ?>
                                </span>

                                <small>
                                    <?= (int) (
                                        $categoria['quantidade']
                                        ?? 0
                                    ) ?>
                                    lançamento(s)
                                </small>
                            </div>

                            <strong class="valor-negativo-financeiro">
                                <?= moedaFinanceiro(
                                    (float) (
                                        $categoria['valor_total']
                                        ?? 0
                                    )
                                ) ?>
                            </strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

    </section>

    <section class="cartao-financeiro">
        <div class="titulo-cartao-financeiro">
            <div>
                <h2>Produtos mais lucrativos</h2>
                <p>Ranking pelo lucro gerado no mês.</p>
            </div>
        </div>

        <?php if (empty($produtosMaisLucrativos)): ?>
            <div class="vazio-financeiro">
                Nenhum produto vendido.
            </div>
        <?php else: ?>
            <div class="tabela-responsiva-financeiro">
                <table class="tabela-financeiro">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Marca</th>
                            <th>Quantidade</th>
                            <th>Venda líquida</th>
                            <th>Custo</th>
                            <th>Lucro</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($produtosMaisLucrativos as $produto): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= escaparFinanceiro(
                                            $produto['produto_nome']
                                            ?? ''
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= escaparFinanceiro(
                                        $produto['produto_marca']
                                        ?: '-'
                                    ) ?>
                                </td>

                                <td>
                                    <?= (int) (
                                        $produto['quantidade']
                                        ?? 0
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaFinanceiro(
                                        (float) (
                                            $produto['total_venda']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaFinanceiro(
                                        (float) (
                                            $produto['total_custo']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <strong class="valor-positivo-financeiro">
                                        <?= moedaFinanceiro(
                                            (float) (
                                                $produto['lucro']
                                                ?? 0
                                            )
                                        ) ?>
                                    </strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="cartao-financeiro">
        <div class="titulo-cartao-financeiro">
            <div>
                <h2>Ordens de serviço recebidas</h2>
                <p>Serviços vinculados a vendas concluídas.</p>
            </div>

            <span class="contador-financeiro">
                <?= count($ordensServico) ?> OS
            </span>
        </div>

        <?php if (empty($ordensServico)): ?>
            <div class="vazio-financeiro">
                Nenhuma ordem de serviço recebida.
            </div>
        <?php else: ?>
            <div class="tabela-responsiva-financeiro">
                <table class="tabela-financeiro tabela-os-financeiro">
                    <thead>
                        <tr>
                            <th>OS</th>
                            <th>Venda</th>
                            <th>Cliente</th>
                            <th>Aparelho</th>
                            <th>Pagamento</th>
                            <th>Data</th>
                            <th>Valor bruto</th>
                            <th>Desconto</th>
                            <th>Recebido</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($ordensServico as $ordem): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= escaparFinanceiro(
                                            $ordem['numero_os'] ?? ''
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= escaparFinanceiro(
                                        $ordem['numero_venda'] ?? ''
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparFinanceiro(
                                        $ordem['cliente_nome'] ?? ''
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparFinanceiro(
                                        $ordem['aparelho'] ?? ''
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparFinanceiro(
                                        nomeFormaPagamentoFinanceiro(
                                            (string) (
                                                $ordem['forma_pagamento']
                                                ?? ''
                                            )
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparFinanceiro(
                                        dataFinanceiro(
                                            $ordem['data_venda']
                                            ?? null
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaFinanceiro(
                                        (float) (
                                            $ordem['valor_bruto']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaFinanceiro(
                                        (float) (
                                            $ordem['desconto']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <strong class="valor-positivo-financeiro">
                                        <?= moedaFinanceiro(
                                            (float) (
                                                $ordem['valor_recebido']
                                                ?? 0
                                            )
                                        ) ?>
                                    </strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="cartao-financeiro">
        <div class="titulo-cartao-financeiro">
            <div>
                <h2>Gastos do mês</h2>
                <p>Despesas lançadas no período.</p>
            </div>

            <span class="contador-financeiro">
                <?= count($gastos) ?> gastos
            </span>
        </div>

        <?php if (empty($gastos)): ?>
            <div class="vazio-financeiro">
                Nenhum gasto cadastrado.
            </div>
        <?php else: ?>
            <div class="tabela-responsiva-financeiro">
                <table class="tabela-financeiro">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Observações</th>
                            <th>Valor</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($gastos as $gasto): ?>
                            <tr>
                                <td>
                                    <?= escaparFinanceiro(
                                        dataFinanceiro(
                                            $gasto['data_gasto']
                                            ?? null
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= escaparFinanceiro(
                                            $gasto['descricao'] ?? ''
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= escaparFinanceiro(
                                        $gasto['categoria'] ?? ''
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparFinanceiro(
                                        $gasto['observacoes']
                                        ?: '-'
                                    ) ?>
                                </td>

                                <td>
                                    <strong class="valor-negativo-financeiro">
                                        <?= moedaFinanceiro(
                                            (float) (
                                                $gasto['valor']
                                                ?? 0
                                            )
                                        ) ?>
                                    </strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

</main>

<style>
    .subtitulo-financeiro {
        display: block;
        margin-bottom: 4px;
        color: #159ee8;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .cartao-financeiro {
        margin-bottom: 22px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .05);
    }

    .titulo-cartao-financeiro {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .titulo-cartao-financeiro h2 {
        margin: 0 0 5px;
        color: #172033;
        font-size: 20px;
    }

    .titulo-cartao-financeiro p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .filtros-financeiro {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        align-items: end;
        gap: 16px;
    }

    .filtros-financeiro label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 14px;
        font-weight: 800;
    }

    .filtros-financeiro select {
        width: 100%;
        min-height: 46px;
        padding: 0 13px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #ffffff;
        font-size: 14px;
        font-weight: 700;
    }

    .acoes-filtros-financeiro {
        display: flex;
        gap: 10px;
    }

    .botao-imprimir-financeiro {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 18px;
        border-radius: 9px;
        background: #243044;
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
    }

    .grade-resumo-financeiro {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 17px;
        margin-bottom: 22px;
    }

    .card-resumo-financeiro {
        min-height: 125px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
    }

    .card-resumo-financeiro span {
        display: block;
        margin-bottom: 11px;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .card-resumo-financeiro strong {
        display: block;
        margin-bottom: 8px;
        color: #172033;
        font-size: 27px;
    }

    .card-resumo-financeiro small {
        color: #64748b;
        font-size: 12px;
    }

    .card-resumo-financeiro.azul {
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .card-resumo-financeiro.azul strong {
        color: #0284c7;
    }

    .card-resumo-financeiro.verde,
    .card-resumo-financeiro.resultado-positivo {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .card-resumo-financeiro.verde strong,
    .card-resumo-financeiro.resultado-positivo strong {
        color: #15803d;
    }

    .card-resumo-financeiro.roxo {
        border-color: #ddd6fe;
        background: #f5f3ff;
    }

    .card-resumo-financeiro.roxo strong {
        color: #6d28d9;
    }

    .card-resumo-financeiro.vermelho,
    .card-resumo-financeiro.resultado-negativo {
        border-color: #fecaca;
        background: #fef2f2;
    }

    .card-resumo-financeiro.vermelho strong,
    .card-resumo-financeiro.resultado-negativo strong {
        color: #b91c1c;
    }

    .grade-dupla-financeiro {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .lista-financeiro {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .item-lista-financeiro {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        min-height: 58px;
        padding: 13px 14px;
        border: 1px solid #edf2f7;
        border-radius: 11px;
        background: #f8fafc;
    }

    .item-lista-financeiro span {
        color: #334155;
        font-size: 14px;
        font-weight: 800;
    }

    .item-lista-financeiro small {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 11px;
    }

    .item-lista-financeiro strong {
        color: #172033;
        white-space: nowrap;
    }

    .contador-financeiro {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 11px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
        font-size: 12px;
        font-weight: 900;
    }

    .tabela-responsiva-financeiro {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
    }

    .tabela-financeiro {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
        background: #ffffff;
    }

    .tabela-os-financeiro {
        min-width: 1200px;
    }

    .tabela-financeiro th,
    .tabela-financeiro td {
        padding: 13px 12px;
        border-bottom: 1px solid #edf2f7;
        color: #1e293b !important;
        font-size: 13px;
        text-align: left;
        vertical-align: middle;
        opacity: 1 !important;
    }

    .tabela-financeiro th {
        background: #e2e8f0;
        color: #334155 !important;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .tabela-financeiro td strong {
        color: #0f172a !important;
    }

    .valor-positivo-financeiro {
        color: #15803d !important;
    }

    .valor-negativo-financeiro {
        color: #b91c1c !important;
    }

    .vazio-financeiro {
        padding: 28px;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        color: #64748b;
        text-align: center;
        font-size: 14px;
    }

    @media (max-width: 1000px) {
        .grade-resumo-financeiro {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .filtros-financeiro,
        .grade-dupla-financeiro {
            grid-template-columns: 1fr;
        }

        .acoes-filtros-financeiro {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 600px) {
        .grade-resumo-financeiro {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>