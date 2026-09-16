<?php

session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$nomeUsuario = $_SESSION['usuario_nome'];
$usuarioId = $_SESSION['usuario_id'];

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "SELECT * FROM transacoes
                WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $transacao = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$transacao) {
            header('Location: ../extrato.php?status=erro');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: ../extrato.php?status=erro');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = trim($_POST['descricao']);
    $data_transacao = $_POST['data_transacao'];
    $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
    $tipo = $_POST['tipo'];
    $id = $_POST['id'];

    try {
        $sql = "UPDATE transacoes
                SET descricao = :descricao, valor = :valor, data_transacao = :data_transacao, tipo = :tipo
                WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':valor', $valor);
        $stmt->bindValue(':data_transacao', $data_transacao);
        $stmt->bindValue(':tipo', $tipo);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);

        $stmt->execute();

        header("Location: ../extrato.php?status=editar_sucesso");
        exit;
    } catch (PDOException $e) {
        header('Location: ../extrato.php?status=erro');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Transação - Diário Financeiro</title>

    <!-- ícone na barra do navegador -->
    <link rel="shortcut icon" href="../img/logo.ico" type="image">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>

    <!-- HEADER -->
    <header class="navbar">
        <!-- Logo -->
        <div class="logo-header">
            <a href="painel.php"><img src="../img/logo.png" alt="Diario Financeiro Logo"></a>
        </div>

        <!-- nav menu -->
        <div class="nav-menu">
            <a href="../painel.php">Painel</a>
            <a href="../extrato.php">Extrato Completo</a>
        </div>

        <!--nav links -->
        <div class="nav-links">
            <a href="../config.php" class="btn-primario">Config</a>
            <a href="../logout.php" class="btn-secundario">Sair</a>
        </div>
    </header>

    <!-- Mensagem de boas-vindas -->
    <section id="boas-vindas">
        <h1>Olá, <?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p>Altere sua movimentação.</p>
    </section>

    <hr>

    <section id="configuracoes">

        <h2>Movimentação</h2>
        <div class="layout-display">
            <div class="card-painel">

                <form action="editar_transacao.php" method="post">

                    <input type="hidden" name="id" value="<?= $transacao['id'] ?>">

                    <div class="form-grupo">
                        <label for="descricao">Descrição:</label>
                        <input type="text" name="descricao" id="descricao" value="<?= htmlspecialchars($transacao['descricao'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="form-grupo">
                        <label for="valor">Valor:</label>
                        <input type="number" name="valor" id="valor" step="0.01" min="0.01" value="<?= $transacao['valor'] ?>" required>
                    </div>

                    <div class="form-grupo">
                        <label for="tipo">Tipo de Transação:</label>
                        <div class="opcoes-radio">
                            <label class="saldo-positivo">
                                <input type="radio" name="tipo" value="entrada" <?= $transacao['tipo'] === 'entrada' ? 'checked' : '' ?> required> Entrada
                            </label>
                            <label class="saldo-negativo">
                                <input type="radio" name="tipo" value="saida" <?= $transacao['tipo'] === 'saida' ? 'checked' : '' ?> required> Saída
                            </label>
                            <label class="saldo-investimento">
                                <input type="radio" name="tipo" value="investimento"  <?= $transacao['tipo'] === 'investimento' ? 'checked' : '' ?> required> Investimento
                            </label>
                        </div>
                    </div>

                    <div class="form-grupo">
                        <label for="data_transacao">Data:</label>
                        <input type="date" name="data_transacao" id="data_transacao" value="<?= $transacao['data_transacao'] ?>" required>
                    </div>


                    <button type="submit" class="btn-form">Salvar</button>

                </form>
            </div>
        </div>
    </section>

</body>

</html>