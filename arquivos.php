<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();


// ============================
// BUSCAR EXAMES
// ============================

$stmt = $pdo->query("
    SELECT
        exames.id,
        exames.titulo,
        exames.descricao,
        exames.data_exame,
        exames.arquivo,
        exames.tipo_arquivo,
        pets.id AS pet_id,
        pets.nome AS pet_nome,
        pets.especie,
        clientes.nome AS cliente_nome
    FROM exames
    INNER JOIN pets
        ON pets.id = exames.pet_id
    INNER JOIN clientes
        ON clientes.id = pets.cliente_id
    ORDER BY exames.data_exame DESC, exames.id DESC
");

$exames = $stmt->fetchAll();

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
        Exames | Dra. Caroline
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
                📎 Documentos
            </span>

            <h1>
                Exames e documentos
            </h1>

            <p>
                Gerencie os exames disponibilizados aos clientes.
            </p>

        </div>

    </div>


    <!-- EXAMES -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                📎 Arquivos
            </span>

            <h2>
                Exames cadastrados
            </h2>

            <p>
                Exames vinculados aos pets dos clientes.
            </p>

        </div>


        <?php if (!$exames): ?>


            <div class="sem-agendamentos">

                <div>
                    📎
                </div>

                <h3>
                    Nenhum exame cadastrado
                </h3>

                <p>
                    Ainda não existem exames cadastrados no sistema.
                </p>

            </div>


        <?php else: ?>


            <?php foreach ($exames as $exame): ?>


                <div
                    style="
                        background:#fff;
                        border:1px solid #eee;
                        border-radius:14px;
                        padding:20px;
                        margin-bottom:15px;
                    "
                >


                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            gap:20px;
                        "
                    >


                        <div>

                            <h3 style="margin-top:0;">

                                📄

                                <?= htmlspecialchars(
                                    $exame['titulo']
                                ) ?>

                            </h3>


                            <p>

                                🐾 Pet:

                                <strong>

                                    <?= htmlspecialchars(
                                        $exame['pet_nome']
                                    ) ?>

                                </strong>

                            </p>


                            <p>

                                👤 Tutor:

                                <?= htmlspecialchars(
                                    $exame['cliente_nome']
                                ) ?>

                            </p>


                            <?php if (
                                $exame['data_exame']
                            ): ?>

                                <p>

                                    📅

                                    <?= date(
                                        'd/m/Y',
                                        strtotime(
                                            $exame['data_exame']
                                        )
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (
                                $exame['descricao']
                            ): ?>

                                <p>

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $exame['descricao']
                                        )
                                    ) ?>

                                </p>

                            <?php endif; ?>

                        </div>


                        <div
                            style="
                                display:flex;
                                gap:10px;
                                flex-wrap:wrap;
                            "
                        >


                            <a
                                href="uploads/exames/<?= rawurlencode(
                                    $exame['arquivo']
                                ) ?>"
                                target="_blank"
                                class="btn-acao"
                            >

                                👁 Visualizar

                            </a>


                            <a
                                href="novo_exame.php?pet_id=<?= $exame['pet_id'] ?>"
                                class="btn-acao"
                            >

                                ➕ Novo exame

                            </a>


                        </div>


                    </div>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </section>


    <!-- ATALHOS -->

    <section class="dashboard-cards">


        <a
            href="clientes.php"
            class="dashboard-card"
            style="text-decoration:none;"
        >

            <div class="dashboard-icone">
                👥
            </div>

            <div>

                <span>
                    Clientes
                </span>

                <strong>
                    Ver clientes →
                </strong>

            </div>

        </a>


        <a
            href="pets.php"
            class="dashboard-card"
            style="text-decoration:none;"
        >

            <div class="dashboard-icone">
                🐾
            </div>

            <div>

                <span>
                    Pets
                </span>

                <strong>
                    Ver pets →
                </strong>

            </div>

        </a>


        <a
            href="painel.php"
            class="dashboard-card"
            style="text-decoration:none;"
        >

            <div class="dashboard-icone">
                🏠
            </div>

            <div>

                <span>
                    Painel
                </span>

                <strong>
                    Voltar →
                </strong>

            </div>

        </a>


    </section>


</main>


</body>

</html>