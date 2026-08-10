<?php
require_once 'config.php';

$dataSelecionada = $_GET['data'] ?? date('Y-m-d');

$dataFormatada = date('d/m/Y', strtotime($dataSelecionada));

$diaSemana = (int) date('N', strtotime($dataSelecionada));

/*
    Busca os horários cadastrados para o dia da semana.
*/
$stmt = $pdo->prepare("
    SELECT horario
    FROM horarios
    WHERE dia_semana = ?
      AND ativo = 1
    ORDER BY horario
");

$stmt->execute([$diaSemana]);

$horarios = $stmt->fetchAll();

/*
    Busca horários já ocupados ou aguardando aprovação.
*/
$stmt = $pdo->prepare("
    SELECT horario_consulta
    FROM agendamentos
    WHERE data_consulta = ?
      AND status IN ('pendente', 'aprovado', 'reagendamento')
");

$stmt->execute([$dataSelecionada]);

$ocupados = $stmt->fetchAll(PDO::FETCH_COLUMN);


/*
    Busca bloqueios do dia.
*/
$stmt = $pdo->prepare("
    SELECT horario_inicio, horario_fim, dia_inteiro
    FROM bloqueios
    WHERE data_bloqueio = ?
");

$stmt->execute([$dataSelecionada]);

$bloqueios = $stmt->fetchAll();


function horarioBloqueado($horario, $bloqueios)
{
    foreach ($bloqueios as $bloqueio) {

        if ($bloqueio['dia_inteiro']) {
            return true;
        }

        if (
            $bloqueio['horario_inicio'] &&
            $bloqueio['horario_fim'] &&
            $horario >= $bloqueio['horario_inicio'] &&
            $horario < $bloqueio['horario_fim']
        ) {
            return true;
        }
    }

    return false;
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

    <title>Agendar consulta | Dra. Caroline</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header class="topo">

    <a href="index.php" class="logo">

        <span class="logo-pata">🐾</span>

        <div>

            <strong>Dra. Caroline</strong>

            <small>Atendimento Veterinário</small>

        </div>

    </a>


    <nav>

        <a href="index.php">Início</a>

        <a href="index.php#sobre">Sobre</a>

        <a href="login.php" class="btn-login">
            🔐 Área da Veterinária
        </a>

    </nav>

</header>


<main>

<section class="agenda-pagina">

    <div class="agenda-titulo">

        <span>🐾 Atendimento veterinário</span>

        <h1>
            Solicite sua consulta
        </h1>

        <p>
            Escolha uma data e um horário disponível.
            Sua solicitação ficará aguardando a confirmação
            da Dra. Caroline.
        </p>

    </div>


    <div class="agenda-container">


        <!-- DATA -->

        <div class="agenda-data">

            <h2>1. Escolha a data</h2>

            <form method="GET">

                <label for="data">
                    Data da consulta
                </label>

                <input
                    type="date"
                    id="data"
                    name="data"
                    value="<?= htmlspecialchars($dataSelecionada) ?>"
                    min="<?= date('Y-m-d') ?>"
                    onchange="this.form.submit()"
                >

            </form>

            <div class="data-escolhida">

                📅 <?= $dataFormatada ?>

            </div>

        </div>


        <!-- HORÁRIOS -->

        <div class="agenda-horarios">

            <h2>2. Escolha o horário</h2>

            <div class="horarios-grid">

                <?php if (!$horarios): ?>

                    <div class="sem-horarios">

                        <span>😿</span>

                        <p>
                            Não há horários configurados
                            para este dia.
                        </p>

                    </div>

                <?php else: ?>

                    <?php foreach ($horarios as $item): ?>

                        <?php

                        $horario = substr(
                            $item['horario'],
                            0,
                            5
                        );

                        $ocupado = in_array(
                            $item['horario'],
                            $ocupados
                        );

                        $bloqueado = horarioBloqueado(
                            $item['horario'],
                            $bloqueios
                        );

                        $indisponivel =
                            $ocupado ||
                            $bloqueado;

                        ?>

                        <button
                            type="button"
                            class="horario <?= $indisponivel ? 'indisponivel' : '' ?>"
                            <?= $indisponivel ? 'disabled' : '' ?>
                            onclick="selecionarHorario(
                                '<?= $horario ?>'
                            )"
                        >

                            <?= $horario ?>

                            <?php if ($ocupado): ?>

                                <small>Ocupado</small>

                            <?php elseif ($bloqueado): ?>

                                <small>Indisponível</small>

                            <?php endif; ?>

                        </button>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>


        <!-- FORMULÁRIO -->

        <div
            class="formulario-agendamento"
            id="formulario"
        >

            <h2>3. Seus dados</h2>

            <p class="horario-selecionado">

                Horário escolhido:

                <strong id="horarioTexto">
                    Nenhum
                </strong>

            </p>


            <form
                action="salvar.php"
                method="POST"
                onsubmit="return validarAgendamento()"
            >

                <input
                    type="hidden"
                    name="data"
                    value="<?= htmlspecialchars($dataSelecionada) ?>"
                >

                <input
                    type="hidden"
                    name="horario"
                    id="horario"
                >


                <div class="form-grid">

                    <div class="campo">

                        <label>
                            Seu nome *
                        </label>

                        <input
                            type="text"
                            name="nome_tutor"
                            placeholder="Digite seu nome"
                            required
                        >

                    </div>


                    <div class="campo">

                        <label>
                            WhatsApp *
                        </label>

                        <input
                            type="tel"
                            name="telefone"
                            placeholder="(11) 99999-9999"
                            required
                        >

                    </div>


                    <div class="campo">

                        <label>
                            E-mail
                        </label>

                        <input
                            type="email"
                            name="email"
                            placeholder="seu@email.com"
                        >

                    </div>


                    <div class="campo">

                        <label>
                            Nome do pet *
                        </label>

                        <input
                            type="text"
                            name="nome_animal"
                            placeholder="Nome do seu pet"
                            required
                        >

                    </div>


                    <div class="campo campo-completo">

                        <label>
                            Espécie *
                        </label>

                        <select
                            name="especie"
                            required
                        >

                            <option value="">
                                Selecione
                            </option>

                            <option value="Cachorro">
                                🐶 Cachorro
                            </option>

                            <option value="Gato">
                                🐱 Gato
                            </option>

                            <option value="Outro">
                                🐾 Outro
                            </option>

                        </select>

                    </div>


                    <div class="campo campo-completo">

                        <label>
                            Motivo da consulta *
                        </label>

                        <textarea
                            name="motivo"
                            rows="5"
                            placeholder="Conte brevemente o motivo da consulta..."
                            required
                        ></textarea>

                    </div>

                </div>


                <button
                    type="submit"
                    class="btn-principal btn-enviar"
                >

                    📅 Enviar solicitação

                </button>


                <p class="aviso-formulario">

                    🔒 Seus dados serão utilizados apenas
                    para o atendimento.

                </p>

            </form>

        </div>

    </div>

</section>

</main>


<footer>

    <div class="footer-logo">
        🐾 <strong>Dra. Caroline</strong>
    </div>

    <p>
        Atendimento veterinário com carinho e dedicação.
    </p>

    <span>
        © <?= date('Y') ?> Dra. Caroline
    </span>

</footer>


<script src="script.js"></script>

</body>

</html>