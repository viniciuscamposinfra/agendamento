<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

$petId = (int)($_GET['pet_id'] ?? 0);

if ($petId <= 0) {
    header('Location: clientes.php');
    exit;
}


// Busca o pet

$stmt = $pdo->prepare("
    SELECT
        pets.id,
        pets.nome,
        pets.especie,
        pets.raca,
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
        Novo atendimento |
        <?= htmlspecialchars($pet['nome']) ?>
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
            href="historico.php?pet_id=<?= $pet['id'] ?>"
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
                🩺 Prontuário
            </span>

            <h1>
                Novo atendimento
            </h1>

            <p>

                <?= htmlspecialchars(
                    $pet['nome']
                ) ?>

                ·

                <?= htmlspecialchars(
                    $pet['especie']
                ) ?>

                · Tutor:

                <?= htmlspecialchars(
                    $pet['cliente_nome']
                ) ?>

            </p>

        </div>

    </div>


    <section class="painel-secao">


        <form
            action="salvar_historico.php"
            method="POST"
            class="formulario-agendamento"
            style="display:block;"
        >


            <input
                type="hidden"
                name="pet_id"
                value="<?= $pet['id'] ?>"
            >


            <div class="form-grid">


                <!-- DATA -->

                <div class="campo campo-completo">

                    <label>
                        Data do atendimento *
                    </label>

                    <input
                        type="date"
                        name="data_consulta"
                        value="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>


                <!-- QUEIXA -->

                <div class="campo campo-completo">

                    <label>
                        Queixa principal
                    </label>

                    <textarea
                        name="queixa_principal"
                        rows="4"
                        placeholder="Qual o motivo da consulta?"
                    ></textarea>

                </div>


                <!-- ANAMNESE -->

                <div class="campo campo-completo">

                    <label>
                        Anamnese
                    </label>

                    <textarea
                        name="anamnese"
                        rows="5"
                        placeholder="Histórico relatado pelo tutor..."
                    ></textarea>

                </div>


                <!-- EXAME FÍSICO -->

                <div class="campo campo-completo">

                    <label>
                        Exame físico
                    </label>

                    <textarea
                        name="exame_fisico"
                        rows="5"
                        placeholder="Temperatura, frequência cardíaca, mucosas, etc."
                    ></textarea>

                </div>


                <!-- DIAGNÓSTICO -->

                <div class="campo campo-completo">

                    <label>
                        Diagnóstico
                    </label>

                    <textarea
                        name="diagnostico"
                        rows="4"
                        placeholder="Diagnóstico ou suspeita diagnóstica..."
                    ></textarea>

                </div>


                <!-- TRATAMENTO -->

                <div class="campo campo-completo">

                    <label>
                        Tratamento
                    </label>

                    <textarea
                        name="tratamento"
                        rows="5"
                        placeholder="Tratamento recomendado..."
                    ></textarea>

                </div>


                <!-- MEDICAMENTOS -->

                <div class="campo campo-completo">

                    <label>
                        💊 Medicamentos
                    </label>

                    <textarea
                        name="medicamentos"
                        rows="5"
                        placeholder="Nome do medicamento, dose e frequência..."
                    ></textarea>

                </div>


                <!-- OBSERVAÇÕES -->

                <div class="campo campo-completo">

                    <label>
                        Observações
                    </label>

                    <textarea
                        name="observacoes"
                        rows="5"
                        placeholder="Outras informações importantes..."
                    ></textarea>

                </div>


            </div>


            <div
                style="
                    margin-top:25px;
                    display:flex;
                    gap:10px;
                "
            >

                <a
                    href="historico.php?pet_id=<?= $pet['id'] ?>"
                    class="btn-sair"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="btn-principal"
                >
                    🩺 Salvar atendimento
                </button>

            </div>


        </form>


    </section>


</main>


</body>

</html>