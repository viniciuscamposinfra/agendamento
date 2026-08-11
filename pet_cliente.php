<?php

require_once 'config.php';

session_start();

if (!isset($_SESSION['cliente_id'])) {
    header('Location: cliente_login.php');
    exit;
}

$clienteId = (int) $_SESSION['cliente_id'];
$petId = (int) ($_GET['id'] ?? 0);

if ($petId <= 0) {
    header('Location: cliente.php');
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
        pets.sexo,
        pets.data_nascimento,
        pets.peso,
        pets.alergias,
        pets.observacoes
    FROM pets
    WHERE pets.id = ?
      AND pets.cliente_id = ?
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
    BUSCAR HISTÓRICO CLÍNICO
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        data_consulta,
        queixa_principal,
        anamnese,
        exame_fisico,
        diagnostico,
        tratamento,
        medicamentos,
        observacoes
    FROM historico_clinico
    WHERE pet_id = ?
    ORDER BY data_consulta DESC, id DESC
");

$stmt->execute([$petId]);

$historicos = $stmt->fetchAll();


/*
    BUSCAR EXAMES
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        titulo,
        descricao,
        data_exame,
        arquivo,
        tipo_arquivo
    FROM exames
    WHERE pet_id = ?
    ORDER BY data_exame DESC, id DESC
");

$stmt->execute([$petId]);

$exames = $stmt->fetchAll();


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
        arquivo_pdf
    FROM receitas
    WHERE pet_id = ?
    ORDER BY data_receita DESC, id DESC
");

$stmt->execute([$petId]);

$receitas = $stmt->fetchAll();


/*
    BUSCAR SOLICITAÇÕES
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        tipo,
        descricao,
        resposta,
        status,
        criado_em,
        atualizado_em
    FROM solicitacoes
    WHERE pet_id = ?
      AND cliente_id = ?
    ORDER BY criado_em DESC, id DESC
");

$stmt->execute([
    $petId,
    $clienteId
]);

$solicitacoes = $stmt->fetchAll();


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

        <?= htmlspecialchars($pet['nome']) ?>

        |

        Área do Cliente

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
                🐾 Ficha do pet
            </span>

            <h1>

                <?= $iconePet ?>

                <?= htmlspecialchars(
                    $pet['nome']
                ) ?>

            </h1>

            <p>

                <?= htmlspecialchars(
                    $pet['especie']
                ) ?>

                <?php if ($pet['raca']): ?>

                    ·

                    <?= htmlspecialchars(
                        $pet['raca']
                    ) ?>

                <?php endif; ?>

            </p>

        </div>


        <a
            href="cliente.php"
            class="btn-sair"
        >

            ← Voltar

        </a>

    </div>


    <!-- DADOS DO PET -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                🐾 Informações
            </span>

            <h2>
                Dados do animal
            </h2>

        </div>


        <div class="dashboard-cards">


            <div class="dashboard-card">

                <div class="dashboard-icone">
                    🐾
                </div>

                <div>

                    <span>
                        Espécie
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $pet['especie']
                        ) ?>

                    </strong>

                </div>

            </div>


            <div class="dashboard-card">

                <div class="dashboard-icone">
                    🧬
                </div>

                <div>

                    <span>
                        Raça
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $pet['raca'] ?: '-'
                        ) ?>

                    </strong>

                </div>

            </div>


            <div class="dashboard-card">

                <div class="dashboard-icone">
                    ⚖️
                </div>

                <div>

                    <span>
                        Peso
                    </span>

                    <strong>

                        <?= $pet['peso']
                            ? htmlspecialchars(
                                $pet['peso']
                            ) . ' kg'
                            : '-' ?>

                    </strong>

                </div>

            </div>


            <div class="dashboard-card">

                <div class="dashboard-icone">
                    ⚥
                </div>

                <div>

                    <span>
                        Sexo
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $pet['sexo'] ?: '-'
                        ) ?>

                    </strong>

                </div>

            </div>


        </div>

    </section>


    <!-- SAÚDE -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                ⚠️ Informações importantes
            </span>

            <h2>
                Saúde
            </h2>

        </div>


        <div class="form-grid">


            <div class="campo">

                <label>
                    Data de nascimento
                </label>

                <div
                    style="
                        padding:14px;
                        background:#fff;
                        border:1px solid #eee;
                        border-radius:10px;
                    "
                >

                    <?php if ($pet['data_nascimento']): ?>

                        <?= date(
                            'd/m/Y',
                            strtotime(
                                $pet['data_nascimento']
                            )
                        ) ?>

                    <?php else: ?>

                        Não informado

                    <?php endif; ?>

                </div>

            </div>


            <div class="campo">

                <label>
                    Alergias
                </label>

                <div
                    style="
                        padding:14px;
                        background:#fff;
                        border:1px solid #eee;
                        border-radius:10px;
                        min-height:50px;
                    "
                >

                    <?= nl2br(
                        htmlspecialchars(
                            $pet['alergias']
                            ?: 'Nenhuma informação cadastrada.'
                        )
                    ) ?>

                </div>

            </div>


            <div class="campo campo-completo">

                <label>
                    Observações
                </label>

                <div
                    style="
                        padding:14px;
                        background:#fff;
                        border:1px solid #eee;
                        border-radius:10px;
                        min-height:70px;
                    "
                >

                    <?= nl2br(
                        htmlspecialchars(
                            $pet['observacoes']
                            ?: 'Nenhuma observação cadastrada.'
                        )
                    ) ?>

                </div>

            </div>


        </div>

    </section>


    <!-- HISTÓRICO CLÍNICO -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                🩺 Prontuário
            </span>

            <h2>
                Histórico clínico
            </h2>

            <p>
                Acompanhe os atendimentos realizados.
            </p>

        </div>


        <?php if (!$historicos): ?>


            <div class="sem-agendamentos">

                <div>
                    🩺
                </div>

                <h3>
                    Nenhum atendimento registrado
                </h3>

                <p>
                    Ainda não existem registros clínicos
                    para este pet.
                </p>

            </div>


        <?php else: ?>


            <?php foreach ($historicos as $historico): ?>


                <div
                    style="
                        background:#fff;
                        border:1px solid #eee;
                        border-radius:14px;
                        padding:25px;
                        margin-bottom:20px;
                    "
                >


                    <h3>

                        🩺 Atendimento de

                        <?= date(
                            'd/m/Y',
                            strtotime(
                                $historico['data_consulta']
                            )
                        ) ?>

                    </h3>


                    <?php if ($historico['queixa_principal']): ?>

                        <div style="margin-top:20px;">

                            <strong>
                                Queixa principal
                            </strong>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico[
                                            'queixa_principal'
                                        ]
                                    )
                                ) ?>

                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['anamnese']): ?>

                        <div style="margin-top:15px;">

                            <strong>
                                Anamnese
                            </strong>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico[
                                            'anamnese'
                                        ]
                                    )
                                ) ?>

                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['exame_fisico']): ?>

                        <div style="margin-top:15px;">

                            <strong>
                                Exame físico
                            </strong>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico[
                                            'exame_fisico'
                                        ]
                                    )
                                ) ?>

                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['diagnostico']): ?>

                        <div style="margin-top:15px;">

                            <strong>
                                Diagnóstico
                            </strong>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico[
                                            'diagnostico'
                                        ]
                                    )
                                ) ?>

                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['tratamento']): ?>

                        <div style="margin-top:15px;">

                            <strong>
                                Tratamento
                            </strong>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico[
                                            'tratamento'
                                        ]
                                    )
                                ) ?>

                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['medicamentos']): ?>

                        <div style="margin-top:15px;">

                            <strong>
                                💊 Medicamentos
                            </strong>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico[
                                            'medicamentos'
                                        ]
                                    )
                                ) ?>

                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($historico['observacoes']): ?>

                        <div style="margin-top:15px;">

                            <strong>
                                Observações
                            </strong>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $historico[
                                            'observacoes'
                                        ]
                                    )
                                ) ?>

                            </p>

                        </div>

                    <?php endif; ?>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </section>


    <!-- EXAMES -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                📎 Documentos
            </span>

            <h2>
                Exames e documentos
            </h2>

            <p>
                Exames e documentos disponibilizados
                pela Dra. Caroline.
            </p>

        </div>


        <?php if (!$exames): ?>


            <div class="sem-agendamentos">

                <div>
                    📎
                </div>

                <h3>
                    Nenhum exame disponível
                </h3>

                <p>
                    Ainda não existem exames disponibilizados
                    para este pet.
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
                        👁 Visualizar
                    </a>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </section>


    <!-- RECEITAS -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                Prescrições
            </span>

            <h2>
                Receitas
            </h2>

            <p>
                Receitas disponibilizadas pela Dra. Caroline.
            </p>

        </div>


        <?php if (!$receitas): ?>


            <div class="sem-agendamentos">

                <div>
                    💊
                </div>

                <h3>
                    Nenhuma receita disponível
                </h3>

                <p>
                    Ainda não existem receitas disponibilizadas
                    para este pet.
                </p>

            </div>


        <?php else: ?>


            <?php foreach ($receitas as $receita): ?>


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
                                Visualizar PDF
                            </a>

                        <?php endif; ?>

                    </div>


                    <div
                        style="
                            margin-top:15px;
                            padding-top:15px;
                            border-top:1px solid #eee;
                        "
                    >

                        <strong>
                            Medicamentos e instruções
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
                        !empty(
                            $receita['observacoes']
                        )
                    ): ?>

                        <div
                            style="
                                margin-top:15px;
                            "
                        >

                            <strong>
                                Orientações
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


    <!-- SOLICITAÇÕES -->

    <section class="painel-secao">

        <div class="secao-topo">

            <span>
                Atendimento
            </span>

            <h2>
                Minhas solicitações
            </h2>

            <p>
                Acompanhe suas solicitações e as respostas
                da Dra. Caroline.
            </p>

        </div>


        <div style="margin-bottom:20px;">

            <a
                href="solicitacao.php?pet_id=<?= $petId ?>"
                class="btn-principal"
            >
                Nova solicitação
            </a>

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
                    Você ainda não enviou nenhuma solicitação
                    para este pet.
                </p>

            </div>


        <?php else: ?>


            <?php foreach ($solicitacoes as $solicitacao): ?>


                <?php

                if (
                    $solicitacao['status'] === 'pendente'
                ) {

                    $statusTexto = 'Pendente';
                    $statusIcone = '🟡';

                } elseif (
                    $solicitacao['status'] === 'em_andamento'
                ) {

                    $statusTexto = 'Em andamento';
                    $statusIcone = '🔵';

                } elseif (
                    $solicitacao['status'] === 'concluida'
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
                        padding:20px;
                        margin-bottom:15px;
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

                                <?= htmlspecialchars(
                                    $solicitacao['tipo']
                                ) ?>

                            </h3>

                            <p
                                style="
                                    font-size:12px;
                                    color:#888;
                                "
                            >

                                Enviada em:

                                <?= date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $solicitacao['criado_em']
                                    )
                                ) ?>

                            </p>

                        </div>


                        <strong>

                            <?= $statusIcone ?>

                            <?= $statusTexto ?>

                        </strong>

                    </div>


                    <div style="margin-top:15px;">

                        <strong>
                            Sua solicitação
                        </strong>

                        <p>

                            <?= nl2br(
                                htmlspecialchars(
                                    $solicitacao['descricao']
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
                                border-left:4px solid #ddd;
                            "
                        >

                            <strong>
                                Resposta da Dra. Caroline
                            </strong>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $solicitacao['resposta']
                                    )
                                ) ?>

                            </p>

                        </div>


                    <?php else: ?>


                        <p
                            style="
                                margin-top:20px;
                                color:#888;
                            "
                        >

                            Aguardando resposta da
                            Dra. Caroline.

                        </p>


                    <?php endif; ?>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </section>


</main>


</body>

</html>