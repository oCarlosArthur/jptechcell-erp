<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$erro = $_SESSION['erro_os'] ?? null;
unset($_SESSION['erro_os']);

?>

<style>
    .topo-nova-os {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .topo-nova-os h1 {
        margin: 0 0 5px;
        color: #ffffff;
        font-size: 32px;
    }

    .topo-nova-os p {
        margin: 0;
        color: #aebed0;
        font-size: 15px;
    }

    .botao-voltar-os {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 18px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 9px;
        background: transparent;
        color: #c7d3e2;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        transition: 0.2s;
    }

    .botao-voltar-os:hover {
        border-color: rgba(255, 255, 255, 0.28);
        background: rgba(255, 255, 255, 0.05);
        color: #ffffff;
    }

    .mensagem-os {
        margin-bottom: 20px;
        padding: 14px 16px;
        border: 1px solid rgba(255, 91, 91, 0.25);
        border-radius: 9px;
        background: rgba(255, 91, 91, 0.1);
        color: #ff7777;
        font-size: 14px;
        font-weight: 700;
    }

    .formulario-os {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .secao-os {
        padding: 24px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        background: #19263a;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .cabecalho-secao-os {
        margin-bottom: 22px;
        padding-bottom: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .cabecalho-secao-os h2 {
        margin: 0 0 5px;
        color: #ffffff;
        font-size: 19px;
    }

    .cabecalho-secao-os p {
        margin: 0;
        color: #8292a7;
        font-size: 13px;
    }

    .grade-os {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 18px;
    }

    .campo-os {
        display: flex;
        flex-direction: column;
        gap: 8px;
        grid-column: span 4;
        min-width: 0;
    }

    .campo-os.metade {
        grid-column: span 6;
    }

    .campo-os.dois-tercos {
        grid-column: span 8;
    }

    .campo-os.um-terco {
        grid-column: span 4;
    }

    .campo-os.inteiro {
        grid-column: 1 / -1;
    }

    .campo-os label {
        color: #c7d3e2;
        font-size: 13px;
        font-weight: 800;
    }

    .campo-os label .obrigatorio {
        color: #ff7777;
    }

    .campo-os input,
    .campo-os select,
    .campo-os textarea {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid rgba(255, 255, 255, 0.13);
        border-radius: 9px;
        outline: none;
        background: #101b2b;
        color: #ffffff;
        font-family: inherit;
        font-size: 14px;
        transition: 0.2s;
    }

    .campo-os input,
    .campo-os select {
        min-height: 46px;
        padding: 0 14px;
    }

    .campo-os textarea {
        min-height: 115px;
        padding: 13px 14px;
        resize: vertical;
    }

    .campo-os input::placeholder,
    .campo-os textarea::placeholder {
        color: #6f8095;
    }

    .campo-os input:focus,
    .campo-os select:focus,
    .campo-os textarea:focus {
        border-color: #52d6ff;
        box-shadow: 0 0 0 3px rgba(82, 214, 255, 0.1);
    }

    .campo-os select option {
        background: #101b2b;
        color: #ffffff;
    }

    .ajuda-campo {
        color: #718399;
        font-size: 11px;
        line-height: 1.4;
    }

    .grupo-opcoes-os {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .opcao-os {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 13px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 9px;
        background: #101b2b;
        color: #c7d3e2;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .opcao-os input {
        width: 16px;
        min-height: auto;
        height: 16px;
        margin: 0;
        accent-color: #20e58f;
    }

    .resumo-valores-os {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .valor-total-os input {
        border-color: rgba(32, 229, 143, 0.35);
        background: rgba(32, 229, 143, 0.06);
        color: #20e58f;
        font-size: 18px;
        font-weight: 900;
    }

    .acoes-formulario-os {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px 24px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        background: #19263a;
    }

    .botao-cancelar-os {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 20px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 9px;
        background: transparent;
        color: #c7d3e2;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s;
    }

    .botao-cancelar-os:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #ffffff;
    }

    .botao-salvar-os {
        min-height: 46px;
        padding: 0 22px;
        border: none;
        border-radius: 9px;
        background: #20e58f;
        color: #ffffff;
        font-size: 14px;
        font-weight: 900;
        cursor: pointer;
        transition: 0.2s;
    }

    .botao-salvar-os:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
    }

    @media (max-width: 900px) {
        .campo-os,
        .campo-os.metade,
        .campo-os.dois-tercos,
        .campo-os.um-terco {
            grid-column: span 6;
        }

        .campo-os.inteiro {
            grid-column: 1 / -1;
        }

        .resumo-valores-os {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .topo-nova-os {
            align-items: stretch;
            flex-direction: column;
        }

        .botao-voltar-os {
            width: 100%;
            box-sizing: border-box;
        }

        .grade-os {
            grid-template-columns: 1fr;
        }

        .campo-os,
        .campo-os.metade,
        .campo-os.dois-tercos,
        .campo-os.um-terco,
        .campo-os.inteiro {
            grid-column: 1;
        }

        .secao-os {
            padding: 17px;
        }

        .acoes-formulario-os {
            align-items: stretch;
            flex-direction: column-reverse;
            padding: 17px;
        }

        .botao-cancelar-os,
        .botao-salvar-os {
            width: 100%;
            box-sizing: border-box;
        }
    }
</style>

<main class="conteudo">

    <header class="topo-nova-os">

        <div>
            <h1>Editar ordem de serviço</h1>

            <p>
                Atualize os dados da ordem <?= htmlspecialchars($ordem['numero_os']) ?>.
            </p>
        </div>

        <a
            class="botao-voltar-os"
            href="index.php?rota=ordem-servico-visualizar&id=<?= (int) $ordem['id'] ?>"
        >
            ← Voltar para ordens
        </a>

    </header>

    <?php if ($erro): ?>

        <div class="mensagem-os">
            <?= htmlspecialchars($erro) ?>
        </div>

    <?php endif; ?>

    <form
        class="formulario-os"
        method="POST"
        action="index.php?rota=ordem-servico-atualizar"
        id="formularioOrdemServico"
    >

        <input
            type="hidden"
            name="id"
            value="<?= (int) $ordem['id'] ?>"
        >

        <section class="secao-os">

            <div class="cabecalho-secao-os">
                <h2>Cliente</h2>

                <p>
                    Selecione o cliente responsável pelo aparelho.
                </p>
            </div>

            <div class="grade-os">

                <div class="campo-os inteiro">

                    <label for="cliente_id">
                        Cliente
                        <span class="obrigatorio">*</span>
                    </label>

                    <select
                        id="cliente_id"
                        name="cliente_id"
                        required
                    >
                        <option value="">
                            Selecione um cliente
                        </option>

                        <?php foreach ($clientes as $cliente): ?>

                            <option
                                value="<?= (int) $cliente['id'] ?>"
                                <?= (int) $ordem['cliente_id'] === (int) $cliente['id']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $cliente['nome']
                                ) ?>

                                <?php if (!empty($cliente['telefone'])): ?>
                                    -
                                    <?= htmlspecialchars(
                                        $cliente['telefone']
                                    ) ?>
                                <?php endif; ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <?php if (empty($clientes)): ?>

                        <span class="ajuda-campo">
                            Nenhum cliente foi encontrado. Cadastre um
                            cliente antes de criar a ordem de serviço.
                        </span>

                    <?php endif; ?>

                </div>

            </div>

        </section>

        <section class="secao-os">

            <div class="cabecalho-secao-os">
                <h2>Dados do aparelho</h2>

                <p>
                    Informe os dados utilizados para identificar o aparelho recebido.
                </p>
            </div>

            <div class="grade-os">

                <div class="campo-os">
                    <label for="aparelho_marca">
                        Marca
                    </label>

                    <input
                        id="aparelho_marca"
                        name="aparelho_marca"
                        type="text"
                        maxlength="100"
                        placeholder="Ex.: Apple, Samsung, Motorola"
                        value="<?= htmlspecialchars((string) ($ordem['aparelho_marca'] ?? '')) ?>"
                    >
                </div>

                <div class="campo-os">
                    <label for="aparelho_modelo">
                        Modelo
                        <span class="obrigatorio">*</span>
                    </label>

                    <input
                        id="aparelho_modelo"
                        name="aparelho_modelo"
                        type="text"
                        maxlength="100"
                        placeholder="Ex.: iPhone 13, Galaxy A54"
                        value="<?= htmlspecialchars((string) ($ordem['aparelho_modelo'] ?? '')) ?>"
                        required
                    >
                </div>

                <div class="campo-os">
                    <label for="aparelho_cor">
                        Cor
                    </label>

                    <input
                        id="aparelho_cor"
                        name="aparelho_cor"
                        type="text"
                        maxlength="50"
                        placeholder="Ex.: Preto"
                        value="<?= htmlspecialchars((string) ($ordem['aparelho_cor'] ?? '')) ?>"
                    >
                </div>

                <div class="campo-os metade">
                    <label for="aparelho_imei">
                        IMEI ou número de série
                    </label>

                    <input
                        id="aparelho_imei"
                        name="aparelho_imei"
                        type="text"
                        maxlength="100"
                        placeholder="Opcional"
                        value="<?= htmlspecialchars((string) ($ordem['aparelho_imei'] ?? '')) ?>"
                    >
                </div>

                <div class="campo-os metade">
                    <label for="aparelho_senha">
                        Senha ou padrão de desbloqueio
                    </label>

                    <input
                        id="aparelho_senha"
                        name="aparelho_senha"
                        type="text"
                        maxlength="100"
                        placeholder="Opcional"
                        value="<?= htmlspecialchars((string) ($ordem['aparelho_senha'] ?? '')) ?>"
                        autocomplete="off"
                    >

                    <span class="ajuda-campo">
                        Esse dado ficará registrado na ordem para uso durante o atendimento.
                    </span>
                </div>

                <div class="campo-os metade">

                    <label>
                        O aparelho liga?
                    </label>

                    <div class="grupo-opcoes-os">

                        <label class="opcao-os">
                            <input
                                type="radio"
                                name="aparelho_ligado"
                                value="1"
                                <?= (string) ($ordem['aparelho_ligado'] ?? '0') === '1' ? 'checked' : '' ?>
                            >
                            Sim
                        </label>

                        <label class="opcao-os">
                            <input
                                type="radio"
                                name="aparelho_ligado"
                                value="0"
                                <?= (string) ($ordem['aparelho_ligado'] ?? '0') !== '1' ? 'checked' : '' ?>
                            >
                            Não
                        </label>

                    </div>

                </div>

                <div class="campo-os metade">

                    <label>
                        O aparelho está desbloqueado?
                    </label>

                    <div class="grupo-opcoes-os">

                        <label class="opcao-os">
                            <input
                                type="radio"
                                name="aparelho_desbloqueado"
                                value="1"
                                <?= (string) ($ordem['aparelho_desbloqueado'] ?? '0') === '1' ? 'checked' : '' ?>
                            >
                            Sim
                        </label>

                        <label class="opcao-os">
                            <input
                                type="radio"
                                name="aparelho_desbloqueado"
                                value="0"
                                <?= (string) ($ordem['aparelho_desbloqueado'] ?? '0') !== '1' ? 'checked' : '' ?>
                            >
                            Não
                        </label>

                    </div>

                </div>

                <div class="campo-os inteiro">
                    <label for="acessorios_entregues">
                        Acessórios entregues com o aparelho
                    </label>

                    <input
                        id="acessorios_entregues"
                        name="acessorios_entregues"
                        type="text"
                        maxlength="255"
                        placeholder="Ex.: carregador, cabo, capinha, chip, caixa"
                        value="<?= htmlspecialchars((string) ($ordem['acessorios_entregues'] ?? '')) ?>"
                    >
                </div>

            </div>

        </section>

        <section class="secao-os">

            <div class="cabecalho-secao-os">
                <h2>Atendimento técnico</h2>

                <p>
                    Registre o problema informado e os detalhes técnicos do serviço.
                </p>
            </div>

            <div class="grade-os">

                <div class="campo-os inteiro">
                    <label for="defeito_relatado">
                        Defeito relatado pelo cliente
                        <span class="obrigatorio">*</span>
                    </label>

                    <textarea
                        id="defeito_relatado"
                        name="defeito_relatado"
                        placeholder="Descreva exatamente o problema informado pelo cliente..."
                        required
                    ><?= htmlspecialchars((string) ($ordem['defeito_relatado'] ?? '')) ?></textarea>
                </div>

                <div class="campo-os inteiro">
                    <label for="diagnostico">
                        Diagnóstico técnico
                    </label>

                    <textarea
                        id="diagnostico"
                        name="diagnostico"
                        placeholder="Pode ser preenchido agora ou posteriormente..."
                    ><?= htmlspecialchars((string) ($ordem['diagnostico'] ?? '')) ?></textarea>
                </div>

                <div class="campo-os inteiro">
                    <label for="servico_realizado">
                        Serviço realizado
                    </label>

                    <textarea
                        id="servico_realizado"
                        name="servico_realizado"
                        placeholder="Descreva o serviço realizado ou deixe para preencher depois..."
                    ><?= htmlspecialchars((string) ($ordem['servico_realizado'] ?? '')) ?></textarea>
                </div>

            </div>

        </section>

        <section class="secao-os">

            <div class="cabecalho-secao-os">
                <h2>Valores</h2>

                <p>
                    O cliente verá apenas o valor da OS, o desconto e o total final.
                </p>
            </div>

            <div class="resumo-valores-os">

                <div class="campo-os">
                    <label for="valor_os">
                        Valor da OS
                        <span class="obrigatorio">*</span>
                    </label>

                    <input
                        id="valor_os"
                        name="valor_os"
                        type="number"
                        min="0.01"
                        step="0.01"
                        placeholder="0,00"
                        value="<?= number_format((float) ($ordem['valor_os'] ?? 0), 2, '.', '') ?>"
                        required
                    >
                </div>

                <div class="campo-os">
                    <label for="desconto">
                        Desconto
                    </label>

                    <input
                        id="desconto"
                        name="desconto"
                        type="number"
                        min="0"
                        step="0.01"
                        value="<?= number_format((float) ($ordem['desconto'] ?? 0), 2, '.', '') ?>"
                    >
                </div>

                <div class="campo-os valor-total-os">
                    <label for="valor_total_exibicao">
                        Total final
                    </label>

                    <input
                        id="valor_total_exibicao"
                        type="text"
                        value="R$ 0,00"
                        readonly
                    >
                </div>

            </div>

        </section>

        <section class="secao-os">

            <div class="cabecalho-secao-os">
                <h2>Prazo, garantia e situação</h2>

                <p>
                    Defina a previsão de entrega e a situação inicial da ordem.
                </p>
            </div>

            <div class="grade-os">

                <div class="campo-os">
                    <label for="data_prevista">
                        Data prevista
                    </label>

                    <input
                        id="data_prevista"
                        name="data_prevista"
                        type="date"
                        value="<?= !empty($ordem['data_prevista'])
                            ? htmlspecialchars(substr((string) $ordem['data_prevista'], 0, 10))
                            : '' ?>"
                    >
                </div>

                <div class="campo-os">
                    <label for="garantia_dias">
                        Garantia em dias
                    </label>

                    <input
                        id="garantia_dias"
                        name="garantia_dias"
                        type="number"
                        min="0"
                        step="1"
                        value="<?= (int) ($ordem['garantia_dias'] ?? 0) ?>"
                    >
                </div>

                <div class="campo-os inteiro">
                    <label for="observacoes">
                        Observações
                    </label>

                    <textarea
                        id="observacoes"
                        name="observacoes"
                        placeholder="Informações adicionais sobre o atendimento..."
                    ><?= htmlspecialchars((string) ($ordem['observacoes'] ?? '')) ?></textarea>
                </div>

            </div>

        </section>

        <div class="acoes-formulario-os">

            <a
                class="botao-cancelar-os"
                href="index.php?rota=ordem-servico-visualizar&id=<?= (int) $ordem['id'] ?>"
            >
                Cancelar
            </a>

            <button
                class="botao-salvar-os"
                type="submit"
                <?= empty($clientes) ? 'disabled' : '' ?>
            >
                Salvar alterações
            </button>

        </div>

    </form>

</main>

<script>
const campoValorOs = document.getElementById('valor_os');
const campoDesconto = document.getElementById('desconto');
const campoTotal = document.getElementById('valor_total_exibicao');
const formulario = document.getElementById('formularioOrdemServico');

function calcularTotalOs() {
    const valorOs = Number(campoValorOs.value) || 0;
    const desconto = Number(campoDesconto.value) || 0;
    const total = Math.max(valorOs - desconto, 0);

    campoTotal.value = total.toLocaleString(
        'pt-BR',
        {
            style: 'currency',
            currency: 'BRL'
        }
    );
}

campoValorOs.addEventListener('input', calcularTotalOs);
campoDesconto.addEventListener('input', calcularTotalOs);

formulario.addEventListener('submit', evento => {
    const valorOs = Number(campoValorOs.value) || 0;
    const desconto = Number(campoDesconto.value) || 0;

    if (valorOs <= 0) {
        evento.preventDefault();

        alert('Informe um valor válido para a ordem de serviço.');

        campoValorOs.focus();

        return;
    }

    if (desconto < 0) {
        evento.preventDefault();

        alert('O desconto não pode ser negativo.');

        campoDesconto.focus();

        return;
    }

    if (desconto > valorOs) {
        evento.preventDefault();

        alert('O desconto não pode ser maior que o valor da OS.');

        campoDesconto.focus();
    }
});

calcularTotalOs();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>