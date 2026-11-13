<?php

session_start();
require_once '../config/conexao.php';

// 1. só entra quem está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

// 2. só aceita o envio do formulário (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: config.php');
    exit;
}

$id = $_SESSION['usuario_id'];
$senha = $_POST['confirmar_senha'] ?? '';

// 3. busca a senha (hash) salva no banco
$stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = :id");
$stmt->bindValue(':id', $id);
$stmt->execute();
$hash = $stmt->fetchColumn();

// 4. senha errada: volta pra configuração
if (!password_verify($senha, $hash)) {
    header('Location: ../app/config.php?status=senha_incorreta');
    exit;
}

// 5. senha certa: apaga a conta (as transações vão junto)
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
$stmt->bindValue(':id', $id);
$stmt->execute();

session_destroy();
header('Location: ../login.php?status=conta_excluida');
exit; 