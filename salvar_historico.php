<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clientes.php');
    exit;
}

$petId = (int)($_POST['pet_id'] ?? 0);

$dataConsulta = $_POST['data_consulta'] ?? '';

$queixaPrincipal = trim(
    $_POST['queixa_principal'] ?? ''
);

$anamnese = trim(
    $_POST['anamnese'] ?? ''
);

$exameFisico = trim(
    $_POST['exame_fisico'] ?? ''
);

$diagnostico = trim(
    $_POST['diagnostico'] ?? ''
);

$tratamento = trim(
    $_POST['tratamento'] ?? ''
);

$medicamentos = trim(
    $_POST['medicamentos'] ?? ''
);

$observacoes = trim(
    $_POST['observacoes'] ?? ''
);


// Valida pet

if ($petId <= 0) {
    die('Pet inválido.');
}


// Verifica se o pet existe

$stmt = $pdo->prepare("
    SELECT id
    FROM pets
    WHERE id = ?
");

$stmt->execute([$petId]);

if (!$stmt->fetch()) {
    die('Pet não encontrado.');
}


// Valida data

if ($dataConsulta === '') {
    die('Informe a data do atendimento.');
}


// Salva histórico

$stmt = $pdo->prepare("
    INSERT INTO historico_clinico (
        pet_id,
        data_consulta,
        queixa_principal,
        anamnese,
        exame_fisico,
        diagnostico,
        tratamento,
        medicamentos,
        observacoes
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $petId,
    $dataConsulta,
    $queixaPrincipal ?: null,
    $anamnese ?: null,
    $exameFisico ?: null,
    $diagnostico ?: null,
    $tratamento ?: null,
    $medicamentos ?: null,
    $observacoes ?: null
]);


// Volta para o histórico

header(
    'Location: historico.php?pet_id=' . $petId
);

exit;