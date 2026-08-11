<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

$petId = (int)($_GET['pet_id'] ?? 0);

if ($petId <= 0) {
    header('Location: clientes.php');
    exit;
}


// Busca o pet e o cliente

$stmt = $pdo->prepare("
    SELECT
        pets.id,
        pets.nome,
        pets.especie,
        pets.raca,
        clientes.id AS cliente_id,
        clientes.nome AS cliente_nome
    FROM pets
    INNER JOIN clientes
        ON clientes.id = pets.cliente_id
    WHERE pets.id = ?
");

$stmt->execute([$petId]);

$pet = $stmt->fetch();

if (!$pet) {
    die('Pet não encontrado.');
}


// Busca histórico

$stmt = $pdo->prepare("
    SELECT
        id,
        data_consulta,
        queixa_principal,
        anamnese,
        exame_fisico,
        diagnostico,
        tratamento,
        medicamentos,
        observacoes
    FROM historico_clinico
    WHERE pet_id = ?
    ORDER BY data_consulta DESC, id DESC
");

$stmt->execute([$petId]);

$historicos = $stmt->fetchAll();

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
        Histórico | <?= htmlspecialchars($pet['nome']) ?>
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body class="painel-body">


<header class="painel-topo">

    <a
        href="clientes.php"
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
            href="pets.php?cliente_id=<?= $pet['cliente_id'] ?>"
            class="btn-sair"
        >
            ← Voltar para pets
        </a>

    </div>

</header>


<main class="painel">


    <!-- CABEÇALHO -->

    <div class="painel-titulo">

        <div>

            <span>
                🐾 <?= htmlspecialchars($pet['especie']) ?>
            </span>

            <h1>
                🐶 <?= htmlspecialchars($pet['nome']) ?>
            </h1>

            <p>
                Tutor:
                <strong>
                    <?= htmlspecialchars($pet['cliente_nome']) ?>
                </strong>
            </p>

        </div>


        <a
            href="novo_historico.php?pet_id=<?= $pet['id'] ?>"
            class="btn-principal"
        >
            🩺 Novo atendimento
        </a>

    </div>


    <!-- HISTÓRICO -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                🩺 Prontuário
            </span>

            <h2>
                Histórico clínico
            </h2>

            <p>
                Registros de atendimentos deste animal.
            </p>

        </div>


        <?php if (!$historicos): ?>


            <div class="sem-agendamentos">

                <div>
                    🩺
                </div>

                <h3>
                    Nenhum atendimento registrado
                </h3>

                <p>
                    Cadastre o primeiro atendimento deste pet.
                </p>


                <br>


                <a
                    href="novo_historico.php?pet_id=<?= $pet['id'] ?>"
                    class="btn-principal"
                >
                    🩺 Registrar atendimento
                </a>

            </div>


        <?php else: ?>


            <?php foreach ($historicos as $historico): ?>


                <div
                    style="
                        background:#fff;
                        border:1px solid #eee;
                        border-radius:14px;
                        padding:25px;
                        margin-bottom:20px;
                    "
                >


                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            margin-bottom:20px;
                        "
                    >

                        <h3>
                            🩺 Consulta de
                            <?= date(
                                'd/m/Y',
                                strtotime(
                                    $historico['data_consulta']
                                )
                            ) ?>
                        </h3>

                    </div>


                    <?php if ($historico['queixa_principal']): ?>

                        <div style="margin-bottom:15px;">

                            <strong>
                                Queixa principal
                            </strong>

                            <p>
                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico['queixa_principal']
                                    )
                                ) ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['anamnese']): ?>

                        <div style="margin-bottom:15px;">

                            <strong>
                                Anamnese
                            </strong>

                            <p>
                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico['anamnese']
                                    )
                                ) ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['exame_fisico']): ?>

                        <div style="margin-bottom:15px;">

                            <strong>
                                Exame físico
                            </strong>

                            <p>
                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico['exame_fisico']
                                    )
                                ) ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['diagnostico']): ?>

                        <div style="margin-bottom:15px;">

                            <strong>
                                Diagnóstico
                            </strong>

                            <p>
                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico['diagnostico']
                                    )
                                ) ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['tratamento']): ?>

                        <div style="margin-bottom:15px;">

                            <strong>
                                Tratamento
                            </strong>

                            <p>
                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico['tratamento']
                                    )
                                ) ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['medicamentos']): ?>

                        <div style="margin-bottom:15px;">

                            <strong>
                                💊 Medicamentos
                            </strong>

                            <p>
                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico['medicamentos']
                                    )
                                ) ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['observacoes']): ?>

                        <div>

                            <strong>
                                Observações
                            </strong>

                            <p>
                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico['observacoes']
                                    )
                                ) ?>
                            </p>

                        </div>

                    <?php endif; ?>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </section>


</main>


</body>

</html>