<?php

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: agenda.php');
    exit;
}


/*
    RECEBE OS DADOS
*/

$nomeTutor = trim($_POST['nome_tutor'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$email = trim($_POST['email'] ?? '');
$nomeAnimal = trim($_POST['nome_animal'] ?? '');
$especie = trim($_POST['especie'] ?? '');
$local = trim($_POST['local'] ?? '');
$motivo = trim($_POST['motivo'] ?? '');
$data = $_POST['data'] ?? '';
$horario = $_POST['horario'] ?? '';



/*
    VALIDAÇÃO
*/

if (
    !$nomeTutor ||
    !$telefone ||
    !$nomeAnimal ||
    !$especie ||
    !$local ||
    !$motivo ||
    !$data ||
    !$horario
) {

    die(
        'Erro: preencha todos os campos obrigatórios.'
    );

}



/*
    VERIFICA SE O HORÁRIO ESTÁ DISPONÍVEL
*/

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM agendamentos
    WHERE data_consulta = ?
      AND horario_consulta = ?
      AND status IN (
          'pendente',
          'aprovado',
          'reagendamento'
      )
");

$stmt->execute([
    $data,
    $horario
]);


if ($stmt->fetchColumn() > 0) {

    die(
        'Esse horário acabou de ser ocupado. '
        . 'Por favor, escolha outro horário.'
    );

}



/*
    VERIFICA BLOQUEIOS
*/

$stmt = $pdo->prepare("
    SELECT
        horario_inicio,
        horario_fim,
        dia_inteiro
    FROM bloqueios
    WHERE data_bloqueio = ?
");

$stmt->execute([
    $data
]);

$bloqueios = $stmt->fetchAll();


foreach ($bloqueios as $bloqueio) {


    if ($bloqueio['dia_inteiro']) {

        die(
            'Esse dia está bloqueado para atendimento.'
        );

    }


    if (
        $bloqueio['horario_inicio'] &&
        $bloqueio['horario_fim'] &&
        $horario >= $bloqueio['horario_inicio'] &&
        $horario < $bloqueio['horario_fim']
    ) {

        die(
            'Esse horário está bloqueado para atendimento.'
        );

    }

}



/*
    SALVA O AGENDAMENTO

    O financeiro começa automaticamente como:

    valor_consulta = NULL
    status_pagamento = pendente
    data_pagamento = NULL
*/

$stmt = $pdo->prepare("
    INSERT INTO agendamentos
    (
        nome_tutor,
        telefone,
        email,
        nome_animal,
        especie,
        local,
        motivo,
        data_consulta,
        horario_consulta,
        status,
        valor_consulta,
        status_pagamento,
        data_pagamento
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        'pendente',
        NULL,
        'pendente',
        NULL
    )
");


$stmt->execute([

    $nomeTutor,
    $telefone,
    $email ?: null,
    $nomeAnimal,
    $especie,
    $local,
    $motivo,
    $data,
    $horario

]);



/*
    CONFIRMAÇÃO
*/

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Solicitação enviada | Dra. Caroline
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>


<main class="agenda-pagina">


    <div class="agenda-titulo">

        <span>
            🐾 Solicitação enviada
        </span>

        <h1>
            Consulta solicitada!
        </h1>

        <p>
            Sua solicitação foi recebida com sucesso.
        </p>

    </div>


    <div class="formulario-agendamento">


        <h2>
            📅 Detalhes da solicitação
        </h2>


        <p>

            <strong>
                Data:
            </strong>

            <?= date(
                'd/m/Y',
                strtotime($data)
            ) ?>

        </p>


        <p>

            <strong>
                Horário:
            </strong>

            <?= htmlspecialchars(
                $horario
            ) ?>

        </p>


        <p>

            <strong>
                Local:
            </strong>

            <?= htmlspecialchars(
                $local
            ) ?>

        </p>


        <p>

            <strong>
                Pet:
            </strong>

            <?= htmlspecialchars(
                $nomeAnimal
            ) ?>

        </p>


        <p>

            🟡

            <strong>
                Aguardando confirmação da Dra. Caroline.
            </strong>

        </p>


        <br>


        <a
            href="agenda.php"
            class="btn-principal"
        >

            ← Voltar para a agenda

        </a>


    </div>


</main>


</body>

</html>