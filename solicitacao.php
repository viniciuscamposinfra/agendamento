<?php

require_once 'config.php';

session_start();

if (!isset($_SESSION['cliente_id'])) {
    header('Location: cliente_login.php');
    exit;
}

$clienteId = (int) $_SESSION['cliente_id'];

$petId = (int) ($_GET['pet_id'] ?? 0);

if ($petId <= 0) {
    header('Location: cliente.php');
    exit;
}


/*
    BUSCAR PET
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        nome,
        especie,
        raca
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


/*
    ÍCONE
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
        Nova solicitação | Dra. Caroline
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body class="painel-body">


<header class="painel-topo">

    <a
        href="cliente.php"
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
                Área do cliente
            </small>

        </div>

    </a>


    <div class="painel-acoes">

        <span>

            Olá,

            <?= htmlspecialchars(
                $_SESSION['cliente_nome']
            ) ?>

            👋

        </span>


        <a
            href="cliente_logout.php"
            class="btn-sair"
        >

            🚪 Sair

        </a>

    </div>

</header>


<main class="painel">


    <!-- CABEÇALHO -->

    <div class="painel-titulo">

        <div>

            <span>
                Solicitação
            </span>

            <h1>
                Nova solicitação
            </h1>

            <p>

                <?= $iconePet ?>

                <strong>
                    <?= htmlspecialchars(
                        $pet['nome']
                    ) ?>
                </strong>

                ·

                <?= htmlspecialchars(
                    $pet['especie']
                ) ?>

            </p>

        </div>


        <a
            href="pet_cliente.php?id=<?= $pet['id'] ?>"
            class="btn-sair"
        >
            ← Voltar
        </a>

    </div>


    <!-- FORMULÁRIO -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                Envie sua solicitação
            </span>

            <h2>
                O que você precisa?
            </h2>

            <p>
                A Dra. Caroline receberá sua solicitação
                e poderá respondê-la pelo sistema.
            </p>

        </div>


        <form
            action="salvar_solicitacao.php"
            method="POST"
        >


            <input
                type="hidden"
                name="pet_id"
                value="<?= $pet['id'] ?>"
            >


            <div class="form-grid">


                <!-- TIPO -->

                <div class="campo campo-completo">

                    <label>
                        Tipo de solicitação *
                    </label>

                    <select
                        name="tipo"
                        required
                    >

                        <option value="">
                            Selecione uma opção
                        </option>

                        <option value="Nova consulta">
                            Nova consulta
                        </option>

                        <option value="Solicitação de exame">
                            Solicitação de exame
                        </option>

                        <option value="Renovação de receita">
                            Renovação de receita
                        </option>

                        <option value="Dúvida">
                            Dúvida
                        </option>

                        <option value="Outro">
                            Outro
                        </option>

                    </select>

                </div>


                <!-- DESCRIÇÃO -->

                <div class="campo campo-completo">

                    <label>
                        Descrição *
                    </label>

                    <textarea
                        name="descricao"
                        rows="7"
                        placeholder="Descreva o que você precisa..."
                        required
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
                    href="pet_cliente.php?id=<?= $pet['id'] ?>"
                    class="btn-sair"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="btn-principal"
                >
                    Enviar solicitação
                </button>

            </div>


        </form>


    </section>


</main>


</body>

</html>