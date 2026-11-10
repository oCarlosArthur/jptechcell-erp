<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sucesso = $_SESSION['sucesso_os'] ?? null;
unset($_SESSION['sucesso_os']);

$erro = $_SESSION['erro_os'] ?? null;
unset($_SESSION['erro_os']);

$statusFormatados = [
    'recebida' => 'Recebida',
    'em_analise' => 'Em análise',
    'aguardando_aprovacao' => 'Aguardando aprovação',
    'aguardando_peca' => 'Aguardando peça',
    'em_manutencao' => 'Em manutenção',
    'pronta' => 'Pronta',
    'entregue' => 'Entregue',
    'cancelada' => 'Cancelada'
];

$statusAtual = $ordem['status'] ?? 'recebida';
$osEncerrada = $statusAtual === 'entregue';
$osCancelada = $statusAtual === 'cancelada';
$osPronta = $statusAtual === 'pronta';
$possuiVenda = !empty($ordem['venda_id']);

$nomeStatus = $statusFormatados[$statusAtual]
    ?? ucfirst(str_replace('_', ' ', $statusAtual));

function exibirValorOs(mixed $valor): string
{
    if ($valor === null || $valor === '') {
        return '-';
    }

    return htmlspecialchars((string) $valor);
}

function formatarDataOrdem(?string $data, bool $comHorario = true): string
{
    if (empty($data)) {
        return '-';
    }

    $formato = $comHorario
        ? 'd/m/Y H:i'
        : 'd/m/Y';

    return date($formato, strtotime($data));
}

function formatarSimNao(mixed $valor): string
{
    return (string) $valor === '1'
        ? 'Sim'
        : 'Não';
}

?>

<style>
    .topo-visualizar-os {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .topo-visualizar-os h1 {
        margin: 0 0 6px;
        color: #ffffff;
        font-size: 32px;
    }

    .topo-visualizar-os p {
        margin: 0;
        color: #aebed0;
        font-size: 15px;
    }

    .acoes-topo-os {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }

    .botao-os {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        box-sizing: border-box;
        padding: 0 18px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        transition: 0.2s;
    }

    .botao-os-voltar {
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: transparent;
        color: #c7d3e2;
    }

    .botao-os-voltar:hover {
        border-color: rgba(255, 255, 255, 0.28);
        background: rgba(255, 255, 255, 0.05);
        color: #ffffff;
    }

    .botao-os-imprimir {
        border: none;
        background: #159ee8;
        color: #ffffff;
    }

    .botao-os-imprimir:hover {
        filter: brightness(1.1);
        color: #ffffff;
    }

    .mensagem-os {
        margin-bottom: 20px;
        padding: 14px 16px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 700;
    }

    .mensagem-os.sucesso {
        border: 1px solid rgba(32, 229, 143, 0.25);
        background: rgba(32, 229, 143, 0.1);
        color: #20e58f;
    }

    .mensagem-os.erro {
        border: 1px solid rgba(255, 91, 91, 0.25);
        background: rgba(255, 91, 91, 0.1);
        color: #ff7777;
    }

    .resumo-os {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .cartao-resumo-os {
        padding: 18px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        background: #19263a;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    }

    .cartao-resumo-os span {
        display: block;
        margin-bottom: 8px;
        color: #8292a7;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .cartao-resumo-os strong {
        display: block;
        color: #ffffff;
        font-size: 18px;
    }

    .cartao-resumo-os.valor strong {
        color: #20e58f;
    }

    .status-visualizacao-os {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(82, 214, 255, 0.13);
        color: #8ee3ff;
        font-size: 12px;
        font-weight: 900;
    }

    .grade-detalhes-os {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
    }

    .secao-detalhes-os {
        padding: 24px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        background: #19263a;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .secao-detalhes-os.inteira {
        grid-column: 1 / -1;
    }

    .secao-detalhes-os h2 {
        margin: 0 0 20px;
        padding-bottom: 13px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        color: #ffffff;
        font-size: 19px;
    }

    .lista-detalhes-os {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .detalhe-os {
        min-width: 0;
    }

    .detalhe-os.inteiro {
        grid-column: 1 / -1;
    }

    .detalhe-os span {
        display: block;
        margin-bottom: 6px;
        color: #8292a7;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .detalhe-os strong,
    .detalhe-os p {
        margin: 0;
        color: #ffffff;
        font-size: 14px;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .detalhe-os p {
        white-space: pre-wrap;
    }

    .valor-destaque-os {
        color: #20e58f !important;
        font-size: 20px !important;
        font-weight: 900;
    }

    .aviso-impressao-os {
        margin-top: 22px;
        padding: 16px 18px;
        border: 1px solid rgba(82, 214, 255, 0.16);
        border-radius: 12px;
        background: rgba(82, 214, 255, 0.06);
        color: #9fb0c4;
        font-size: 13px;
        line-height: 1.6;
    }

    .area-status-os {
    margin-bottom: 22px;
    padding: 22px;
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 16px;
    background: #19263a;
}

.cabecalho-status-os {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 18px;
}

.cabecalho-status-os h2 {
    margin: 0;
    color: #ffffff;
    font-size: 19px;
}

.form-status-os {
    display: flex;
    align-items: flex-end;
    gap: 12px;
}

.campo-status-os {
    flex: 1;
    min-width: 220px;
}

.campo-status-os label {
    display: block;
    margin-bottom: 7px;
    color: #9aabc0;
    font-size: 13px;
    font-weight: 800;
}

.campo-status-os select {
    width: 100%;
    height: 46px;
    padding: 0 13px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 9px;
    outline: none;
    background: #101c2d;
    color: #ffffff;
    font-size: 14px;
}

.botao-alterar-status {
    min-height: 46px;
    padding: 0 18px;
    border: none;
    border-radius: 9px;
    background: #159ee8;
    color: #ffffff;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
}

.botao-marcar-pronta {
    min-height: 46px;
    padding: 0 18px;
    border: none;
    border-radius: 9px;
    background: #20c77a;
    color: #07180f;
    font-size: 14px;
    font-weight: 900;
    cursor: pointer;
}

.situacao-financeira-os {
    margin-top: 15px;
    padding: 13px 15px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.04);
    color: #aebed0;
    font-size: 14px;
}

.situacao-financeira-os strong {
    color: #ffffff;
}

.aviso-pronta-os {
    padding: 16px;
    border: 1px solid rgba(32, 229, 143, 0.2);
    border-radius: 10px;
    background: rgba(32, 229, 143, 0.08);
    color: #aef7d1;
    line-height: 1.6;
}

.aviso-encerrada-os {
    padding: 16px;
    border: 1px solid rgba(82, 214, 255, 0.2);
    border-radius: 10px;
    background: rgba(82, 214, 255, 0.08);
    color: #b8edff;
    line-height: 1.6;
}

html,
body {
    max-width: 100%;
    overflow-x: hidden;
}

* {
    box-sizing: border-box;
}

.conteudo {
    width: auto;
    max-width: 100%;
    min-width: 0;
    overflow-x: hidden;
}

.botao-os-editar {
    border: none;
    background: #f2b84b;
    color: #17202d;
}

.botao-os-editar:hover {
    filter: brightness(1.08);
    color: #17202d;
}

.topo-visualizar-os,
.resumo-os,
.grade-detalhes-os,
.secao-detalhes-os,
.lista-detalhes-os,
.detalhe-os {
    min-width: 0;
    max-width: 100%;
}

@media (max-width: 750px) {
    .cabecalho-status-os {
        align-items: stretch;
        flex-direction: column;
    }

    .form-status-os {
        align-items: stretch;
        flex-direction: column;
    }

    .campo-status-os {
        width: 100%;
        min-width: 0;
    }

    .botao-alterar-status,
    .botao-marcar-pronta {
        width: 100%;
    }
}

    @media (max-width: 1000px) {
        .resumo-os {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .topo-visualizar-os {
            align-items: stretch;
            flex-direction: column;
        }

        .acoes-topo-os {
            justify-content: stretch;
        }

        .botao-os {
            flex: 1;
        }

        .grade-detalhes-os {
            grid-template-columns: 1fr;
        }

        .secao-detalhes-os.inteira {
            grid-column: 1;
        }
    }

    @media (max-width: 600px) {
        .resumo-os {
            grid-template-columns: 1fr;
        }

        .lista-detalhes-os {
            grid-template-columns: 1fr;
        }

        .detalhe-os.inteiro {
            grid-column: 1;
        }

        .secao-detalhes-os {
            padding: 17px;
        }

        .acoes-topo-os {
            flex-direction: column;
        }

        .botao-os {
            width: 100%;
        }
    }

.area-historico-os {
    margin-top: 22px;
    padding: 24px;
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 16px;
    background: #19263a;
}

.cabecalho-historico-os {
    margin-bottom: 22px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.cabecalho-historico-os h2 {
    margin: 0 0 5px;
    color: #ffffff;
    font-size: 19px;
}

.cabecalho-historico-os p {
    margin: 0;
    color: #8292a7;
    font-size: 13px;
}

.lista-historico-os {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 18px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.item-historico-os {
    position: relative;
    padding: 18px 18px 18px 50px;
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 12px;
    background: #101c2d;
}

.item-historico-os::before {
    position: absolute;
    top: 21px;
    left: 20px;
    width: 13px;
    height: 13px;
    border: 3px solid #19263a;
    border-radius: 50%;
    background: #52d6ff;
    box-shadow: 0 0 0 2px rgba(82, 214, 255, 0.25);
    content: "";
}

.item-historico-os.tipo-status::before {
    background: #f2b84b;
    box-shadow: 0 0 0 2px rgba(242, 184, 75, 0.25);
}

.item-historico-os.tipo-criacao::before {
    background: #20e58f;
    box-shadow: 0 0 0 2px rgba(32, 229, 143, 0.25);
}

.topo-item-historico {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 10px;
}

.descricao-historico {
    margin: 0;
    color: #ffffff;
    font-size: 14px;
    font-weight: 900;
}

.data-historico {
    flex-shrink: 0;
    color: #8292a7;
    font-size: 12px;
    font-weight: 700;
}

.valores-historico {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.valor-historico {
    padding: 12px;
    border-radius: 9px;
    background: rgba(255, 255, 255, 0.04);
}

.valor-historico span {
    display: block;
    margin-bottom: 5px;
    color: #8292a7;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
}

.valor-historico p {
    margin: 0;
    color: #ffffff;
    font-size: 13px;
    line-height: 1.5;
    overflow-wrap: anywhere;
    white-space: pre-wrap;
}

.historico-vazio {
    padding: 18px;
    border: 1px dashed rgba(255, 255, 255, 0.12);
    border-radius: 10px;
    color: #8292a7;
    text-align: center;
}

@media (max-width: 600px) {
    .area-historico-os {
        padding: 17px;
    }

    .item-historico-os {
        padding: 17px;
    }

    .item-historico-os::before {
        display: none;
    }

    .topo-item-historico {
        flex-direction: column;
    }

    .valores-historico {
        grid-template-columns: 1fr;
    }
}

</style>

<main class="conteudo">

    <header class="topo-visualizar-os">

        <div>
            <h1>
                <?= htmlspecialchars($ordem['numero_os']) ?>
            </h1>

            <p>
                Detalhes completos da ordem de serviço.
            </p>
        </div>

        <div class="acoes-topo-os">

            <a
                class="botao-os botao-os-voltar"
                href="index.php?rota=ordens-servico"
            >
                ← Voltar
            </a>

            <?php if (
                !$osEncerrada
                && !$possuiVenda
            ): ?>

                <a
                    class="botao-os botao-os-editar"
                    href="index.php?rota=ordem-servico-editar&id=<?= (int) $ordem['id'] ?>"
                >
                    Editar OS
                </a>

            <?php endif; ?>

            <a
                class="botao-os botao-os-imprimir"
                href="index.php?rota=ordem-servico-imprimir&id=<?= (int) $ordem['id'] ?>"
            >
                Imprimir ordem de serviço
            </a>

        </div>

    </header>

    <?php if ($sucesso): ?>

        <div class="mensagem-os sucesso">
            <?= htmlspecialchars($sucesso) ?>
        </div>

    <?php endif; ?>

    <?php if ($erro): ?>

        <div class="mensagem-os erro">
            <?= htmlspecialchars($erro) ?>
        </div>

    <?php endif; ?>

    <section class="area-status-os">

    <div class="cabecalho-status-os">

        <h2>Andamento da ordem de serviço</h2>

        <span class="status-visualizacao-os">
            <?= htmlspecialchars($nomeStatus) ?>
        </span>

    </div>

    <?php if ($osEncerrada): ?>

        <div class="aviso-encerrada-os">
            Esta ordem de serviço foi entregue e está encerrada.

            <?php if ($possuiVenda): ?>
                <br>
                Venda vinculada:
                <strong>
                    <?= htmlspecialchars(
                        (string) $ordem['venda_id']
                    ) ?>
                </strong>
            <?php endif; ?>
        </div>

    <?php elseif ($osCancelada): ?>

        <div class="aviso-encerrada-os">
            Esta ordem de serviço está cancelada.
            Para reabrir, selecione outro status abaixo.
        </div>

        <form
            class="form-status-os"
            action="index.php?rota=ordem-servico-status"
            method="POST"
        >

            <input
                type="hidden"
                name="id"
                value="<?= (int) $ordem['id'] ?>"
            >

            <div class="campo-status-os">

                <label for="status">
                    Novo status
                </label>

                <select name="status" id="status" required>

                    <option value="recebida">
                        Recebida
                    </option>

                    <option value="em_analise">
                        Em análise
                    </option>

                    <option value="aguardando_aprovacao">
                        Aguardando aprovação
                    </option>

                    <option value="aguardando_peca">
                        Aguardando peça
                    </option>

                    <option value="em_manutencao">
                        Em manutenção
                    </option>

                    <option value="pronta">
                        Pronta
                    </option>

                </select>

            </div>

            <button
                type="submit"
                class="botao-alterar-status"
            >
                Reabrir ordem
            </button>

        </form>

    <?php elseif ($osPronta): ?>

        <div class="aviso-pronta-os">
            O aparelho está pronto.

            <br>

            A próxima etapa será adicionar esta ordem a uma venda.

            <br>

            Depois que o pagamento for concluído, o sistema marcará
            automaticamente a OS como entregue.
        </div>

        <div class="situacao-financeira-os">
            Situação financeira:
            <strong>Aguardando pagamento</strong>
        </div>

    <?php else: ?>

        <form
            class="form-status-os"
            action="index.php?rota=ordem-servico-status"
            method="POST"
        >

            <input
                type="hidden"
                name="id"
                value="<?= (int) $ordem['id'] ?>"
            >

            <div class="campo-status-os">

                <label for="status">
                    Alterar status
                </label>

                <select name="status" id="status" required>

                    <option
                        value="recebida"
                        <?= $statusAtual === 'recebida'
                            ? 'selected'
                            : '' ?>
                    >
                        Recebida
                    </option>

                    <option
                        value="em_analise"
                        <?= $statusAtual === 'em_analise'
                            ? 'selected'
                            : '' ?>
                    >
                        Em análise
                    </option>

                    <option
                        value="aguardando_aprovacao"
                        <?= $statusAtual === 'aguardando_aprovacao'
                            ? 'selected'
                            : '' ?>
                    >
                        Aguardando aprovação
                    </option>

                    <option
                        value="aguardando_peca"
                        <?= $statusAtual === 'aguardando_peca'
                            ? 'selected'
                            : '' ?>
                    >
                        Aguardando peça
                    </option>

                    <option
                        value="em_manutencao"
                        <?= $statusAtual === 'em_manutencao'
                            ? 'selected'
                            : '' ?>
                    >
                        Em manutenção
                    </option>

                    <option
                        value="pronta"
                        <?= $statusAtual === 'pronta'
                            ? 'selected'
                            : '' ?>
                    >
                        Pronta
                    </option>

                    <option value="cancelada">
                        Cancelada
                    </option>

                </select>

            </div>

            <button
                type="submit"
                class="botao-alterar-status"
            >
                Salvar status
            </button>

        </form>

        <form
            action="index.php?rota=ordem-servico-status"
            method="POST"
            onsubmit="
                return confirm(
                    'Confirma que o aparelho está pronto?'
                );
            "
        >

            <input
                type="hidden"
                name="id"
                value="<?= (int) $ordem['id'] ?>"
            >

            <input
                type="hidden"
                name="status"
                value="pronta"
            >

            <button
                type="submit"
                class="botao-marcar-pronta"
            >
                Finalizar serviço e marcar como pronta
            </button>

        </form>

    <?php endif; ?>

</section>

    <section class="resumo-os">

        <div class="cartao-resumo-os">
            <span>Cliente</span>

            <strong>
                <?= htmlspecialchars($ordem['cliente_nome']) ?>
            </strong>
        </div>

        <div class="cartao-resumo-os">
            <span>Aparelho</span>

            <strong>
                <?= htmlspecialchars(
                    trim(
                        ($ordem['aparelho_marca'] ?? '')
                        . ' '
                        . ($ordem['aparelho_modelo'] ?? '')
                    )
                ) ?>
            </strong>
        </div>

        <div class="cartao-resumo-os valor">
            <span>Total final</span>

            <strong>
                R$ <?= number_format(
                    (float) $ordem['valor_total'],
                    2,
                    ',',
                    '.'
                ) ?>
            </strong>
        </div>

        <div class="cartao-resumo-os">
            <span>Status</span>

            <strong>
                <span class="status-visualizacao-os">
                    <?= htmlspecialchars($nomeStatus) ?>
                </span>
            </strong>
        </div>

    </section>

    <div class="grade-detalhes-os">

        <section class="secao-detalhes-os">

            <h2>Dados do cliente</h2>

            <div class="lista-detalhes-os">

                <div class="detalhe-os inteiro">
                    <span>Nome</span>

                    <strong>
                        <?= htmlspecialchars($ordem['cliente_nome']) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>CPF/CNPJ</span>

                    <strong>
                        <?= exibirValorOs(
                            $ordem['cliente_cpf_cnpj'] ?? null
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>Telefone</span>

                    <strong>
                        <?= exibirValorOs(
                            $ordem['cliente_telefone'] ?? null
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>WhatsApp</span>

                    <strong>
                        <?= exibirValorOs(
                            $ordem['cliente_whatsapp'] ?? null
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>E-mail</span>

                    <strong>
                        <?= exibirValorOs(
                            $ordem['cliente_email'] ?? null
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os inteiro">
                    <span>Endereço</span>

                    <strong>
                        <?php
                        $partesEndereco = array_filter([
                            $ordem['cliente_endereco'] ?? '',
                            $ordem['cliente_numero'] ?? '',
                            $ordem['cliente_bairro'] ?? '',
                            $ordem['cliente_cidade'] ?? '',
                            $ordem['cliente_estado'] ?? ''
                        ]);

                        echo !empty($partesEndereco)
                            ? htmlspecialchars(
                                implode(', ', $partesEndereco)
                            )
                            : '-';
                        ?>
                    </strong>
                </div>

            </div>

        </section>

        <section class="secao-detalhes-os">

            <h2>Dados do aparelho</h2>

            <div class="lista-detalhes-os">

                <div class="detalhe-os">
                    <span>Marca</span>

                    <strong>
                        <?= exibirValorOs(
                            $ordem['aparelho_marca'] ?? null
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>Modelo</span>

                    <strong>
                        <?= htmlspecialchars(
                            $ordem['aparelho_modelo']
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>Cor</span>

                    <strong>
                        <?= exibirValorOs(
                            $ordem['aparelho_cor'] ?? null
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>IMEI ou número de série</span>

                    <strong>
                        <?= exibirValorOs(
                            $ordem['aparelho_imei'] ?? null
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>Senha</span>

                    <strong>
                        <?= exibirValorOs(
                            $ordem['aparelho_senha'] ?? null
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>Aparelho liga</span>

                    <strong>
                        <?= formatarSimNao(
                            $ordem['aparelho_ligado'] ?? 0
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>Está desbloqueado</span>

                    <strong>
                        <?= formatarSimNao(
                            $ordem['aparelho_desbloqueado'] ?? 0
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os inteiro">
                    <span>Acessórios entregues</span>

                    <strong>
                        <?= exibirValorOs(
                            $ordem['acessorios_entregues'] ?? null
                        ) ?>
                    </strong>
                </div>

            </div>

        </section>

        <section class="secao-detalhes-os inteira">

            <h2>Atendimento técnico</h2>

            <div class="lista-detalhes-os">

                <div class="detalhe-os inteiro">
                    <span>Defeito relatado</span>

                    <p>
                        <?= exibirValorOs(
                            $ordem['defeito_relatado'] ?? null
                        ) ?>
                    </p>
                </div>

                <div class="detalhe-os inteiro">
                    <span>Diagnóstico</span>

                    <p>
                        <?= exibirValorOs(
                            $ordem['diagnostico'] ?? null
                        ) ?>
                    </p>
                </div>

                <div class="detalhe-os inteiro">
                    <span>Serviço realizado</span>

                    <p>
                        <?= exibirValorOs(
                            $ordem['servico_realizado'] ?? null
                        ) ?>
                    </p>
                </div>

                <div class="detalhe-os inteiro">
                    <span>Observações</span>

                    <p>
                        <?= exibirValorOs(
                            $ordem['observacoes'] ?? null
                        ) ?>
                    </p>
                </div>

            </div>

        </section>

        <section class="secao-detalhes-os">

            <h2>Valores</h2>

            <div class="lista-detalhes-os">

                <div class="detalhe-os">
                    <span>Valor da OS</span>

                    <strong>
                        R$ <?= number_format(
                            (float) $ordem['valor_os'],
                            2,
                            ',',
                            '.'
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>Desconto</span>

                    <strong>
                        R$ <?= number_format(
                            (float) $ordem['desconto'],
                            2,
                            ',',
                            '.'
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os inteiro">
                    <span>Total final</span>

                    <strong class="valor-destaque-os">
                        R$ <?= number_format(
                            (float) $ordem['valor_total'],
                            2,
                            ',',
                            '.'
                        ) ?>
                    </strong>
                </div>

            </div>

        </section>

        <section class="secao-detalhes-os">

            <h2>Prazo e garantia</h2>

            <div class="lista-detalhes-os">

                <div class="detalhe-os">
                    <span>Criada em</span>

                    <strong>
                        <?= formatarDataOrdem(
                            $ordem['data_entrada'] ?? null
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os">
                    <span>Prazo para entrega</span>

                    <strong>
                        <?= formatarDataOrdem(
                            $ordem['data_prevista'] ?? null,
                            false
                        ) ?>
                    </strong>
                </div>

                <div class="detalhe-os inteiro">
                    <span>Garantia</span>

                    <strong>
                        <?= (int) ($ordem['garantia_dias'] ?? 0) ?>
                        dias
                    </strong>
                </div>

            </div>

        </section>

    </div>


    <section class="area-historico-os">

        <div class="cabecalho-historico-os">

            <h2>Histórico da ordem de serviço</h2>

            <p>
                Registro automático de todas as alterações realizadas.
            </p>

        </div>

        <?php if (!empty($historico)): ?>

            <ol class="lista-historico-os">

                <?php foreach ($historico as $registro): ?>

                    <?php
                    $tipoHistorico = preg_replace(
                        '/[^a-z_]/',
                        '',
                        (string) ($registro['tipo'] ?? 'edicao')
                    );
                    ?>

                    <li
                        class="item-historico-os tipo-<?= htmlspecialchars($tipoHistorico) ?>"
                    >

                        <div class="topo-item-historico">

                            <p class="descricao-historico">
                                <?= htmlspecialchars(
                                    (string) $registro['descricao']
                                ) ?>
                            </p>

                            <time class="data-historico">
                                <?= date(
                                    'd/m/Y \à\s H:i',
                                    strtotime(
                                        (string) $registro['criado_em']
                                    )
                                ) ?>
                            </time>

                        </div>

                        <?php if (
                            $registro['valor_anterior'] !== null
                            || $registro['valor_novo'] !== null
                        ): ?>

                            <div class="valores-historico">

                                <div class="valor-historico">

                                    <span>Antes</span>

                                    <p>
                                        <?= htmlspecialchars(
                                            (string) (
                                                $registro['valor_anterior']
                                                ?? 'Não informado'
                                            )
                                        ) ?>
                                    </p>

                                </div>

                                <div class="valor-historico">

                                    <span>Depois</span>

                                    <p>
                                        <?= htmlspecialchars(
                                            (string) (
                                                $registro['valor_novo']
                                                ?? 'Não informado'
                                            )
                                        ) ?>
                                    </p>

                                </div>

                            </div>

                        <?php endif; ?>

                    </li>

                <?php endforeach; ?>

            </ol>

        <?php else: ?>

            <div class="historico-vazio">
                Ainda não há alterações registradas nesta ordem.
            </div>

        <?php endif; ?>

    </section>


    <div class="aviso-impressao-os">
        O botão de impressão já está nesta tela. No próximo passo,
        criaremos a página em folha A4 com a logo, dados da loja,
        via da empresa e via do cliente.
    </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>