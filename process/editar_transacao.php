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
$descricao = trim($_POST['descricao'] ?? '');
$valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
$tipo = $_POST['tipo'] ?? '';
$categoriaId = ($_POST['categoria_id'] ?? '') !== '' ? (int) $_POST['categoria_id'] : null;
$data_transacao = $_POST['data_transacao'] ?? '';

// 4. confere se está tudo certo; se não, volta com aviso
if ($id <= 0 || $descricao === '' || $valor === false || $valor <= 0
    || !in_array($tipo, ['entrada', 'saida', 'investimento']) || $data_transacao === '') {
    header('Location: ../app/extrato.php?status=erro');
    exit;
}

// 5. atualiza no banco (só se a transação for deste usuário)
try {
    $sql = "UPDATE transacoes
            SET descricao = :descricao, valor = :valor, tipo = :tipo,
                categoria_id = :categoria_id, data_transacao = :data_transacao
            WHERE id = :id AND usuario_id = :usuario_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'descricao'      => $descricao,
        'valor'          => $valor,
        'tipo'           => $tipo,
        'categoria_id'   => $categoriaId,
        'data_transacao' => $data_transacao,
        'id'             => $id,
        'usuario_id'     => $usuarioId,
    ]);

    header('Location: ../app/extrato.php?status=editar_sucesso');
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: ../app/extrato.php?status=erro');
}
exit;