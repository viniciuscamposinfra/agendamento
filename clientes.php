<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();


/*
    BUSCAR CLIENTES
*/

$stmt = $pdo->query("
    SELECT
        id,
        nome,
        telefone,
        email,
        criado_em
    FROM clientes
    ORDER BY nome ASC
");

$clientes = $stmt->fetchAll();

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
        Clientes | Dra. Caroline
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

        <span>
            Olá,
            <?= htmlspecialchars(
                $_SESSION['usuario_nome']
            ) ?>
            👋
        </span>


        <a
            href="logout.php"
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
                👤 Administração
            </span>

            <h1>
                Clientes
            </h1>

            <p>
                Gerencie os acessos dos tutores.
            </p>

        </div>


        <a
            href="cliente_cadastro.php"
            class="btn-principal"
        >
            ➕ Novo cliente
        </a>

    </div>


    <!-- LISTA -->

    <section class="painel-secao">


        <?php if (!$clientes): ?>


            <div class="sem-agendamentos">

                <div>
                    👤
                </div>


                <h3>
                    Nenhum cliente cadastrado
                </h3>


                <p>
                    Clique em "Novo cliente" para
                    criar o primeiro acesso.
                </p>


                <br>


                <a
                    href="cliente_cadastro.php"
                    class="btn-principal"
                >
                    ➕ Cadastrar primeiro cliente
                </a>

            </div>


        <?php else: ?>


            <div class="tabela-container">


                <table>


                    <thead>

                        <tr>

                            <th>
                                Nome
                            </th>

                            <th>
                                Telefone
                            </th>

                            <th>
                                E-mail
                            </th>

                            <th>
                                Cadastro
                            </th>

                            <th>
                                Ação
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $clientes as $cliente
                        ): ?>


                            <tr>


                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $cliente['nome']
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $cliente['telefone']
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $cliente['email']
                                    ) ?>

                                </td>


                                <td>

                                    <?= date(
                                        'd/m/Y',
                                        strtotime(
                                            $cliente['criado_em']
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <a
                                        href="cliente.php?id=<?= $cliente['id'] ?>"
                                        class="btn-acao"
                                    >
                                        Ver
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