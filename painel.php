<?php

session_start();
require_once 'config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$data_inicio = $_SESSION['data_inicio'] ?? date('Y-m-01');

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['status'])) {
    $status = $_GET['status'] ?? '';

    switch ($status) {
        case 'sucesso':
            $mensagem = "Registro incluído com sucesso!";
            break;

        case 'invalido':
            $mensagem = "Registro não incluído";
            break;

        default:
            $mensagem = "Inválido.";
            break;
    }
}

$usuarioId = $_SESSION['usuario_id'];
$nomeUsuario = $_SESSION['usuario_nome'];

try {
    // 1. Soma TOTAL de ENTRADAS
    $sqlEntradas = "SELECT SUM(valor) AS total FROM transacoes WHERE usuario_id = :usuario_id AND tipo = 'entrada' AND MONTH(data_transacao) = MONTH(CURRENT_DATE()) AND YEAR(data_transacao) = YEAR(CURRENT_DATE())";
    $stmtEntradas = $pdo->prepare($sqlEntradas);
    $stmtEntradas->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
    $stmtEntradas->execute();
    $resEntradas = $stmtEntradas->fetch(PDO::FETCH_ASSOC);
    $entradas = $resEntradas['total'] ?? 0;

    // 2. Soma TOTAL de SAÍDAS
    $sqlSaidas = "SELECT SUM(valor) AS total FROM transacoes WHERE usuario_id = :usuario_id AND tipo = 'saida' AND MONTH(data_transacao) = MONTH(CURRENT_DATE()) AND YEAR(data_transacao) = YEAR(CURRENT_DATE())";
    $stmtSaidas = $pdo->prepare($sqlSaidas);
    $stmtSaidas->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
    $stmtSaidas->execute();
    $resSaidas = $stmtSaidas->fetch(PDO::FETCH_ASSOC);
    $saidas = $resSaidas['total'] ?? 0;

    // soma TOTAL de INVESTIMENTOS
    $sqlInvestimentos = "SELECT SUM(valor) AS total FROM transacoes WHERE usuario_id = :usuario_id AND tipo = 'investimento' AND MONTH(data_transacao) = MONTH(CURRENT_DATE()) AND YEAR (data_transacao) = YEAR(CURRENT_DATE())";
    $stmtInvestimentos = $pdo->prepare($sqlInvestimentos);
    $stmtInvestimentos->bindValue(":usuario_id", $usuarioId, PDO::PARAM_INT);
    $stmtInvestimentos->execute();
    $resInvestimentos = $stmtInvestimentos->fetch(PDO::FETCH_ASSOC);
    $investimentos = $resInvestimentos['total'] ?? 0;

    // 3. Calcula o SALDO no PHP
    $saldo = $entradas - $saidas - $investimentos;

    // 4. Busca as últimas 5 movimentações (Continua igual!)
    $sqlMov = "SELECT id, descricao, valor, tipo, data_transacao 
               FROM transacoes 
               WHERE usuario_id = :usuario_id
               AND MONTH(data_transacao) = MONTH(CURRENT_DATE())
               AND YEAR(data_transacao) = YEAR(CURRENT_DATE())
               ORDER BY data_transacao DESC, id DESC 
               LIMIT 4";

    $stmtMov = $pdo->prepare($sqlMov);
    $stmtMov->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
    $stmtMov->execute();
    $ultimasMovimentacoes = $stmtMov->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $mensagem = "Erro ao carregar dados:";
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Diário Financeiro</title>

    <!-- ícone na barra do navegador -->
    <link rel="shortcut icon" href="img/logo.ico" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">

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
            <a href="painel.php"><img src="img/logo.png" alt="Diario Financeiro Logo"></a>
        </div>

        <!-- nav menu -->
        <div class="nav-menu">
            <a href="process/processar_lancamento.php">Incluir Lançamento</a>
            <a href="painel.php">Painel</a>
            <a href="extrato.php">Extrato Completo</a>
        </div>

        <!--nav links -->
        <div class="nav-links">
            <a href="config.php" class="btn-primario">Config</a>
            <a href="logout.php" class="btn-secundario">Sair</a>
        </div>
    </header>

    <!-- Mensagem de boas-vindas -->
    <section id="boas-vindas">
        <h1>Olá, <?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p>Acompanhe suas movimentações.</p>
    </section>

    <hr>

    <?php if (!empty($mensagem)): ?>
        <span class="mensagem"><?php echo $mensagem ?></span>
    <?php endif; ?>

    <!-- Resumo mensal -->
    <section id="resumo-financeiro">
        <h2>Resumo Mensal</h2>
        <span>Resumo das movimentações de <?= date("m/Y", strtotime($data_inicio)) ?>.</span>


        <div class="layout-display">
            <div class="card-painel">
                <h3 class="saldo-positivo">Entradas</h3>
                <p>Valor das receitas:</p>
                <span><strong>R$ <?= number_format($entradas, 2, ',', '.') ?></strong></span>
            </div>

            <div class="card-painel">
                <h3 class="saldo-negativo">Saídas</h3>
                <p>Valor dos gastos:</p>
                <span><strong>R$ <?= number_format($saidas, 2, ',', '.') ?></strong></span>
            </div>

            <div class="card-painel">
                <h3 class="saldo-investimento">Investimentos</h3>
                <p>Valor investido:</p>
                <span><strong>R$ <?= number_format($investimentos, 2, ',', '.') ?></strong></span>
            </div>

            <div class="card-painel">
                <h3>Saldo Atual</h3>
                <p>Valor líquido do mês:</p>
                <span><strong>R$ <?= number_format($saldo, 2, ',', '.') ?></strong></span>
            </div>
        </div>
    </section>

    <hr>

    <!-- Últimas Movimentações -->
    <section id="ultimas-movimentacoes">
        <h2>Últimas Movimentações</h2>
        <span>Mostrando dados de <?= date("m/Y", strtotime($data_inicio)) ?>.</span>

        <div class="layout-display">

            <?php if (!empty($ultimasMovimentacoes)): ?>

                <?php foreach ($ultimasMovimentacoes as $movimentacao): ?>

                    <div class="card-painel">

                        <?php if ($movimentacao['tipo'] === 'entrada'): ?>
                            <h3 class="saldo-positivo"><?= htmlspecialchars($movimentacao['descricao'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <?php elseif ($movimentacao['tipo'] === 'investimento'): ?>
                            <h3 class="saldo-investimento"><?= htmlspecialchars($movimentacao['descricao'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <?php else: ?>
                            <h3 class="saldo-negativo"><?= htmlspecialchars($movimentacao['descricao'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <?php endif; ?>
                        <span>Valor: <strong>R$ <?= number_format($movimentacao['valor'], 2, ',', '.') ?></strong></span>
                        <span>Data: <strong><?= date('d/m/Y', strtotime($movimentacao['data_transacao'])) ?></strong></span>
                        <span>Tipo: <strong><?= ucfirst($movimentacao['tipo']) ?></strong></span>
                        <span>Categoria: <strong>Em desenvolvimento</strong></span>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>
                <span>Ainda não há movimentações nesse mês.</span>
            <?php endif; ?>

        </div>
        <div class="opcoes">

            <a href="extrato.php" class="btn-secundario">Extrato completo</a>
            <a href="process/processar_lancamento.php" class="btn-secundario">Novo lançamento</a>
        </div>
    </section>


</body>

</html>