<?php

require_once __DIR__ . '/../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$loginDigitado = trim($_POST['usuario'] ?? '');
$senhaDigitada = $_POST['senha'] ?? '';

if ($loginDigitado === '' || $senhaDigitada === '') {
    header('Location: index.php?erro=campos');
    exit;
}

$modelUsuario = new Usuario();
$usuarioEncontrado = $modelUsuario->buscarPorUsuario($loginDigitado);

$loginValido =
    $usuarioEncontrado &&
    (int) $usuarioEncontrado['ativo'] === 1 &&
    password_verify($senhaDigitada, $usuarioEncontrado['senha']);

if (!$loginValido) {
    header('Location: index.php?erro=login');
    exit;
}

session_regenerate_id(true);

$_SESSION['usuario_id'] = $usuarioEncontrado['id'];
$_SESSION['usuario_nome'] = $usuarioEncontrado['nome'];
$_SESSION['usuario_login'] = $usuarioEncontrado['usuario'];

header('Location: index.php?rota=dashboard');
exit;