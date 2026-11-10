<?php

$configuracao = $configuracao ?? [];

if (!empty($_SESSION['dados_formulario'])) {
    $configuracao = array_merge(
        $configuracao,
        $_SESSION['dados_formulario']
    );

    unset($_SESSION['dados_formulario']);
}

function valorConfiguracao(
    array $configuracao,
    string $campo,
    string $padrao = ''
): string {
    return htmlspecialchars(
        (string) ($configuracao[$campo] ?? $padrao),
        ENT_QUOTES,
        'UTF-8'
    );
}

$logoAtual = trim(
    (string) ($configuracao['logo'] ?? '')
);

$corPrincipal = trim(
    (string) (
        $configuracao['cor_principal']
        ?? '#159ee8'
    )
);

if (
    !preg_match(
        '/^#[0-9A-Fa-f]{6}$/',
        $corPrincipal
    )
) {
    $corPrincipal = '#159ee8';
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="conteudo-principal configuracoes-pagina">

    <section class="cabecalho-pagina">
        <div>
            <span class="configuracoes-subtitulo">
                Personalização do sistema
            </span>

            <h1>Configurações</h1>

            <p>
                Atualize os dados da empresa, a logo,
                a cor principal e gerencie os dados
                do sistema.
            </p>
        </div>
    </section>

    <?php if (!empty($_SESSION['sucesso'])): ?>
        <div class="alerta-configuracao alerta-sucesso-configuracao">
            <?= htmlspecialchars(
                $_SESSION['sucesso'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['erro'])): ?>
        <div class="alerta-configuracao alerta-erro-configuracao">
            <?= htmlspecialchars(
                $_SESSION['erro'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <form
        action="index.php?rota=configuracoes-salvar"
        method="post"
        enctype="multipart/form-data"
        id="formConfiguracoes"
    >

        <section class="cartao-configuracao">

            <div class="cabecalho-cartao-configuracao">
                <div class="icone-cartao-configuracao">
                    🏢
                </div>

                <div>
                    <h2>Dados da empresa</h2>

                    <p>
                        Informações que poderão aparecer
                        nas impressões e documentos.
                    </p>
                </div>
            </div>

            <div class="area-logo-configuracao">

                <div class="preview-logo-configuracao">

                    <?php if ($logoAtual !== ''): ?>
                        <img
                            src="<?= valorConfiguracao(
                                $configuracao,
                                'logo'
                            ) ?>"
                            alt="Logo da empresa"
                            id="previewLogo"
                        >
                    <?php else: ?>
                        <div
                            class="logo-vazia-configuracao"
                            id="logoVazia"
                        >
                            <span>🖼️</span>
                            <small>Sem logo</small>
                        </div>

                        <img
                            src=""
                            alt="Pré-visualização da logo"
                            id="previewLogo"
                            style="display: none;"
                        >
                    <?php endif; ?>

                </div>

                <div class="campo-logo-configuracao">
                    <label for="logo">
                        Logo da empresa
                    </label>

                    <input
                        type="file"
                        id="logo"
                        name="logo"
                        accept=".jpg,.jpeg,.png,.webp"
                    >

                    <small>
                        Formatos permitidos: JPG, PNG ou
                        WEBP. Tamanho máximo de 2 MB.
                    </small>
                </div>

            </div>

            <div class="grade-campos-configuracao">

                <div class="campo-configuracao campo-grande">
                    <label for="nome_loja">
                        Nome da empresa
                        <strong>*</strong>
                    </label>

                    <input
                        type="text"
                        id="nome_loja"
                        name="nome_loja"
                        maxlength="150"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'nome_loja'
                        ) ?>"
                        placeholder="Ex.: JP TechCell"
                        required
                    >
                </div>

                <div class="campo-configuracao">
                    <label for="cpf_cnpj">
                        CNPJ
                    </label>

                    <input
                        type="text"
                        id="cpf_cnpj"
                        name="cpf_cnpj"
                        maxlength="18"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'cpf_cnpj'
                        ) ?>"
                        placeholder="00.000.000/0000-00"
                    >
                </div>

                <div class="campo-configuracao">
                    <label for="telefone">
                        Telefone
                    </label>

                    <input
                        type="text"
                        id="telefone"
                        name="telefone"
                        maxlength="20"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'telefone'
                        ) ?>"
                        placeholder="(00) 0000-0000"
                    >
                </div>

                <div class="campo-configuracao">
                    <label for="whatsapp">
                        WhatsApp
                    </label>

                    <input
                        type="text"
                        id="whatsapp"
                        name="whatsapp"
                        maxlength="20"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'whatsapp'
                        ) ?>"
                        placeholder="(00) 00000-0000"
                    >
                </div>

                <div class="campo-configuracao campo-grande">
                    <label for="email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="150"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'email'
                        ) ?>"
                        placeholder="empresa@email.com"
                    >
                </div>

                <div class="campo-configuracao campo-grande">
                    <label for="instagram">
                        Instagram
                    </label>

                    <input
                        type="text"
                        id="instagram"
                        name="instagram"
                        maxlength="150"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'instagram'
                        ) ?>"
                        placeholder="@nomedaempresa"
                    >
                </div>

            </div>

        </section>

        <section class="cartao-configuracao">

            <div class="cabecalho-cartao-configuracao">
                <div class="icone-cartao-configuracao">
                    📍
                </div>

                <div>
                    <h2>Endereço</h2>

                    <p>
                        Informe o CEP para preencher
                        automaticamente o endereço.
                    </p>
                </div>
            </div>

            <div class="grade-campos-configuracao grade-endereco">

                <div class="campo-configuracao">
                    <label for="cep">
                        CEP
                    </label>

                    <div class="campo-com-botao-configuracao">
                        <input
                            type="text"
                            id="cep"
                            name="cep"
                            maxlength="9"
                            value="<?= valorConfiguracao(
                                $configuracao,
                                'cep'
                            ) ?>"
                            placeholder="00000-000"
                        >

                        <button
                            type="button"
                            id="buscarCep"
                            class="botao-buscar-cep"
                        >
                            Buscar
                        </button>
                    </div>

                    <small id="mensagemCep"></small>
                </div>

                <div class="campo-configuracao campo-endereco">
                    <label for="endereco">
                        Endereço
                    </label>

                    <input
                        type="text"
                        id="endereco"
                        name="endereco"
                        maxlength="150"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'endereco'
                        ) ?>"
                        placeholder="Rua, avenida..."
                    >
                </div>

                <div class="campo-configuracao">
                    <label for="numero">
                        Número
                    </label>

                    <input
                        type="text"
                        id="numero"
                        name="numero"
                        maxlength="20"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'numero'
                        ) ?>"
                        placeholder="Ex.: 123"
                    >
                </div>

                <div class="campo-configuracao">
                    <label for="bairro">
                        Bairro
                    </label>

                    <input
                        type="text"
                        id="bairro"
                        name="bairro"
                        maxlength="100"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'bairro'
                        ) ?>"
                        placeholder="Bairro"
                    >
                </div>

                <div class="campo-configuracao">
                    <label for="cidade">
                        Cidade
                    </label>

                    <input
                        type="text"
                        id="cidade"
                        name="cidade"
                        maxlength="100"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'cidade'
                        ) ?>"
                        placeholder="Cidade"
                    >
                </div>

                <div class="campo-configuracao">
                    <label for="estado">
                        Estado
                    </label>

                    <input
                        type="text"
                        id="estado"
                        name="estado"
                        maxlength="2"
                        value="<?= valorConfiguracao(
                            $configuracao,
                            'estado'
                        ) ?>"
                        placeholder="UF"
                    >
                </div>

            </div>

        </section>

        <section class="cartao-configuracao">

            <div class="cabecalho-cartao-configuracao">
                <div class="icone-cartao-configuracao">
                    🎨
                </div>

                <div>
                    <h2>Aparência</h2>

                    <p>
                        Escolha a cor principal utilizada
                        nos botões e detalhes do sistema.
                    </p>
                </div>
            </div>

            <div class="area-cor-configuracao">

                <div
                    class="preview-cor-configuracao"
                    id="previewCor"
                    style="background: <?= htmlspecialchars(
                        $corPrincipal,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>;"
                >
                    <span>Pré-visualização</span>

                    <strong id="textoCor">
                        <?= htmlspecialchars(
                            strtoupper($corPrincipal),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>
                </div>

                <div class="seletor-cor-configuracao">
                    <label for="cor_principal">
                        Cor principal
                    </label>

                    <div class="input-cor-configuracao">
                        <input
                            type="color"
                            id="corPrincipalVisual"
                            value="<?= htmlspecialchars(
                                $corPrincipal,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                        <input
                            type="text"
                            id="cor_principal"
                            name="cor_principal"
                            maxlength="7"
                            value="<?= htmlspecialchars(
                                $corPrincipal,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >
                    </div>

                    <div class="cores-rapidas-configuracao">
                        <button
                            type="button"
                            class="cor-rapida"
                            data-cor="#159ee8"
                            style="background: #159ee8;"
                            title="Azul"
                        ></button>

                        <button
                            type="button"
                            class="cor-rapida"
                            data-cor="#16a34a"
                            style="background: #16a34a;"
                            title="Verde"
                        ></button>

                        <button
                            type="button"
                            class="cor-rapida"
                            data-cor="#ea580c"
                            style="background: #ea580c;"
                            title="Laranja"
                        ></button>

                        <button
                            type="button"
                            class="cor-rapida"
                            data-cor="#dc2626"
                            style="background: #dc2626;"
                            title="Vermelho"
                        ></button>

                        <button
                            type="button"
                            class="cor-rapida"
                            data-cor="#7c3aed"
                            style="background: #7c3aed;"
                            title="Roxo"
                        ></button>

                        <button
                            type="button"
                            class="cor-rapida"
                            data-cor="#334155"
                            style="background: #334155;"
                            title="Cinza"
                        ></button>
                    </div>
                </div>

            </div>

        </section>

        <div class="acoes-salvar-configuracao">
            <button
                type="submit"
                class="botao-salvar-configuracao"
                id="botaoSalvarConfiguracoes"
                style="background: <?= htmlspecialchars(
                    $corPrincipal,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>;"
            >
                Salvar alterações
            </button>
        </div>

    </form>

    <section class="cartao-configuracao">

        <div class="cabecalho-cartao-configuracao">
            <div class="icone-cartao-configuracao">
                💾
            </div>

            <div>
                <h2>Backup</h2>

                <p>
                    Faça uma cópia do banco ou restaure
                    um backup criado anteriormente.
                </p>
            </div>
        </div>

        <div class="acoes-backup-configuracao">

            <a
                href="index.php?rota=configuracoes-backup"
                class="acao-configuracao backup"
            >
                <span class="acao-icone-configuracao">
                    ⬇
                </span>

                <div>
                    <strong>Fazer backup</strong>

                    <small>
                        Baixar uma cópia do banco de dados
                    </small>
                </div>
            </a>

            <button
                type="button"
                class="acao-configuracao restaurar"
                id="abrirRestauracao"
            >
                <span class="acao-icone-configuracao">
                    ⬆
                </span>

                <div>
                    <strong>Restaurar backup</strong>

                    <small>
                        Importar um arquivo de backup SQL
                    </small>
                </div>
            </button>

        </div>

        <form
            action="index.php?rota=configuracoes-restaurar"
            method="post"
            enctype="multipart/form-data"
            id="formRestauracao"
            class="form-restauracao-configuracao"
        >
            <label for="arquivo_backup">
                Selecione o arquivo de backup
            </label>

            <input
                type="file"
                id="arquivo_backup"
                name="arquivo_backup"
                accept=".sql"
                required
            >

            <div
                id="arquivoBackupSelecionado"
                class="arquivo-backup-selecionado"
                aria-live="polite"
            >
                Nenhum arquivo selecionado.
            </div>

            <div class="acoes-restauracao-configuracao">
                <button
                    type="button"
                    id="cancelarRestauracao"
                    class="botao-cancelar-configuracao"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="botao-restaurar-configuracao"
                >
                    Restaurar banco
                </button>
            </div>
        </form>

    </section>

    <section class="cartao-configuracao zona-perigo-configuracao">

        <div class="cabecalho-cartao-configuracao">
            <div class="icone-cartao-configuracao perigo">
                ⚠
            </div>

            <div>
                <h2>Zona de perigo</h2>

                <p>
                    Utilize esta opção somente quando
                    realmente desejar zerar o sistema.
                </p>
            </div>
        </div>

        <div class="conteudo-perigo-configuracao">

            <div>
                <strong>
                    Limpar todos os dados do sistema
                </strong>

                <p>
                    Clientes, produtos, estoque, vendas,
                    ordens de serviço e gastos serão
                    apagados permanentemente.
                </p>

                <small>
                    O usuário administrador, os dados da
                    empresa e a logo serão mantidos.
                </small>
            </div>

            <button
                type="button"
                id="abrirLimpeza"
                class="botao-limpar-configuracao"
            >
                Limpar sistema
            </button>

        </div>

    </section>

</main>

<div
    class="modal-configuracao"
    id="modalLimpeza"
    aria-hidden="true"
>
    <div class="modal-conteudo-configuracao">

        <div class="modal-icone-perigo">
            ⚠
        </div>

        <h2>Limpar o sistema?</h2>

        <p>
            Essa ação apagará permanentemente todos
            os dados operacionais.
        </p>

        <p>
            Para continuar, digite:
            <strong>CONFIRMAR</strong>
        </p>

        <form
            action="index.php?rota=configuracoes-limpar"
            method="post"
            id="formLimpeza"
        >
            <input
                type="text"
                name="confirmacao"
                id="confirmacaoLimpeza"
                placeholder="Digite CONFIRMAR"
                autocomplete="off"
                required
            >

            <div class="acoes-modal-configuracao">
                <button
                    type="button"
                    id="cancelarLimpeza"
                    class="botao-cancelar-configuracao"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    id="confirmarLimpeza"
                    class="botao-confirmar-limpeza"
                    disabled
                >
                    Apagar todos os dados
                </button>
            </div>
        </form>

    </div>
</div>

<style>
    :root {
        --cor-configuracao:
            <?= htmlspecialchars(
                $corPrincipal,
                ENT_QUOTES,
                'UTF-8'
            ) ?>;
    }

    .configuracoes-pagina {
        padding-bottom: 40px;
    }

    .configuracoes-subtitulo {
        display: block;
        margin-bottom: 4px;
        color: var(--cor-configuracao);
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .alerta-configuracao {
        margin-bottom: 20px;
        padding: 14px 17px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
    }

    .alerta-sucesso-configuracao {
        border: 1px solid #86efac;
        background: #f0fdf4;
        color: #166534;
    }

    .alerta-erro-configuracao {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .cartao-configuracao {
        margin-bottom: 22px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 5px 20px rgba(15, 23, 42, .05);
    }

    .cabecalho-cartao-configuracao {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 22px;
    }

    .cabecalho-cartao-configuracao h2 {
        margin: 0 0 5px;
        color: #172033;
        font-size: 20px;
    }

    .cabecalho-cartao-configuracao p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .icone-cartao-configuracao {
        display: flex;
        flex: 0 0 45px;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: #eff6ff;
        font-size: 21px;
    }

    .icone-cartao-configuracao.perigo {
        background: #fef2f2;
    }

    .area-logo-configuracao {
        display: flex;
        align-items: center;
        gap: 22px;
        margin-bottom: 25px;
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        background: #f8fafc;
    }

    .preview-logo-configuracao {
        display: flex;
        flex: 0 0 145px;
        align-items: center;
        justify-content: center;
        width: 145px;
        height: 105px;
        overflow: hidden;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
    }

    .preview-logo-configuracao img {
        display: block;
        width: 100%;
        height: 100%;
        padding: 8px;
        object-fit: contain;
    }

    .logo-vazia-configuracao {
        color: #94a3b8;
        text-align: center;
    }

    .logo-vazia-configuracao span {
        display: block;
        margin-bottom: 5px;
        font-size: 28px;
    }

    .logo-vazia-configuracao small {
        font-size: 12px;
    }

    .campo-logo-configuracao {
        flex: 1;
    }

    .campo-logo-configuracao label,
    .campo-configuracao label,
    .seletor-cor-configuracao label,
    .form-restauracao-configuracao label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 13px;
        font-weight: 800;
    }

    .campo-logo-configuracao input {
        display: block;
        width: 100%;
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #ffffff;
    }

    .campo-logo-configuracao small {
        display: block;
        margin-top: 7px;
        color: #64748b;
        font-size: 11px;
    }

    .grade-campos-configuracao {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .grade-endereco {
        grid-template-columns:
            minmax(190px, .7fr)
            minmax(260px, 1.4fr)
            minmax(130px, .5fr);
    }

    .campo-grande {
        grid-column: span 1;
    }

    .campo-endereco {
        grid-column: span 1;
    }

    .campo-configuracao strong {
        color: #dc2626;
    }

    .campo-configuracao input {
        width: 100%;
        min-height: 46px;
        padding: 0 13px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #ffffff;
        color: #172033;
        font-size: 14px;
        outline: none;
        transition: .2s;
    }

    .campo-configuracao input:focus,
    .seletor-cor-configuracao input:focus {
        border-color: var(--cor-configuracao);
        box-shadow:
            0 0 0 3px
            color-mix(
                in srgb,
                var(--cor-configuracao) 18%,
                transparent
            );
    }

    .campo-com-botao-configuracao {
        display: flex;
        gap: 8px;
    }

    .botao-buscar-cep {
        min-width: 86px;
        border: 0;
        border-radius: 9px;
        background: #334155;
        color: #ffffff;
        cursor: pointer;
        font-weight: 800;
    }

    #mensagemCep {
        display: block;
        min-height: 15px;
        margin-top: 5px;
        color: #64748b;
        font-size: 11px;
    }

    .area-cor-configuracao {
        display: grid;
        grid-template-columns: 230px 1fr;
        align-items: center;
        gap: 25px;
    }

    .preview-cor-configuracao {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 120px;
        padding: 22px;
        border-radius: 14px;
        color: #ffffff;
    }

    .preview-cor-configuracao span {
        margin-bottom: 7px;
        font-size: 12px;
        opacity: .9;
    }

    .preview-cor-configuracao strong {
        font-size: 24px;
    }

    .input-cor-configuracao {
        display: flex;
        gap: 10px;
    }

    .input-cor-configuracao input[type="color"] {
        flex: 0 0 55px;
        width: 55px;
        height: 46px;
        padding: 3px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #ffffff;
        cursor: pointer;
    }

    .input-cor-configuracao input[type="text"] {
        width: 150px;
        min-height: 46px;
        padding: 0 13px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        text-transform: uppercase;
    }

    .cores-rapidas-configuracao {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin-top: 13px;
    }

    .cor-rapida {
        width: 31px;
        height: 31px;
        border: 3px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 0 1px #cbd5e1;
        cursor: pointer;
    }

    .acoes-salvar-configuracao {
        display: flex;
        justify-content: flex-end;
        margin: -6px 0 24px;
    }

    .botao-salvar-configuracao {
        min-height: 47px;
        padding: 0 25px;
        border: 0;
        border-radius: 10px;
        color: #ffffff;
        cursor: pointer;
        font-size: 14px;
        font-weight: 900;
    }

    .acoes-backup-configuracao {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .acao-configuracao {
        display: flex;
        align-items: center;
        gap: 13px;
        min-height: 82px;
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        color: #172033;
        cursor: pointer;
        font-family: inherit;
        text-align: left;
        text-decoration: none;
        transition: .2s;
    }

    .acao-configuracao:hover {
        border-color: var(--cor-configuracao);
        transform: translateY(-2px);
    }

    .acao-icone-configuracao {
        display: flex;
        flex: 0 0 45px;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 11px;
        font-size: 21px;
    }

    .acao-configuracao.backup
    .acao-icone-configuracao {
        background: #dcfce7;
        color: #15803d;
    }

    .acao-configuracao.restaurar
    .acao-icone-configuracao {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .acao-configuracao strong {
        display: block;
        margin-bottom: 4px;
        font-size: 14px;
    }

    .acao-configuracao small {
        color: #64748b;
        font-size: 11px;
    }

    .form-restauracao-configuracao {
        display: none;
        margin-top: 18px;
        padding: 18px;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        background: #eff6ff;
    }

    .form-restauracao-configuracao.ativo {
        display: block;
    }

    .form-restauracao-configuracao input {
        width: 100%;
        padding: 10px;
        border: 1px solid #93c5fd;
        border-radius: 9px;
        background: #ffffff;
    }

    .arquivo-backup-selecionado {
        margin-top: 10px;
        padding: 11px 13px;
        border: 1px solid #bfdbfe;
        border-radius: 9px;
        background: #ffffff;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        word-break: break-word;
    }

    .arquivo-backup-selecionado.com-arquivo {
        border-color: #86efac;
        background: #f0fdf4;
        color: #166534;
    }

    .acoes-restauracao-configuracao {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 14px;
    }

    .botao-cancelar-configuracao,
    .botao-restaurar-configuracao {
        min-height: 41px;
        padding: 0 17px;
        border: 0;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 800;
    }

    .botao-cancelar-configuracao {
        background: #e2e8f0;
        color: #334155;
    }

    .botao-restaurar-configuracao {
        background: #2563eb;
        color: #ffffff;
    }

    .zona-perigo-configuracao {
        border-color: #fecaca;
    }

    .conteudo-perigo-configuracao {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 18px;
        border: 1px solid #fecaca;
        border-radius: 12px;
        background: #fef2f2;
    }

    .conteudo-perigo-configuracao strong {
        color: #991b1b;
        font-size: 14px;
    }

    .conteudo-perigo-configuracao p {
        margin: 6px 0;
        color: #7f1d1d;
        font-size: 13px;
    }

    .conteudo-perigo-configuracao small {
        color: #991b1b;
        font-size: 11px;
    }

    .botao-limpar-configuracao {
        flex: 0 0 auto;
        min-height: 43px;
        padding: 0 18px;
        border: 0;
        border-radius: 9px;
        background: #dc2626;
        color: #ffffff;
        cursor: pointer;
        font-weight: 900;
    }

    .modal-configuracao {
        position: fixed;
        z-index: 9999;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, .68);
    }

    .modal-configuracao.ativo {
        display: flex;
    }

    .modal-conteudo-configuracao {
        width: 100%;
        max-width: 440px;
        padding: 28px;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 20px 50px rgba(15, 23, 42, .25);
        text-align: center;
    }

    .modal-icone-perigo {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 62px;
        height: 62px;
        margin: 0 auto 15px;
        border-radius: 50%;
        background: #fef2f2;
        font-size: 27px;
    }

    .modal-conteudo-configuracao h2 {
        margin: 0 0 10px;
        color: #991b1b;
    }

    .modal-conteudo-configuracao p {
        color: #64748b;
        font-size: 14px;
    }

    .modal-conteudo-configuracao input {
        width: 100%;
        min-height: 45px;
        margin-top: 10px;
        padding: 0 13px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        text-align: center;
        text-transform: uppercase;
    }

    .acoes-modal-configuracao {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 18px;
    }

    .botao-confirmar-limpeza {
        min-height: 41px;
        padding: 0 16px;
        border: 0;
        border-radius: 8px;
        background: #dc2626;
        color: #ffffff;
        cursor: pointer;
        font-weight: 900;
    }

    .botao-confirmar-limpeza:disabled {
        cursor: not-allowed;
        opacity: .45;
    }

    @media (max-width: 900px) {
        .grade-endereco {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .area-cor-configuracao {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .grade-campos-configuracao,
        .grade-endereco,
        .acoes-backup-configuracao {
            grid-template-columns: 1fr;
        }

        .area-logo-configuracao,
        .conteudo-perigo-configuracao {
            align-items: stretch;
            flex-direction: column;
        }

        .preview-logo-configuracao {
            width: 100%;
            flex-basis: 130px;
        }

        .botao-limpar-configuracao {
            width: 100%;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const inputLogo =
        document.getElementById('logo');

    const previewLogo =
        document.getElementById('previewLogo');

    const logoVazia =
        document.getElementById('logoVazia');

    inputLogo.addEventListener(
        'change',
        function () {
            const arquivo = this.files[0];

            if (!arquivo) {
                return;
            }

            if (arquivo.size > 2 * 1024 * 1024) {
                alert(
                    'A logo deve ter no máximo 2 MB.'
                );

                this.value = '';

                return;
            }

            const tiposPermitidos = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (
                !tiposPermitidos.includes(
                    arquivo.type
                )
            ) {
                alert(
                    'Selecione uma imagem JPG, PNG ou WEBP.'
                );

                this.value = '';

                return;
            }

            const leitor = new FileReader();

            leitor.onload = function (evento) {
                previewLogo.src =
                    evento.target.result;

                previewLogo.style.display =
                    'block';

                if (logoVazia) {
                    logoVazia.style.display =
                        'none';
                }
            };

            leitor.readAsDataURL(arquivo);
        }
    );


    const cep =
        document.getElementById('cep');

    const botaoBuscarCep =
        document.getElementById('buscarCep');

    const mensagemCep =
        document.getElementById('mensagemCep');

    const endereco =
        document.getElementById('endereco');

    const bairro =
        document.getElementById('bairro');

    const cidade =
        document.getElementById('cidade');

    const estado =
        document.getElementById('estado');

    function aplicarMascaraCep(valor) {
        const numeros =
            valor.replace(/\D/g, '')
                .substring(0, 8);

        if (numeros.length > 5) {
            return numeros.substring(0, 5)
                + '-'
                + numeros.substring(5);
        }

        return numeros;
    }

    cep.addEventListener('input', function () {
        this.value =
            aplicarMascaraCep(this.value);
    });

    async function consultarCep() {
        const cepLimpo =
            cep.value.replace(/\D/g, '');

        if (cepLimpo.length !== 8) {
            mensagemCep.textContent =
                'Informe um CEP com 8 números.';

            mensagemCep.style.color =
                '#b91c1c';

            return;
        }

        mensagemCep.textContent =
            'Buscando endereço...';

        mensagemCep.style.color =
            '#64748b';

        botaoBuscarCep.disabled = true;

        try {
            const resposta = await fetch(
                'https://viacep.com.br/ws/'
                + cepLimpo
                + '/json/'
            );

            if (!resposta.ok) {
                throw new Error(
                    'Não foi possível consultar o CEP.'
                );
            }

            const dados = await resposta.json();

            if (dados.erro) {
                mensagemCep.textContent =
                    'CEP não encontrado.';

                mensagemCep.style.color =
                    '#b91c1c';

                return;
            }

            endereco.value =
                dados.logradouro || '';

            bairro.value =
                dados.bairro || '';

            cidade.value =
                dados.localidade || '';

            estado.value =
                dados.uf || '';

            mensagemCep.textContent =
                'Endereço encontrado.';

            mensagemCep.style.color =
                '#15803d';

            document
                .getElementById('numero')
                .focus();

        } catch (erro) {
            mensagemCep.textContent =
                'Não foi possível consultar o CEP.';

            mensagemCep.style.color =
                '#b91c1c';
        } finally {
            botaoBuscarCep.disabled = false;
        }
    }

    botaoBuscarCep.addEventListener(
        'click',
        consultarCep
    );

    cep.addEventListener(
        'blur',
        function () {
            if (
                this.value.replace(/\D/g, '')
                    .length === 8
            ) {
                consultarCep();
            }
        }
    );


    const telefone =
        document.getElementById('telefone');

    const whatsapp =
        document.getElementById('whatsapp');

    const cpfCnpj =
        document.getElementById('cpf_cnpj');

    function mascaraTelefone(valor) {
        const numeros =
            valor.replace(/\D/g, '')
                .substring(0, 11);

        if (numeros.length <= 10) {
            return numeros
                .replace(
                    /^(\d{2})(\d)/g,
                    '($1) $2'
                )
                .replace(
                    /(\d{4})(\d)/,
                    '$1-$2'
                );
        }

        return numeros
            .replace(
                /^(\d{2})(\d)/g,
                '($1) $2'
            )
            .replace(
                /(\d{5})(\d)/,
                '$1-$2'
            );
    }

    telefone.addEventListener(
        'input',
        function () {
            this.value =
                mascaraTelefone(this.value);
        }
    );

    whatsapp.addEventListener(
        'input',
        function () {
            this.value =
                mascaraTelefone(this.value);
        }
    );

    cpfCnpj.addEventListener(
        'input',
        function () {
            let numeros =
                this.value.replace(/\D/g, '')
                    .substring(0, 14);

            numeros = numeros
                .replace(
                    /^(\d{2})(\d)/,
                    '$1.$2'
                )
                .replace(
                    /^(\d{2})\.(\d{3})(\d)/,
                    '$1.$2.$3'
                )
                .replace(
                    /\.(\d{3})(\d)/,
                    '.$1/$2'
                )
                .replace(
                    /(\d{4})(\d)/,
                    '$1-$2'
                );

            this.value = numeros;
        }
    );


    const seletorVisual =
        document.getElementById(
            'corPrincipalVisual'
        );

    const inputCor =
        document.getElementById(
            'cor_principal'
        );

    const previewCor =
        document.getElementById(
            'previewCor'
        );

    const textoCor =
        document.getElementById(
            'textoCor'
        );

    const botaoSalvar =
        document.getElementById(
            'botaoSalvarConfiguracoes'
        );

    function atualizarCor(cor) {
        const corValida =
            /^#[0-9A-Fa-f]{6}$/.test(cor);

        if (!corValida) {
            return;
        }

        cor = cor.toUpperCase();

        seletorVisual.value = cor;
        inputCor.value = cor;

        previewCor.style.background = cor;
        textoCor.textContent = cor;
        botaoSalvar.style.background = cor;

        document.documentElement.style
            .setProperty(
                '--cor-configuracao',
                cor
            );
    }

    seletorVisual.addEventListener(
        'input',
        function () {
            atualizarCor(this.value);
        }
    );

    inputCor.addEventListener(
        'input',
        function () {
            atualizarCor(this.value);
        }
    );

    document
        .querySelectorAll('.cor-rapida')
        .forEach(function (botao) {
            botao.addEventListener(
                'click',
                function () {
                    atualizarCor(
                        this.dataset.cor
                    );
                }
            );
        });


    const abrirRestauracao =
        document.getElementById(
            'abrirRestauracao'
        );

    const cancelarRestauracao =
        document.getElementById(
            'cancelarRestauracao'
        );

    const formRestauracao =
        document.getElementById(
            'formRestauracao'
        );

    const inputArquivoBackup =
        document.getElementById(
            'arquivo_backup'
        );

    const arquivoBackupSelecionado =
        document.getElementById(
            'arquivoBackupSelecionado'
        );

    function formatarTamanhoArquivo(bytes) {
        if (bytes < 1024) {
            return bytes + ' bytes';
        }

        if (bytes < 1024 * 1024) {
            return (
                bytes / 1024
            ).toFixed(1) + ' KB';
        }

        return (
            bytes / (1024 * 1024)
        ).toFixed(1) + ' MB';
    }

    inputArquivoBackup.addEventListener(
        'change',
        function () {
            const arquivo = this.files[0];

            if (!arquivo) {
                arquivoBackupSelecionado
                    .textContent =
                    'Nenhum arquivo selecionado.';

                arquivoBackupSelecionado
                    .classList
                    .remove('com-arquivo');

                return;
            }

            arquivoBackupSelecionado
                .textContent =
                'Arquivo selecionado: '
                + arquivo.name
                + ' ('
                + formatarTamanhoArquivo(
                    arquivo.size
                )
                + ')';

            arquivoBackupSelecionado
                .classList
                .add('com-arquivo');
        }
    );

    abrirRestauracao.addEventListener(
        'click',
        function () {
            formRestauracao.classList
                .add('ativo');
        }
    );

    cancelarRestauracao.addEventListener(
        'click',
        function () {
            formRestauracao.classList
                .remove('ativo');

            inputArquivoBackup.value = '';

            arquivoBackupSelecionado
                .textContent =
                'Nenhum arquivo selecionado.';

            arquivoBackupSelecionado
                .classList
                .remove('com-arquivo');
        }
    );


    const modalLimpeza =
        document.getElementById(
            'modalLimpeza'
        );

    const abrirLimpeza =
        document.getElementById(
            'abrirLimpeza'
        );

    const cancelarLimpeza =
        document.getElementById(
            'cancelarLimpeza'
        );

    const confirmacaoLimpeza =
        document.getElementById(
            'confirmacaoLimpeza'
        );

    const confirmarLimpeza =
        document.getElementById(
            'confirmarLimpeza'
        );

    abrirLimpeza.addEventListener(
        'click',
        function () {
            modalLimpeza.classList
                .add('ativo');

            modalLimpeza.setAttribute(
                'aria-hidden',
                'false'
            );

            confirmacaoLimpeza.value = '';
            confirmarLimpeza.disabled = true;

            setTimeout(function () {
                confirmacaoLimpeza.focus();
            }, 100);
        }
    );

    function fecharModalLimpeza() {
        modalLimpeza.classList
            .remove('ativo');

        modalLimpeza.setAttribute(
            'aria-hidden',
            'true'
        );

        confirmacaoLimpeza.value = '';
        confirmarLimpeza.disabled = true;
    }

    cancelarLimpeza.addEventListener(
        'click',
        fecharModalLimpeza
    );

    modalLimpeza.addEventListener(
        'click',
        function (evento) {
            if (evento.target === modalLimpeza) {
                fecharModalLimpeza();
            }
        }
    );

    confirmacaoLimpeza.addEventListener(
        'input',
        function () {
            const confirmou =
                this.value.trim()
                    .toUpperCase()
                === 'CONFIRMAR';

            confirmarLimpeza.disabled =
                !confirmou;
        }
    );

});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>