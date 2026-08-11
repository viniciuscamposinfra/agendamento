<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

$clienteId = (int)($_GET['cliente_id'] ?? 0);
$petId = (int)($_GET['id'] ?? 0);

if ($clienteId <= 0) {
    header('Location: clientes.php');
    exit;
}

// Busca cliente
$stmt = $pdo->prepare("
    SELECT id, nome
    FROM clientes
    WHERE id = ?
");

$stmt->execute([$clienteId]);

$cliente = $stmt->fetch();

if (!$cliente) {
    die('Cliente não encontrado.');
}


// Se estiver editando um pet
$pet = null;

if ($petId > 0) {

    $stmt = $pdo->prepare("
        SELECT *
        FROM pets
        WHERE id = ?
          AND cliente_id = ?
    ");

    $stmt->execute([
        $petId,
        $clienteId
    ]);

    $pet = $stmt->fetch();

    if (!$pet) {
        die('Pet não encontrado.');
    }
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
        <?= $pet ? 'Editar pet' : 'Novo pet' ?> |
        Dra. Caroline
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
            href="pets.php?cliente_id=<?= $cliente['id'] ?>"
            class="btn-sair"
        >
            ← Voltar para pets
        </a>

    </div>

</header>


<main class="painel">


    <div class="painel-titulo">

        <div>

            <span>
                🐾 Cadastro do animal
            </span>

            <h1>
                <?= $pet ? 'Editar pet' : 'Novo pet' ?>
            </h1>

            <p>
                Tutor:
                <strong>
                    <?= htmlspecialchars($cliente['nome']) ?>
                </strong>
            </p>

        </div>

    </div>


    <section class="painel-secao">


        <form
            action="salvar_pet.php"
            method="POST"
            class="formulario-agendamento"
            style="display:block;"
        >


            <input
                type="hidden"
                name="cliente_id"
                value="<?= $cliente['id'] ?>"
            >


            <input
                type="hidden"
                name="id"
                value="<?= $pet['id'] ?? '' ?>"
            >


            <div class="form-grid">


                <!-- NOME -->

                <div class="campo campo-completo">

                    <label>
                        Nome do pet *
                    </label>

                    <input
                        type="text"
                        name="nome"
                        value="<?= htmlspecialchars(
                            $pet['nome'] ?? ''
                        ) ?>"
                        placeholder="Ex.: Thor"
                        required
                    >

                </div>


                <!-- ESPÉCIE -->

                <div class="campo">

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

                        <option
                            value="Cachorro"
                            <?= ($pet['especie'] ?? '') === 'Cachorro'
                                ? 'selected'
                                : '' ?>
                        >
                            🐶 Cachorro
                        </option>

                        <option
                            value="Gato"
                            <?= ($pet['especie'] ?? '') === 'Gato'
                                ? 'selected'
                                : '' ?>
                        >
                            🐱 Gato
                        </option>

                        <option
                            value="Outro"
                            <?= ($pet['especie'] ?? '') === 'Outro'
                                ? 'selected'
                                : '' ?>
                        >
                            🐾 Outro
                        </option>

                    </select>

                </div>


                <!-- RAÇA -->

                <div class="campo">

                    <label>
                        Raça
                    </label>

                    <input
                        type="text"
                        name="raca"
                        value="<?= htmlspecialchars(
                            $pet['raca'] ?? ''
                        ) ?>"
                        placeholder="Ex.: Golden Retriever"
                    >

                </div>


                <!-- SEXO -->

                <div class="campo">

                    <label>
                        Sexo
                    </label>

                    <select name="sexo">

                        <option value="">
                            Selecione
                        </option>

                        <option
                            value="Macho"
                            <?= ($pet['sexo'] ?? '') === 'Macho'
                                ? 'selected'
                                : '' ?>
                        >
                            ♂ Macho
                        </option>

                        <option
                            value="Fêmea"
                            <?= ($pet['sexo'] ?? '') === 'Fêmea'
                                ? 'selected'
                                : '' ?>
                        >
                            ♀ Fêmea
                        </option>

                    </select>

                </div>


                <!-- NASCIMENTO -->

                <div class="campo">

                    <label>
                        Data de nascimento
                    </label>

                    <input
                        type="date"
                        name="data_nascimento"
                        value="<?= htmlspecialchars(
                            $pet['data_nascimento'] ?? ''
                        ) ?>"
                    >

                </div>


                <!-- PESO -->

                <div class="campo">

                    <label>
                        Peso atual (kg)
                    </label>

                    <input
                        type="number"
                        name="peso"
                        step="0.01"
                        min="0"
                        value="<?= htmlspecialchars(
                            $pet['peso'] ?? ''
                        ) ?>"
                        placeholder="Ex.: 12.50"
                    >

                </div>


                <!-- ALERGIAS -->

                <div class="campo campo-completo">

                    <label>
                        Alergias
                    </label>

                    <textarea
                        name="alergias"
                        rows="3"
                        placeholder="Informe alergias conhecidas..."
                    ><?= htmlspecialchars(
                        $pet['alergias'] ?? ''
                    ) ?></textarea>

                </div>


                <!-- OBSERVAÇÕES -->

                <div class="campo campo-completo">

                    <label>
                        Observações
                    </label>

                    <textarea
                        name="observacoes"
                        rows="4"
                        placeholder="Outras informações importantes..."
                    ><?= htmlspecialchars(
                        $pet['observacoes'] ?? ''
                    ) ?></textarea>

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
                    href="pets.php?cliente_id=<?= $cliente['id'] ?>"
                    class="btn-sair"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="btn-principal"
                >

                    🐾
                    <?= $pet ? 'Salvar alterações' : 'Cadastrar pet' ?>

                </button>

            </div>


        </form>


    </section>


</main>


</body>

</html>