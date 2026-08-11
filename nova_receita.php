<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

$petId = (int)($_GET['pet_id'] ?? 0);

if ($petId <= 0) {
    header('Location: clientes.php');
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
        Nova receita |
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
            href="receitas.php?pet_id=<?= $pet['id'] ?>"
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
                💊 Prescrição
            </span>

            <h1>
                Nova receita
            </h1>

            <p>

                Pet:

                <strong>
                    <?= htmlspecialchars(
                        $pet['nome']
                    ) ?>
                </strong>

                · Tutor:

                <?= htmlspecialchars(
                    $pet['cliente_nome']
                ) ?>

            </p>

        </div>

    </div>


    <!-- FORMULÁRIO -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                💊 Medicamentos
            </span>

            <h2>
                Informações da receita
            </h2>

            <p>
                Preencha os medicamentos e orientações
                para o paciente.
            </p>

        </div>


        <form
            action="salvar_receita.php"
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
                        Data da receita *
                    </label>

                    <input
                        type="date"
                        name="data_receita"
                        value="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>


                <!-- TÍTULO -->

                <div class="campo">

                    <label>
                        Título *
                    </label>

                    <input
                        type="text"
                        name="titulo"
                        value="Receita veterinária"
                        required
                    >

                </div>


                <!-- MEDICAMENTOS -->

                <div class="campo campo-completo">

                    <label>
                        💊 Medicamentos e instruções *
                    </label>

                    <textarea
                        name="medicamentos"
                        rows="10"
                        placeholder="Ex.:

1. Dipirona 500 mg
Administrar 1 comprimido a cada 8 horas durante 5 dias.

2. Amoxicilina 250 mg
Administrar conforme orientação veterinária."
                        required
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
                        placeholder="Orientações adicionais..."
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
                    href="receitas.php?pet_id=<?= $pet['id'] ?>"
                    class="btn-sair"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="btn-principal"
                >
                    💊 Salvar receita
                </button>

            </div>


        </form>


    </section>


</main>


</body>

</html>