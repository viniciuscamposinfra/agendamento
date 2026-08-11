<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

$clienteId = (int)($_GET['cliente_id'] ?? 0);

if ($clienteId <= 0) {
    header('Location: clientes.php');
    exit;
}


// ============================
// BUSCA CLIENTE
// ============================

$stmt = $pdo->prepare("
    SELECT
        id,
        nome,
        telefone,
        email
    FROM clientes
    WHERE id = ?
");

$stmt->execute([$clienteId]);

$cliente = $stmt->fetch();

if (!$cliente) {
    die('Cliente não encontrado.');
}


// ============================
// BUSCA PETS
// ============================

$stmt = $pdo->prepare("
    SELECT
        id,
        nome,
        especie,
        raca,
        sexo,
        data_nascimento,
        peso
    FROM pets
    WHERE cliente_id = ?
    ORDER BY nome
");

$stmt->execute([$clienteId]);

$pets = $stmt->fetchAll();

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
        Pets | <?= htmlspecialchars($cliente['nome']) ?>
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
            href="clientes.php"
            class="btn-sair"
        >
            ← Clientes
        </a>

    </div>

</header>


<main class="painel">


    <!-- CABEÇALHO -->

    <div class="painel-titulo">

        <div>

            <span>
                👤 Cliente
            </span>

            <h1>
                <?= htmlspecialchars($cliente['nome']) ?>
            </h1>

            <p>

                <?= htmlspecialchars(
                    $cliente['telefone']
                ) ?>

                <?php if ($cliente['email']): ?>

                    ·

                    <?= htmlspecialchars(
                        $cliente['email']
                    ) ?>

                <?php endif; ?>

            </p>

        </div>


        <a
            href="pet.php?cliente_id=<?= $cliente['id'] ?>"
            class="btn-principal"
        >
            🐾 Novo pet
        </a>

    </div>


    <!-- PETS -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                🐶 Animais
            </span>

            <h2>
                Pets do cliente
            </h2>

        </div>


        <?php if (!$pets): ?>


            <div class="sem-agendamentos">

                <div>
                    🐾
                </div>

                <h3>
                    Nenhum pet cadastrado
                </h3>

                <p>
                    Cadastre o primeiro animal deste cliente.
                </p>

                <br>

                <a
                    href="pet.php?cliente_id=<?= $cliente['id'] ?>"
                    class="btn-principal"
                >
                    🐾 Cadastrar pet
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
                                Espécie
                            </th>

                            <th>
                                Raça
                            </th>

                            <th>
                                Sexo
                            </th>

                            <th>
                                Peso
                            </th>

                            <th>
                                Ações
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach ($pets as $pet): ?>


                            <tr>


                                <td>

                                    <?php

                                    if ($pet['especie'] === 'Gato') {
                                        echo '🐱';
                                    } elseif ($pet['especie'] === 'Cachorro') {
                                        echo '🐶';
                                    } else {
                                        echo '🐾';
                                    }

                                    ?>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $pet['nome']
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $pet['especie']
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $pet['raca'] ?: '-'
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $pet['sexo'] ?: '-'
                                    ) ?>

                                </td>


                                <td>

                                    <?= $pet['peso']
                                        ? htmlspecialchars(
                                            $pet['peso']
                                        ) . ' kg'
                                        : '-' ?>

                                </td>


                                <td>

                                    <a
                                        href="pet.php?id=<?= $pet['id'] ?>&cliente_id=<?= $cliente['id'] ?>"
                                        class="btn-acao"
                                    >
                                        ✏️ Editar
                                    </a>


                                    <a
                                        href="historico.php?pet_id=<?= $pet['id'] ?>"
                                        class="btn-acao"
                                        style="
                                            margin-left:5px;
                                        "
                                    >
                                        🩺 Histórico
                                    </a>


                                    <a
                                        href="exames.php?pet_id=<?= $pet['id'] ?>"
                                        class="btn-acao"
                                        style="
                                            margin-left:5px;
                                        "
                                    >
                                        📎 Exames
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