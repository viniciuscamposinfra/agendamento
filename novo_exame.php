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
        Novo exame |
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
            href="exames.php?pet_id=<?= $pet['id'] ?>"
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
                📎 Documentos do pet
            </span>

            <h1>
                Adicionar exame
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


    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                📄 Novo documento
            </span>

            <h2>
                Informações do exame
            </h2>

            <p>
                Adicione um exame, laudo ou imagem
                para este pet.
            </p>

        </div>


        <form
            action="salvar_exame.php"
            method="POST"
            enctype="multipart/form-data"
        >


            <input
                type="hidden"
                name="pet_id"
                value="<?= $pet['id'] ?>"
            >


            <div class="form-grid">


                <!-- TÍTULO -->

                <div class="campo campo-completo">

                    <label>
                        Nome do exame *
                    </label>

                    <input
                        type="text"
                        name="titulo"
                        placeholder="Ex.: Hemograma, Ultrassom, Raio-X..."
                        required
                    >

                </div>


                <!-- DATA -->

                <div class="campo">

                    <label>
                        Data do exame
                    </label>

                    <input
                        type="date"
                        name="data_exame"
                        value="<?= date('Y-m-d') ?>"
                    >

                </div>


                <!-- ARQUIVO -->

                <div class="campo">

                    <label>
                        Arquivo *
                    </label>

                    <input
                        type="file"
                        name="arquivo"
                        accept=".pdf,.jpg,.jpeg,.png"
                        required
                    >

                    <small
                        style="
                            display:block;
                            margin-top:6px;
                            color:#777;
                        "
                    >
                        PDF, JPG, JPEG ou PNG.
                    </small>

                </div>


                <!-- DESCRIÇÃO -->

                <div class="campo campo-completo">

                    <label>
                        Descrição / observações
                    </label>

                    <textarea
                        name="descricao"
                        rows="5"
                        placeholder="Ex.: Exame realizado durante consulta..."
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
                    href="exames.php?pet_id=<?= $pet['id'] ?>"
                    class="btn-sair"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="btn-principal"
                >
                    📎 Salvar exame
                </button>

            </div>


        </form>


    </section>


</main>


</body>

</html>