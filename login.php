<?php

session_start();
session_regenerate_id(true);
require_once 'config/conexao.php';

$mensagem = '';
$status = '';

if (isset($_GET['status'])) {
    $status = $_GET['status'] ?? '';

    switch ($status) {
        case 'cadastro_sucesso':
            $mensagem = "Usuário cadastrado com sucesso!";
            break;

        case 'logout':
            $mensagem = "Sessão encerrada com sucesso.";
            break;

        default:
            $mensagem = "Inválido.";
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (!empty($email) && !empty($senha)) {
        try {
            // buscar o usuario por email
            $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // verificar se o usuario existe e se a senha está correta
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // guarda as informações do usuaário na sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];

                // redirecionar para o painel.php
                header('Location: painel.php');
                exit;
            } else {
                $mensagem = "E-mail ou senha incorretos.";
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $mensagem = "Erro no sistema: ";
        }
    } else {
        $mensagem = "Preencha todos os campos.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Diário Financeiro</title>

    <!-- ícone na barra do navegador -->
    <link rel="shortcut icon" href="img/logo.ico" type="image">

    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="page-auth">

    <div class="login-cadastro">

        <?php if (!empty($mensagem)): ?>
            <span class="mensagem"><?php echo $mensagem ?></span>
        <?php endif; ?>

        <img src="img/logo.png" alt="Logo Diário Financeiro">

        <h2>Login</h2>
        <p>Acesse sua conta</p>

        <form action="login.php" method="post">


            <div class="form-grupo">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-grupo">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" required>
            </div>

            <button type="submit" class="btn-form">Entrar</button>

        </form>

        <span>Esqueceu a senha? <a href="process/recuperar_senha.php" class="link-rodape">Recuperar senha</a></span>

        <span>Não possui uma conta? <a href="cadastro.php" class="link-rodape">Criar conta</a></span>

        <span><a href="index.html" class="btn-primario">Voltar</a></span>

    </div>

    <!-- Javascript -->
    <script src="js/script.js"></script>

</body>

</html>