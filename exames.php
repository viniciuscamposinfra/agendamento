<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

$petId = (int)($_GET['pet_id'] ?? 0);

if ($petId <= 0) {
    header('Location: clientes.php');
    exit;
}


// Busca pet

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


// Busca exames

$stmt = $pdo->prepare("
    SELECT
        id,
        titulo,
        descricao,
        data_exame,
        arquivo,
        tipo_arquivo,
        criado_em
    FROM exames
    WHERE pet_id = ?
    ORDER BY data_exame DESC, id DESC
");

$stmt->execute([$petId]);

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
        Exames |
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
            href="pets.php?cliente_id=<?= $pet['cliente_id'] ?>"
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
                📎 Documentos do pet
            </span>

            <h1>
                <?= htmlspecialchars($pet['nome']) ?>
            </h1>

            <p>

                Tutor:

                <strong>
                    <?= htmlspecialchars(
                        $pet['cliente_nome']
                    ) ?>
                </strong>

            </p>

        </div>


        <a
            href="novo_exame.php?pet_id=<?= $pet['id'] ?>"
            class="btn-principal"
        >
            📎 Novo exame
        </a>

    </div>


    <!-- LISTA -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                📎 Arquivos
            </span>

            <h2>
                Exames e documentos
            </h2>

            <p>
                Exames, laudos, imagens e outros documentos
                deste pet.
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
                    Envie o primeiro exame ou documento.
                </p>


                <br>


                <a
                    href="novo_exame.php?pet_id=<?= $pet['id'] ?>"
                    class="btn-principal"
                >
                    📎 Adicionar exame
                </a>

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


                        <?php if ($exame['data_exame']): ?>

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


                        <?php if ($exame['descricao']): ?>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $exame['descricao']
                                    )
                                ) ?>

                            </p>

                        <?php endif; ?>


                    </div>


                    <a
                        href="uploads/exames/<?= rawurlencode(
                            $exame['arquivo']
                        ) ?>"
                        target="_blank"
                        class="btn-acao"
                    >
                        👁 Abrir
                    </a>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </section>


</main>


</body>

</html>