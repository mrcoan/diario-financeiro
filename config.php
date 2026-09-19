<?php

session_start();

require_once 'config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoNome = trim($_POST['nome']);
    $novaSenha = $_POST['senha'];

    if (!empty($novoNome)) {
        if (!empty($novaSenha)) {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios
                    SET nome = :nome, senha = :senha
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':senha', $senhaHash);
        } else {
            $sql = "UPDATE usuarios
                    SET nome = :nome
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
        }
        $stmt->bindValue(':nome', $novoNome);
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $mensagem = 'Dados atualizados!';
            $_SESSION['usuario_nome'] = $novoNome;
        }
    }
}

$nomeUsuario = $_SESSION['usuario_nome'];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações da conta - Diário Financeiro</title>

    <!-- ícone na barra do navegador -->
    <link rel="shortcut icon" href="img/logo.ico" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>

    <!-- HEADER -->
    <header class="navbar">
        <!-- Logo -->
        <div class="logo-header">
            <a href="painel.php"><img src="img/logo.png" alt="Diario Financeiro Logo"></a>
        </div>

        <!-- nav menu -->
        <div class="nav-menu">
            <a href="process/processar_lancamento.php">Incluir Lançamento</a>
            <a href="painel.php">Painel</a>
            <a href="extrato.php">Extrato Completo</a>
        </div>

        <!--nav links -->
        <div class="nav-links">
            <a href="config.php" class="btn-primario">Config</a>
            <a href="logout.php" class="btn-secundario">Sair</a>
        </div>
    </header>

    <!-- Mensagem de boas-vindas -->
    <section id="boas-vindas">
        <h1>Olá, <?= htmlspecialchars($nomeUsuario) ?>!</h1>
        <p>Mantenha seus dados atualizados.</p>
    </section>

    <hr>

    <?php if (!empty($mensagem)): ?>
        <span class="mensagem"><?php echo $mensagem ?></span>
    <?php endif; ?>
    
    <!-- Configurações dos dados -->
    <section id="configuracoes">

        <h2>Seus Dados</h2>
        <div class="layout-display">
            <div class="card-painel">

                <form action="config.php" method="post">

                    <div class="form-grupo">
                        <label for="nome">Nome:</label>
                        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="form-grupo">
                        <label for="senha">Senha:</label>
                        <input type="password" name="senha" id="senha" placeholder="Deixe em branco para manter a senha atual">
                    </div>

                    <button type="submit" class="btn-form">Salvar</button>

                </form>

            </div>

        </div>

    </section>

</body>

</html>