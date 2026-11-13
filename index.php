<?php

$base = '';

?>

<?php require 'templates/head.php' ?>

    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
        
    <title>Diário Financeiro - Seu Controle Pessoal</title>
</head>

<body class="body-layout">
    
    <!-- Header -->
    <?php require 'templates/header_publico.php' ?>

    <main>

        <!-- Hero - Apresentação -->
        <section id="hero" data-aos="fade-right">
            <div class="container layout-display hero-display">

                <article class="layout-display apresentacao">
                    <img src="img/logo.png" alt="Logo Diário Financeiro">
                    <h1>Diário Financeiro</h1>
                    <p>
                        Assuma o controle total do seu dinheiro:
                        Acompanhe suas entradas, despesas e saldos mensais numa interface
                        simples, prática e feita para o seu dia a dia.
                    </p>
                    <div>
                        <a href="cadastro.php" class="btn btn-cta">Comece a organizar agora</a>
                    </div>
                </article>

                <!-- Slider main container -->
                <div class="swiper">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                        <!-- Slides -->
                        <div class="swiper-slide">
                            <h2>Filtros de pesquisa</h2>
                            <img src="img/destaque-filtros.png" alt="Filtros de pesquisa">
                        </div>
                        <div class="swiper-slide">
                            <h2>Saldo organizado</h2>
                            <img src="img/destaque-investimentos.png" alt="Acompanhe o saldo por tipo de lançamento">
                        </div>
                        <div class="swiper-slide">
                            <h2>Acesso seguro</h2>
                            <img src="img/destaque-seguranca.png" alt="Acesso seguro">
                        </div>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>

                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>

            </div>
        </section>

    </main>

    <!-- Footer -->
    <?php require 'templates/footer.php' ?>

    <!-- Javascript -->
    <?php require 'templates/bibliotecas_js.php' ?>

</body>

</html>