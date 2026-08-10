<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcoes.php';

exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: painel.php');
    exit;
}

$data = $_POST['data'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$horarioInicio = $_POST['horario_inicio'] ?? '';
$horarioFim = $_POST['horario_fim'] ?? '';
$motivo = trim($_POST['motivo'] ?? '');


/* Verifica a data */

if (!$data) {
    die('Erro: nenhuma data foi informada.');
}

if ($data < date('Y-m-d')) {
    die('Erro: não é possível bloquear uma data passada.');
}


/* Bloqueio do dia inteiro */

if ($tipo === 'dia') {

    $stmt = $pdo->prepare("
        INSERT INTO bloqueios
        (
            data_bloqueio,
            horario_inicio,
            horario_fim,
            dia_inteiro,
            motivo
        )
        VALUES (?, NULL, NULL, 1, ?)
    ");

    $stmt->execute([
        $data,
        $motivo ?: null
    ]);

}


/* Bloqueio de horário */

elseif ($tipo === 'horario') {

    if (!$horarioInicio || !$horarioFim) {
        die('Erro: informe o horário inicial e final.');
    }

    if ($horarioInicio >= $horarioFim) {
        die('Erro: o horário final precisa ser maior que o horário inicial.');
    }

    $stmt = $pdo->prepare("
        INSERT INTO bloqueios
        (
            data_bloqueio,
            horario_inicio,
            horario_fim,
            dia_inteiro,
            motivo
        )
        VALUES (?, ?, ?, 0, ?)
    ");

    $stmt->execute([
        $data,
        $horarioInicio,
        $horarioFim,
        $motivo ?: null
    ]);

}

else {

    die('Erro: tipo de bloqueio inválido.');

}


/* Volta para o painel */

header('Location: painel.php?sucesso=bloqueio');
exit;