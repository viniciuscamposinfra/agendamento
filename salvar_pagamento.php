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

$valor = trim(
    $_POST['valor_consulta'] ?? ''
);


if ($id <= 0) {

    die('Erro: agendamento inválido.');

}


/*
    CONVERTER VALOR

    Exemplos:

    150
    150,00
    1.500,00
*/

if ($valor !== '') {

    $valor = str_replace('.', '', $valor);

    $valor = str_replace(',', '.', $valor);

    $valor = (float) $valor;

} else {

    $valor = null;

}


try {


    /*
        VERIFICA SE O AGENDAMENTO EXISTE
    */

    $stmt = $pdo->prepare("
        SELECT id
        FROM agendamentos
        WHERE id = ?
    ");

    $stmt->execute([
        $id
    ]);


    if (!$stmt->fetch()) {

        die('Erro: agendamento não encontrado.');

    }


    /*
        SALVA O VALOR

        IMPORTANTE:
        continua como PENDENTE.
    */

    $stmt = $pdo->prepare("
        UPDATE agendamentos
        SET valor_consulta = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $valor,
        $id
    ]);


    /*
        VOLTA PARA OS DETALHES
    */

    header(
        'Location: ver_agendamento.php?id=' . $id
    );

    exit;


} catch (PDOException $e) {

    echo '<h2>Erro ao salvar o pagamento</h2>';

    echo '<pre>';

    echo htmlspecialchars(
        $e->getMessage()
    );

    echo '</pre>';

}