<?php

require_once 'config.php';

session_start();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {

        $erro = 'Preencha o e-mail e a senha.';

    } else {

        $stmt = $pdo->prepare("
            SELECT
                id,
                nome,
                telefone,
                email,
                senha
            FROM clientes
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $cliente = $stmt->fetch();

        if (
            $cliente &&
            password_verify($senha, $cliente['senha'])
        ) {

            session_regenerate_id(true);

            $_SESSION['cliente_id'] = $cliente['id'];
            $_SESSION['cliente_nome'] = $cliente['nome'];
            $_SESSION['cliente_email'] = $cliente['email'];

            header('Location: cliente.php');

            exit;

        } else {

            $erro = 'E-mail ou senha incorretos.';

        }

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
        Área do Cliente | Dra. Caroline
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body class="painel-body">


<header class="painel-topo">

    <a
        href="index.php"
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

</header>


<main class="painel">


    <section
        class="painel-secao"
        style="
            max-width:500px;
            margin:60px auto;
        "
    >


        <div class="secao-topo">

            <span>
                🐾 Área exclusiva
            </span>

            <h2>
                Acesse sua conta
            </h2>

            <p>
                Consulte suas consultas, receitas e documentos.
            </p>

        </div>


        <?php if ($erro): ?>

            <div
                style="
                    background:#ffe8ed;
                    color:#a33;
                    padding:15px;
                    border-radius:10px;
                    margin-bottom:20px;
                "
            >

                ⚠️
                <?= htmlspecialchars($erro) ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            class="formulario-agendamento"
            style="display:block;"
        >


            <div class="campo">

                <label>
                    E-mail
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="seu@email.com"
                    required
                    autofocus
                >

            </div>


            <div class="campo">

                <label>
                    Senha
                </label>

                <input
                    type="password"
                    name="senha"
                    placeholder="Sua senha"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-principal"
                style="width:100%;"
            >
                🔐 Entrar
            </button>


        </form>


        <p
            style="
                text-align:center;
                margin-top:20px;
            "
        >

            Ainda não possui acesso?

            <br>

            Solicite seu cadastro à Dra. Caroline.

        </p>


    </section>


</main>


</body>

</html>