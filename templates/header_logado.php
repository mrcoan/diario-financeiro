<?php $paginaAtual = basename($_SERVER['SCRIPT_NAME']); ?>

<header class="navbar">

    <!-- nav menu -->
    <div class="nav-menu">
        <a href="painel.php" <?= $paginaAtual === 'painel.php' ? 'class="active" aria-current="page"' : '' ?>><i class="fa-solid fa-rectangle-list"></i><br>Painel</a>
        <a href="extrato.php" <?= $paginaAtual === 'extrato.php' ? 'class="active" aria-current="page"' : '' ?>><i class="fa-solid fa-sheet-plastic"></i><br>Extrato</a>
        <a href="graficos.php" <?= $paginaAtual === 'graficos.php' ? 'class="active" aria-current="page"' : '' ?>><i class="fa-solid fa-chart-line"></i><br>Gráficos</a>
    </div>

    <!-- Usuário -->
    <details class="usuario">
        <summary class="avatar btn btn-primario" aria-label="Menu do usuário">
            <i class="fa-solid fa-user"></i>
        </summary>
        <div class="nav-links">
            <a href="config.php" class="btn-usuario btn-primario"><i class="fa-solid fa-gear"></i> Config</a>
            <a href="<?= $base ?>process/logout.php" class="btn-usuario btn-excluir"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
        </div>
    </details>
</header>