<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcoes.php';

exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: painel.php');
    exit;
}


/*
    BUSCAR AGENDAMENTO
*/

$stmt = $pdo->prepare("
    SELECT
        id,
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
        data_pagamento,
        nova_data,
        novo_horario,
        observacao,
        criado_em
    FROM agendamentos
    WHERE id = ?
");

$stmt->execute([$id]);

$agendamento = $stmt->fetch();

if (!$agendamento) {
    die('Agendamento não encontrado.');
}


/*
    STATUS
*/

$statusTexto = [

    'pendente' =>
        '🟡 Pendente',

    'aprovado' =>
        '🟢 Aprovado',

    'recusado' =>
        '🔴 Recusado',

    'reagendamento' =>
        '🔄 Reagendamento',

    'cancelado' =>
        '⚫ Cancelado'

];

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
        Agendamento | Dra. Caroline
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body class="painel-body">


<header class="painel-topo">

    <a
        href="painel.php"
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
                Painel administrativo
            </small>

        </div>

    </a>


    <div class="painel-acoes">

        <a
            href="painel.php"
            class="btn-sair"
        >
            ← Voltar
        </a>

    </div>

</header>


<main class="painel">


    <div class="painel-titulo">

        <div>

            <span>
                📅 Agendamento
            </span>

            <h1>
                Detalhes da consulta
            </h1>

            <p>
                Confira os dados antes de aprovar o atendimento.
            </p>

        </div>

    </div>


    <!-- DADOS DA CONSULTA -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                🐾 Paciente
            </span>

            <h2>
                <?= htmlspecialchars(
                    $agendamento['nome_animal']
                ) ?>
            </h2>

        </div>


        <div class="form-grid">


            <div class="campo">

                <label>
                    Tutor
                </label>

                <div class="campo-visual">

                    <?= htmlspecialchars(
                        $agendamento['nome_tutor']
                    ) ?>

                </div>

            </div>


            <div class="campo">

                <label>
                    Telefone
                </label>

                <div class="campo-visual">

                    <?= htmlspecialchars(
                        $agendamento['telefone']
                    ) ?>

                </div>

            </div>


            <div class="campo">

                <label>
                    Pet
                </label>

                <div class="campo-visual">

                    🐾

                    <?= htmlspecialchars(
                        $agendamento['nome_animal']
                    ) ?>

                </div>

            </div>


            <div class="campo">

                <label>
                    Espécie
                </label>

                <div class="campo-visual">

                    <?= htmlspecialchars(
                        $agendamento['especie']
                    ) ?>

                </div>

            </div>


            <div class="campo">

                <label>
                    Data
                </label>

                <div class="campo-visual">

                    <?= date(
                        'd/m/Y',
                        strtotime(
                            $agendamento['data_consulta']
                        )
                    ) ?>

                </div>

            </div>


            <div class="campo">

                <label>
                    Horário
                </label>

                <div class="campo-visual">

                    <?= substr(
                        $agendamento['horario_consulta'],
                        0,
                        5
                    ) ?>

                </div>

            </div>


            <div class="campo">

                <label>
                    Local
                </label>

                <div class="campo-visual">

                    <?= htmlspecialchars(
                        $agendamento['local']
                    ) ?>

                </div>

            </div>


            <div class="campo">

                <label>
                    Status
                </label>

                <div class="campo-visual">

                    <?= $statusTexto[
                        $agendamento['status']
                    ] ?? $agendamento['status'] ?>

                </div>

            </div>


            <div class="campo campo-completo">

                <label>
                    Motivo da consulta
                </label>

                <div class="campo-visual">

                    <?= nl2br(
                        htmlspecialchars(
                            $agendamento['motivo']
                        )
                    ) ?>

                </div>

            </div>


        </div>


        <!-- AÇÕES -->

        <div
            style="
                margin-top:30px;
                display:flex;
                gap:10px;
                flex-wrap:wrap;
            "
        >


            <?php if (
                $agendamento['status'] === 'pendente'
            ): ?>

                <form
                    action="aprovar_agendamento.php"
                    method="POST"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $agendamento['id'] ?>"
                    >

                    <button
                        type="submit"
                        class="btn-principal"
                        onclick="return confirm(
                            'Deseja aprovar esta consulta?'
                        )"
                    >
                        🟢 Aprovar consulta
                    </button>

                </form>


                <form
                    action="recusar_agendamento.php"
                    method="POST"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $agendamento['id'] ?>"
                    >

                    <button
                        type="submit"
                        class="btn-sair"
                        onclick="return confirm(
                            'Deseja recusar esta consulta?'
                        )"
                    >
                        🔴 Recusar
                    </button>

                </form>

            <?php endif; ?>


            <?php if (
                $agendamento['status'] === 'aprovado'
            ): ?>

                <a
                    href="novo_atendimento.php?pet_id=<?= $agendamento['id'] ?>"
                    class="btn-principal"
                >
                    🩺 Registrar atendimento
                </a>

            <?php endif; ?>


        </div>

    </section>


    <!-- PAGAMENTO -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                💰 Financeiro
            </span>

            <h2>
                Pagamento da consulta
            </h2>

        </div>


        <form
            action="salvar_pagamento.php"
            method="POST"
        >

            <input
                type="hidden"
                name="id"
                value="<?= $agendamento['id'] ?>"
            >


            <div class="form-grid">


                <div class="campo">

                    <label>
                        Valor da consulta
                    </label>

                    <input
                        type="number"
                        name="valor_consulta"
                        step="0.01"
                        min="0"
                        value="<?= $agendamento['valor_consulta'] !== null
                            ? htmlspecialchars(
                                $agendamento['valor_consulta']
                            )
                            : '' ?>"
                        placeholder="Ex.: 150,00"
                    >

                </div>


                <div class="campo">

                    <label>
                        Status do pagamento
                    </label>

                    <div class="campo-visual">

                        <?php if (
                            $agendamento['status_pagamento']
                            === 'pago'
                        ): ?>

                            🟢 Pago

                        <?php else: ?>

                            🟡 Pendente

                        <?php endif; ?>

                    </div>

                </div>


            </div>


            <div style="margin-top:20px;">

                <button
                    type="submit"
                    class="btn-principal"
                >
                    💾 Salvar valor
                </button>


                <?php if (
                    $agendamento['status_pagamento']
                    === 'pendente'
                ): ?>

                    <a
                        href="marcar_pagamento.php?id=<?= $agendamento['id'] ?>"
                        class="btn-acao"
                        onclick="return confirm(
                            'Deseja marcar esta consulta como paga?'
                        )"
                    >
                        🟢 Marcar como pago
                    </a>

                <?php endif; ?>

            </div>

        </form>

    </section>


</main>


</body>

</html>