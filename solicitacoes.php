<?php

require_once 'config.php';
require_once 'funcoes.php';

exigirLogin();


/*
    BUSCAR SOLICITAÇÕES
*/

$stmt = $pdo->query("
    SELECT
        solicitacoes.id,
        solicitacoes.tipo,
        solicitacoes.descricao,
        solicitacoes.resposta,
        solicitacoes.status,
        solicitacoes.criado_em,

        clientes.nome AS cliente_nome,

        pets.nome AS pet_nome,
        pets.especie

    FROM solicitacoes

    INNER JOIN clientes
        ON clientes.id = solicitacoes.cliente_id

    INNER JOIN pets
        ON pets.id = solicitacoes.pet_id

    ORDER BY
        CASE
            WHEN solicitacoes.status = 'pendente'
            THEN 1
            WHEN solicitacoes.status = 'em_andamento'
            THEN 2
            ELSE 3
        END,

        solicitacoes.criado_em DESC
");

$solicitacoes = $stmt->fetchAll();

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
        Solicitações | Dra. Caroline
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
                Solicitações
            </h1>

            <p>
                Solicitações enviadas pelos clientes.
            </p>

        </div>

    </div>


    <!-- LISTA -->

    <section class="painel-secao">


        <div class="secao-topo">

            <span>
                Central de atendimento
            </span>

            <h2>
                Solicitações recebidas
            </h2>

        </div>


        <?php if (!$solicitacoes): ?>


            <div class="sem-agendamentos">

                <div>
                    📋
                </div>

                <h3>
                    Nenhuma solicitação
                </h3>

                <p>
                    Ainda não existem solicitações dos clientes.
                </p>

            </div>


        <?php else: ?>


            <?php foreach ($solicitacoes as $solicitacao): ?>


                <?php

                if (
                    $solicitacao['status']
                    === 'pendente'
                ) {

                    $statusTexto = 'Pendente';

                    $statusIcone = '🟡';

                } elseif (
                    $solicitacao['status']
                    === 'em_andamento'
                ) {

                    $statusTexto = 'Em andamento';

                    $statusIcone = '🔵';

                } elseif (
                    $solicitacao['status']
                    === 'concluida'
                ) {

                    $statusTexto = 'Concluída';

                    $statusIcone = '🟢';

                } else {

                    $statusTexto = 'Cancelada';

                    $statusIcone = '🔴';

                }

                ?>


                <div
                    style="
                        background:#fff;
                        border:1px solid #eee;
                        border-radius:14px;
                        padding:25px;
                        margin-bottom:20px;
                    "
                >


                    <!-- CABEÇALHO -->

                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            align-items:flex-start;
                            gap:20px;
                        "
                    >

                        <div>

                            <h3
                                style="
                                    margin-top:0;
                                    margin-bottom:8px;
                                "
                            >

                                <?= htmlspecialchars(
                                    $solicitacao['tipo']
                                ) ?>

                            </h3>


                            <p
                                style="
                                    margin:0;
                                    color:#666;
                                "
                            >

                                Cliente:

                                <strong>
                                    <?= htmlspecialchars(
                                        $solicitacao[
                                            'cliente_nome'
                                        ]
                                    ) ?>
                                </strong>

                                <br>

                                Pet:

                                <strong>
                                    <?= htmlspecialchars(
                                        $solicitacao[
                                            'pet_nome'
                                        ]
                                    ) ?>
                                </strong>

                            </p>

                        </div>


                        <div>

                            <?= $statusIcone ?>

                            <?= $statusTexto ?>

                        </div>

                    </div>


                    <hr
                        style="
                            border:0;
                            border-top:1px solid #eee;
                            margin:20px 0;
                        "
                    >


                    <!-- DESCRIÇÃO -->

                    <div>

                        <strong>
                            Solicitação
                        </strong>

                        <p>

                            <?= nl2br(
                                htmlspecialchars(
                                    $solicitacao[
                                        'descricao'
                                    ]
                                )
                            ) ?>

                        </p>

                    </div>


                    <?php if (
                        !empty(
                            $solicitacao['resposta']
                        )
                    ): ?>

                        <div
                            style="
                                margin-top:20px;
                                padding:15px;
                                background:#f7f7f7;
                                border-radius:10px;
                            "
                        >

                            <strong>
                                Resposta
                            </strong>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $solicitacao[
                                            'resposta'
                                        ]
                                    )
                                ) ?>

                            </p>

                        </div>

                    <?php endif; ?>


                    <!-- DATA -->

                    <p
                        style="
                            font-size:12px;
                            color:#888;
                            margin-top:20px;
                        "
                    >

                        Solicitação enviada em

                        <?= date(
                            'd/m/Y H:i',
                            strtotime(
                                $solicitacao[
                                    'criado_em'
                                ]
                            )
                        ) ?>

                    </p>

<a
    href="editar_solicitacao.php?id=<?= $solicitacao['id'] ?>"
    class="btn-principal"
>
    Abrir solicitação
</a>
                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </section>


</main>


</body>

</html>