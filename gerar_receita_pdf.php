<?php

require_once 'config.php';
require_once 'funcoes.php';
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

exigirLogin();

$receitaId = (int)($_GET['id'] ?? 0);

if ($receitaId <= 0) {
    die('Receita inválida.');
}


/*
    BUSCAR RECEITA
*/

$stmt = $pdo->prepare("
    SELECT
        receitas.id,
        receitas.data_receita,
        receitas.titulo,
        receitas.medicamentos,
        receitas.observacoes,
        receitas.pet_id,

        pets.nome AS pet_nome,
        pets.especie,
        pets.raca,

        clientes.nome AS cliente_nome

    FROM receitas

    INNER JOIN pets
        ON pets.id = receitas.pet_id

    INNER JOIN clientes
        ON clientes.id = pets.cliente_id

    WHERE receitas.id = ?
");

$stmt->execute([$receitaId]);

$receita = $stmt->fetch();

if (!$receita) {
    die('Receita não encontrada.');
}


/*
    CONFIGURAÇÃO DO DOMPDF
*/

$options = new Options();

$options->set(
    'isRemoteEnabled',
    true
);

$options->set(
    'defaultFont',
    'DejaVu Sans'
);

$dompdf = new Dompdf($options);


/*
    DADOS
*/

$petNome = htmlspecialchars(
    $receita['pet_nome'],
    ENT_QUOTES,
    'UTF-8'
);

$tutorNome = htmlspecialchars(
    $receita['cliente_nome'],
    ENT_QUOTES,
    'UTF-8'
);

$especie = htmlspecialchars(
    $receita['especie'],
    ENT_QUOTES,
    'UTF-8'
);

$raca = htmlspecialchars(
    $receita['raca'] ?: '-',
    ENT_QUOTES,
    'UTF-8'
);

$titulo = htmlspecialchars(
    $receita['titulo'],
    ENT_QUOTES,
    'UTF-8'
);

$medicamentos = nl2br(
    htmlspecialchars(
        $receita['medicamentos'],
        ENT_QUOTES,
        'UTF-8'
    )
);

$observacoes = nl2br(
    htmlspecialchars(
        $receita['observacoes'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    )
);

$dataReceita = date(
    'd/m/Y',
    strtotime(
        $receita['data_receita']
    )
);


/*
    HTML DO PDF
*/

$html = <<<HTML

<!DOCTYPE html>

<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<style>

@page {
    margin: 28px;
}

body {
    font-family: DejaVu Sans, sans-serif;
    margin: 0;
    color: #333333;
    background: #ffffff;
}


/* CABEÇALHO */

.header {
    background: #f2f7f4;
    border-radius: 22px;
    padding: 25px;
    margin-bottom: 25px;
    text-align: center;
}

.clinica {
    font-size: 26px;
    font-weight: bold;
    color: #26352e;
}

.subtitulo {
    font-size: 12px;
    color: #6d766f;
    margin-top: 6px;
}

.data {
    font-size: 10px;
    color: #777777;
    margin-top: 10px;
}


/* TÍTULO */

.titulo-principal {
    text-align: center;
    font-size: 21px;
    font-weight: bold;
    color: #26352e;
    margin: 22px 0;
}


/* CARTÕES */

.card {
    border: 1px solid #dddddd;
    border-radius: 18px;
    padding: 18px;
    margin-bottom: 18px;
    background: #ffffff;
}

.card-titulo {
    font-size: 15px;
    font-weight: bold;
    color: #26352e;
    margin-bottom: 14px;
}


/* DADOS DO PET */

.pet-table {
    width: 100%;
    border-collapse: collapse;
}

.pet-table td {
    width: 50%;
    padding: 7px 4px;
    font-size: 12px;
}

.label {
    color: #777777;
    font-weight: bold;
}


/* MEDICAMENTOS */

.medicamentos {
    font-size: 13px;
    line-height: 1.8;
    color: #333333;
}


/* ORIENTAÇÕES */

.observacoes {
    font-size: 12px;
    line-height: 1.7;
    color: #444444;
}


/* ASSINATURA */

.assinatura {
    margin-top: 65px;
    text-align: center;
}

.linha-assinatura {
    width: 220px;
    border-top: 1px solid #555555;
    margin: 0 auto 8px auto;
}

.assinatura-nome {
    font-size: 13px;
    font-weight: bold;
    color: #333333;
}

.assinatura-cargo {
    font-size: 10px;
    color: #777777;
    margin-top: 3px;
}


/* RODAPÉ */

.rodape {
    margin-top: 35px;
    text-align: center;
    font-size: 9px;
    color: #999999;
}

.rodape-linha {
    margin-top: 5px;
}

</style>

</head>


<body>


<!-- CABEÇALHO -->

<div class="header">

    <div class="clinica">
        Dra. Caroline
    </div>

    <div class="subtitulo">
        Atendimento Veterinário
    </div>

    <div class="data">
        Receita emitida em {$dataReceita}
    </div>

</div>


<!-- TÍTULO -->

<div class="titulo-principal">

    RECEITA VETERINÁRIA

</div>


<!-- DADOS DO PACIENTE -->

<div class="card">

    <div class="card-titulo">

        DADOS DO PACIENTE

    </div>


    <table class="pet-table">

        <tr>

            <td>

                <span class="label">
                    Pet:
                </span>

                {$petNome}

            </td>


            <td>

                <span class="label">
                    Tutor:
                </span>

                {$tutorNome}

            </td>

        </tr>


        <tr>

            <td>

                <span class="label">
                    Espécie:
                </span>

                {$especie}

            </td>


            <td>

                <span class="label">
                    Raça:
                </span>

                {$raca}

            </td>

        </tr>

    </table>

</div>


<!-- RECEITA -->

<div class="card">

    <div class="card-titulo">

        {$titulo}

    </div>


    <div class="medicamentos">

        {$medicamentos}

    </div>

</div>


HTML;


/*
    ORIENTAÇÕES
*/

if (!empty($receita['observacoes'])) {

    $html .= <<<HTML

<div class="card">

    <div class="card-titulo">

        ORIENTAÇÕES

    </div>


    <div class="observacoes">

        {$observacoes}

    </div>

</div>

HTML;

}


/*
    ASSINATURA
*/

$html .= <<<HTML

<div class="assinatura">

    <div class="linha-assinatura"></div>

    <div class="assinatura-nome">
        Dra. Caroline
    </div>

    <div class="assinatura-cargo">
        Médica Veterinária
    </div>

</div>


<div class="rodape">

    Dra. Caroline - Atendimento Veterinário

    <div class="rodape-linha">
        Documento gerado pelo sistema de atendimento veterinário.
    </div>

</div>


</body>

</html>

HTML;


/*
    GERAR PDF
*/

$dompdf->loadHtml($html);

$dompdf->setPaper(
    'A4',
    'portrait'
);

$dompdf->render();


/*
    SALVAR PDF
*/

$pasta = __DIR__ . '/uploads/receitas/';

if (!is_dir($pasta)) {

    mkdir(
        $pasta,
        0755,
        true
    );
}


$nomeArquivo =
    'receita_' .
    $receitaId .
    '.pdf';


$caminhoArquivo =
    $pasta .
    $nomeArquivo;


file_put_contents(
    $caminhoArquivo,
    $dompdf->output()
);


/*
    SALVAR NO BANCO
*/

$stmt = $pdo->prepare("
    UPDATE receitas
    SET arquivo_pdf = ?
    WHERE id = ?
");

$stmt->execute([
    $nomeArquivo,
    $receitaId
]);


/*
    ABRIR PDF
*/

$dompdf->stream(
    $nomeArquivo,
    [
        'Attachment' => false
    ]
);

exit;