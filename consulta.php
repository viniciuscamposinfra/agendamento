<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();


// ============================
// BUSCAR PETS
// ============================

$stmt = $pdo->query("
    SELECT
        pets.id,
        pets.nome,
        pets.especie,
        pets.raca,
        clientes.nome AS cliente_nome
    FROM pets
    INNER JOIN clientes
        ON clientes.id = pets.cliente_id
    ORDER BY pets.nome ASC
");

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
        Registrar atendimento | Dra. Caroline
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

            ← Voltar ao painel

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
                Registrar atendimento
            </h1>

            <p>
                Selecione o pet para registrar um novo atendimento.
            </p>

        </div>

    </div>


    <!-- PETS -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                🐾 Paciente
            </span>

            <h2>
                Escolha o pet
            </h2>

            <p>
                Selecione o animal que será atendido.
            </p>

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
                    Cadastre um pet antes de registrar um atendimento.
                </p>

                <br>

                <a
                    href="pets.php"
                    class="btn-principal"
                >

                    🐾 Cadastrar pet

                </a>

            </div>


        <?php else: ?>


            <div class="dashboard-cards">


                <?php foreach ($pets as $pet): ?>


                    <div
                        class="dashboard-card"
                        style="display:block;"
                    >


                        <div
                            style="
                                font-size:40px;
                                margin-bottom:10px;
                            "
                        >

                            <?php

                            if ($pet['especie'] === 'Gato') {

                                echo '🐱';

                            } elseif (
                                $pet['especie'] === 'Cachorro'
                            ) {

                                echo '🐶';

                            } else {

                                echo '🐾';

                            }

                            ?>

                        </div>


                        <h3
                            style="
                                margin:0 0 8px;
                            "
                        >

                            <?= htmlspecialchars(
                                $pet['nome']
                            ) ?>

                        </h3>


                        <p>

                            <?= htmlspecialchars(
                                $pet['especie']
                            ) ?>

                            <?php if ($pet['raca']): ?>

                                ·

                                <?= htmlspecialchars(
                                    $pet['raca']
                                ) ?>

                            <?php endif; ?>

                        </p>


                        <small>

                            👤 Tutor:

                            <?= htmlspecialchars(
                                $pet['cliente_nome']
                            ) ?>

                        </small>


                        <div
                            style="
                                margin-top:15px;
                            "
                        >

                            <a
                                href="novo_atendimento.php?pet_id=<?= $pet['id'] ?>"
                                class="btn-acao"
                                style="
                                    display:inline-block;
                                    text-decoration:none;
                                "
                            >

                                🩺 Registrar atendimento

                            </a>

                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </section>


</main>


</body>

</html>