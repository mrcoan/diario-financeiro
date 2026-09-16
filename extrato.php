<?php

session_start();
require_once 'config/conexao.php';

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

// 1. Se o usuário enviou o formulário de filtro via GET
if (isset($_GET['data_inicio']) || isset($_GET['data_fim']) || isset($_GET['tipo_filtro'])) {
    $_SESSION['data_inicio'] = $_GET['data_inicio'] ?? date('Y-m-01');
    $_SESSION['data_fim'] = $_GET['data_fim'] ?? date('Y-m-t');
    $_SESSION['tipo_filtro'] = $_GET['tipo_filtro'] ?? 'todos';
}

// 2. Recupera da SESSÃO (ou define o padrão para o primeiro acesso)
$data_inicio = $_SESSION['data_inicio'] ?? date('Y-m-01');
$data_fim    = $_SESSION['data_fim']    ?? date('Y-m-t');
$tipo_filtro = $_SESSION['tipo_filtro'] ?? 'todos';

try {
    // buscar todas as movimentações do usuário
    $sql = "SELECT * FROM transacoes
            WHERE usuario_id = :usuario_id
            AND data_transacao BETWEEN :data_inicio AND :data_fim";
    if ($tipo_filtro === 'entrada' || $tipo_filtro === 'saida' || $tipo_filtro === 'investimento') {
        $sql .= " AND tipo = :tipo";
    }
    $sql .= " ORDER BY data_transacao DESC, id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
    $stmt->bindValue(':data_inicio', $data_inicio);
    $stmt->bindValue(':data_fim', $data_fim);
    if ($tipo_filtro === 'entrada' || $tipo_filtro === 'saida' || $tipo_filtro === 'investimento') {
        $stmt->bindValue(':tipo', $tipo_filtro);
    }
    $stmt->execute();
    $transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $mensagem = "Erro ao carregar o extrato:";
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrato - Diário Financeiro</title>

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
        <p>Consulte e filtre todo o seu histórico de lançamentos.</p>
    </section>

    <hr>

    <section id="filtro-extrato">

        <div class="layout-display">

            <!-- Lançamentos -->
            <div class="card-painel">

                <h3>Lançamento de movimentação</h3>
                <br>

                <form action="process/processar_lancamento.php" method="post">

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
                            <label class="saldo-positivo"><input type="radio" name="tipo" value="entrada" required> Entrada</label>
                            <label class="saldo-negativo"><input type="radio" name="tipo" value="saida" required> Saída</label>
                            <label class="saldo-investimento"><input type="radio" name="tipo" value="investimento" required> Investimento</label>
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
                        <button type="submit" class="btn-form" title="Adicionar"><img src="img/add.png" alt="Adicionar lançamento"></button>
                    </div>

                </form>

            </div>

            <!-- Filtro de pesquisa -->
            <div class="card-painel">

                <h3>Filtro de pesquisa</h3>
                <br>

                <form action="extrato.php" method="get">

                    <div class="form-grupo">
                        <label for="tipo_filtro">Tipo:</label>
                        <select name="tipo_filtro" id="tipo_filtro">
                            <option value="todos" <?= $tipo_filtro === 'todos' ? 'selected' : '' ?>>Todos</option>
                            <option value="entrada" <?= $tipo_filtro === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                            <option value="saida" <?= $tipo_filtro === 'saida' ? 'selected' : '' ?>>Saída</option>
                            <option value="investimento" <?= $tipo_filtro === 'investimento' ? 'selected' : '' ?>>Investimento</option>
                        </select>
                    </div>

                    <!-- Categoria - Em desenvolvimento
                    <div class="form-grupo">
                        <label for="categoria_filtro">Categoria:</label>
                        <select name="categoria_filtro" id="tipo_filtro">
                            <option value="todos" <?= $categoria_filtro === 'todos' ? 'selected' : '' ?>></option>
                        </select>
                    </div>
                    -->

                    <div class="form-grupo">
                        <label for="data_inicio">De:</label>
                        <input type="date" name="data_inicio" id="data_inicio" value="<?= $data_inicio ?>">
                    </div>

                    <div class="form-grupo">
                        <label for="data_fim">Até:</label>
                        <input type="date" name="data_fim" id="data_fim" value="<?= $data_fim ?>">
                    </div>

                    <div class="opcoes">
                        <button type="submit" class="btn-form" title="Filtrar"><img src="img/filter-icon.png" alt="Filtrar"></button>
                    </div>

                </form>
            </div>

        </div>
    </section>

    <hr>

    <?php if (!empty($mensagem)): ?>
        <span class="mensagem"><?php echo $mensagem ?></span>
    <?php endif; ?>

    <!-- Extrato das movimentações -->
    <section id="extrato">

        <h2>Extrato Financeiro</h2>
        <span>Mostrando dados de <?= date("d/m/Y", strtotime($data_inicio)) ?> até <?= date('d/m/Y', strtotime($data_fim)) ?></span>
        <span><?= count($transacoes) ?> registro(s) encontrado(s).</span>

        <div class="layout-display-extrato">

            <?php if (!empty($transacoes)): ?>

                <?php foreach ($transacoes as $transacao): ?>

                    <div class="card-painel-extrato">
                        <div>
                            <?php if ($transacao['tipo'] === 'entrada'): ?>
                                <h3 class="saldo-positivo"><?= htmlspecialchars($transacao['descricao'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <?php elseif ($transacao['tipo'] === 'investimento'): ?>
                                <h3 class="saldo-investimento"><?= htmlspecialchars($transacao['descricao'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <?php else: ?>
                                <h3 class="saldo-negativo"><?= htmlspecialchars($transacao['descricao'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <?php endif; ?>
                            <span>Valor: <strong>R$ <?= number_format($transacao['valor'], 2, ',', '.') ?></strong></span>
                            <span>Data: <strong><?= date('d/m/Y', strtotime($transacao['data_transacao'])) ?></strong></span>
                            <span>Categoria: <strong>Em desenvolvimento</strong></span>
                        </div>
                        <div class="opcoes">
                            <a title="Editar" href="process/editar_transacao.php?id=<?= $transacao['id'] ?>" class="btn-editar"><img src="img/edit.png" alt="Editar transação"></a>
                            <a title="Excluir" href="process/excluir_transacao.php?id=<?= $transacao['id'] ?>" class="btn-excluir"><img src="img/delete.png" alt="Excluir transação"></a>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php else: ?>
                <span>Ainda não há movimentações.</span>
            <?php endif; ?>

        </div>
    </section>

</body>

</html>