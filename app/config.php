<?php

session_start();

$base = '../';

require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
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

if (($_GET['status'] ?? '') === 'senha_incorreta') {
    $mensagem = 'Senha incorreta. Sua conta não foi apagada.';
}

$nomeUsuario = $_SESSION['usuario_nome'];

?>

<?php require '../templates/head.php' ?>

<!-- CSS -->
<link rel="stylesheet" href="<?= $base ?>css/style.css">

<title>Configurações da conta - Diário Financeiro</title>
</head>

<body class="body-layout">

    <!-- HEADER -->
    <?php require '../templates/header_logado.php' ?>

    <main>

        <!-- Mensagem Toast -->
        <?php require '../templates/toast.php' ?>

        <!-- Mensagem de boas-vindas -->
        <section id="boas-vindas">
            <h1>Olá, <?= htmlspecialchars($nomeUsuario) ?>!</h1>
            <p>Mantenha seus dados atualizados.</p>
        </section>

        <hr>

        <!-- Configurações dos dados -->
        <section id="config-usuario" class="container" data-aos="fade-right">
            <div class="layout-display">

                <div class="menu-section-titulo">
                    <h2 class="titulo-section">Seus Dados</h2>
                </div>

                <form action="config.php" method="post" class="form-config-usuario">

                    <div class="form-grupo">
                        <label for="nome">Nome:</label>
                        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="form-grupo">
                        <label for="senha">Senha:</label>
                        <input type="password" name="senha" id="senha" minlength="8" placeholder="Deixe em branco para manter a senha atual">
                    </div>

                    <div class="form-opcoes">
                        <button type="submit" class="btn btn-form">Salvar</button>
                    </div>

                </form>

                <div class="menu-section-titulo">
                    <h2 class="titulo-section">Apagar Conta</h2>
                </div>

                <div>
                    <button type="button" onclick="document.getElementById('apagar-conta').showModal()" class="btn btn-excluir">Excluir Conta</button>

                    <dialog id="apagar-conta">

                        <div class="menu-dialog">
                            <div>
                                <h3>Excluir Conta?</h3>
                            </div>
                            <button type="button" onclick="document.getElementById('apagar-conta').close()" class="btn btn-primario" title="Fechar"><i class="fa-solid fa-xmark"></i></button>
                        </div>

                        <form action="process/excluir_conta.php" method="post" class="form-dialog">
                            <p>Todos os seus dados serão apagados.</p>

                            <div class="form-grupo">
                                <label for="confirmar_senha">Confirmar senha:</label>
                                <div class="campo-senha">
                                    <input type="password" name="confirmar_senha" id="confirmar_senha" required>
                                    <button type="button" id="mostrar-senha" onclick="toggleSenha('confirmar_senha', this)" class="btn-senha"><i class="fa-solid fa-eye"></i></button>
                                </div>
                            </div>

                            <div class="form-opcoes">
                                <button type="submit" class="btn btn-excluir">Apagar</button>
                            </div>

                        </form>

                    </dialog>
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