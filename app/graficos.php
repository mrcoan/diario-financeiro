<?php

session_start();

$base = '../';

require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

?>

<?php require '../templates/head.php' ?>

<!-- CSS -->
<link rel="stylesheet" href="../css/style.css">

<title>Gráficos - Diário Financeiro</title>

</head>

<body class="body-layout">

    <!-- HEADER -->
    <?php require '../templates/header_logado.php' ?>

    <main>

        <!-- Resumo mensal -->
        <section id="resumo-mensal" class="container" data-aos="fade-right">

            <div class="layout-display menu-section">
                <div class="menu-section-titulo">
                    <h2 class="titulo-section">Análise Gráfica - Em Desenvolvimento</h2>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <?php require '../templates/footer.php' ?>

    <!-- Javascript -->
    <?php require '../templates/bibliotecas_js.php' ?>

</body>

</html>