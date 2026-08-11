<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clientes.php');
    exit;
}


$petId = (int)($_POST['pet_id'] ?? 0);

$dataReceita = $_POST['data_receita'] ?? '';

$titulo = trim(
    $_POST['titulo'] ?? ''
);

$medicamentos = trim(
    $_POST['medicamentos'] ?? ''
);

$observacoes = trim(
    $_POST['observacoes'] ?? ''
);


// ============================
// VALIDAÇÕES
// ============================

if ($petId <= 0) {
    die('Pet inválido.');
}

if ($titulo === '') {
    die('Informe o título da receita.');
}

if ($medicamentos === '') {
    die('Informe os medicamentos.');
}


// ============================
// VERIFICA SE O PET EXISTE
// ============================

$stmt = $pdo->prepare("
    SELECT id
    FROM pets
    WHERE id = ?
");

$stmt->execute([$petId]);

if (!$stmt->fetch()) {
    die('Pet não encontrado.');
}


// ============================
// SALVAR RECEITA
// ============================

$stmt = $pdo->prepare("
    INSERT INTO receitas (
        pet_id,
        data_receita,
        titulo,
        medicamentos,
        observacoes
    )
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $petId,
    $dataReceita ?: date('Y-m-d'),
    $titulo,
    $medicamentos,
    $observacoes ?: null
]);


// ============================
// VOLTAR
// ============================

header(
    'Location: receitas.php?pet_id=' .
    $petId
);

exit;