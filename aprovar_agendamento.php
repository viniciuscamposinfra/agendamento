<?php

ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcoes.php';

exigirLogin();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: painel.php');
    exit;

}


$id = (int) ($_POST['id'] ?? 0);


if ($id <= 0) {

    die('Erro: agendamento inválido.');

}


try {

    /*
        VERIFICA SE O AGENDAMENTO EXISTE
    */

    $stmt = $pdo->prepare("
        SELECT
            id,
            status
        FROM agendamentos
        WHERE id = ?
    ");

    $stmt->execute([
        $id
    ]);

    $agendamento = $stmt->fetch();


    if (!$agendamento) {

        die('Erro: agendamento não encontrado.');

    }


    /*
        APROVA A CONSULTA
    */

    if ($agendamento['status'] !== 'pendente') {

        die(
            'Essa consulta não está mais pendente. '
            . 'Status atual: '
            . htmlspecialchars(
                $agendamento['status']
            )
        );

    }


    $stmt = $pdo->prepare("
        UPDATE agendamentos
        SET status = 'aprovado'
        WHERE id = ?
    ");

    $stmt->execute([
        $id
    ]);


    /*
        VOLTA PARA O PAINEL
    */

    header('Location: painel.php');

    exit;


} catch (PDOException $e) {

    echo '<h2>Erro ao aprovar a consulta</h2>';

    echo '<pre>';

    echo htmlspecialchars(
        $e->getMessage()
    );

    echo '</pre>';

}