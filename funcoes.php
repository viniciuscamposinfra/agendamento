<?php

function iniciarSessao()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function usuarioLogado()
{
    iniciarSessao();

    return isset($_SESSION['usuario_id']);
}

function exigirLogin()
{
    if (!usuarioLogado()) {
        header('Location: login.php');
        exit;
    }
}

function fazerLogin($usuario)
{
    iniciarSessao();

    session_regenerate_id(true);

    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_login'] = $usuario['usuario'];
}

function fazerLogout()
{
    iniciarSessao();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}