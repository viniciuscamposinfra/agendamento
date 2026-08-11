<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

$petId = (int)($_GET['pet_id'] ?? 0);

if ($petId <= 0) {
    header('Location: clientes.php');
    exit;
}


/*
    BUSCAR PET
*/

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


/*
    BUSCAR RECEITAS
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        data_receita,
        titulo,
        medicamentos,
        observacoes,
        arquivo_pdf,
        criado_em
    FROM receitas
    WHERE pet_id = ?
    ORDER BY data_receita DESC, id DESC
");

$stmt->execute([$petId]);

$receitas = $stmt->fetchAll();


/*
    ÍCONE DO PET
*/

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

        Receitas |

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
                💊 Prescrições
            </span>

            <h1>

                <?= $iconePet ?>

                Receitas de

                <?= htmlspecialchars(
                    $pet['nome']
                ) ?>

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
            href="nova_receita.php?pet_id=<?= $pet['id'] ?>"
            class="btn-principal"
        >
            💊 Nova receita
        </a>

    </div>


    <!-- RECEITAS -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                💊 Histórico
            </span>

            <h2>
                Receitas
            </h2>

            <p>
                Receitas e prescrições deste pet.
            </p>

        </div>


        <?php if (!$receitas): ?>


            <div class="sem-agendamentos">

                <div>
                    💊
                </div>

                <h3>
                    Nenhuma receita cadastrada
                </h3>

                <p>
                    Cadastre a primeira receita deste pet.
                </p>


                <br>


                <a
                    href="nova_receita.php?pet_id=<?= $pet['id'] ?>"
                    class="btn-principal"
                >
                    💊 Nova receita
                </a>

            </div>


        <?php else: ?>


            <?php foreach ($receitas as $receita): ?>


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
                            align-items:flex-start;
                            gap:20px;
                        "
                    >

                        <div>

                            <h3 style="margin-top:0;">

                                💊

                                <?= htmlspecialchars(
                                    $receita['titulo']
                                ) ?>

                            </h3>


                            <p>

                                📅

                                <?= date(
                                    'd/m/Y',
                                    strtotime(
                                        $receita['data_receita']
                                    )
                                ) ?>

                            </p>

                        </div>


                        <div>

                            <?php if (
                                !empty(
                                    $receita['arquivo_pdf']
                                )
                            ): ?>

                                <a
                                    href="uploads/receitas/<?= rawurlencode(
                                        $receita['arquivo_pdf']
                                    ) ?>"
                                    target="_blank"
                                    class="btn-acao"
                                >
                                    📄 Ver PDF
                                </a>

                            <?php endif; ?>


                            <a
                                href="gerar_receita_pdf.php?id=<?= $receita['id'] ?>"
                                target="_blank"
                                class="btn-acao"
                                style="margin-left:5px;"
                            >
                                📄 Gerar PDF
                            </a>

                        </div>

                    </div>


                    <hr
                        style="
                            border:0;
                            border-top:1px solid #eee;
                            margin:20px 0;
                        "
                    >


                    <div>

                        <strong>
                            💊 Medicamentos e instruções
                        </strong>


                        <p>

                            <?= nl2br(
                                htmlspecialchars(
                                    $receita['medicamentos']
                                )
                            ) ?>

                        </p>

                    </div>


                    <?php if (
                        $receita['observacoes']
                    ): ?>

                        <div
                            style="
                                margin-top:20px;
                            "
                        >

                            <strong>
                                📝 Observações
                            </strong>


                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $receita['observacoes']
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