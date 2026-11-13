<?php

session_start();
require_once '../config/conexao.php';

// 1. só entra quem está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

// 2. só aceita envio de formulário (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../app/extrato.php');
    exit;
}

// 3. pega o que veio do formulário
$usuarioId = $_SESSION['usuario_id'];
$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: ../app/extrato.php?status=erro');
    exit;
}

// 4. apaga (só se a transação for deste usuário)
try {
    $sql = "DELETE FROM transacoes WHERE id = :id AND usuario_id = :usuario_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id, 'usuario_id' => $usuarioId]);

    // rowCount() = quantas linhas foram apagadas; 0 significa que não achou
    $status = $stmt->rowCount() > 0 ? 'excluir_sucesso' : 'erro';
} catch (PDOException $e) {
    error_log($e->getMessage());
    $status = 'erro';
}

// 5. volta para o extrato com o aviso
header('Location: ../app/extrato.php?status=' . $status);
exit;