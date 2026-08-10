<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcoes.php';

iniciarSessao();

if (usuarioLogado()) {
    header('Location: painel.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("
        SELECT id, nome, usuario, senha
        FROM usuarios
        WHERE usuario = ?
        LIMIT 1
    ");

    $stmt->execute([$usuario]);

    $usuarioBanco = $stmt->fetch();

    if ($usuarioBanco && password_verify($senha, $usuarioBanco['senha'])) {

        fazerLogin($usuarioBanco);

        header('Location: painel.php');
        exit;

    } else {

        $erro = 'Usuário ou senha incorretos.';

    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | Dra. Caroline</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="pagina-login">

<div class="login-container">

    <div class="login-card">

        <div class="login-logo">
            🐾
        </div>

        <span class="login-tag">
            Área exclusiva
        </span>

        <h1>
            Dra. Caroline
        </h1>

        <p class="login-descricao">
            Entre no painel para gerenciar
            sua agenda.
        </p>


        <?php if ($erro): ?>

            <div class="login-erro">
                <?= htmlspecialchars($erro) ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="campo">

                <label>
                    Usuário
                </label>

                <input
                    type="text"
                    name="usuario"
                    placeholder="Digite seu usuário"
                    required
                >

            </div>


            <div class="campo">

                <label>
                    Senha
                </label>

                <input
                    type="password"
                    name="senha"
                    placeholder="Digite sua senha"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-principal btn-login-submit"
            >
                🔐 Entrar
            </button>

        </form>


        <a
            href="index.php"
            class="voltar-site"
        >
            ← Voltar para o site
        </a>

    </div>

</div>

</body>

</html>