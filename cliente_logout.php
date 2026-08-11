<?php

session_start();


// Limpa todas as informações da sessão
$_SESSION = [];


// Se existir cookie de sessão, remove também
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


// Destrói a sessão
session_destroy();


// Volta para o login do cliente
header('Location: cliente_login.php');

exit;