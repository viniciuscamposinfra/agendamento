<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clientes.php');
    exit;
}

$petId = (int)($_POST['pet_id'] ?? 0);

$titulo = trim(
    $_POST['titulo'] ?? ''
);

$descricao = trim(
    $_POST['descricao'] ?? ''
);

$dataExame = $_POST['data_exame'] ?? '';


// ============================
// VALIDAÇÕES
// ============================

if ($petId <= 0) {
    die('Pet inválido.');
}

if ($titulo === '') {
    die('Informe o nome do exame.');
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
// VERIFICA ARQUIVO
// ============================

if (
    !isset($_FILES['arquivo']) ||
    $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK
) {
    die('Selecione um arquivo.');
}


$arquivo = $_FILES['arquivo'];


// ============================
// TAMANHO MÁXIMO
// 10 MB
// ============================

$limite = 10 * 1024 * 1024;

if ($arquivo['size'] > $limite) {
    die('O arquivo é muito grande. Limite: 10 MB.');
}


// ============================
// EXTENSÃO
// ============================

$extensao = strtolower(
    pathinfo(
        $arquivo['name'],
        PATHINFO_EXTENSION
    )
);


$extensoesPermitidas = [
    'pdf',
    'jpg',
    'jpeg',
    'png'
];


if (!in_array(
    $extensao,
    $extensoesPermitidas,
    true
)) {
    die(
        'Tipo de arquivo não permitido. ' .
        'Use PDF, JPG, JPEG ou PNG.'
    );
}


// ============================
// CRIA PASTA
// ============================

$pasta = __DIR__ . '/uploads/exames/';


if (!is_dir($pasta)) {

    mkdir(
        $pasta,
        0755,
        true
    );

}


// ============================
// GERA NOME ÚNICO
// ============================

$nomeArquivo =
    'pet_' .
    $petId .
    '_' .
    uniqid() .
    '.' .
    $extensao;


$caminhoCompleto =
    $pasta .
    $nomeArquivo;


// ============================
// MOVE ARQUIVO
// ============================

if (!move_uploaded_file(
    $arquivo['tmp_name'],
    $caminhoCompleto
)) {

    die(
        'Não foi possível salvar o arquivo.'
    );

}


// ============================
// TIPO DO ARQUIVO
// ============================

$tipoArquivo =
    $arquivo['type']
    ?? 'application/octet-stream';


// ============================
// SALVA NO BANCO
// ============================

$stmt = $pdo->prepare("
    INSERT INTO exames (
        pet_id,
        titulo,
        descricao,
        data_exame,
        arquivo,
        tipo_arquivo
    )
    VALUES (?, ?, ?, ?, ?, ?)
");


$stmt->execute([
    $petId,
    $titulo,
    $descricao ?: null,
    $dataExame ?: null,
    $nomeArquivo,
    $tipoArquivo
]);


// ============================
// VOLTA PARA EXAMES
// ============================

header(
    'Location: exames.php?pet_id=' .
    $petId
);

exit;