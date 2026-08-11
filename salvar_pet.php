<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clientes.php');
    exit;
}

$clienteId = (int)($_POST['cliente_id'] ?? 0);
$petId = (int)($_POST['id'] ?? 0);

$nome = trim($_POST['nome'] ?? '');
$especie = trim($_POST['especie'] ?? '');
$raca = trim($_POST['raca'] ?? '');
$sexo = trim($_POST['sexo'] ?? '');
$dataNascimento = $_POST['data_nascimento'] ?? '';
$peso = $_POST['peso'] ?? '';
$alergias = trim($_POST['alergias'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');


// Valida cliente

if ($clienteId <= 0) {
    die('Cliente inválido.');
}


// Verifica se cliente existe

$stmt = $pdo->prepare("
    SELECT id
    FROM clientes
    WHERE id = ?
");

$stmt->execute([$clienteId]);

if (!$stmt->fetch()) {
    die('Cliente não encontrado.');
}


// Valida campos obrigatórios

if ($nome === '' || $especie === '') {
    die('Nome e espécie são obrigatórios.');
}


// Trata campos opcionais

$dataNascimento = $dataNascimento !== ''
    ? $dataNascimento
    : null;

$peso = $peso !== ''
    ? $peso
    : null;


// NOVO PET

if ($petId <= 0) {

    $stmt = $pdo->prepare("
        INSERT INTO pets (
            cliente_id,
            nome,
            especie,
            raca,
            sexo,
            data_nascimento,
            peso,
            alergias,
            observacoes
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $clienteId,
        $nome,
        $especie,
        $raca ?: null,
        $sexo ?: null,
        $dataNascimento,
        $peso,
        $alergias ?: null,
        $observacoes ?: null
    ]);


// EDITAR PET

} else {

    $stmt = $pdo->prepare("
        UPDATE pets
        SET
            nome = ?,
            especie = ?,
            raca = ?,
            sexo = ?,
            data_nascimento = ?,
            peso = ?,
            alergias = ?,
            observacoes = ?
        WHERE id = ?
          AND cliente_id = ?
    ");

    $stmt->execute([
        $nome,
        $especie,
        $raca ?: null,
        $sexo ?: null,
        $dataNascimento,
        $peso,
        $alergias ?: null,
        $observacoes ?: null,
        $petId,
        $clienteId
    ]);
}


// Volta para a lista de pets

header(
    'Location: pets.php?cliente_id=' . $clienteId
);

exit;