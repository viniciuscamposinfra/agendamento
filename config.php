<?php

$host = "br1026.hostgator.com.br";
$porta = "3306";
$banco = "vin25708_agendamentoveterinario";
$usuario = "vin25708_adminag";
$senha = "BVHiojo%NJOS9558";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Erro ao conectar com o banco de dados.');
}