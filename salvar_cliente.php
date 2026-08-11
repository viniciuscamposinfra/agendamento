<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();


// Verifica se veio pelo formulário

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: clientes.php');
    exit;

}


// Recebe os dados

$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmarSenha = $_POST['confirmar_senha'] ?? '';


// Valida campos

if (
    $nome === '' ||
    $telefone === '' ||
    $email === '' ||
    $senha === '' ||
    $confirmarSenha === ''
) {

    die('Preencha todos os campos obrigatórios.');

}


// Verifica e-mail

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    die('E-mail inválido.');

}


// Verifica senha

if (strlen($senha) < 6) {

    die('A senha deve ter pelo menos 6 caracteres.');

}


// Confirma senha

if ($senha !== $confirmarSenha) {

    die('As senhas não são iguais.');

}


// Verifica se e-mail já existe

$stmt = $pdo->prepare("
    SELECT id
    FROM clientes
    WHERE email = ?
    LIMIT 1
");

$stmt->execute([$email]);

if ($stmt->fetch()) {

    die('Já existe um cliente cadastrado com este e-mail.');

}


// Criptografa a senha

$senhaHash = password_hash(
    $senha,
    PASSWORD_DEFAULT
);


// Salva cliente

$stmt = $pdo->prepare("
    INSERT INTO clientes (
        nome,
        telefone,
        email,
        senha
    )
    VALUES (?, ?, ?, ?)
");

$stmt->execute([
    $nome,
    $telefone,
    $email,
    $senhaHash
]);


// Volta para lista de clientes

header('Location: clientes.php');

exit;