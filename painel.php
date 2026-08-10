<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcoes.php';

exigirLogin();


/* =========================
   CARDS
========================= */

$stmt = $pdo->query("
    SELECT COUNT(*) AS total
    FROM agendamentos
");

$totalAgendamentos = $stmt->fetch()['total'];


$stmt = $pdo->query("
    SELECT COUNT(*) AS total
    FROM agendamentos
    WHERE status = 'pendente'
");

$totalPendentes = $stmt->fetch()['total'];


$stmt = $pdo->query("
    SELECT COUNT(*) AS total
    FROM agendamentos
    WHERE status = 'aprovado'
");

$totalAprovados = $stmt->fetch()['total'];


$stmt = $pdo->query("
    SELECT COUNT(*) AS total
    FROM bloqueios
    WHERE data_bloqueio >= CURDATE()
");

$totalBloqueios = $stmt->fetch()['total'];


/* =========================
   PRÓXIMOS AGENDAMENTOS
========================= */

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


/* =========================
   BLOQUEIOS FUTUROS
========================= */

$stmt = $pdo->query("
    SELECT
        id,
        data_bloqueio,
        horario_inicio,
        horario_fim,
        dia_inteiro,
        motivo
    FROM bloqueios
    WHERE data_bloqueio >= CURDATE()
    ORDER BY data_bloqueio ASC, horario_inicio ASC
");

$bloqueios = $stmt->fetchAll();


/* =========================
   CALENDÁRIO
========================= */

$mesCalendario = isset($_GET['mes'])
    ? (int) $_GET['mes']
    : (int) date('m');

$anoCalendario = isset($_GET['ano'])
    ? (int) $_GET['ano']
    : (int) date('Y');


if ($mesCalendario < 1) {
    $mesCalendario = 12;
    $anoCalendario--;
}

if ($mesCalendario > 12) {
    $mesCalendario = 1;
    $anoCalendario++;
}


$primeiroDia = mktime(
    0,
    0,
    0,
    $mesCalendario,
    1,
    $anoCalendario
);

$ultimoDia = (int) date(
    't',
    $primeiroDia
);

$diaSemanaInicio = (int) date(
    'N',
    $primeiroDia
);


$nomeMeses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];

$nomeMes = $nomeMeses[$mesCalendario];


/* Datas do mês */

$inicioMes = sprintf(
    '%04d-%02d-01',
    $anoCalendario,
    $mesCalendario
);

$fimMes = date(
    'Y-m-t',
    $primeiroDia
);


/* Consultas do mês */

$stmt = $pdo->prepare("
    SELECT
        data_consulta,
        status
    FROM agendamentos
    WHERE data_consulta BETWEEN ? AND ?
");

$stmt->execute([
    $inicioMes,
    $fimMes
]);

$consultasCalendario = [];

foreach ($stmt->fetchAll() as $consulta) {

    $dia = (int) date(
        'j',
        strtotime($consulta['data_consulta'])
    );

    $consultasCalendario[$dia][] =
        $consulta['status'];
}


/* Bloqueios do mês */

$stmt = $pdo->prepare("
    SELECT
        data_bloqueio,
        dia_inteiro,
        horario_inicio,
        horario_fim
    FROM bloqueios
    WHERE data_bloqueio BETWEEN ? AND ?
");

$stmt->execute([
    $inicioMes,
    $fimMes
]);

$bloqueiosCalendario = [];

foreach ($stmt->fetchAll() as $bloqueio) {

    $dia = (int) date(
        'j',
        strtotime($bloqueio['data_bloqueio'])
    );

    $bloqueiosCalendario[$dia][] =
        $bloqueio;
}


/* =========================
   NAVEGAÇÃO DO CALENDÁRIO
========================= */

$mesAnterior = $mesCalendario - 1;
$anoAnterior = $anoCalendario;

if ($mesAnterior < 1) {
    $mesAnterior = 12;
    $anoAnterior--;
}


$mesProximo = $mesCalendario + 1;
$anoProximo = $anoCalendario;

if ($mesProximo > 12) {
    $mesProximo = 1;
    $anoProximo++;
}

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

            <strong>
                Dra. Caroline
            </strong>

            <small>
                Painel administrativo
            </small>

        </div>

    </a>


    <div class="painel-acoes">

        <span>
            Olá,
            <?= htmlspecialchars($_SESSION['usuario_nome']) ?>
            👋
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


    <!-- =========================
         TÍTULO
    ========================== -->

    <div class="painel-titulo">

        <div>

            <span>
                🐾 Área administrativa
            </span>

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


    <!-- =========================
         CARDS
    ========================== -->

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


    <!-- =========================
         BLOQUEAR AGENDA
    ========================== -->

    <section class="painel-secao bloqueio-secao">

        <div class="secao-topo">

            <span>
                🚫 Disponibilidade
            </span>

            <h2>
                Bloquear agenda
            </h2>

            <p>
                Bloqueie um dia inteiro ou apenas um período
                em que você não poderá atender.
            </p>

        </div>


        <div class="bloqueio-conteudo">


            <form
                action="bloquear.php"
                method="POST"
                class="form-bloqueio"
            >


                <div class="campo">

                    <label>
                        Data *
                    </label>

                    <input
                        type="date"
                        name="data"
                        min="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>


                <div class="campo">

                    <label>
                        Tipo de bloqueio *
                    </label>

                    <select
                        name="tipo"
                        id="tipoBloqueio"
                        onchange="alterarTipoBloqueio()"
                        required
                    >

                        <option value="dia">
                            🚫 Dia inteiro
                        </option>

                        <option value="horario">
                            🕐 Apenas um horário
                        </option>

                    </select>

                </div>


                <div
                    class="horarios-bloqueio"
                    id="horariosBloqueio"
                >

                    <div class="campo">

                        <label>
                            Horário inicial
                        </label>

                        <input
                            type="time"
                            name="horario_inicio"
                        >

                    </div>


                    <div class="campo">

                        <label>
                            Horário final
                        </label>

                        <input
                            type="time"
                            name="horario_fim"
                        >

                    </div>

                </div>


                <div class="campo campo-motivo">

                    <label>
                        Motivo
                    </label>

                    <input
                        type="text"
                        name="motivo"
                        placeholder="Ex.: Atendimento presencial"
                    >

                </div>


                <button
                    type="submit"
                    class="btn-principal"
                >
                    🚫 Bloquear agenda
                </button>

            </form>


            <!-- BLOQUEIOS EXISTENTES -->

            <div class="bloqueios-lista">

                <h3>
                    Bloqueios futuros
                </h3>


                <?php if (!$bloqueios): ?>

                    <div class="sem-bloqueios">

                        <span>
                            📅
                        </span>

                        <p>
                            Nenhum bloqueio cadastrado.
                        </p>

                    </div>


                <?php else: ?>


                    <?php foreach ($bloqueios as $bloqueio): ?>

                        <div class="bloqueio-item">


                            <div class="bloqueio-data">

                                <strong>

                                    <?= date(
                                        'd/m/Y',
                                        strtotime(
                                            $bloqueio['data_bloqueio']
                                        )
                                    ) ?>

                                </strong>


                                <?php if ($bloqueio['dia_inteiro']): ?>

                                    <span>
                                        🚫 Dia inteiro
                                    </span>

                                <?php else: ?>

                                    <span>

                                        🕐

                                        <?= substr(
                                            $bloqueio['horario_inicio'],
                                            0,
                                            5
                                        ) ?>

                                        até

                                        <?= substr(
                                            $bloqueio['horario_fim'],
                                            0,
                                            5
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                            </div>


                            <div class="bloqueio-motivo">

                                <?= htmlspecialchars(
                                    $bloqueio['motivo']
                                    ?: 'Sem motivo informado'
                                ) ?>

                            </div>


                            <a
                                href="desbloquear.php?id=<?= $bloqueio['id'] ?>"
                                class="btn-remover"
                                onclick="return confirm(
                                    'Deseja remover este bloqueio?'
                                )"
                            >
                                🗑️ Remover
                            </a>


                        </div>

                    <?php endforeach; ?>


                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- =========================
         AGENDAMENTOS
    ========================== -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                📅 Próximos horários
            </span>

            <h2>
                Agenda
            </h2>

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

                            $status =
                                $agendamento['status'];

                            $statusTexto = [

                                'pendente'
                                    => '🟡 Pendente',

                                'aprovado'
                                    => '🟢 Aprovado',

                                'recusado'
                                    => '🔴 Recusado',

                                'reagendamento'
                                    => '🔄 Reagendamento',

                                'cancelado'
                                    => '⚫ Cancelado'

                            ];

                            ?>


                            <tr>


                                <td>

                                    <?= date(
                                        'd/m/Y',
                                        strtotime(
                                            $agendamento[
                                                'data_consulta'
                                            ]
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <?= substr(
                                        $agendamento[
                                            'horario_consulta'
                                        ],
                                        0,
                                        5
                                    ) ?>

                                </td>


                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $agendamento[
                                                'nome_tutor'
                                            ]
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    🐾

                                    <?= htmlspecialchars(
                                        $agendamento[
                                            'nome_animal'
                                        ]
                                    ) ?>

                                </td>


                                <td>

                                    <span
                                        class="
                                            status
                                            status-<?= $status ?>
                                        "
                                    >

                                        <?= $statusTexto[
                                            $status
                                        ] ?? $status ?>

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


    <!-- =========================
         CALENDÁRIO
    ========================== -->

    <section class="painel-secao calendario-secao">


        <div class="calendario-topo">


            <div>

                <span>
                    📅 Visualização
                </span>

                <h2>
                    Calendário
                </h2>

            </div>


            <div class="calendario-navegacao">


                <a
                    href="?mes=<?= $mesAnterior ?>&ano=<?= $anoAnterior ?>"
                    class="btn-mes"
                >
                    ‹
                </a>


                <strong>
                    <?= $nomeMes ?>
                    <?= $anoCalendario ?>
                </strong>


                <a
                    href="?mes=<?= $mesProximo ?>&ano=<?= $anoProximo ?>"
                    class="btn-mes"
                >
                    ›
                </a>


            </div>

        </div>


        <div class="legenda-calendario">

            <span>
                🟢 Consulta aprovada
            </span>

            <span>
                🟡 Pendente
            </span>

            <span>
                🚫 Bloqueado
            </span>

        </div>


        <div class="calendario">


            <div class="dias-semana">

                <div>Seg</div>
                <div>Ter</div>
                <div>Qua</div>
                <div>Qui</div>
                <div>Sex</div>
                <div>Sáb</div>
                <div>Dom</div>

            </div>


            <div class="dias-calendario">


                <?php

                /*
                    Espaços antes do primeiro dia
                */

                for (
                    $i = 1;
                    $i < $diaSemanaInicio;
                    $i++
                ):

                ?>

                    <div class="dia vazio"></div>

                <?php endfor; ?>


                <?php for (
                    $dia = 1;
                    $dia <= $ultimoDia;
                    $dia++
                ): ?>


                    <?php

                    $dataAtual = sprintf(
                        '%04d-%02d-%02d',
                        $anoCalendario,
                        $mesCalendario,
                        $dia
                    );


                    $hoje =
                        $dataAtual === date('Y-m-d');


                    $consultas =
                        $consultasCalendario[$dia]
                        ?? [];


                    $bloqueiosDia =
                        $bloqueiosCalendario[$dia]
                        ?? [];


                    $diaBloqueado = false;


                    foreach (
                        $bloqueiosDia
                        as $bloqueio
                    ) {

                        if (
                            $bloqueio['dia_inteiro']
                        ) {

                            $diaBloqueado = true;

                            break;
                        }

                    }


                    $temAprovado =
                        in_array(
                            'aprovado',
                            $consultas
                        );


                    $temPendente =
                        in_array(
                            'pendente',
                            $consultas
                        );

                    ?>


                    <div
                        class="
                            dia
                            <?= $hoje
                                ? 'hoje'
                                : '' ?>

                            <?= $diaBloqueado
                                ? 'dia-bloqueado'
                                : '' ?>
                        "
                    >


                        <div class="numero-dia">

                            <?= $dia ?>


                            <?php if ($hoje): ?>

                                <small>
                                    Hoje
                                </small>

                            <?php endif; ?>

                        </div>


                        <div class="indicadores">


                            <?php if ($diaBloqueado): ?>

                                <span
                                    class="indicador bloqueado"
                                    title="Dia bloqueado"
                                >
                                    🚫
                                </span>

                            <?php endif; ?>


                            <?php if ($temAprovado): ?>

                                <span
                                    class="indicador aprovado"
                                    title="Consulta aprovada"
                                >
                                    🟢
                                </span>

                            <?php endif; ?>


                            <?php if ($temPendente): ?>

                                <span
                                    class="indicador pendente"
                                    title="Consulta pendente"
                                >
                                    🟡
                                </span>

                            <?php endif; ?>


                        </div>

                    </div>


                <?php endfor; ?>


            </div>

        </div>

    </section>


</main>


<script src="script.js"></script>

</body>

</html>