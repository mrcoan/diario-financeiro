<?php

session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$nomeUsuario = $_SESSION['usuario_nome'];

$mensagem = '';

if (isset($_GET['status'])) {
    $status = $_GET['status'] ?? '';

    switch ($status) {
        case 'sucesso':
            $mensagem = "Registro incluído com sucesso!";
            break;

        case 'excluir_sucesso':
            $mensagem = "Registro excluído com sucesso!";
            break;

        case 'editar_sucesso':
            $mensagem = "Alteração realizada com sucesso!";
            break;

        case 'erro':
            $mensagem = "Não foi possível realizar a solicitação.";
            break;

        default:
            $mensagem = "Inválido.";
            break;
    }
}

// verificar se a requsição veio via POST
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
                header('location: processar_lancamento.php?status=sucesso');
                exit;
            }
        } catch (PDOException $e) { // Mostra o erro exato na tela em vez de redirecionar
            error_log($e->getMessage());
            header('Location: processar_lancamento.php?status=invalido');
            exit;
        }
    } else {
        // dados invalidos
        header('Location: processar_lancamento.php?status=invalido');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processar Lançamento - Diário Financeiro</title>

    <!-- ícone na barra do navegador -->
    <link rel="shortcut icon" href="../img/logo.ico" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>

    <!-- HEADER -->
    <header class="navbar">
        <!-- Logo -->
        <div class="logo-header">
            <a href="../painel.php"><img src="../img/logo.png" alt="Diario Financeiro Logo"></a>
        </div>

        <!-- nav menu -->
        <div class="nav-menu">
            <a href="processar_lancamento.php">Incluir Lançamento</a>
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
        <p>Inclua lançamentos para seu controle financeiro.</p>
    </section>

    <hr>

    <section id="lancamentos">



        <div class="layout-display">

            <!-- Lançamentos -->
            <div class="card-painel">

                <?php if (!empty($mensagem)): ?>
                    <span class="mensagem"><?php echo $mensagem ?></span>
                <?php endif; ?>

                <h3>Lançamento de movimentação</h3>
                <br>

                <form action="processar_lancamento.php" method="post">

                    <div class="form-grupo">
                        <label for="descricao">Descrição:</label>
                        <input type="text" name="descricao" id="descricao" placeholder="Ex: Supermercado" required>
                    </div>

                    <div class="form-grupo">
                        <label for="valor">Valor:</label>
                        <input type="number" name="valor" id="valor" step="0.01" min="0.01" placeholder="0,00" required>
                    </div>

                    <div class="form-grupo">
                        <label for="tipo">Tipo de Transação:</label>
                        <div class="opcoes-radio">
                            <div><label class="saldo-positivo"><input type="radio" name="tipo" value="entrada" required> Entrada</label></div>
                            <div><label class="saldo-negativo"><input type="radio" name="tipo" value="saida" required> Saída</label></div>
                            <div><label class="saldo-investimento"><input type="radio" name="tipo" value="investimento" required> Investimento</label></div>
                        </div>
                    </div>

                    <!-- Categoria - Em desenvolvimento
                    <div class="form-grupo">
                        <label for="categoria">Categoria (opcional):</label>
                        <select name="categoria_id" id="categoria">
                            <option value="">Sem categoria</option>

                            <optgroup label="ENTRADAS">
                                <option value="1">Salário / Proventos</option>
                                <option value="2">Renda Extra / Freelance</option>
                                <option value="3">Venda de Itens</option>
                            </optgroup>

                            <optgroup label="SAÍDAS">
                                <option value="4">Alimentação</option>
                                <option value="5">Moradia</option>
                                <option value="6">Transporte</option>
                                <option value="7">Saúde</option>
                                <option value="8">Lazer & Estilo de Vida</option>
                            </optgroup>

                            <optgroup label="INVESTIMENTOS">
                                <option value="9">Reserva de Emergência</option>
                                <option value="10">Renda Fixa / CDB</option>
                                <option value="11">Ações / Renda Variável</option>
                            </optgroup>
                        </select>
                    </div>
                    -->

                    <div class="form-grupo">
                        <label for="data">Data:</label>
                        <input type="date" name="data" id="data" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="opcoes">
                        <button type="submit" class="btn-form" title="Adicionar"><img src="../img/add.png" alt="Adicionar lançamento"></button>
                    </div>

                </form>

            </div>

        </div>

    </section>

</body>

</html>