<?php

$ordens = $dados['ordens'] ?? [];
$resumo = $dados['resumo'] ?? [];
$clientesRanking = $dados['clientes_ranking'] ?? [];
$aparelhosRanking = $dados['aparelhos_ranking'] ?? [];

$dataInicio = $dados['data_inicio']
    ?? ($_GET['data_inicio'] ?? date('Y-m-01'));

$dataFim = $dados['data_fim']
    ?? ($_GET['data_fim'] ?? date('Y-m-d'));

function escaparRelatorioOs(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function moedaRelatorioOs(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function dataRelatorioOs(?string $data): string
{
    if (empty($data)) {
        return '-';
    }

    return date('d/m/Y H:i', strtotime($data));
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

            <h1>Relatório de Ordens de Serviço</h1>

            <p>
                Consulte quantidade, status, valores,
                clientes e aparelhos atendidos.
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
            <?= escaparRelatorioOs($_SESSION['erro']) ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <section class="cartao-os">

        <div class="titulo-cartao-os">
            <div>
                <h2>Período do relatório</h2>
                <p>Escolha as datas de entrada das ordens.</p>
            </div>
        </div>

        <form
            action="index.php"
            method="get"
            class="filtros-os"
        >
            <input
                type="hidden"
                name="rota"
                value="relatorios-ordens-servico"
            >

            <div>
                <label for="data_inicio">Data inicial</label>
                <input
                    type="date"
                    id="data_inicio"
                    name="data_inicio"
                    value="<?= escaparRelatorioOs($dataInicio) ?>"
                    required
                >
            </div>

            <div>
                <label for="data_fim">Data final</label>
                <input
                    type="date"
                    id="data_fim"
                    name="data_fim"
                    value="<?= escaparRelatorioOs($dataFim) ?>"
                    required
                >
            </div>

            <div class="acoes-filtros-os">
                <button
                    type="submit"
                    class="botao-primario"
                >
                    Aplicar filtro
                </button>

                <a
                    href="index.php?rota=relatorios-imprimir-ordens-servico&data_inicio=<?= urlencode($dataInicio) ?>&data_fim=<?= urlencode($dataFim) ?>"
                    target="_blank"
                    class="botao-imprimir-os"
                >
                    Imprimir relatório
                </a>
            </div>
        </form>

    </section>

    <section class="grade-resumo-os">

        <article class="card-resumo-os">
            <span>Total de ordens</span>
            <strong><?= (int) ($resumo['total_ordens'] ?? 0) ?></strong>
            <small>Entradas no período</small>
        </article>

        <article class="card-resumo-os alerta">
            <span>Em aberto</span>
            <strong><?= (int) ($resumo['ordens_em_aberto'] ?? 0) ?></strong>
            <small>Ainda não finalizadas</small>
        </article>

        <article class="card-resumo-os verde">
            <span>Finalizadas</span>
            <strong><?= (int) ($resumo['ordens_finalizadas'] ?? 0) ?></strong>
            <small>Entregues ou canceladas</small>
        </article>

        <article class="card-resumo-os azul">
            <span>Valor total</span>
            <strong>
                <?= moedaRelatorioOs(
                    (float) ($resumo['valor_total'] ?? 0)
                ) ?>
            </strong>
            <small>Valor líquido das OS</small>
        </article>

        <article class="card-resumo-os">
            <span>Descontos</span>
            <strong>
                <?= moedaRelatorioOs(
                    (float) ($resumo['descontos'] ?? 0)
                ) ?>
            </strong>
            <small>Total concedido</small>
        </article>

        <article class="card-resumo-os verde">
            <span>Ticket médio</span>
            <strong>
                <?= moedaRelatorioOs(
                    (float) ($resumo['ticket_medio'] ?? 0)
                ) ?>
            </strong>
            <small>Média por ordem</small>
        </article>

    </section>

    <section class="cartao-os">
        <div class="titulo-cartao-os">
            <div>
                <h2>Ordens por status</h2>
                <p>Distribuição das ordens no período.</p>
            </div>
        </div>

        <div class="grade-status-os">
            <?php
            $statusNomes = [
                'recebida' => 'Recebidas',
                'em_analise' => 'Em análise',
                'aguardando_aprovacao' => 'Aguardando aprovação',
                'aguardando_peca' => 'Aguardando peça',
                'em_manutencao' => 'Em manutenção',
                'pronta' => 'Prontas',
                'venda' => 'Na venda',
                'entregue' => 'Entregues',
                'cancelada' => 'Canceladas'
            ];
            ?>

            <?php foreach ($statusNomes as $status => $nome): ?>
                <div class="status-os">
                    <span><?= escaparRelatorioOs($nome) ?></span>
                    <strong>
                        <?= (int) (
                            $resumo['status'][$status]
                            ?? 0
                        ) ?>
                    </strong>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="grade-rankings-os">

        <article class="cartao-os">
            <div class="titulo-cartao-os">
                <div>
                    <h2>Clientes com mais OS</h2>
                    <p>Ranking por quantidade.</p>
                </div>
            </div>

            <?php if (empty($clientesRanking)): ?>
                <div class="vazio-os">
                    Nenhuma ordem registrada.
                </div>
            <?php else: ?>
                <div class="lista-ranking-os">
                    <?php foreach ($clientesRanking as $indice => $cliente): ?>
                        <div class="item-ranking-os">
                            <span class="posicao-os">
                                <?= $indice + 1 ?>
                            </span>

                            <div class="dados-ranking-os">
                                <strong>
                                    <?= escaparRelatorioOs(
                                        $cliente['cliente_nome'] ?? ''
                                    ) ?>
                                </strong>

                                <small>
                                    <?= moedaRelatorioOs(
                                        (float) (
                                            $cliente['valor_total']
                                            ?? 0
                                        )
                                    ) ?>
                                    em serviços
                                </small>
                            </div>

                            <strong class="valor-ranking-os">
                                <?= (int) (
                                    $cliente['quantidade']
                                    ?? 0
                                ) ?>
                                OS
                            </strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

        <article class="cartao-os">
            <div class="titulo-cartao-os">
                <div>
                    <h2>Aparelhos mais atendidos</h2>
                    <p>Ranking por quantidade de ordens.</p>
                </div>
            </div>

            <?php if (empty($aparelhosRanking)): ?>
                <div class="vazio-os">
                    Nenhum aparelho registrado.
                </div>
            <?php else: ?>
                <div class="lista-ranking-os">
                    <?php foreach ($aparelhosRanking as $indice => $aparelho): ?>
                        <div class="item-ranking-os">
                            <span class="posicao-os">
                                <?= $indice + 1 ?>
                            </span>

                            <div class="dados-ranking-os">
                                <strong>
                                    <?= escaparRelatorioOs(
                                        $aparelho['aparelho'] ?? ''
                                    ) ?>
                                </strong>

                                <small>
                                    <?= moedaRelatorioOs(
                                        (float) (
                                            $aparelho['valor_total']
                                            ?? 0
                                        )
                                    ) ?>
                                    em serviços
                                </small>
                            </div>

                            <strong class="valor-ranking-os">
                                <?= (int) (
                                    $aparelho['quantidade']
                                    ?? 0
                                ) ?>
                                OS
                            </strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

    </section>

    <section class="cartao-os">

        <div class="titulo-cartao-os">
            <div>
                <h2>Ordens de serviço completas</h2>
                <p>Relação das ordens com entrada no período.</p>
            </div>

            <span class="contador-os">
                <?= count($ordens) ?> ordens
            </span>
        </div>

        <?php if (empty($ordens)): ?>
            <div class="vazio-os">
                Nenhuma ordem encontrada.
            </div>
        <?php else: ?>
            <div class="tabela-responsiva-os">
                <table class="tabela-os">
                    <thead>
                        <tr>
                            <th>OS</th>
                            <th>Cliente</th>
                            <th>Aparelho</th>
                            <th>Defeito</th>
                            <th>Entrada</th>
                            <th>Prevista</th>
                            <th>Valor</th>
                            <th>Desconto</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($ordens as $ordem): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= escaparRelatorioOs(
                                            $ordem['numero_os'] ?? ''
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <strong>
                                        <?= escaparRelatorioOs(
                                            $ordem['cliente_nome'] ?? ''
                                        ) ?>
                                    </strong>
                                    <small>
                                        <?= escaparRelatorioOs(
                                            $ordem['cliente_whatsapp']
                                            ?: (
                                                $ordem['cliente_telefone']
                                                ?: '-'
                                            )
                                        ) ?>
                                    </small>
                                </td>

                                <td>
                                    <?= escaparRelatorioOs(
                                        $ordem['aparelho']
                                        ?? 'Não informado'
                                    ) ?>
                                </td>

                                <td class="coluna-defeito">
                                    <?= escaparRelatorioOs(
                                        $ordem['defeito_relatado']
                                        ?? '-'
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparRelatorioOs(
                                        dataRelatorioOs(
                                            $ordem['data_entrada']
                                            ?? null
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= escaparRelatorioOs(
                                        dataRelatorioOs(
                                            $ordem['data_prevista']
                                            ?? null
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaRelatorioOs(
                                        (float) (
                                            $ordem['valor_os']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= moedaRelatorioOs(
                                        (float) (
                                            $ordem['desconto']
                                            ?? 0
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <strong class="positivo-os">
                                        <?= moedaRelatorioOs(
                                            (float) (
                                                $ordem['valor_total']
                                                ?? 0
                                            )
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="badge-status-os <?= escaparRelatorioOs(
                                        $ordem['status'] ?? ''
                                    ) ?>">
                                        <?= escaparRelatorioOs(
                                            $ordem['status_nome']
                                            ?? ''
                                        ) ?>
                                    </span>
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

    .cartao-os {
        margin-bottom: 22px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .05);
    }

    .titulo-cartao-os {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .titulo-cartao-os h2 {
        margin: 0 0 5px;
        color: #172033;
        font-size: 20px;
    }

    .titulo-cartao-os p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .filtros-os {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        align-items: end;
        gap: 16px;
    }

    .filtros-os label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 14px;
        font-weight: 800;
    }

    .filtros-os input {
        width: 100%;
        min-height: 46px;
        padding: 0 13px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 700;
    }

    .acoes-filtros-os {
        display: flex;
        gap: 10px;
    }

    .botao-imprimir-os {
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

    .grade-resumo-os {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 17px;
        margin-bottom: 22px;
    }

    .card-resumo-os {
        min-height: 125px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
    }

    .card-resumo-os span {
        display: block;
        margin-bottom: 11px;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .card-resumo-os strong {
        display: block;
        margin-bottom: 8px;
        color: #172033;
        font-size: 27px;
    }

    .card-resumo-os small {
        color: #64748b;
        font-size: 12px;
    }

    .card-resumo-os.azul {
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .card-resumo-os.azul strong {
        color: #0284c7;
    }

    .card-resumo-os.verde {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .card-resumo-os.verde strong {
        color: #15803d;
    }

    .card-resumo-os.alerta {
        border-color: #fed7aa;
        background: #fff7ed;
    }

    .card-resumo-os.alerta strong {
        color: #c2410c;
    }

    .grade-status-os {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .status-os {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        background: #f8fafc;
    }

    .status-os span {
        color: #475569;
        font-size: 13px;
        font-weight: 700;
    }

    .status-os strong {
        color: #172033;
        font-size: 20px;
    }

    .grade-rankings-os {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .lista-ranking-os {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .item-ranking-os {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 64px;
        padding: 13px;
        border: 1px solid #edf2f7;
        border-radius: 11px;
        background: #f8fafc;
    }

    .posicao-os {
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

    .dados-ranking-os {
        flex: 1;
    }

    .dados-ranking-os strong {
        display: block;
        color: #172033;
        font-size: 14px;
    }

    .dados-ranking-os small {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
    }

    .valor-ranking-os {
        color: #159ee8 !important;
        white-space: nowrap;
    }

    .contador-os {
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

    .tabela-responsiva-os {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
    }

    .tabela-os {
        width: 100%;
        min-width: 1450px;
        border-collapse: collapse;
        background: #ffffff;
    }

    .tabela-os th,
    .tabela-os td {
        padding: 13px 11px;
        border-bottom: 1px solid #edf2f7;
        color: #1e293b !important;
        font-size: 13px;
        text-align: left;
        vertical-align: middle;
        white-space: nowrap;
        opacity: 1 !important;
    }

    .tabela-os th {
        background: #e2e8f0;
        color: #334155 !important;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .tabela-os td strong {
        color: #0f172a !important;
        font-size: 14px;
    }

    .tabela-os td small {
        display: block;
        margin-top: 4px;
        color: #64748b !important;
        font-size: 11px;
    }

    .coluna-defeito {
        max-width: 290px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .positivo-os {
        color: #15803d !important;
    }

    .badge-status-os {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 10px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 800;
    }

    .badge-status-os.recebida,
    .badge-status-os.em_analise {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-status-os.aguardando_aprovacao,
    .badge-status-os.aguardando_peca {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-status-os.em_manutencao {
        background: #ede9fe;
        color: #6d28d9;
    }

    .badge-status-os.pronta,
    .badge-status-os.venda {
        background: #dcfce7;
        color: #166534;
    }

    .badge-status-os.entregue {
        background: #ccfbf1;
        color: #0f766e;
    }

    .badge-status-os.cancelada {
        background: #fee2e2;
        color: #991b1b;
    }

    .vazio-os {
        padding: 28px;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        color: #64748b;
        text-align: center;
        font-size: 14px;
    }

    @media (max-width: 1000px) {
        .grade-resumo-os,
        .grade-status-os {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .filtros-os,
        .grade-rankings-os {
            grid-template-columns: 1fr;
        }

        .acoes-filtros-os {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 600px) {
        .grade-resumo-os,
        .grade-status-os {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>