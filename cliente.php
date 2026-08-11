<?php

require_once 'config.php';

session_start();


// Verifica login do cliente

if (!isset($_SESSION['cliente_id'])) {

    header('Location: cliente_login.php');
    exit;

}


$clienteId = $_SESSION['cliente_id'];
$clienteNome = $_SESSION['cliente_nome'];


// Próximas consultas

$stmt = $pdo->prepare("
    SELECT
        id,
        nome_animal,
        especie,
        data_consulta,
        horario_consulta,
        status,
        nova_data,
        novo_horario
    FROM agendamentos
    WHERE nome_tutor = ?
      AND data_consulta >= CURDATE()
      AND status IN ('pendente', 'aprovado', 'reagendamento')
    ORDER BY data_consulta ASC, horario_consulta ASC
    LIMIT 5
");

$stmt->execute([$clienteNome]);

$consultas = $stmt->fetchAll();

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
        Área do Cliente | Dra. Caroline
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body class="painel-body">


<header class="painel-topo">

    <a
        href="index.php"
        class="logo"
    >

        <span class="logo-pata">
            🐾
        </span>

        <div>

            <strong>
                Dra. Caroline
            </strong>

            <small>
                Área do cliente
            </small>

        </div>

    </a>


    <div class="painel-acoes">

        <span>
            Olá,
            <?= htmlspecialchars($clienteNome) ?>
            👋
        </span>

        <a
            href="cliente_logout.php"
            class="btn-sair"
        >
            🚪 Sair
        </a>

    </div>

</header>


<main class="painel">


    <!-- CABEÇALHO -->

    <div class="painel-titulo">

        <div>

            <span>
                🐾 Área do tutor
            </span>

            <h1>
                Olá, <?= htmlspecialchars($clienteNome) ?>!
            </h1>

            <p>
                Aqui você pode acompanhar seus atendimentos
                com a Dra. Caroline.
            </p>

        </div>


        <a
            href="agenda.php"
            class="btn-principal"
        >
            📅 Nova consulta
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
                    Consultas
                </span>

                <strong>
                    <?= count($consultas) ?>
                </strong>

            </div>

        </div>


        <div class="dashboard-card">

            <div class="dashboard-icone">
                🐶
            </div>

            <div>

                <span>
                    Meus pets
                </span>

                <strong>
                    -
                </strong>

            </div>

        </div>


        <div class="dashboard-card">

            <div class="dashboard-icone">
                💊
            </div>

            <div>

                <span>
                    Receitas
                </span>

                <strong>
                    -
                </strong>

            </div>

        </div>


        <div class="dashboard-card">

            <div class="dashboard-icone">
                📎
            </div>

            <div>

                <span>
                    Documentos
                </span>

                <strong>
                    -
                </strong>

            </div>

        </div>


    </section>


    <!-- PRÓXIMAS CONSULTAS -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                📅 Seus atendimentos
            </span>

            <h2>
                Próximas consultas
            </h2>

        </div>


        <?php if (!$consultas): ?>


            <div class="sem-agendamentos">

                <div>
                    🐾
                </div>

                <h3>
                    Nenhuma consulta agendada
                </h3>

                <p>
                    Quando você solicitar uma consulta,
                    ela aparecerá aqui.
                </p>


                <br>


                <a
                    href="agenda.php"
                    class="btn-principal"
                >
                    📅 Agendar consulta
                </a>

            </div>


        <?php else: ?>


            <div class="tabela-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Pet
                            </th>

                            <th>
                                Data
                            </th>

                            <th>
                                Horário
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $consultas as $consulta
                        ): ?>


                            <tr>


                                <td>

                                    🐾

                                    <strong>

                                        <?= htmlspecialchars(
                                            $consulta['nome_animal']
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    <?= date(
                                        'd/m/Y',
                                        strtotime(
                                            $consulta['data_consulta']
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <?= substr(
                                        $consulta['horario_consulta'],
                                        0,
                                        5
                                    ) ?>

                                </td>


                                <td>


                                    <?php

                                    $status = [
                                        'pendente'
                                            => '🟡 Aguardando confirmação',

                                        'aprovado'
                                            => '🟢 Confirmada',

                                        'reagendamento'
                                            => '🔄 Novo horário'
                                    ];

                                    ?>


                                    <span
                                        class="status status-<?= htmlspecialchars(
                                            $consulta['status']
                                        ) ?>"
                                    >

                                        <?= $status[
                                            $consulta['status']
                                        ] ?? $consulta['status'] ?>

                                    </span>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


        <?php endif; ?>


    </section>


    <!-- ACESSOS -->

    <section class="dashboard-cards">


        <a
            href="solicitacoes.php"
            class="dashboard-card"
            style="text-decoration:none;"
        >

            <div class="dashboard-icone">
                📋
            </div>

            <div>

                <span>
                    Minhas solicitações
                </span>

                <strong>
                    →
                </strong>

            </div>

        </a>


        <a
            href="receitas.php"
            class="dashboard-card"
            style="text-decoration:none;"
        >

            <div class="dashboard-icone">
                💊
            </div>

            <div>

                <span>
                    Minhas receitas
                </span>

                <strong>
                    →
                </strong>

            </div>

        </a>


        <a
            href="documentos.php"
            class="dashboard-card"
            style="text-decoration:none;"
        >

            <div class="dashboard-icone">
                📎
            </div>

            <div>

                <span>
                    Meus exames
                </span>

                <strong>
                    →
                </strong>

            </div>

        </a>


    </section>


</main>


</body>

</html>