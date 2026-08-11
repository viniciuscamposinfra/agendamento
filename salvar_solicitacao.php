<?php

require_once 'config.php';

session_start();

if (!isset($_SESSION['cliente_id'])) {
    header('Location: cliente_login.php');
    exit;
}

$clienteId = (int) $_SESSION['cliente_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cliente.php');
    exit;
}

$petId = (int) ($_POST['pet_id'] ?? 0);

$tipo = trim(
    $_POST['tipo'] ?? ''
);

$descricao = trim(
    $_POST['descricao'] ?? ''
);


/*
    VALIDAÇÕES
*/

if ($petId <= 0) {
    die('Pet inválido.');
}

if ($tipo === '') {
    die('Selecione o tipo de solicitação.');
}

if ($descricao === '') {
    die('Informe a descrição da solicitação.');
}


/*
    VERIFICAR SE O PET PERTENCE AO CLIENTE
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM pets
    WHERE id = ?
      AND cliente_id = ?
");

$stmt->execute([
    $petId,
    $clienteId
]);

if (!$stmt->fetch()) {
    die('Pet não encontrado.');
}


/*
    SALVAR SOLICITAÇÃO
*/

$stmt = $pdo->prepare("
    INSERT INTO solicitacoes (
        cliente_id,
        pet_id,
        tipo,
        descricao,
        status
    )
    VALUES (?, ?, ?, ?, 'pendente')
");

$stmt->execute([
    $clienteId,
    $petId,
    $tipo,
    $descricao
]);


/*
    VOLTAR PARA O PET
*/

header(
    'Location: pet_cliente.php?id=' . $petId
);

exit;