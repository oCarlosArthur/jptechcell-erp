<?php

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

$statusAtual = trim(
    (string) ($ordem['status'] ?? 'recebida')
);

$nomeStatus = $statusFormatados[$statusAtual]
    ?? ucfirst(
        str_replace('_', ' ', $statusAtual)
    );

function imprimirTextoOs(mixed $valor): string
{
    if ($valor === null || trim((string) $valor) === '') {
        return '-';
    }

    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

function imprimirDataOs(
    mixed $data,
    bool $comHorario = true
): string {
    if (empty($data)) {
        return '-';
    }

    $timestamp = strtotime((string) $data);

    if ($timestamp === false) {
        return '-';
    }

    return date(
        $comHorario ? 'd/m/Y H:i' : 'd/m/Y',
        $timestamp
    );
}

function imprimirSimNaoOs(mixed $valor): string
{
    return (string) $valor === '1'
        ? 'Sim'
        : 'Não';
}

function imprimirDinheiroOs(mixed $valor): string
{
    return 'R$ ' . number_format(
        (float) $valor,
        2,
        ',',
        '.'
    );
}

function montarEnderecoOs(array $ordem): string
{
    $logradouro = trim(
        (string) ($ordem['cliente_endereco'] ?? '')
    );

    $numero = trim(
        (string) ($ordem['cliente_numero'] ?? '')
    );

    $bairro = trim(
        (string) ($ordem['cliente_bairro'] ?? '')
    );

    $cidade = trim(
        (string) ($ordem['cliente_cidade'] ?? '')
    );

    $estado = trim(
        (string) ($ordem['cliente_estado'] ?? '')
    );

    $partes = [];

    if ($logradouro !== '') {
        $enderecoInicial = $logradouro;

        if ($numero !== '') {
            $enderecoInicial .= ', ' . $numero;
        }

        $partes[] = $enderecoInicial;
    }

    if ($bairro !== '') {
        $partes[] = $bairro;
    }

    $cidadeEstado = '';

    if ($cidade !== '') {
        $cidadeEstado = $cidade;
    }

    if ($estado !== '') {
        $cidadeEstado .= $cidadeEstado !== ''
            ? ' - ' . $estado
            : $estado;
    }

    if ($cidadeEstado !== '') {
        $partes[] = $cidadeEstado;
    }

    if (empty($partes)) {
        return '-';
    }

    return htmlspecialchars(
        implode(' • ', $partes),
        ENT_QUOTES,
        'UTF-8'
    );
}

$marca = trim(
    (string) ($ordem['aparelho_marca'] ?? '')
);

$modelo = trim(
    (string) ($ordem['aparelho_modelo'] ?? '')
);

$aparelhoCompleto = trim(
    $marca . ' ' . $modelo
);

if ($aparelhoCompleto === '') {
    $aparelhoCompleto = '-';
}

$numeroOs = imprimirTextoOs(
    $ordem['numero_os'] ?? '-'
);

$clienteNome = imprimirTextoOs(
    $ordem['cliente_nome'] ?? '-'
);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= $numeroOs ?> - Impressão
    </title>

    <style>
        @page { size: A4 portrait; margin: 5mm; }
        * { box-sizing: border-box; }
        html, body { margin:0; padding:0; background:#e9edf2; color:#111; font-family:Arial, Helvetica, sans-serif; }
        body { padding:16px; }
        .barra-acoes { display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:16px; }
        .botao-acao { display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:0 18px; border:none; border-radius:8px; font-size:14px; font-weight:800; text-decoration:none; cursor:pointer; }
        .botao-voltar { border:1px solid #c7ced8; background:#fff; color:#293444; }
        .botao-imprimir { background:#159ee8; color:#fff; }
        .folha-a4 { width:210mm; min-height:297mm; margin:0 auto; padding:4mm 5mm; background:#fff; box-shadow:0 3px 18px rgba(0,0,0,.18); }
        .via-os { width:100%; height:137mm; overflow:hidden; border:1.2px solid #111; background:#fff; page-break-inside:avoid; break-inside:avoid; }
        .separador-vias { position:relative; height:5mm; border-top:1px dashed #555; border-bottom:1px dashed #555; }
        .separador-vias span { position:absolute; top:50%; left:50%; padding:0 7px; background:#fff; color:#555; font-size:6.5px; font-weight:800; letter-spacing:.4px; transform:translate(-50%,-50%); }
        .cabecalho-os { display:grid; grid-template-columns:31mm 1fr 43mm; align-items:center; min-height:18mm; padding:1.8mm 2.2mm; border-bottom:1.2px solid #111; }
        .logo-os { display:flex; align-items:center; justify-content:flex-start; }
        .logo-os img { display:block; width:29mm; max-height:14mm; object-fit:contain; }
        .empresa-os { padding:0 2mm; text-align:center; }
        .empresa-os h1 { margin:0 0 .5mm; font-size:11px; line-height:1; text-transform:uppercase; }
        .empresa-os p { margin:0; color:#444; font-size:5.8px; line-height:1.2; }
        .identificacao-os { text-align:right; }
        .tipo-via { display:inline-block; margin-bottom:.8mm; padding:.8mm 1.6mm; border:1px solid #222; border-radius:3px; font-size:6px; font-weight:800; text-transform:uppercase; }
        .numero-os { display:block; font-size:11px; font-weight:900; line-height:1.1; }
        .status-os { display:block; margin-top:.5mm; font-size:5.8px; font-weight:800; text-transform:uppercase; }
        .bloco-os { border-bottom:1.1px solid #111; }
        .titulo-bloco { padding:.75mm 1.7mm; border-bottom:1px solid #111; background:#e7e7e7; font-size:6.1px; font-weight:900; letter-spacing:.2px; text-transform:uppercase; }
        .grade { display:grid; }
        .grade-cliente { grid-template-columns:2fr 1fr 1fr; }
        .grade-aparelho { grid-template-columns:1.35fr 1.35fr .75fr 1.55fr; }
        .grade-condicao { grid-template-columns:.8fr 1fr 2fr; }
        .grade-valores { grid-template-columns:repeat(4,1fr); }
        .grade-datas-os { grid-template-columns:1fr 1fr; }
        .campo { min-width:0; min-height:6.6mm; padding:.8mm 1.5mm; border-right:.8px solid #777; border-bottom:.8px solid #777; }
        .campo:last-child { border-right:none; }
        .campo-largo { grid-column:1/-1; }
        .rotulo { display:block; margin-bottom:.3mm; color:#555; font-size:4.9px; font-weight:800; line-height:1.05; text-transform:uppercase; }
        .valor { display:block; color:#111; font-size:6.2px; font-weight:600; line-height:1.15; overflow-wrap:anywhere; }
        .texto-tecnico { min-height:7.2mm; max-height:8.5mm; overflow:hidden; padding:.8mm 1.5mm; border-bottom:.8px solid #777; }
        .texto-tecnico:last-child { border-bottom:none; }
        .texto-tecnico .valor { font-weight:normal; white-space:pre-wrap; }
        .total-final .valor { font-size:7.5px; font-weight:900; }
        .rodape-os { display:grid; grid-template-columns:1.55fr 1fr; min-height:16mm; }
        .termos-os { padding:1mm 1.5mm; border-right:1px solid #222; font-size:4.7px; line-height:1.25; text-align:justify; }
        .assinaturas-os { display:grid; grid-template-columns:1fr 1fr; align-items:end; gap:2mm; min-height:11mm; padding:3.5mm 2mm 1mm; }
        .assinatura { padding-top:1mm; border-top:1px solid #222; font-size:5px; font-weight:800; text-align:center; }
        .observacao-rodape { margin-top:.4mm; color:#555; font-size:4.6px; text-align:center; }
        @media print {
            html, body { width:210mm; height:297mm; padding:0; overflow:hidden; background:#fff; }
            .barra-acoes { display:none !important; }
            .folha-a4 { width:210mm; height:297mm; min-height:297mm; margin:0; padding:4mm 5mm; overflow:hidden; box-shadow:none; }
            .via-os { height:137mm; page-break-inside:avoid; break-inside:avoid; }
        }
        @media screen and (max-width:900px) {
            body { padding:10px; }
            .folha-a4 { width:100%; min-height:auto; overflow-x:auto; }
            .via-os { min-width:720px; }
        }
    </style>

</head>

<body>

    <div class="barra-acoes">

        <a
            class="botao-acao botao-voltar"
            href="index.php?rota=ordem-servico-visualizar&id=<?= (int) $ordem['id'] ?>"
        >
            ← Voltar para a OS
        </a>

        <button
            class="botao-acao botao-imprimir"
            type="button"
            onclick="window.print()"
        >
            Imprimir
        </button>

    </div>

    <main class="folha-a4">

        <?php foreach (
            [
                'Via da loja',
                'Via do cliente'
            ] as $indiceVia => $nomeVia
        ): ?>

            <section class="via-os">

                <header class="cabecalho-os">

                    <div class="logo-os">

                        <img
                            src="assets/img/logo-jptechcell.png"
                            alt="Logo JP TechCell"
                        >

                    </div>

                    <div class="empresa-os">

                        <h1>JP TechCell</h1>

                        <p>
                            Assistência técnica e comércio de celulares
                        </p>

                        <p>
                            Documento de recebimento e acompanhamento
                            do aparelho
                        </p>

                    </div>

                    <div class="identificacao-os">

                        <span class="tipo-via">
                            <?= htmlspecialchars($nomeVia) ?>
                        </span>

                        <strong class="numero-os">
                            <?= $numeroOs ?>
                        </strong>

                        <span class="status-os">
                            Status:
                            <?= htmlspecialchars($nomeStatus) ?>
                        </span>

                    </div>

                </header>

                <section class="bloco-os">

                    <div class="titulo-bloco">
                        Dados do cliente
                    </div>

                    <div class="grade grade-cliente">

                        <div class="campo">

                            <span class="rotulo">Cliente</span>

                            <span class="valor">
                                <?= $clienteNome ?>
                            </span>

                        </div>

                        <div class="campo">

                            <span class="rotulo">CPF/CNPJ</span>

                            <span class="valor">
                                <?= imprimirTextoOs(
                                    $ordem['cliente_cpf_cnpj']
                                        ?? null
                                ) ?>
                            </span>

                        </div>

                        <div class="campo">

                            <span class="rotulo">Telefone</span>

                            <span class="valor">
                                <?= imprimirTextoOs(
                                    $ordem['cliente_telefone']
                                        ?? null
                                ) ?>
                            </span>

                        </div>

                        <div class="campo campo-largo">

                            <span class="rotulo">Endereço</span>

                            <span class="valor">
                                <?= montarEnderecoOs($ordem) ?>
                            </span>

                        </div>

                    </div>

                </section>

                <section class="bloco-os">

                    <div class="titulo-bloco">
                        Dados do aparelho
                    </div>

                    <div class="grade grade-aparelho">

                        <div class="campo">

                            <span class="rotulo">Marca</span>

                            <span class="valor">
                                <?= imprimirTextoOs(
                                    $ordem['aparelho_marca']
                                        ?? null
                                ) ?>
                            </span>

                        </div>

                        <div class="campo">

                            <span class="rotulo">Modelo</span>

                            <span class="valor">
                                <?= imprimirTextoOs(
                                    $ordem['aparelho_modelo']
                                        ?? null
                                ) ?>
                            </span>

                        </div>

                        <div class="campo">

                            <span class="rotulo">Cor</span>

                            <span class="valor">
                                <?= imprimirTextoOs(
                                    $ordem['aparelho_cor']
                                        ?? null
                                ) ?>
                            </span>

                        </div>

                        <div class="campo">

                            <span class="rotulo">
                                IMEI / Nº de série
                            </span>

                            <span class="valor">
                                <?= imprimirTextoOs(
                                    $ordem['aparelho_imei']
                                        ?? null
                                ) ?>
                            </span>

                        </div>

                    </div>

                    <div class="grade grade-condicao">

                        <div class="campo">

                            <span class="rotulo">Aparelho liga</span>

                            <span class="valor">
                                <?= imprimirSimNaoOs(
                                    $ordem['aparelho_ligado']
                                        ?? 0
                                ) ?>
                            </span>

                        </div>

                        <div class="campo">

                            <span class="rotulo">
                                Está desbloqueado
                            </span>

                            <span class="valor">
                                <?= imprimirSimNaoOs(
                                    $ordem[
                                        'aparelho_desbloqueado'
                                    ] ?? 0
                                ) ?>
                            </span>

                        </div>

                        <div class="campo">

                            <span class="rotulo">
                                Acessórios entregues
                            </span>

                            <span class="valor">
                                <?= imprimirTextoOs(
                                    $ordem[
                                        'acessorios_entregues'
                                    ] ?? null
                                ) ?>
                            </span>

                        </div>

                    </div>

                </section>

                <section class="bloco-os">

                    <div class="titulo-bloco">
                        Atendimento técnico
                    </div>

                    <div class="texto-tecnico">

                        <span class="rotulo">
                            Defeito relatado pelo cliente
                        </span>

                        <span class="valor">
                            <?= imprimirTextoOs(
                                $ordem['defeito_relatado']
                                    ?? null
                            ) ?>
                        </span>

                    </div>

                    <div class="texto-tecnico">

                        <span class="rotulo">
                            Diagnóstico
                        </span>

                        <span class="valor">
                            <?= imprimirTextoOs(
                                $ordem['diagnostico']
                                    ?? null
                            ) ?>
                        </span>

                    </div>

                    <div class="texto-tecnico">

                        <span class="rotulo">
                            Serviço realizado
                        </span>

                        <span class="valor">
                            <?= imprimirTextoOs(
                                $ordem['servico_realizado']
                                    ?? null
                            ) ?>
                        </span>

                    </div>

                </section>

                <section class="bloco-os">

                    <div class="titulo-bloco">
                        Valores, prazo e garantia
                    </div>

                    <div class="grade grade-valores">

                        <div class="campo">

                            <span class="rotulo">Valor da OS</span>

                            <span class="valor">
                                <?= imprimirDinheiroOs(
                                    $ordem['valor_os'] ?? 0
                                ) ?>
                            </span>

                        </div>

                        <div class="campo">

                            <span class="rotulo">Desconto</span>

                            <span class="valor">
                                <?= imprimirDinheiroOs(
                                    $ordem['desconto'] ?? 0
                                ) ?>
                            </span>

                        </div>

                        <div class="campo total-final">

                            <span class="rotulo">Total</span>

                            <span class="valor">
                                <?= imprimirDinheiroOs(
                                    $ordem['valor_total'] ?? 0
                                ) ?>
                            </span>

                        </div>

                        <div class="campo">

                            <span class="rotulo">
                                Garantia
                            </span>

                            <span class="valor">
                                <?= (int) (
                                    $ordem['garantia_dias'] ?? 0
                                ) ?>
                                dias
                            </span>

                        </div>

                    </div>

                    <div class="grade grade-datas-os">

                        <div class="campo">

                            <span class="rotulo">
                                Criada em
                            </span>

                            <span class="valor">
                                <?= imprimirDataOs(
                                    $ordem['data_entrada']
                                        ?? null
                                ) ?>
                            </span>

                        </div>

                        <div class="campo">

                            <span class="rotulo">
                                Prazo para entrega
                            </span>

                            <span class="valor">
                                <?= imprimirDataOs(
                                    $ordem['data_prevista']
                                        ?? null,
                                    false
                                ) ?>
                            </span>

                        </div>

                    </div>

                </section>

                <section class="bloco-os">

                    <div class="titulo-bloco">
                        Observações
                    </div>

                    <div class="texto-tecnico">

                        <span class="valor">
                            <?= imprimirTextoOs(
                                $ordem['observacoes'] ?? null
                            ) ?>
                        </span>

                    </div>

                </section>

                <footer class="rodape-os">

                    <div class="termos-os">

                        Declaro que entreguei o aparelho e os
                        acessórios descritos acima nas condições
                        informadas nesta ordem de serviço.

                        A assistência não se responsabiliza por dados,
                        arquivos, aplicativos, senhas ou informações
                        armazenadas no aparelho.

                        O prazo informado é uma previsão e poderá ser
                        alterado após análise técnica ou disponibilidade
                        de peças.

                    </div>

                    <div>

                        <div class="assinaturas-os">

                            <div class="assinatura">
                                Assinatura do cliente
                            </div>

                            <div class="assinatura">
                                Assinatura da loja
                            </div>

                        </div>

                        <div class="observacao-rodape">
                            <?= $aparelhoCompleto ?>
                            •
                            <?= $numeroOs ?>
                        </div>

                    </div>

                </footer>

            </section>

            <?php if ($indiceVia === 0): ?>

                <div class="separador-vias">
                    <span>
                        RECORTE AQUI
                    </span>
                </div>

            <?php endif; ?>

        <?php endforeach; ?>

    </main>

</body>

</html>