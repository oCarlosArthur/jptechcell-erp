<?php

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$erroVenda = $_SESSION['erro_venda'] ?? null;
unset($_SESSION['erro_venda']);

$produtosVenda = [];

foreach ($produtos as $produto) {
    $produtosVenda[] = [
        'id' => (int) $produto['id'],
        'nome' => $produto['nome'],
        'marca' => $produto['marca'] ?? '',
        'codigo_barras' => $produto['codigo_barras'] ?? '',
        'preco_venda' => (float) $produto['preco_venda'],
        'estoque' => (int) $produto['quantidade_estoque']
    ];
}

$ordensVenda = [];

foreach ($ordensServicoProntas as $ordemServico) {
    $ordensVenda[] = [
        'id' => (int) $ordemServico['id'],

        'numero_os' =>
            $ordemServico['numero_os'],

        'cliente_id' =>
            (int) $ordemServico['cliente_id'],

        'cliente_nome' =>
            $ordemServico['cliente_nome'],

        'cliente_documento' =>
            $ordemServico['cliente_documento'] ?? '',

        'aparelho' => trim(
            ($ordemServico['aparelho_marca'] ?? '')
            . ' '
            . ($ordemServico['aparelho_modelo'] ?? '')
        ),

        'valor_total' =>
            (float) $ordemServico['valor_total']
    ];
}

?>

<style>
    .topo-nova-venda {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .topo-nova-venda h1 {
        margin: 0 0 5px;
        color: #ffffff;
        font-size: 32px;
    }

    .topo-nova-venda p {
        margin: 0;
        color: #aebed0;
        font-size: 15px;
    }

    .botao-voltar-venda {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 18px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 9px;
        background: transparent;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }

    .painel-nova-venda {
        margin-bottom: 22px;
        padding: 24px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        background: #19263a;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .titulo-painel-venda {
        margin: 0 0 5px;
        color: #ffffff;
        font-size: 20px;
    }

    .descricao-painel-venda {
        margin: 0 0 22px;
        color: #9fb0c4;
        font-size: 13px;
    }

    .grade-venda {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .campo-venda-total {
        grid-column: 1 / -1;
    }

    .campo-venda label {
        display: block;
        margin-bottom: 7px;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
    }

    .campo-venda input,
    .campo-venda select,
    .campo-venda textarea {
        width: 100%;
        min-height: 46px;
        box-sizing: border-box;
        padding: 0 14px;
        border: 1px solid rgba(255, 255, 255, 0.13);
        border-radius: 9px;
        outline: none;
        background: #101b2b;
        color: #ffffff;
        font-size: 14px;
    }

    .campo-venda textarea {
        min-height: 100px;
        padding-top: 12px;
        resize: vertical;
    }

    .campo-venda input:focus,
    .campo-venda select:focus,
    .campo-venda textarea:focus {
        border-color: #52d6ff;
        box-shadow: 0 0 0 3px rgba(82, 214, 255, 0.1);
    }

    .campo-venda input[readonly] {
        color: #52d6ff;
        font-weight: 800;
    }

    .tipo-cliente-venda {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .opcao-cliente-venda {
        position: relative;
        flex: 1;
        min-width: 210px;
    }

    .opcao-cliente-venda input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .opcao-cliente-venda label {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        margin: 0;
        padding: 10px 15px;
        box-sizing: border-box;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 9px;
        background: rgba(16, 27, 43, 0.75);
        color: #c7d3e2;
        cursor: pointer;
    }

    .opcao-cliente-venda input:checked + label {
        border-color: #20e58f;
        background: rgba(32, 229, 143, 0.13);
        color: #20e58f;
    }

    .campo-cliente-cadastrado {
        display: none;
        margin-top: 16px;
    }

    .campo-cliente-cadastrado.visivel {
        display: block;
    }

    .aviso-consumidor-final {
        margin-top: 10px;
        padding: 11px 13px;
        border: 1px solid rgba(82, 214, 255, 0.16);
        border-radius: 8px;
        background: rgba(82, 214, 255, 0.06);
        color: #a9c9dc;
        font-size: 12px;
    }

    .busca-produto-venda {
        display: flex;
        align-items: flex-end;
        gap: 12px;
    }

    .area-localizar-produto {
        position: relative;
        flex: 1;
        min-width: 0;
    }

    .lista-sugestoes-produtos {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 50;
        display: none;
        max-height: 330px;
        overflow-y: auto;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 10px;
        background: #101b2b;
        box-shadow: 0 16px 35px rgba(0, 0, 0, 0.35);
    }

    .lista-sugestoes-produtos.visivel {
        display: block;
    }

    .sugestao-produto {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        width: 100%;
        padding: 14px;
        border: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        background: transparent;
        color: #ffffff;
        text-align: left;
        cursor: pointer;
    }

    .sugestao-produto:hover,
    .sugestao-produto.selecionada {
        background: rgba(21, 158, 232, 0.13);
    }

    .sugestao-produto-nome {
        display: block;
        margin-bottom: 4px;
        color: #ffffff;
        font-weight: 800;
    }

    .sugestao-produto-detalhes {
        color: #8fa1b6;
        font-size: 12px;
    }

    .sugestao-produto-preco {
        flex-shrink: 0;
        color: #20e58f;
        font-weight: 800;
    }

    .nenhuma-sugestao {
        padding: 18px;
        color: #9fb0c4;
        text-align: center;
    }

    .botao-adicionar-produto,
    .botao-finalizar-venda {
        min-height: 46px;
        padding: 0 20px;
        border: none;
        border-radius: 9px;
        background: #159ee8;
        color: #ffffff;
        font-weight: 800;
        cursor: pointer;
    }

    .botao-finalizar-venda {
        background: #20c979;
        color: #08140f;
    }

    .botao-vincular-os {
        min-height: 46px;
        padding: 0 18px;
        border: 1px solid rgba(82, 214, 255, 0.3);
        border-radius: 9px;
        background: rgba(82, 214, 255, 0.08);
        color: #8ee3ff;
        font-weight: 800;
        cursor: pointer;
        transition: 0.2s;
    }

    .botao-vincular-os:hover {
        border-color: #52d6ff;
        background: rgba(82, 214, 255, 0.15);
        color: #ffffff;
    }

    .ajuda-busca-produto {
        display: block;
        margin-top: 7px;
        color: #8fa1b6;
        font-size: 12px;
    }

    .mensagem,
    .mensagem-produto {
        margin-bottom: 20px;
        padding: 14px 16px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 700;
    }

    .mensagem.erro,
    .mensagem-produto.erro {
        border: 1px solid rgba(255, 91, 91, 0.25);
        background: rgba(255, 91, 91, 0.1);
        color: #ff7777;
    }

    .mensagem-produto {
        display: none;
        margin-top: 12px;
        margin-bottom: 0;
    }

    .mensagem-produto.sucesso {
        display: block;
        border: 1px solid rgba(32, 229, 143, 0.25);
        background: rgba(32, 229, 143, 0.09);
        color: #20e58f;
    }

    .mensagem-produto.erro {
        display: block;
    }

    .carrinho-vazio {
        margin-top: 20px;
        padding: 34px 20px;
        border: 1px dashed rgba(255, 255, 255, 0.13);
        border-radius: 10px;
        color: #9fb0c4;
        text-align: center;
    }

    .tabela-carrinho-container {
        display: none;
        margin-top: 24px;
        overflow-x: auto;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
    }

    .tabela-carrinho-container.visivel {
        display: block;
    }

    .tabela-carrinho {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
    }

    .tabela-carrinho th,
    .tabela-carrinho td {
        padding: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        color: #dbe5f0;
        font-size: 13px;
        text-align: left;
    }

    .tabela-carrinho th {
        background: rgba(16, 27, 43, 0.75);
        color: #9fb0c4;
        font-size: 12px;
        text-transform: uppercase;
    }

    .produto-carrinho-nome {
        display: block;
        margin-bottom: 4px;
        color: #ffffff;
        font-weight: 800;
    }

    .produto-carrinho-codigo {
        color: #8fa1b6;
        font-size: 12px;
    }

    .quantidade-carrinho {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .botao-quantidade {
        width: 34px;
        height: 34px;
        border: 1px solid rgba(255, 255, 255, 0.13);
        border-radius: 7px;
        background: #101b2b;
        color: #ffffff;
        font-size: 18px;
        cursor: pointer;
    }

    .input-quantidade {
        width: 58px;
        height: 34px;
        border: 1px solid rgba(255, 255, 255, 0.13);
        border-radius: 7px;
        background: #101b2b;
        color: #ffffff;
        text-align: center;
    }

    .botao-remover-item {
        padding: 8px 12px;
        border: 1px solid rgba(255, 91, 91, 0.25);
        border-radius: 7px;
        background: rgba(255, 91, 91, 0.08);
        color: #ff7777;
        font-weight: 700;
        cursor: pointer;
    }

    .resumo-venda,
    .acoes-finalizar-venda {
        display: none;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .resumo-venda.visivel,
    .acoes-finalizar-venda.visivel {
        display: flex;
    }

    .resumo-venda-caixa {
        width: 100%;
        max-width: 390px;
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        background: rgba(16, 27, 43, 0.55);
    }

    .linha-resumo-venda {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 15px;
        color: #c7d3e2;
    }

    .linha-resumo-venda.total {
        margin-bottom: 0;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: #ffffff;
        font-size: 20px;
        font-weight: 900;
    }

    .linha-resumo-venda.total strong {
        color: #20e58f;
    }

    .campo-desconto {
        width: 130px;
        min-height: 40px;
        padding: 0 10px;
        border: 1px solid rgba(255, 255, 255, 0.13);
        border-radius: 7px;
        background: #101b2b;
        color: #ffffff;
        text-align: right;
    }

    .fundo-modal-os {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(4, 10, 20, 0.78);
}

.fundo-modal-os.visivel {
    display: flex;
}

.modal-os {
    width: 100%;
    max-width: 720px;
    max-height: 85vh;
    overflow-y: auto;
    padding: 24px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    background: #19263a;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
}

.topo-modal-os {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 20px;
}

.topo-modal-os h2 {
    margin: 0 0 5px;
    color: #ffffff;
    font-size: 22px;
}

.topo-modal-os p {
    margin: 0;
    color: #9fb0c4;
    font-size: 13px;
}

.fechar-modal-os {
    width: 40px;
    height: 40px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 8px;
    background: transparent;
    color: #ffffff;
    font-size: 22px;
    cursor: pointer;
}

.lista-ordens-venda {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.ordem-venda-opcao {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    width: 100%;
    padding: 16px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    background: #101b2b;
    color: #ffffff;
    text-align: left;
    cursor: pointer;
}

.ordem-venda-opcao:hover {
    border-color: rgba(82, 214, 255, 0.35);
    background: rgba(82, 214, 255, 0.07);
}

.numero-ordem-venda {
    display: block;
    margin-bottom: 5px;
    color: #52d6ff;
    font-weight: 900;
}

.detalhes-ordem-venda {
    display: block;
    color: #9fb0c4;
    font-size: 13px;
    line-height: 1.5;
}

.valor-ordem-venda {
    flex-shrink: 0;
    color: #20e58f;
    font-size: 17px;
    font-weight: 900;
}

.sem-ordens-venda {
    padding: 25px;
    border: 1px dashed rgba(255, 255, 255, 0.14);
    border-radius: 10px;
    color: #9fb0c4;
    text-align: center;
}

    @media (max-width: 800px) {
        .topo-nova-venda,
        .busca-produto-venda {
            align-items: stretch;
            flex-direction: column;
        }

        .grade-venda {
            grid-template-columns: 1fr;
        }

        .campo-venda-total {
            grid-column: 1;
        }

        .painel-nova-venda {
            padding: 16px;
        }
    }
</style>

<main class="conteudo">

    <header class="topo-nova-venda">
        <div>
            <h1>Nova venda</h1>

            <p>
                Venda produtos para consumidor final ou cliente cadastrado.
            </p>
        </div>

        <a
            href="index.php?rota=vendas"
            class="botao-voltar-venda"
        >
            Voltar
        </a>
    </header>

    <?php if ($erroVenda): ?>
        <div class="mensagem erro">
            <?= htmlspecialchars($erroVenda) ?>
        </div>
    <?php endif; ?>

    <form
        id="formVenda"
        action="index.php?rota=venda-salvar"
        method="POST"
        autocomplete="off"
    >

        <section class="painel-nova-venda">

            <h2 class="titulo-painel-venda">
                Dados da venda
            </h2>

            <p class="descricao-painel-venda">
                Informe o cliente e a forma de pagamento.
            </p>

            <div class="grade-venda">

                <div class="campo-venda">
                    <label for="numeroVenda">
                        Número da venda
                    </label>

                    <input
                        type="text"
                        id="numeroVenda"
                        value="<?= htmlspecialchars($numeroVenda) ?>"
                        readonly
                    >
                </div>

                <div class="campo-venda">
                    <label for="formaPagamento">
                        Forma de pagamento *
                    </label>

                    <select
                        id="formaPagamento"
                        name="forma_pagamento"
                        required
                    >
                        <option value="pix">PIX</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="debito">Cartão de débito</option>
                        <option value="credito">Cartão de crédito</option>
                        <option value="parcelado">Parcelado</option>
                    </select>
                </div>

                <div class="campo-venda campo-venda-total">

                    <label>Cliente da venda</label>

                    <div class="tipo-cliente-venda">

                        <div class="opcao-cliente-venda">
                            <input
                                type="radio"
                                id="consumidorFinal"
                                name="tipo_cliente"
                                value="final"
                                checked
                            >

                            <label for="consumidorFinal">
                                Consumidor final
                            </label>
                        </div>

                        <div class="opcao-cliente-venda">
                            <input
                                type="radio"
                                id="clienteCadastrado"
                                name="tipo_cliente"
                                value="cadastrado"
                            >

                            <label for="clienteCadastrado">
                                Cliente cadastrado
                            </label>
                        </div>

                    </div>

                    <div
                        id="campoClienteCadastrado"
                        class="campo-cliente-cadastrado"
                    >
                        <label for="clienteId">
                            Selecione o cliente
                        </label>

                        <select
                            id="clienteId"
                            name="cliente_id"
                        >
                            <option value="">
                                Selecione um cliente...
                            </option>

                            <?php foreach ($clientes as $cliente): ?>
                                <option
                                    value="<?= (int) $cliente['id'] ?>"
                                >
                                    <?= htmlspecialchars($cliente['nome']) ?>

                                    <?php if (!empty($cliente['cpf_cnpj'])): ?>
                                        —
                                        <?= htmlspecialchars(
                                            $cliente['cpf_cnpj']
                                        ) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div
                        id="avisoConsumidorFinal"
                        class="aviso-consumidor-final"
                    >
                        A venda será registrada sem vínculo com um cliente.
                    </div>

                </div>

                <div class="campo-venda campo-venda-total">
                    <label for="observacoes">
                        Observações
                    </label>

                    <textarea
                        id="observacoes"
                        name="observacoes"
                        maxlength="3000"
                        placeholder="Observações da venda..."
                    ></textarea>
                </div>

            </div>

        </section>

        <section class="painel-nova-venda">

            <h2 class="titulo-painel-venda">
                Itens da venda
            </h2>

            <p class="descricao-painel-venda">
                Pesquise pelo nome, marca ou etiqueta do produto.
            </p>

            <div class="busca-produto-venda">

                <div class="campo-venda area-localizar-produto">

                    <label for="buscaProduto">
                        Localizar produto
                    </label>

                    <input
                        type="text"
                        id="buscaProduto"
                        placeholder="Ex.: carregador, Samsung ou JP000014"
                        autocomplete="off"
                    >

                    <div
                        id="listaSugestoesProdutos"
                        class="lista-sugestoes-produtos"
                    ></div>

                    <small class="ajuda-busca-produto">
                        Digite parte do nome ou passe a etiqueta e pressione Enter.
                    </small>

                </div>

                <button
                    type="button"
                    id="botaoAdicionarProduto"
                    class="botao-adicionar-produto"
                >
                    Adicionar produto
                </button>

                <button
                    type="button"
                    id="botaoVincularOs"
                    class="botao-vincular-os"
                >
                    Vincular OS
                </button>

            </div>

            <div
                id="mensagemProduto"
                class="mensagem-produto"
            ></div>

            <div
                id="carrinhoVazio"
                class="carrinho-vazio"
            >
                Nenhum produto adicionado à venda.
            </div>

            <div
                id="tabelaCarrinhoContainer"
                class="tabela-carrinho-container"
            >
                <table class="tabela-carrinho">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ação</th>
                        </tr>
                    </thead>

                    <tbody id="corpoCarrinho"></tbody>
                </table>
            </div>

            <div id="camposOcultosItens"></div>

            <div
                id="resumoVenda"
                class="resumo-venda"
            >
                <div class="resumo-venda-caixa">

                    <div class="linha-resumo-venda">
                        <span>Subtotal</span>
                        <strong id="subtotalVenda">
                            R$ 0,00
                        </strong>
                    </div>

                    <div class="linha-resumo-venda">
                        <label for="descontoVenda">
                            Desconto
                        </label>

                        <input
                            type="number"
                            id="descontoVenda"
                            name="desconto"
                            class="campo-desconto"
                            min="0"
                            step="0.01"
                            value="0.00"
                        >
                    </div>

                    <div class="linha-resumo-venda total">
                        <span>Total</span>

                        <strong id="totalVenda">
                            R$ 0,00
                        </strong>
                    </div>

                </div>
            </div>

            <div
                id="acoesFinalizarVenda"
                class="acoes-finalizar-venda"
            >
                <button
                    type="submit"
                    class="botao-finalizar-venda"
                >
                    Finalizar venda
                </button>
            </div>

        </section>

    </form>

</main>

<div
    id="fundoModalOs"
    class="fundo-modal-os"
>
    <div class="modal-os">

        <div class="topo-modal-os">

            <div>
                <h2>Vincular ordem de serviço</h2>

                <p>
                    Selecione uma OS pronta e aguardando pagamento.
                </p>
            </div>

            <button
                type="button"
                id="fecharModalOs"
                class="fechar-modal-os"
                aria-label="Fechar"
            >
                ×
            </button>

        </div>

        <div
            id="listaOrdensVenda"
            class="lista-ordens-venda"
        ></div>

    </div>
</div>

<script>
const produtosDisponiveis = <?= json_encode(
    $produtosVenda,
    JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
    | JSON_HEX_AMP
) ?>;

const ordensDisponiveis = <?= json_encode(
    $ordensVenda,
    JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
    | JSON_HEX_AMP
) ?>;

const consumidorFinal =
    document.getElementById('consumidorFinal');

const clienteCadastrado =
    document.getElementById('clienteCadastrado');

const campoClienteCadastrado =
    document.getElementById('campoClienteCadastrado');

const clienteId =
    document.getElementById('clienteId');

const avisoConsumidorFinal =
    document.getElementById('avisoConsumidorFinal');

const buscaProduto =
    document.getElementById('buscaProduto');

const listaSugestoes =
    document.getElementById('listaSugestoesProdutos');

const botaoAdicionar =
    document.getElementById('botaoAdicionarProduto');

const mensagemProduto =
    document.getElementById('mensagemProduto');

const carrinhoVazio =
    document.getElementById('carrinhoVazio');

const tabelaContainer =
    document.getElementById('tabelaCarrinhoContainer');

const corpoCarrinho =
    document.getElementById('corpoCarrinho');

const camposOcultos =
    document.getElementById('camposOcultosItens');

const resumoVenda =
    document.getElementById('resumoVenda');

const subtotalVenda =
    document.getElementById('subtotalVenda');

const descontoVenda =
    document.getElementById('descontoVenda');

const totalVenda =
    document.getElementById('totalVenda');

const acoesFinalizar =
    document.getElementById('acoesFinalizarVenda');

const formVenda =
    document.getElementById('formVenda');

const botaoVincularOs =
    document.getElementById('botaoVincularOs');

const fundoModalOs =
    document.getElementById('fundoModalOs');

const fecharModalOs =
    document.getElementById('fecharModalOs');

const listaOrdensVenda =
    document.getElementById('listaOrdensVenda');

let carrinho = [];

let ordemSelecionada = null;

let sugestoes = [];

let indiceSelecionado = -1;

function normalizar(texto) {
    return String(texto ?? '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();
}

function moeda(valor) {
    return Number(valor).toLocaleString(
        'pt-BR',
        {
            style: 'currency',
            currency: 'BRL'
        }
    );
}

function escapar(texto) {
    const elemento = document.createElement('div');
    elemento.textContent = texto ?? '';
    return elemento.innerHTML;
}

function atualizarCliente() {
    if (clienteCadastrado.checked) {
        campoClienteCadastrado.classList.add('visivel');
        avisoConsumidorFinal.style.display = 'none';
        clienteId.required = true;
    } else {
        campoClienteCadastrado.classList.remove('visivel');
        avisoConsumidorFinal.style.display = 'block';
        clienteId.required = false;
        clienteId.value = '';
    }
}

function mostrarMensagem(texto, tipo) {
    mensagemProduto.textContent = texto;
    mensagemProduto.className =
        'mensagem-produto ' + tipo;

    clearTimeout(mostrarMensagem.tempo);

    mostrarMensagem.tempo = setTimeout(() => {
        mensagemProduto.className = 'mensagem-produto';
    }, 3500);
}

function pesquisarProdutos(termo) {
    const pesquisa = normalizar(termo);

    if (pesquisa.length < 2) {
        return [];
    }

    return produtosDisponiveis
        .filter(produto => {
            return normalizar(produto.nome).includes(pesquisa)
                || normalizar(produto.marca).includes(pesquisa)
                || normalizar(produto.codigo_barras).includes(pesquisa);
        })
        .sort((a, b) => {
            const nomeA = normalizar(a.nome);
            const nomeB = normalizar(b.nome);

            const aComeca = nomeA.startsWith(pesquisa);
            const bComeca = nomeB.startsWith(pesquisa);

            if (aComeca && !bComeca) return -1;
            if (!aComeca && bComeca) return 1;

            return nomeA.localeCompare(nomeB);
        })
        .slice(0, 10);
}

function renderizarSugestoes() {
    listaSugestoes.innerHTML = '';

    if (buscaProduto.value.trim().length < 2) {
        listaSugestoes.classList.remove('visivel');
        return;
    }

    listaSugestoes.classList.add('visivel');

    if (sugestoes.length === 0) {
        listaSugestoes.innerHTML = `
            <div class="nenhuma-sugestao">
                Nenhum produto encontrado.
            </div>
        `;
        return;
    }

    sugestoes.forEach((produto, indice) => {
        const botao = document.createElement('button');

        botao.type = 'button';

        botao.className =
            'sugestao-produto'
            + (indice === indiceSelecionado
                ? ' selecionada'
                : '');

        botao.innerHTML = `
            <span>
                <span class="sugestao-produto-nome">
                    ${escapar(produto.nome)}
                </span>

                <span class="sugestao-produto-detalhes">
                    ${produto.marca
                        ? escapar(produto.marca) + ' — '
                        : ''}

                    ${escapar(produto.codigo_barras)}
                    — Estoque: ${produto.estoque}
                </span>
            </span>

            <span class="sugestao-produto-preco">
                ${moeda(produto.preco_venda)}
            </span>
        `;

        botao.addEventListener('click', () => {
            adicionarProduto(produto);
        });

        listaSugestoes.appendChild(botao);
    });
}

function adicionarProduto(produto) {
    if (produto.estoque <= 0) {
        mostrarMensagem(
            'Esse produto está sem estoque.',
            'erro'
        );
        return;
    }

    const existente = carrinho.find(
        item => item.id === produto.id
    );

    if (existente) {
        if (existente.quantidade >= produto.estoque) {
            mostrarMensagem(
                'Estoque máximo atingido para esse produto.',
                'erro'
            );
            return;
        }

        existente.quantidade++;
    } else {
        carrinho.push({
            ...produto,
            quantidade: 1
        });
    }

    buscaProduto.value = '';
    sugestoes = [];
    indiceSelecionado = -1;
    listaSugestoes.classList.remove('visivel');

    renderizarCarrinho();

    mostrarMensagem(
        produto.nome + ' adicionado.',
        'sucesso'
    );

    buscaProduto.focus();
}

function adicionarDigitado() {
    const termo = normalizar(buscaProduto.value);

    if (!termo) {
        mostrarMensagem(
            'Digite o nome ou a etiqueta do produto.',
            'erro'
        );
        return;
    }

    const codigoExato = produtosDisponiveis.find(
        produto =>
            normalizar(produto.codigo_barras) === termo
    );

    if (codigoExato) {
        adicionarProduto(codigoExato);
        return;
    }

    if (
        indiceSelecionado >= 0
        && sugestoes[indiceSelecionado]
    ) {
        adicionarProduto(
            sugestoes[indiceSelecionado]
        );
        return;
    }

    if (sugestoes.length === 1) {
        adicionarProduto(sugestoes[0]);
        return;
    }

    mostrarMensagem(
        'Selecione um produto na lista.',
        'erro'
    );
}

function alterarQuantidade(produtoId, quantidade) {
    const item = carrinho.find(
        produto => produto.id === produtoId
    );

    if (!item) return;

    quantidade = parseInt(quantidade, 10);

    if (isNaN(quantidade) || quantidade < 1) {
        quantidade = 1;
    }

    if (quantidade > item.estoque) {
        quantidade = item.estoque;

        mostrarMensagem(
            'Quantidade limitada ao estoque disponível.',
            'erro'
        );
    }

    item.quantidade = quantidade;
    renderizarCarrinho();
}

function removerProduto(produtoId) {
    carrinho = carrinho.filter(
        produto => produto.id !== produtoId
    );

    renderizarCarrinho();
}

function calcularSubtotal() {
    const subtotalProdutos =
        carrinho.reduce((total, item) => {
            return total
                + Number(item.preco_venda)
                * Number(item.quantidade);
        }, 0);

    const valorOrdem =
        ordemSelecionada
            ? Number(ordemSelecionada.valor_total)
            : 0;

    return subtotalProdutos + valorOrdem;
}

function atualizarTotais() {
    const subtotal = calcularSubtotal();

    let desconto = Number(descontoVenda.value);

    if (isNaN(desconto) || desconto < 0) {
        desconto = 0;
    }

    if (desconto > subtotal) {
        desconto = subtotal;
        descontoVenda.value = desconto.toFixed(2);
    }

    subtotalVenda.textContent = moeda(subtotal);
    totalVenda.textContent = moeda(subtotal - desconto);
}

function atualizarCamposOcultos() {
    camposOcultos.innerHTML = '';

    carrinho.forEach((item, indice) => {
        camposOcultos.insertAdjacentHTML(
            'beforeend',
            `
                <input
                    type="hidden"
                    name="itens[${indice}][produto_id]"
                    value="${item.id}"
                >

                <input
                    type="hidden"
                    name="itens[${indice}][quantidade]"
                    value="${item.quantidade}"
                >
            `
        );
    });

    if (ordemSelecionada) {
        camposOcultos.insertAdjacentHTML(
            'beforeend',
            `
                <input
                    type="hidden"
                    name="ordem_servico_id"
                    value="${ordemSelecionada.id}"
                >
            `
        );
    }
}

function renderizarCarrinho() {
    corpoCarrinho.innerHTML = '';

    const possuiItens =
        carrinho.length > 0
        || ordemSelecionada !== null;

    if (!possuiItens) {
        carrinhoVazio.style.display = 'block';

        carrinhoVazio.textContent =
            'Nenhum produto ou ordem de serviço adicionada à venda.';

        tabelaContainer.classList.remove(
            'visivel'
        );

        resumoVenda.classList.remove(
            'visivel'
        );

        acoesFinalizar.classList.remove(
            'visivel'
        );

        atualizarCamposOcultos();
        atualizarTotais();

        return;
    }

    carrinhoVazio.style.display = 'none';

    tabelaContainer.classList.add(
        'visivel'
    );

    resumoVenda.classList.add(
        'visivel'
    );

    acoesFinalizar.classList.add(
        'visivel'
    );

    /*
     * Mostra a OS como primeiro item.
     */
    if (ordemSelecionada) {
        const linhaOrdem =
            document.createElement('tr');

        linhaOrdem.innerHTML = `
            <td>
                <span class="produto-carrinho-nome">
                    🔧 Ordem de Serviço
                    ${escapar(
                        ordemSelecionada.numero_os
                    )}
                </span>

                <span class="produto-carrinho-codigo">
                    ${escapar(
                        ordemSelecionada.aparelho || '-'
                    )}
                    — Cliente:
                    ${escapar(
                        ordemSelecionada.cliente_nome
                    )}
                </span>
            </td>

            <td>
                ${moeda(
                    ordemSelecionada.valor_total
                )}
            </td>

            <td>
                1
            </td>

            <td>
                ${moeda(
                    ordemSelecionada.valor_total
                )}
            </td>

            <td>
                <button
                    type="button"
                    class="botao-remover-item remover-os"
                >
                    Remover
                </button>
            </td>
        `;

        linhaOrdem
            .querySelector('.remover-os')
            .addEventListener('click', () => {
                ordemSelecionada = null;

                renderizarCarrinho();

                mostrarMensagem(
                    'Ordem de serviço removida da venda.',
                    'sucesso'
                );
            });

        corpoCarrinho.appendChild(
            linhaOrdem
        );
    }

    /*
     * Mostra os produtos normalmente.
     */
    carrinho.forEach(item => {
        const linha =
            document.createElement('tr');

        linha.innerHTML = `
            <td>
                <span class="produto-carrinho-nome">
                    ${escapar(item.nome)}
                </span>

                <span class="produto-carrinho-codigo">
                    ${escapar(item.codigo_barras)}
                    — Estoque: ${item.estoque}
                </span>
            </td>

            <td>
                ${moeda(item.preco_venda)}
            </td>

            <td>
                <div class="quantidade-carrinho">
                    <button
                        type="button"
                        class="botao-quantidade diminuir"
                    >
                        −
                    </button>

                    <input
                        type="number"
                        class="input-quantidade"
                        min="1"
                        max="${item.estoque}"
                        value="${item.quantidade}"
                    >

                    <button
                        type="button"
                        class="botao-quantidade aumentar"
                    >
                        +
                    </button>
                </div>
            </td>

            <td>
                ${moeda(
                    item.preco_venda
                    * item.quantidade
                )}
            </td>

            <td>
                <button
                    type="button"
                    class="botao-remover-item"
                >
                    Remover
                </button>
            </td>
        `;

        linha.querySelector('.diminuir')
            .addEventListener('click', () => {
                alterarQuantidade(
                    item.id,
                    item.quantidade - 1
                );
            });

        linha.querySelector('.aumentar')
            .addEventListener('click', () => {
                alterarQuantidade(
                    item.id,
                    item.quantidade + 1
                );
            });

        linha.querySelector(
            '.input-quantidade'
        ).addEventListener(
            'change',
            evento => {
                alterarQuantidade(
                    item.id,
                    evento.target.value
                );
            }
        );

        linha.querySelector(
            '.botao-remover-item'
        ).addEventListener(
            'click',
            () => {
                removerProduto(item.id);
            }
        );

        corpoCarrinho.appendChild(linha);
    });

    atualizarCamposOcultos();
    atualizarTotais();
}

function renderizarOrdensServico() {
    listaOrdensVenda.innerHTML = '';

    if (ordensDisponiveis.length === 0) {
        listaOrdensVenda.innerHTML = `
            <div class="sem-ordens-venda">
                Nenhuma ordem de serviço pronta está disponível.
            </div>
        `;

        return;
    }

    ordensDisponiveis.forEach(ordem => {
        const botao = document.createElement('button');

        botao.type = 'button';
        botao.className = 'ordem-venda-opcao';

        botao.innerHTML = `
            <span>
                <span class="numero-ordem-venda">
                    ${escapar(ordem.numero_os)}
                </span>

                <span class="detalhes-ordem-venda">
                    Cliente: ${escapar(ordem.cliente_nome)}
                    <br>
                    Aparelho: ${escapar(ordem.aparelho || '-')}
                </span>
            </span>

            <span class="valor-ordem-venda">
                ${moeda(ordem.valor_total)}
            </span>
        `;

        botao.addEventListener('click', () => {
            selecionarOrdemServico(ordem);
        });

        listaOrdensVenda.appendChild(botao);
    });
}

function selecionarOrdemServico(ordem) {
    ordemSelecionada = ordem;

    clienteCadastrado.checked = true;
    consumidorFinal.checked = false;

    atualizarCliente();

    clienteId.value =
        String(ordem.cliente_id);

    fundoModalOs.classList.remove('visivel');

    renderizarCarrinho();

    mostrarMensagem(
        `${ordem.numero_os} adicionada à venda.`,
        'sucesso'
    );
}

consumidorFinal.addEventListener(
    'change',
    atualizarCliente
);

clienteCadastrado.addEventListener(
    'change',
    atualizarCliente
);

buscaProduto.addEventListener('input', () => {
    sugestoes = pesquisarProdutos(
        buscaProduto.value
    );

    indiceSelecionado = -1;
    renderizarSugestoes();
});

buscaProduto.addEventListener('keydown', evento => {
    if (evento.key === 'ArrowDown') {
        evento.preventDefault();

        if (sugestoes.length > 0) {
            indiceSelecionado++;

            if (indiceSelecionado >= sugestoes.length) {
                indiceSelecionado = 0;
            }

            renderizarSugestoes();
        }
    }

    if (evento.key === 'ArrowUp') {
        evento.preventDefault();

        if (sugestoes.length > 0) {
            indiceSelecionado--;

            if (indiceSelecionado < 0) {
                indiceSelecionado = sugestoes.length - 1;
            }

            renderizarSugestoes();
        }
    }

    if (evento.key === 'Enter') {
        evento.preventDefault();
        adicionarDigitado();
    }

    if (evento.key === 'Escape') {
        listaSugestoes.classList.remove('visivel');
    }
});

botaoAdicionar.addEventListener(
    'click',
    adicionarDigitado
);

descontoVenda.addEventListener(
    'input',
    atualizarTotais
);

formVenda.addEventListener('submit', evento => {
    if (
        carrinho.length === 0
        && !ordemSelecionada
    ) {
        evento.preventDefault();

        mostrarMensagem(
            'Adicione pelo menos um produto ou uma ordem de serviço.',
            'erro'
        );

        return;
    }

    if (
        clienteCadastrado.checked
        && !clienteId.value
    ) {
        evento.preventDefault();

        mostrarMensagem(
            'Selecione o cliente cadastrado.',
            'erro'
        );

        return;
    }

    if (ordemSelecionada) {
        const clienteDaOrdem =
            String(ordemSelecionada.cliente_id);

        if (
            String(clienteId.value)
            !== clienteDaOrdem
        ) {
            evento.preventDefault();

            mostrarMensagem(
                'O cliente selecionado é diferente do cliente da ordem de serviço.',
                'erro'
            );
        }
    }
});

document.addEventListener('click', evento => {
    if (!evento.target.closest('.area-localizar-produto')) {
        listaSugestoes.classList.remove('visivel');
    }
});

botaoVincularOs.addEventListener('click', () => {
    renderizarOrdensServico();
    fundoModalOs.classList.add('visivel');
});

fecharModalOs.addEventListener('click', () => {
    fundoModalOs.classList.remove('visivel');
});

fundoModalOs.addEventListener('click', evento => {
    if (evento.target === fundoModalOs) {
        fundoModalOs.classList.remove('visivel');
    }
});

atualizarCliente();
renderizarCarrinho();
buscaProduto.focus();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>