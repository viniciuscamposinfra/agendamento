<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcoes.php';

exigirLogin();

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    die('Erro: bloqueio inválido.');
}

$stmt = $pdo->prepare("
    DELETE FROM bloqueios
    WHERE id = ?
");

$stmt->execute([$id]);

header('Location: painel.php?sucesso=desbloqueado');
exit;