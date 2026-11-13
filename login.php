<?php

session_start();
session_regenerate_id(true);

$base = '';

require_once 'config/conexao.php';

$mensagem = '';
$status = '';

if (isset($_GET['status'])) {
    $status = $_GET['status'] ?? '';

    switch ($status) {
        case 'cadastro_sucesso':
            $mensagem = "Usuário cadastrado com sucesso! Faça login para começar.";
            break;

        case 'logout':
            $mensagem = "Sessão encerrada com sucesso.";
            break;
        
        case 'conta_excluida':
            $mensagem = "Conta excluída com sucesso.";
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
                header('Location: app/painel.php');
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

<?php require 'templates/head.php' ?>

<!-- CSS -->
<link rel="stylesheet" href="css/style.css">

<title>Login - Diário Financeiro</title>

</head>

<body class="page-auth">

    <!-- Mensagem Toast -->
    <?php require 'templates/toast.php' ?>

    <main class="login-cadastro" data-aos="fade-right">

        <h2>Login</h2>

        <form action="login.php" method="post">

            <div class="form-grupo">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-grupo">
                <label for="senha">Senha:</label>
                <div class="campo-senha">
                    <input type="password" name="senha" id="senha" required>
                    <button type="button" id="mostrar-senha" onclick="toggleSenha('senha', this)" class="btn-senha"><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>

            <button type="submit" class="btn btn-form">Entrar</button>

        </form>

        <div>
            <p>Esqueceu a senha? <a href="recuperar_senha.php" class="link-rodape">Recuperar senha</a></p>
            <p>Não possui uma conta? <a href="cadastro.php" class="link-rodape">Criar conta</a></p>
        </div>

        <div>
            <a href="index.php" class="btn btn-primario">Voltar</a>
        </div>

    </main>

    <!-- Javascript -->
    <?php require 'templates/bibliotecas_js.php' ?>

</body>

</html>