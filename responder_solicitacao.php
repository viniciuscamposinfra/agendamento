<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: solicitacoes.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

$resposta = trim(
    $_POST['resposta'] ?? ''
);

$status = $_POST['status'] ?? 'pendente';


/*
    VALIDAR STATUS
*/

$statusPermitidos = [
    'pendente',
    'em_andamento',
    'concluida',
    'cancelada'
];

if (!in_array($status, $statusPermitidos, true)) {
    die('Status inválido.');
}

if ($id <= 0) {
    die('Solicitação inválida.');
}


/*
    VERIFICAR SE EXISTE
*/

$stmt = $pdo->prepare("
    SELECT id, pet_id
    FROM solicitacoes
    WHERE id = ?
");

$stmt->execute([$id]);

$solicitacao = $stmt->fetch();

if (!$solicitacao) {
    die('Solicitação não encontrada.');
}


/*
    SALVAR RESPOSTA E STATUS
*/

$stmt = $pdo->prepare("
    UPDATE solicitacoes
    SET
        resposta = ?,
        status = ?
    WHERE id = ?
");

$stmt->execute([
    $resposta ?: null,
    $status,
    $id
]);


/*
    VOLTAR PARA SOLICITAÇÕES
*/

header('Location: solicitacoes.php');

exit;