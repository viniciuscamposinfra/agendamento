<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

$petId = (int) ($_GET['pet_id'] ?? 0);

if ($petId <= 0) {
    header('Location: consulta.php');
    exit;
}


// ============================
// BUSCAR PET
// ============================

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


// ============================
// ÍCONE
// ============================

if ($pet['especie'] === 'Gato') {

    $iconePet = '🐱';

} elseif ($pet['especie'] === 'Cachorro') {

    $iconePet = '🐶';

} else {

    $iconePet = '🐾';

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
        Atendimento | <?= htmlspecialchars($pet['nome']) ?>
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
            href="consulta.php"
            class="btn-sair"
        >
            ← Voltar
        </a>

    </div>

</header>


<main class="painel">


    <!-- CABEÇALHO -->

    <div class="painel-titulo">

        <div>

            <span>
                🩺 Prontuário
            </span>

            <h1>
                Novo atendimento
            </h1>

            <p>

                <?= $iconePet ?>

                <strong>
                    <?= htmlspecialchars($pet['nome']) ?>
                </strong>

                ·

                <?= htmlspecialchars($pet['especie']) ?>

                · Tutor:

                <?= htmlspecialchars($pet['cliente_nome']) ?>

            </p>

        </div>

    </div>


    <!-- FORMULÁRIO -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                🩺 Atendimento
            </span>

            <h2>
                Registro clínico
            </h2>

            <p>
                Registre as informações do atendimento realizado.
            </p>

        </div>


        <form
            action="salvar_consulta.php"
            method="POST"
        >


            <input
                type="hidden"
                name="pet_id"
                value="<?= $pet['id'] ?>"
            >


            <div class="form-grid">


                <!-- DATA -->

                <div class="campo">

                    <label>
                        Data da consulta *
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
                        placeholder="Descreva o motivo principal da consulta..."
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
                        placeholder="Histórico, sintomas, evolução do quadro..."
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
                        placeholder="Descreva os achados do exame físico..."
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
                        placeholder="Informe o diagnóstico..."
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
                        placeholder="Informe o tratamento indicado..."
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
                        placeholder="Medicamentos, doses e horários..."
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
                    href="consulta.php"
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