<?php

session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$usuarioId = $_SESSION['usuario_id'];

// verificar se a requsição veio via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioId = $_SESSION['usuario_id'];
    $descricao = trim($_POST['descricao']);
    $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
    $tipo = $_POST['tipo'] ?? '';
    $categoriaId = ($_POST['categoria_id'] ?? '') !== '' ? (int) $_POST['categoria_id'] : null;
    $data_transacao = $_POST['data'] ?? '';

    // validar se os campos obrigatorios foram preenchidos
    if (!empty($descricao) && $valor !== false && $valor > 0 && in_array($tipo, ['entrada', 'saida', 'investimento']) && !empty($data_transacao)) {
        try {
            $sql ="INSERT INTO transacoes (usuario_id, descricao, valor, tipo, categoria_id, data_transacao)
                    VALUES (:usuario_id, :descricao, :valor, :tipo, :categoria_id, :data_transacao)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'usuario_id'     => $usuarioId,
                'descricao'      => $descricao,
                'valor'          => $valor,
                'tipo'           => $tipo,
                'categoria_id'   => $categoriaId,
                'data_transacao' => $data_transacao,
            ]);

            header('Location: ../app/painel.php?status=sucesso');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            header('Location: ../app/painel.php?status=invalido');
            exit;
        }
        }
}