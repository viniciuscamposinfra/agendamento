<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

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
        Novo cliente | Dra. Caroline
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
            href="clientes.php"
            class="btn-sair"
        >
            ← Clientes
        </a>

    </div>

</header>


<main class="painel">


    <div class="painel-titulo">

        <div>

            <span>
                👤 Novo cadastro
            </span>

            <h1>
                Criar cliente
            </h1>

            <p>
                Crie o acesso do tutor à área do cliente.
            </p>

        </div>

    </div>


    <section class="painel-secao">


        <form
            action="salvar_cliente.php"
            method="POST"
            class="formulario-agendamento"
            style="display:block;"
        >


            <div class="form-grid">


                <!-- NOME -->

                <div class="campo campo-completo">

                    <label>
                        Nome completo *
                    </label>

                    <input
                        type="text"
                        name="nome"
                        placeholder="Nome do tutor"
                        required
                    >

                </div>


                <!-- TELEFONE -->

                <div class="campo">

                    <label>
                        WhatsApp *
                    </label>

                    <input
                        type="tel"
                        name="telefone"
                        placeholder="(11) 99999-9999"
                        required
                    >

                </div>


                <!-- EMAIL -->

                <div class="campo">

                    <label>
                        E-mail / Login *
                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="cliente@email.com"
                        required
                    >

                </div>


                <!-- SENHA -->

                <div class="campo">

                    <label>
                        Senha *
                    </label>

                    <input
                        type="password"
                        name="senha"
                        placeholder="Crie uma senha"
                        minlength="6"
                        required
                    >

                </div>


                <!-- CONFIRMAR SENHA -->

                <div class="campo">

                    <label>
                        Confirmar senha *
                    </label>

                    <input
                        type="password"
                        name="confirmar_senha"
                        placeholder="Digite a senha novamente"
                        minlength="6"
                        required
                    >

                </div>


            </div>


            <div
                style="
                    margin-top:25px;
                    padding:15px;
                    background:#fff5f9;
                    border-radius:12px;
                "
            >

                🔐 O cliente usará o e-mail e a senha
                cadastrados para acessar a área exclusiva
                dele.

            </div>


            <div
                style="
                    margin-top:25px;
                    display:flex;
                    gap:10px;
                "
            >

                <a
                    href="clientes.php"
                    class="btn-sair"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="btn-principal"
                >
                    👤 Criar cliente
                </button>

            </div>


        </form>


    </section>


</main>


</body>

</html>