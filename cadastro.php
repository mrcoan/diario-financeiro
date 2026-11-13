<?php

$base = '';

require_once 'config/conexao.php';

$mensagem = '';


// VERIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (!empty($nome) && !empty($email) && !empty($senha) && !empty($confirmar_senha)) {

        if (strlen($senha) < 8) {
            $mensagem = "A senha deve ter pelo menos 8 caracteres";
        } else if ($senha === $confirmar_senha) {
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
                }
            }
        } else {
            $mensagem = "As senhas não coincidem.";
        }
    } else {
        $mensagem = "Preencha todos os campos";
    }
}

?>

<?php require 'templates/head.php' ?>

<!-- CSS -->
<link rel="stylesheet" href="css/style.css">

<title>Cadastro - Diário Financeiro</title>
</head>

<body class="page-auth">

    <!-- Mensagem Toast -->
    <?php require 'templates/toast.php' ?>

    <main class="login-cadastro" data-aos="fade-right">

        <h2>Cadastre-se</h2>

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
                <div class="campo-senha">
                    <input type="password" name="senha" id="senha" minlength="8" required>
                    <button type="button" id="mostrar-senha" class="btn-senha" onclick="toggleSenha('senha', this)"><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>

            <div class="form-grupo">
                <label for="confirmar_senha">Confirmar senha:</label>
                <div class="campo-senha">
                    <input type="password" name="confirmar_senha" id="confirmar_senha" minlength="8" required>
                    <button type="button" id="confirmar-senha" class="btn-senha" onclick="toggleSenha('confirmar_senha', this)"><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>

            <button type="submit" class="btn btn-form">Cadastrar</button>

        </form>

        <div>
            <p>Já possui uma conta? <a href="login.php" class="link-rodape">Entrar</a></p>
        </div>

        <div>
            <a href="index.php" class="btn btn-primario">Voltar</a>
        </div>

    </main>

    <!-- Javascript -->
    <?php require 'templates/bibliotecas_js.php' ?>

</body>

</html>