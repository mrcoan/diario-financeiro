<?php

session_start();
require_once '../config/conexao.php';

// proteção de acesso à sessão
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $usuarioId = $_SESSION['usuario_id'];
    $id = $_GET['id'] ?? '';

    if (empty($id)) {
        header('Location: ../extrato.php?status=erro');
        exit;
    }
    
    try {
    
        $sql = "DELETE FROM transacoes WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header('Location: ../extrato.php?status=excluir_sucesso');
            exit;
        } else {
            header('Location: ../extrato.php?status=erro');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: ../extrato.php?status=erro');
        exit;    
    }

} else {
    header('Location: ../extrato.php');
    exit;
}

