<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcoes.php';

exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    die('Agendamento inválido.');
}


/*
    Marca como pago.
*/

$stmt = $pdo->prepare("
    UPDATE agendamentos
    SET
        status_pagamento = 'pago',
        data_pagamento = CURDATE()
    WHERE id = ?
");

$stmt->execute([$id]);


header(
    'Location: ver_agendamento.php?id='
    . $id
);

exit;