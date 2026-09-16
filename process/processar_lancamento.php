<?php

session_start();
require_once '../config/conexao.php';

// verificar se o usuario esta logado por segurança
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

// verificar se a requsição veio vis POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioId = $_SESSION['usuario_id'];
    $descricao = trim($_POST['descricao']);
    $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
    $tipo = $_POST['tipo'] ?? '';
    $data_transacao = $_POST['data'] ?? '';

    // validar se os campos obrigatorios foram preenchidos
    if (!empty($descricao) && $valor !== false && $valor > 0 && in_array($tipo, ['entrada', 'saida', 'investimento']) && !empty($data_transacao)) {
        try {
            $sql = "INSERT INTO `transacoes` (`usuario_id`, `descricao`, `valor`, `tipo`, `data_transacao`) VALUES (:usuario_id, :descricao, :valor, :tipo, :data_transacao)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':valor', $valor);
            $stmt->bindValue(':tipo', $tipo);
            $stmt->bindValue(':data_transacao', $data_transacao);

            if ($stmt->execute()) {
                header('location: ../extrato.php?status=sucesso');
                exit;
            }
        } catch (PDOException $e) { // Mostra o erro exato na tela em vez de redirecionar
            error_log($e->getMessage());
            header('Location: ../painel.php?status=invalido');
            exit;
        }
    } else {
        // dados invalidos
        header('Location: ../painel.php?status=invalido');
        exit;
    }
} else {
    header('Location: ../painel.php');
    exit;
}
