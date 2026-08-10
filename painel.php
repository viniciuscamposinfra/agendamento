<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

$stmt = $pdo->query("
    SELECT
        COUNT(*) AS total
    FROM agendamentos
");

$totalAgendamentos = $stmt->fetch()['total'];


$stmt = $pdo->query("
    SELECT
        COUNT(*) AS total
    FROM agendamentos
    WHERE status = 'pendente'
");

$totalPendentes = $stmt->fetch()['total'];


$stmt = $pdo->query("
    SELECT
        COUNT(*) AS total
    FROM agendamentos
    WHERE status = 'aprovado'
");

$totalAprovados = $stmt->fetch()['total'];


$stmt = $pdo->query("
    SELECT
        COUNT(*) AS total
    FROM bloqueios
    WHERE data_bloqueio >= CURDATE()
");

$totalBloqueios = $stmt->fetch()['total'];


/*
    Próximas solicitações
*/
$stmt = $pdo->query("
    SELECT
        id,
        nome_tutor,
        telefone,
        nome_animal,
        especie,
        motivo,
        data_consulta,
        horario_consulta,
        status
    FROM agendamentos
    WHERE data_consulta >= CURDATE()
    ORDER BY data_consulta ASC, horario_consulta ASC
    LIMIT 10
");

$agendamentos = $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Painel | Dra. Caroline</title>

    <link rel="stylesheet" href="style.css">

</head>


<body class="painel-body">


<header class="painel-topo">

    <a href="index.php" class="logo">

        <span class="logo-pata">
            🐾
        </span>

        <div>

            <strong>Dra. Caroline</strong>

            <small>
                Painel administrativo
            </small>

        </div>

    </a>


    <div class="painel-acoes">

        <span>
            Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?> 👋
        </span>

        <a
            href="logout.php"
            class="btn-sair"
        >
            🚪 Sair
        </a>

    </div>

</header>


<main class="painel">

    <div class="painel-titulo">

        <div>

            <span>🐾 Área administrativa</span>

            <h1>
                Sua agenda
            </h1>

            <p>
                Gerencie suas consultas e horários.
            </p>

        </div>


        <a
            href="agenda.php"
            target="_blank"
            class="btn-principal"
        >
            👁 Ver agenda pública
        </a>

    </div>


    <!-- CARDS -->

    <section class="dashboard-cards">


        <div class="dashboard-card">

            <div class="dashboard-icone">
                📅
            </div>

            <div>

                <span>
                    Total de consultas
                </span>

                <strong>
                    <?= $totalAgendamentos ?>
                </strong>

            </div>

        </div>


        <div class="dashboard-card pendente">

            <div class="dashboard-icone">
                🟡
            </div>

            <div>

                <span>
                    Aguardando análise
                </span>

                <strong>
                    <?= $totalPendentes ?>
                </strong>

            </div>

        </div>


        <div class="dashboard-card aprovado">

            <div class="dashboard-icone">
                🟢
            </div>

            <div>

                <span>
                    Confirmadas
                </span>

                <strong>
                    <?= $totalAprovados ?>
                </strong>

            </div>

        </div>


        <div class="dashboard-card bloqueio">

            <div class="dashboard-icone">
                🚫
            </div>

            <div>

                <span>
                    Bloqueios futuros
                </span>

                <strong>
                    <?= $totalBloqueios ?>
                </strong>

            </div>

        </div>


    </section>


    <!-- AGENDAMENTOS -->

    <section class="painel-secao">

        <div class="secao-topo">

            <div>

                <span>
                    Próximos horários
                </span>

                <h2>
                    Agenda
                </h2>

            </div>

        </div>


        <?php if (!$agendamentos): ?>

            <div class="sem-agendamentos">

                <div>
                    🐶
                </div>

                <h3>
                    Nenhum agendamento ainda
                </h3>

                <p>
                    Quando alguém solicitar uma consulta,
                    ela aparecerá aqui.
                </p>

            </div>

        <?php else: ?>


            <div class="tabela-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Data
                            </th>

                            <th>
                                Horário
                            </th>

                            <th>
                                Tutor
                            </th>

                            <th>
                                Pet
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Ação
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($agendamentos as $agendamento): ?>

                            <?php

                            $status = $agendamento['status'];

                            $statusTexto = [
                                'pendente' => '🟡 Pendente',
                                'aprovado' => '🟢 Aprovado',
                                'recusado' => '🔴 Recusado',
                                'reagendamento' => '🔄 Reagendamento',
                                'cancelado' => '⚫ Cancelado'
                            ];

                            ?>

                            <tr>

                                <td>

                                    <?= date(
                                        'd/m/Y',
                                        strtotime(
                                            $agendamento['data_consulta']
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <?= substr(
                                        $agendamento['horario_consulta'],
                                        0,
                                        5
                                    ) ?>

                                </td>


                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $agendamento['nome_tutor']
                                        ) ?>
                                    </strong>

                                </td>


                                <td>

                                    🐾
                                    <?= htmlspecialchars(
                                        $agendamento['nome_animal']
                                    ) ?>

                                </td>


                                <td>

                                    <span class="status status-<?= $status ?>">

                                        <?= $statusTexto[$status] ?? $status ?>

                                    </span>

                                </td>


                                <td>

                                    <a
                                        href="#"
                                        class="btn-acao"
                                    >
                                        Ver
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


        <?php endif; ?>

    </section>

</main>

</body>

</html>