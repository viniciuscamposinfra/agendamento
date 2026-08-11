<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die('Solicitação inválida.');
}


/*
    BUSCAR SOLICITAÇÃO
*/

$stmt = $pdo->prepare("
    SELECT
        s.id,
        s.tipo,
        s.descricao,
        s.resposta,
        s.status,
        s.criado_em,

        c.nome AS cliente_nome,

        p.nome AS pet_nome,
        p.especie,
        p.raca

    FROM solicitacoes s

    INNER JOIN clientes c
        ON c.id = s.cliente_id

    INNER JOIN pets p
        ON p.id = s.pet_id

    WHERE s.id = ?
");

$stmt->execute([$id]);

$solicitacao = $stmt->fetch();

if (!$solicitacao) {
    die('Solicitação não encontrada.');
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
        Solicitação | Dra. Caroline
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body class="painel-body">


<header class="painel-topo">

    <a
        href="solicitacoes.php"
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
            href="solicitacoes.php"
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
                Atendimento
            </span>

            <h1>
                Solicitação
            </h1>

            <p>

                <?= htmlspecialchars(
                    $solicitacao['tipo']
                ) ?>

            </p>

        </div>

    </div>


    <!-- DADOS -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                Dados
            </span>

            <h2>
                Cliente e pet
            </h2>

        </div>


        <div class="dashboard-cards">


            <div class="dashboard-card">

                <div class="dashboard-icone">
                    👤
                </div>

                <div>

                    <span>
                        Cliente
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $solicitacao['cliente_nome']
                        ) ?>

                    </strong>

                </div>

            </div>


            <div class="dashboard-card">

                <div class="dashboard-icone">
                    🐾
                </div>

                <div>

                    <span>
                        Pet
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $solicitacao['pet_nome']
                        ) ?>

                    </strong>

                </div>

            </div>


            <div class="dashboard-card">

                <div class="dashboard-icone">
                    📋
                </div>

                <div>

                    <span>
                        Tipo
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $solicitacao['tipo']
                        ) ?>

                    </strong>

                </div>

            </div>


            <div class="dashboard-card">

                <div class="dashboard-icone">
                    📅
                </div>

                <div>

                    <span>
                        Enviada em
                    </span>

                    <strong>

                        <?= date(
                            'd/m/Y H:i',
                            strtotime(
                                $solicitacao['criado_em']
                            )
                        ) ?>

                    </strong>

                </div>

            </div>


        </div>

    </section>


    <!-- SOLICITAÇÃO -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                Mensagem do cliente
            </span>

            <h2>
                Solicitação
            </h2>

        </div>


        <div
            style="
                background:#fff;
                border:1px solid #eee;
                border-radius:14px;
                padding:25px;
            "
        >

            <?= nl2br(
                htmlspecialchars(
                    $solicitacao['descricao']
                )
            ) ?>

        </div>

    </section>


    <!-- RESPOSTA -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                Atendimento
            </span>

            <h2>
                Responder solicitação
            </h2>

        </div>


        <form
            action="responder_solicitacao.php"
            method="POST"
        >


            <input
                type="hidden"
                name="id"
                value="<?= $solicitacao['id'] ?>"
            >


            <div class="campo">

                <label>
                    Resposta para o cliente
                </label>

                <textarea
                    name="resposta"
                    rows="8"
                    placeholder="Digite aqui a resposta para o cliente..."
                ><?= htmlspecialchars(
                    $solicitacao['resposta'] ?? ''
                ) ?></textarea>

            </div>


            <div
                class="campo"
                style="margin-top:20px;"
            >

                <label>
                    Status
                </label>

                <select
                    name="status"
                    required
                >

                    <option
                        value="pendente"
                        <?= $solicitacao['status'] === 'pendente'
                            ? 'selected'
                            : '' ?>
                    >
                        Pendente
                    </option>


                    <option
                        value="em_andamento"
                        <?= $solicitacao['status'] === 'em_andamento'
                            ? 'selected'
                            : '' ?>
                    >
                        Em andamento
                    </option>


                    <option
                        value="concluida"
                        <?= $solicitacao['status'] === 'concluida'
                            ? 'selected'
                            : '' ?>
                    >
                        Concluída
                    </option>


                    <option
                        value="cancelada"
                        <?= $solicitacao['status'] === 'cancelada'
                            ? 'selected'
                            : '' ?>
                    >
                        Cancelada
                    </option>

                </select>

            </div>


            <div
                style="
                    margin-top:25px;
                "
            >

                <button
                    type="submit"
                    class="btn-principal"
                >
                    Salvar resposta
                </button>

            </div>


        </form>

    </section>


</main>


</body>

</html>