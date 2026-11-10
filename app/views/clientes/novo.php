<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$erroCliente = $_SESSION['erro_cliente'] ?? null;
$dadosCliente = $_SESSION['dados_cliente'] ?? [];

unset(
    $_SESSION['erro_cliente'],
    $_SESSION['dados_cliente']
);

$tipoSelecionado = $dadosCliente['tipo'] ?? 'F';

?>

<style>
    .form-cliente {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .campo-cliente {
        min-width: 0;
    }

    .campo-cliente-total {
        grid-column: 1 / -1;
    }

    .campo-cliente label {
        display: block;
        margin-bottom: 7px;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
    }

    .campo-cliente input,
    .campo-cliente select,
    .campo-cliente textarea {
        width: 100%;
        box-sizing: border-box;
    }

    .campo-cliente textarea {
        resize: vertical;
        min-height: 110px;
    }

    .campo-cliente small {
        display: block;
        margin-top: 6px;
        color: #aebed0;
        font-size: 12px;
        line-height: 1.4;
    }

    .tipo-cliente-opcoes {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .tipo-cliente-opcao {
        position: relative;
        flex: 1;
        min-width: 180px;
    }

    .tipo-cliente-opcao input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .tipo-cliente-opcao label {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        margin: 0;
        padding: 10px 15px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 9px;
        background: rgba(20, 38, 60, 0.65);
        color: #c7d3e2;
        cursor: pointer;
        transition: 0.2s;
    }

    .tipo-cliente-opcao input:checked + label {
        border-color: #20e58f;
        background: rgba(32, 229, 143, 0.13);
        color: #20e58f;
    }

    .secao-formulario {
        grid-column: 1 / -1;
        margin-top: 6px;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.09);
    }

    .secao-formulario h2 {
        margin: 0 0 4px;
        color: #ffffff;
        font-size: 18px;
    }

    .secao-formulario p {
        margin: 0;
        color: #aebed0;
        font-size: 13px;
    }

    .indicador-cep {
        display: none;
        margin-top: 6px;
        color: #52d6ff;
        font-size: 12px;
    }

    .indicador-cep.visivel {
        display: block;
    }

    .acoes-cliente {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 5px;
    }

    @media (max-width: 800px) {
        .form-cliente {
            grid-template-columns: 1fr;
        }

        .campo-cliente-total,
        .secao-formulario,
        .acoes-cliente {
            grid-column: 1;
        }
    }
</style>

<main class="conteudo">

    <header class="topo">

        <div>
            <h1>Novo cliente</h1>

            <p>
                Cadastre os dados pessoais e de contato do cliente.
            </p>
        </div>

        <a
            class="botao-cancelar"
            href="index.php?rota=clientes"
        >
            Voltar
        </a>

    </header>

    <?php if ($erroCliente): ?>

        <div class="mensagem erro">
            <?= htmlspecialchars($erroCliente) ?>
        </div>

    <?php endif; ?>

    <section class="painel-formulario">

        <form
            class="form-cliente"
            action="index.php?rota=cliente-salvar"
            method="POST"
            autocomplete="off"
        >

            <div class="campo-cliente campo-cliente-total">

                <label>Tipo de cliente *</label>

                <div class="tipo-cliente-opcoes">

                    <div class="tipo-cliente-opcao">

                        <input
                            type="radio"
                            id="tipoFisica"
                            name="tipo"
                            value="F"
                            <?= $tipoSelecionado === 'F'
                                ? 'checked'
                                : '' ?>
                        >

                        <label for="tipoFisica">
                            Pessoa Física
                        </label>

                    </div>

                    <div class="tipo-cliente-opcao">

                        <input
                            type="radio"
                            id="tipoJuridica"
                            name="tipo"
                            value="J"
                            <?= $tipoSelecionado === 'J'
                                ? 'checked'
                                : '' ?>
                        >

                        <label for="tipoJuridica">
                            Pessoa Jurídica
                        </label>

                    </div>

                </div>

            </div>

            <div class="campo-cliente">

                <label id="labelNome" for="nome">
                    Nome completo *
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="180"
                    value="<?= htmlspecialchars(
                        $dadosCliente['nome'] ?? ''
                    ) ?>"
                    required
                    autofocus
                >

            </div>

            <div class="campo-cliente">

                <label id="labelCpfCnpj" for="cpfCnpj">
                    CPF *
                </label>

                <input
                    type="text"
                    id="cpfCnpj"
                    name="cpf_cnpj"
                    maxlength="18"
                    inputmode="numeric"
                    value="<?= htmlspecialchars(
                        $dadosCliente['cpf_cnpj'] ?? ''
                    ) ?>"
                    required
                >

            </div>

            <div class="secao-formulario">

                <h2>Contato</h2>

                <p>
                    O WhatsApp será preenchido automaticamente com o telefone.
                </p>

            </div>

            <div class="campo-cliente">

                <label for="telefone">
                    Telefone *
                </label>

                <input
                    type="text"
                    id="telefone"
                    name="telefone"
                    maxlength="15"
                    inputmode="tel"
                    value="<?= htmlspecialchars(
                        $dadosCliente['telefone'] ?? ''
                    ) ?>"
                    required
                >

            </div>

            <div class="campo-cliente">

                <label for="whatsapp">
                    WhatsApp *
                </label>

                <input
                    type="text"
                    id="whatsapp"
                    name="whatsapp"
                    maxlength="15"
                    inputmode="tel"
                    value="<?= htmlspecialchars(
                        $dadosCliente['whatsapp'] ?? ''
                    ) ?>"
                    required
                >

                <small>
                    Você pode alterar este número caso seja diferente.
                </small>

            </div>

            <div class="campo-cliente">

                <label for="email">
                    E-mail
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="180"
                    value="<?= htmlspecialchars(
                        $dadosCliente['email'] ?? ''
                    ) ?>"
                >

            </div>

            <div class="campo-cliente">

                <label for="dataNascimento">
                    Data de nascimento
                </label>

                <input
                    type="date"
                    id="dataNascimento"
                    name="data_nascimento"
                    value="<?= htmlspecialchars(
                        $dadosCliente['data_nascimento'] ?? ''
                    ) ?>"
                >

            </div>

            <div class="secao-formulario">

                <h2>Endereço</h2>

                <p>
                    O preenchimento do endereço é opcional.
                </p>

            </div>

            <div class="campo-cliente">

                <label for="cep">
                    CEP
                </label>

                <input
                    type="text"
                    id="cep"
                    name="cep"
                    maxlength="9"
                    inputmode="numeric"
                    value="<?= htmlspecialchars(
                        $dadosCliente['cep'] ?? ''
                    ) ?>"
                    placeholder="00000-000"
                >

                <span
                    id="indicadorCep"
                    class="indicador-cep"
                >
                    Consultando CEP...
                </span>

                <small id="mensagemCep"></small>

            </div>

            <div class="campo-cliente">

                <label for="endereco">
                    Endereço
                </label>

                <input
                    type="text"
                    id="endereco"
                    name="endereco"
                    maxlength="180"
                    value="<?= htmlspecialchars(
                        $dadosCliente['endereco'] ?? ''
                    ) ?>"
                >

            </div>

            <div class="campo-cliente">

                <label for="numero">
                    Número
                </label>

                <input
                    type="text"
                    id="numero"
                    name="numero"
                    maxlength="20"
                    value="<?= htmlspecialchars(
                        $dadosCliente['numero'] ?? ''
                    ) ?>"
                >

            </div>

            <div class="campo-cliente">

                <label for="bairro">
                    Bairro
                </label>

                <input
                    type="text"
                    id="bairro"
                    name="bairro"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $dadosCliente['bairro'] ?? ''
                    ) ?>"
                >

            </div>

            <div class="campo-cliente">

                <label for="cidade">
                    Cidade
                </label>

                <input
                    type="text"
                    id="cidade"
                    name="cidade"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $dadosCliente['cidade'] ?? ''
                    ) ?>"
                >

            </div>

            <div class="campo-cliente">

                <label for="estado">
                    Estado
                </label>

                <input
                    type="text"
                    id="estado"
                    name="estado"
                    maxlength="2"
                    value="<?= htmlspecialchars(
                        $dadosCliente['estado'] ?? ''
                    ) ?>"
                    placeholder="SP"
                >

            </div>

            <div class="campo-cliente campo-cliente-total">

                <label for="observacoes">
                    Observações
                </label>

                <textarea
                    id="observacoes"
                    name="observacoes"
                    maxlength="3000"
                    placeholder="Informações adicionais sobre o cliente..."
                ><?= htmlspecialchars(
                    $dadosCliente['observacoes'] ?? ''
                ) ?></textarea>

            </div>

            <div class="acoes-cliente">

                <a
                    class="botao-cancelar"
                    href="index.php?rota=clientes"
                >
                    Cancelar
                </a>

                <button
                    class="botao-salvar"
                    type="submit"
                >
                    Salvar cliente
                </button>

            </div>

        </form>

    </section>

</main>

<script>
const tipoFisica = document.getElementById('tipoFisica');
const tipoJuridica = document.getElementById('tipoJuridica');

const labelNome = document.getElementById('labelNome');
const labelCpfCnpj = document.getElementById('labelCpfCnpj');

const campoCpfCnpj = document.getElementById('cpfCnpj');
const campoTelefone = document.getElementById('telefone');
const campoWhatsapp = document.getElementById('whatsapp');

const campoCep = document.getElementById('cep');
const campoEndereco = document.getElementById('endereco');
const campoBairro = document.getElementById('bairro');
const campoCidade = document.getElementById('cidade');
const campoEstado = document.getElementById('estado');
const campoNumero = document.getElementById('numero');

const indicadorCep = document.getElementById('indicadorCep');
const mensagemCep = document.getElementById('mensagemCep');

let ultimoWhatsappAutomatico = campoWhatsapp.value;
let whatsappEditadoManualmente = false;

function somenteNumeros(valor) {
    return valor.replace(/\D/g, '');
}

function formatarCPF(valor) {
    const numeros = somenteNumeros(valor).slice(0, 11);

    return numeros
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}

function formatarCNPJ(valor) {
    const numeros = somenteNumeros(valor).slice(0, 14);

    return numeros
        .replace(/^(\d{2})(\d)/, '$1.$2')
        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1/$2')
        .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
}

function formatarTelefone(valor) {
    const numeros = somenteNumeros(valor).slice(0, 11);

    if (numeros.length <= 10) {
        return numeros
            .replace(/^(\d{2})(\d)/, '($1) $2')
            .replace(/(\d{4})(\d)/, '$1-$2');
    }

    return numeros
        .replace(/^(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{5})(\d)/, '$1-$2');
}

function formatarCEP(valor) {
    return somenteNumeros(valor)
        .slice(0, 8)
        .replace(/(\d{5})(\d)/, '$1-$2');
}

function atualizarTipoCliente() {
    if (tipoJuridica.checked) {
        labelNome.textContent = 'Razão social *';
        labelCpfCnpj.textContent = 'CNPJ *';
        campoCpfCnpj.value = formatarCNPJ(campoCpfCnpj.value);
        campoCpfCnpj.maxLength = 18;
    } else {
        labelNome.textContent = 'Nome completo *';
        labelCpfCnpj.textContent = 'CPF *';
        campoCpfCnpj.value = formatarCPF(campoCpfCnpj.value);
        campoCpfCnpj.maxLength = 14;
    }
}

tipoFisica.addEventListener('change', atualizarTipoCliente);
tipoJuridica.addEventListener('change', atualizarTipoCliente);

campoCpfCnpj.addEventListener('input', function() {
    this.value = tipoJuridica.checked
        ? formatarCNPJ(this.value)
        : formatarCPF(this.value);
});

campoTelefone.addEventListener('input', function() {
    this.value = formatarTelefone(this.value);

    const whatsappPodeSerAtualizado =
        !whatsappEditadoManualmente
        || campoWhatsapp.value === ''
        || campoWhatsapp.value === ultimoWhatsappAutomatico;

    if (whatsappPodeSerAtualizado) {
        campoWhatsapp.value = this.value;
        ultimoWhatsappAutomatico = this.value;
        whatsappEditadoManualmente = false;
    }
});

campoWhatsapp.addEventListener('input', function() {
    this.value = formatarTelefone(this.value);

    whatsappEditadoManualmente =
        this.value !== ultimoWhatsappAutomatico;
});

campoCep.addEventListener('input', function() {
    this.value = formatarCEP(this.value);
});

campoEstado.addEventListener('input', function() {
    this.value = this.value
        .replace(/[^a-zA-Z]/g, '')
        .slice(0, 2)
        .toUpperCase();
});

campoCep.addEventListener('blur', consultarCep);

async function consultarCep() {
    const cep = somenteNumeros(campoCep.value);

    mensagemCep.textContent = '';

    if (cep.length === 0) {
        return;
    }

    if (cep.length !== 8) {
        mensagemCep.textContent =
            'Informe um CEP com 8 números.';
        return;
    }

    indicadorCep.classList.add('visivel');

    try {
        const resposta = await fetch(
            `https://viacep.com.br/ws/${cep}/json/`
        );

        if (!resposta.ok) {
            throw new Error('Falha ao consultar o CEP.');
        }

        const dados = await resposta.json();

        if (dados.erro) {
            mensagemCep.textContent =
                'CEP não encontrado. Preencha o endereço manualmente.';
            return;
        }

        campoEndereco.value = dados.logradouro ?? '';
        campoBairro.value = dados.bairro ?? '';
        campoCidade.value = dados.localidade ?? '';
        campoEstado.value = dados.uf ?? '';

        mensagemCep.textContent =
            'Endereço preenchido automaticamente.';

        campoNumero.focus();

    } catch (erro) {
        mensagemCep.textContent =
            'Não foi possível consultar o CEP. Preencha manualmente.';
    } finally {
        indicadorCep.classList.remove('visivel');
    }
}

atualizarTipoCliente();

campoTelefone.value = formatarTelefone(campoTelefone.value);
campoWhatsapp.value = formatarTelefone(campoWhatsapp.value);
campoCep.value = formatarCEP(campoCep.value);

ultimoWhatsappAutomatico = campoWhatsapp.value;
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>