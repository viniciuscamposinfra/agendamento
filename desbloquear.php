<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcoes.php';

exigirLogin();

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    header('Location: painel.php');
    exit;
}

$stmt = $pdo->prepare("
    DELETE FROM bloqueios
    WHERE id = ?
");

$stmt->execute([$id]);

header('Location: painel.php?sucesso=desbloqueado');
exit;