<?php

$clientes = $dados['clientes'] ?? [];
$resumo = $dados['resumo'] ?? [];
$maisCompraram = $dados['mais_compraram'] ?? [];
$maisFrequentes = $dados['mais_frequentes'] ?? [];
$semCompras = $dados['sem_compras'] ?? [];

$dataInicio = $dados['data_inicio']
    ?? ($_GET['data_inicio'] ?? date('Y-m-01'));

$dataFim = $dados['data_fim']
    ?? ($_GET['data_fim'] ?? date('Y-m-d'));

function escaparRelatorioClientes(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function moedaRelatorioClientes(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function dataRelatorioClientes(?string $data): string
{
    return empty($data)
        ? 'Sem compra no período'
        : date('d/m/Y H:i', strtotime($data));
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="conteudo-principal">

    <section class="cabecalho-pagina">
        <div>
            <span class="subtitulo-pagina">
                Relatórios gerenciais
            </span>

            <h1>Relatório de Clientes</h1>

            <p>
                Consulte compras, valores movimentados,
                frequência e clientes sem compras.
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
            <?= escaparRelatorioClientes($_SESSION['erro']) ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <section class="cartao-clientes">
        <div class="titulo-cartao-clientes">
            <div>
                <h2>Período do relatório</h2>
                <p>Escolha as datas para analisar as compras.</p>
            </div>
        </div>

        <form
            action="index.php"
            method="get"
            class="filtros-clientes"
        >
            <input
                type="hidden"
                name="rota"
                value="relatorios-clientes"
            >

            <div>
                <label for="data_inicio">Data inicial</label>
                <input
                    type="date"
                    id="data_inicio"
                    name="data_inicio"
                    value="<?= escaparRelatorioClientes($dataInicio) ?>"
                    required
                >
            </div>

            <div>
                <label for="data_fim">Data final</label>
                <input
                    type="date"
                    id="data_fim"
                    name="data_fim"
                    value="<?= escaparRelatorioClientes($dataFim) ?>"
                    required
                >
            </div>

            <div class="acoes-filtros-clientes">
                <button
                    type="submit"
                    class="botao-primario"
                >
                    Aplicar filtro
                </button>

                <a
                    href="index.php?rota=relatorios-imprimir-clientes&data_inicio=<?= urlencode($dataInicio) ?>&data_fim=<?= urlencode($dataFim) ?>"
                    target="_blank"
                    class="botao-imprimir-clientes"
                >
                    Imprimir relatório
                </a>
            </div>
        </form>
    </section>

    <section class="grade-resumo-clientes">

        <article class="card-resumo-clientes">
            <span>Clientes ativos</span>
            <strong><?= (int) ($resumo['total_clientes'] ?? 0) ?></strong>
            <small>Total cadastrado</small>
        </article>

        <article class="card-resumo-clientes verde">
            <span>Compraram</span>
            <strong><?= (int) ($resumo['clientes_com_compra'] ?? 0) ?></strong>
            <small>Clientes com movimentação</small>
        </article>

        <article class="card-resumo-clientes alerta">
            <span>Sem compras</span>
            <strong><?= (int) ($resumo['clientes_sem_compra'] ?? 0) ?></strong>
            <small>Sem movimentação</small>
        </article>

        <article class="card-resumo-clientes">
            <span>Compras realizadas</span>
            <strong><?= (int) ($resumo['quantidade_compras'] ?? 0) ?></strong>
            <small>Vendas concluídas</small>
        </article>

        <article class="card-resumo-clientes azul">
            <span>Total movimentado</span>
            <strong>
                <?= moedaRelatorioClientes(
                    (float) ($resumo['total_movimentado'] ?? 0)
                ) ?>
            </strong>
            <small>Total gasto pelos clientes</small>
        </article>

        <article class="card-resumo-clientes verde">
            <span>Ticket médio</span>
            <strong>
                <?= moedaRelatorioClientes(
                    (float) ($resumo['ticket_medio_geral'] ?? 0)
                ) ?>
            </strong>
            <small>Média por compra</small>
        </article>

    </section>

    <section class="grade-destaques-clientes">

        <article class="cartao-clientes">
            <div class="titulo-cartao-clientes">
                <div>
                    <h2>Quem mais comprou</h2>
                    <p>Ranking por valor gasto.</p>
                </div>
            </div>

            <?php if (empty($maisCompraram)): ?>
                <div class="vazio-clientes">
                    Nenhuma compra registrada.
                </div>
            <?php else: ?>
                <div class="lista-clientes">
                    <?php foreach ($maisCompraram as $indice => $cliente): ?>
                        <div class="item-ranking-clientes">
                            <span class="posicao-clientes">
                                <?= $indice + 1 ?>
                            </span>

                            <div class="dados-ranking-clientes">
                                <strong>
                                    <?= escaparRelatorioClientes(
                                        $cliente['nome'] ?? ''
                                    ) ?>
                                </strong>
                                <small>
                                    <?= (int) (
                                        $cliente['quantidade_compras']
                                        ?? 0
                                    ) ?>
                                    compras
                                </small>
                            </div>

                            <strong class="valor-ranking-clientes">
                                <?= moedaRelatorioClientes(
                                    (float) (
                                        $cliente['total_gasto']
                                        ?? 0
                                    )
                                ) ?>
                            </strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

        <article class="cartao-clientes">
            <div class="titulo-cartao-clientes">
                <div>
                    <h2>Mais frequentes</h2>
                    <p>Ranking por quantidade de compras.</p>
                </div>
            </div>

            <?php if (empty($maisFrequentes)): ?>
                <div class="vazio-clientes">
                    Nenhuma compra registrada.
                </div>
            <?php else: ?>
                <div class="lista-clientes">
                    <?php foreach ($maisFrequentes as $indice => $cliente): ?>
                        <div class="item-ranking-clientes">
                            <span class="posicao-clientes">
                                <?= $indice + 1 ?>
                            </span>

                            <div class="dados-ranking-clientes">
                                <strong>
                                    <?= escaparRelatorioClientes(
                                        $cliente['nome'] ?? ''
                                    ) ?>
                                </strong>
                                <small>
                                    <?= moedaRelatorioClientes(
                                        (float) (
                                            $cliente['total_gasto']
                                            ?? 0
                                        )
                                    ) ?>
                                    movimentados
                                </small>
                            </div>

                            <strong class="valor-ranking-clientes">
                                <?= (int) (
                                    $cliente['quantidade_compras']
                                    ?? 0
                                ) ?>
                                compras
                            </strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

    </section>

    <section class="cartao-clientes">
        <div class="titulo-cartao-clientes">
            <div>
                <h2>Clientes sem compras</h2>
                <p>Sem movimentação no período selecionado.</p>
            </div>

            <span class="contador-clientes">
                <?= count($semCompras) ?>
            </span>
        </div>

        <?php if (empty($semCompras)): ?>
            <div class="vazio-clientes">
                Todos os clientes tiveram movimentação.
            </div>
        <?php else: ?>
            <div class="grade-sem-compras-clientes">
                <?php foreach ($semCompras as $cliente): ?>
                    <div class="cliente-sem-compra">
                        <strong>
                            <?= escaparRelatorioClientes(
                                $cliente['nome'] ?? ''
                            ) ?>
                        </strong>
                        <small>
                            <?= escaparRelatorioClientes(
                                $cliente['whatsapp']
                                ?: (
                                    $cliente['telefone']
                                    ?: 'Contato não informado'
                                )
                            ) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="cartao-clientes">
        <div class="titulo-cartao-clientes">
            <div>
                <h2>Desempenho completo</h2>
                <p>Resultado individual dos clientes ativos.</p>
            </div>

            <span class="contador-clientes">
                <?= count($clientes) ?> clientes
            </span>
        </div>

        <?php if (empty($clientes)): ?>
            <div class="vazio-clientes">
                Nenhum cliente encontrado.
            </div>
        <?php else: ?>
            <div class="tabela-responsiva-clientes">
                <table class="tabela-clientes">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>CPF/CNPJ</th>
                            <th>Contato</th>
                            <th>Localidade</th>
                            <th>Compras</th>
                            <th>Total gasto</th>
                            <th>Ticket médio</th>
                            <th>Última compra</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= escaparRelatorioClientes(
                                            $cliente['nome'] ?? ''
                                        ) ?>
                                    </strong>
                                    <small>
                                        <?= escaparRelatorioClientes(
                                            $cliente['tipo']
                                            ?? 'Não informado'
                                        ) ?>
                                    </small>
                                </td>

                                <td>
                                    <?= escaparRelatorioClientes(
                                        $cliente['cpf_cnpj'] ?: '-'
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparRelatorioClientes(
                                        $cliente['whatsapp']
                                        ?: ($cliente['telefone'] ?: '-')
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparRelatorioClientes(
                                        $cliente['localidade']
                                        ?? 'Não informada'
                                    ) ?>
                                </td>

                                <td>
                                    <?= (int) (
                                        $cliente['quantidade_compras']
                                        ?? 0
                                    ) ?>
                                </td>

                                <td>
                                    <strong class="positivo-clientes">
                                        <?= moedaRelatorioClientes(
                                            (float) (
                                                $cliente['total_gasto']
                                                ?? 0
                                            )
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= moedaRelatorioClientes(
                                        (float) (
                                            $cliente['ticket_medio']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparRelatorioClientes(
                                        dataRelatorioClientes(
                                            $cliente['ultima_compra']
                                            ?? null
                                        )
                                    ) ?>
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
    .subtitulo-pagina {
        display: block;
        margin-bottom: 4px;
        color: #159ee8;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .cartao-clientes {
        margin-bottom: 22px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .05);
    }

    .titulo-cartao-clientes {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .titulo-cartao-clientes h2 {
        margin: 0 0 5px;
        color: #172033;
        font-size: 20px;
    }

    .titulo-cartao-clientes p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .filtros-clientes {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        align-items: end;
        gap: 16px;
    }

    .filtros-clientes label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 14px;
        font-weight: 800;
    }

    .filtros-clientes input {
        width: 100%;
        min-height: 46px;
        padding: 0 13px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 700;
    }

    .acoes-filtros-clientes {
        display: flex;
        gap: 10px;
    }

    .botao-imprimir-clientes {
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

    .grade-resumo-clientes {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 17px;
        margin-bottom: 22px;
    }

    .card-resumo-clientes {
        min-height: 125px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
    }

    .card-resumo-clientes span {
        display: block;
        margin-bottom: 11px;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .card-resumo-clientes strong {
        display: block;
        margin-bottom: 8px;
        color: #172033;
        font-size: 27px;
    }

    .card-resumo-clientes small {
        color: #64748b;
        font-size: 12px;
    }

    .card-resumo-clientes.azul {
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .card-resumo-clientes.azul strong {
        color: #0284c7;
    }

    .card-resumo-clientes.verde {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .card-resumo-clientes.verde strong {
        color: #15803d;
    }

    .card-resumo-clientes.alerta {
        border-color: #fed7aa;
        background: #fff7ed;
    }

    .card-resumo-clientes.alerta strong {
        color: #c2410c;
    }

    .grade-destaques-clientes {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .lista-clientes {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .item-ranking-clientes {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 64px;
        padding: 13px;
        border: 1px solid #edf2f7;
        border-radius: 11px;
        background: #f8fafc;
    }

    .posicao-clientes {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        border-radius: 50%;
        background: #172033;
        color: #ffffff;
        font-size: 12px;
        font-weight: 900;
    }

    .dados-ranking-clientes {
        flex: 1;
    }

    .dados-ranking-clientes strong {
        display: block;
        color: #172033;
        font-size: 14px;
    }

    .dados-ranking-clientes small {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
    }

    .valor-ranking-clientes {
        color: #159ee8 !important;
        font-size: 14px;
        white-space: nowrap;
    }

    .contador-clientes {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        min-height: 34px;
        padding: 0 10px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
        font-size: 12px;
        font-weight: 900;
    }

    .grade-sem-compras-clientes {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .cliente-sem-compra {
        padding: 13px;
        border: 1px solid #fed7aa;
        border-radius: 10px;
        background: #fff7ed;
    }

    .cliente-sem-compra strong,
    .cliente-sem-compra small {
        display: block;
    }

    .cliente-sem-compra small {
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
    }

    .tabela-responsiva-clientes {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
    }

    .tabela-clientes {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
        background: #ffffff;
    }

    .tabela-clientes th,
    .tabela-clientes td {
        padding: 13px 11px;
        border-bottom: 1px solid #edf2f7;
        color: #1e293b !important;
        font-size: 13px;
        text-align: left;
        vertical-align: middle;
        white-space: nowrap;
        opacity: 1 !important;
    }

    .tabela-clientes th {
        background: #e2e8f0;
        color: #334155 !important;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .tabela-clientes td strong {
        color: #0f172a !important;
        font-size: 14px;
    }

    .tabela-clientes td small {
        display: block;
        margin-top: 4px;
        color: #64748b !important;
        font-size: 11px;
    }

    .positivo-clientes {
        color: #15803d !important;
    }

    .vazio-clientes {
        padding: 28px;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        color: #64748b;
        text-align: center;
        font-size: 14px;
    }

    @media (max-width: 1000px) {
        .grade-resumo-clientes,
        .grade-sem-compras-clientes {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .filtros-clientes,
        .grade-destaques-clientes {
            grid-template-columns: 1fr;
        }

        .acoes-filtros-clientes {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 600px) {
        .grade-resumo-clientes,
        .grade-sem-compras-clientes {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>