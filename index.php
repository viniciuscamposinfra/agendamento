<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dra. Caroline | Atendimento Veterinário</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="topo">

    <div class="logo">
        <span class="logo-pata">🐾</span>

        <div>
            <strong>Dra. Caroline</strong>
            <small>Atendimento Veterinário</small>
        </div>
    </div>

    <nav>
        <a href="#inicio">Início</a>
        <a href="#sobre">Sobre</a>
        <a href="#atendimento">Atendimento</a>
        <a href="login.php" class="btn-login">🔐 Área da Veterinária</a>
        <a href="cliente_login.php">
    👤 Área do Cliente
</a>
    </nav>

</header>


<main>

    <!-- HERO -->

    <section class="hero" id="inicio">

        <div class="hero-conteudo">

            <span class="tag">
                🐾 Cuidado que começa pelo carinho
            </span>

            <h1>
                Saúde e carinho para
                <span>quem faz parte da família.</span>
            </h1>

            <p>
                Agende uma consulta de forma simples e rápida.
                Escolha o melhor horário e aguarde a confirmação
                da Dra. Caroline.
            </p>

            <div class="hero-botoes">

                <a href="agenda.php" class="btn-principal">
                    📅 Solicitar consulta
                </a>

                <a href="#sobre" class="btn-secundario">
                    Conheça o atendimento
                </a>

            </div>

        </div>


        <div class="hero-imagem">

            <div class="circulo"></div>

            <img
                src="imagens/cachorro1.jpg"
                alt="Cachorro"
                class="cachorro-principal"
            >

            <div class="decoracao decoracao-1">🐾</div>
            <div class="decoracao decoracao-2">♡</div>
            <div class="decoracao decoracao-3">🐾</div>

        </div>

    </section>


    <!-- BENEFÍCIOS -->

    <section class="beneficios" id="atendimento">

        <div class="titulo-secao">

            <span>🐶 Nosso atendimento</span>

            <h2>
                Cuidar também é uma forma de amar.
            </h2>

            <p>
                Um atendimento pensado para oferecer
                tranquilidade, carinho e cuidado.
            </p>

        </div>


        <div class="cards">

            <div class="card">

                <div class="icone">💗</div>

                <h3>Atendimento com carinho</h3>

                <p>
                    Cada paciente recebe atenção
                    individual e muito cuidado.
                </p>

            </div>


            <div class="card">

                <div class="icone">📅</div>

                <h3>Agendamento fácil</h3>

                <p>
                    Escolha a data e o horário que
                    deseja solicitar sua consulta.
                </p>

            </div>


            <div class="card">

                <div class="icone">🐾</div>

                <h3>Cuidado personalizado</h3>

                <p>
                    Cada pet possui suas necessidades
                    e merece um atendimento especial.
                </p>

            </div>

        </div>

    </section>


    <!-- SOBRE -->

    <section class="sobre" id="sobre">

        <div class="sobre-imagens">

            <img
                src="imagens/cachorro2.jpg"
                alt="Cachorro"
            >

            <img
                src="imagens/cachorro3.jpg"
                alt="Cachorro"
            >

        </div>


        <div class="sobre-texto">

            <span>💕 Um atendimento especial</span>

            <h2>
                Seu pet merece ser cuidado
                com carinho.
            </h2>

            <p>
                A Dra. Caroline acredita que um bom
                atendimento veterinário vai muito além
                de uma consulta.
            </p>

            <p>
                É ouvir, cuidar, acompanhar e oferecer
                segurança para você e para o seu melhor
                amigo.
            </p>

            <a href="agenda.php" class="btn-principal">
                Quero solicitar uma consulta
            </a>

        </div>

    </section>


    <!-- CTA -->

    <section class="cta">

        <div>

            <span>🐾 Vamos cuidar do seu melhor amigo?</span>

            <h2>
                Solicite uma consulta.
            </h2>

            <p>
                Escolha uma data e horário disponível.
                A confirmação será feita pela Dra. Caroline.
            </p>

            <a href="agenda.php" class="btn-branco">
                📅 Agendar consulta
            </a>

        </div>

        <img
            src="imagens/cachorro4.jpg"
            alt="Cachorro"
        >

    </section>

</main>


<footer>

    <div class="footer-logo">
        🐾 <strong>Dra. Caroline</strong>
    </div>

    <p>
        Atendimento veterinário com carinho e dedicação.
    </p>

    <span>
        © <?php echo date('Y'); ?> Dra. Caroline
    </span>

</footer>


</body>
</html>