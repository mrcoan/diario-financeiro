<?php

require_once 'config/conexao.php';

$mensagem = '';


// VERIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (!empty($nome) && !empty($email) && !empty($senha)) {

        // criptografia da senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            // prepara a execução
            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', $senhaHash);

            if ($stmt->execute()) {
                header('Location: login.php?status=cadastro_sucesso');
                exit;
            }
        } catch (PDOException $e) {
            //trata e-mail duplicado
            if ($e->getCode() == 23000) {
                $mensagem = "Este e-mail já está cadastrado.";
            } else {
                error_log($e->getMessage());
                $mensagem = "Erro ao cadastrar.";
                exit;
            }
        }
    } else {
        $mensagem = "Preencha todos os campos";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Diário Financeiro</title>

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

        <h2>Cadastre-se</h2>
        <p>Crie uma nova conta</p>

        <form action="cadastro.php" method="post">

            <div class="form-grupo">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" required>
            </div>

            <div class="form-grupo">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-grupo">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" required>
            </div>

            <button type="submit" class="btn-form">Cadastrar</button>

        </form>

        <span>Já possui uma conta? <a href="login.php" class="link-rodape">Entrar</a></span>

        <span><a href="index.html" class="btn-primario">Voltar</a></span>

    </div>


    <!-- Javascript -->
    <script src="js/script.js"></script>

</body>

</html>