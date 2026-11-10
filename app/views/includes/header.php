<?php

$corPrincipalSistema = '#00d26a';
$nomeEmpresaSistema = 'JP TechCell ERP';

try {
    require_once __DIR__ . '/../../core/Database.php';

    $pdoConfiguracao = Database::conectar();

    $stmtConfiguracao = $pdoConfiguracao->query(
        "
        SELECT nome_loja, cor_principal
        FROM configuracoes
        ORDER BY id ASC
        LIMIT 1
        "
    );

    $configuracaoSistema =
        $stmtConfiguracao->fetch(PDO::FETCH_ASSOC);

    if (!empty($configuracaoSistema['nome_loja'])) {
        $nomeEmpresaSistema =
            trim($configuracaoSistema['nome_loja']);
    }

    if (
        !empty($configuracaoSistema['cor_principal'])
        && preg_match(
            '/^#[0-9A-Fa-f]{6}$/',
            trim($configuracaoSistema['cor_principal'])
        )
    ) {
        $corPrincipalSistema =
            trim($configuracaoSistema['cor_principal']);
    }
} catch (Throwable $erro) {
    $corPrincipalSistema = '#00d26a';
}

$corPrincipalSistema =
    strtoupper($corPrincipalSistema);

$nomeEmpresaEscapado = htmlspecialchars(
    $nomeEmpresaSistema,
    ENT_QUOTES,
    'UTF-8'
);

$corPrincipalEscapada = htmlspecialchars(
    $corPrincipalSistema,
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>

<html
    lang="pt-BR"
    style="
        --cor-principal: <?= $corPrincipalEscapada ?>;
        --cor-principal-hover:
            color-mix(
                in srgb,
                <?= $corPrincipalEscapada ?> 82%,
                #000000
            );
        --cor-principal-escura:
            color-mix(
                in srgb,
                <?= $corPrincipalEscapada ?> 68%,
                #000000
            );
        --cor-principal-clara:
            color-mix(
                in srgb,
                <?= $corPrincipalEscapada ?> 13%,
                #ffffff
            );
        --cor-principal-muito-clara:
            color-mix(
                in srgb,
                <?= $corPrincipalEscapada ?> 6%,
                #ffffff
            );
        --cor-principal-transparente:
            color-mix(
                in srgb,
                <?= $corPrincipalEscapada ?> 18%,
                transparent
            );
        --cor-principal-texto-claro:
            color-mix(
                in srgb,
                <?= $corPrincipalEscapada ?> 55%,
                #ffffff
            );
    "
>

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="theme-color"
        content="<?= $corPrincipalEscapada ?>"
    >

    <title><?= $nomeEmpresaEscapado ?></title>

    <link
        rel="stylesheet"
        href="/jptechcell/public/assets/css/style.css?v=4"
    >

</head>

<body>

<div class="sistema">