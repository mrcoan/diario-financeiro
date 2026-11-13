<?php

session_start();

$base = '../';

require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$data_inicio = date('Y-m-01');

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

    // Listar as categorias
    $stmt = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome = 'Outros', nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    $sqlMov = "SELECT t.id, t.descricao, t.valor, t.tipo, t.data_transacao, c.nome AS categoria_nome
            FROM transacoes t
            LEFT JOIN categorias c ON c.id = t.categoria_id
            WHERE t.usuario_id = :usuarioId
            AND MONTH(t.data_transacao) = MONTH(CURRENT_DATE())
            AND YEAR(t.data_transacao) = YEAR(CURRENT_DATE())
            ORDER BY t.data_transacao DESC, t.id DESC
            LIMIT 5";

    $stmtMov = $pdo->prepare($sqlMov);
    $stmtMov->bindValue(':usuarioId', $usuarioId, PDO::PARAM_INT);
    $stmtMov->execute();
    $ultimasMovimentacoes = $stmtMov->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $mensagem = "Erro ao carregar dados:";
    exit;
}

?>

<?php require '../templates/head.php' ?>

<!-- CSS -->
<link rel="stylesheet" href="<?= $base ?>css/style.css">

<title>Painel - Diário Financeiro</title>

</head>

<body class="body-layout">

    <!-- HEADER -->
    <?php require '../templates/header_logado.php' ?>

    <main>

        <!-- Mensagem Toast -->
        <?php require '../templates/toast.php' ?>

        <!-- Mensagem de boas-vindas -->
        <section id="boas-vindas" class="container" data-aos="fade-right">
            <h1>Olá, <?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?>!</h1>
            <p>Acompanhe suas movimentações.</p>
        </section>

        <hr>

        <!-- Resumo mensal -->
        <section id="resumo-mensal" class="container" data-aos="fade-right">

            <div class="layout-display menu-section">
                <div class="menu-section-titulo">
                    <h2 class="titulo-section">Resumo de <?= date("m/Y", strtotime($data_inicio)) ?></h2>
                </div>

                <!-- Botão Novo Lançamento -->
                <div class="menu-section-btn">
                    <button onclick="document.getElementById('lancamento').showModal()" class="btn btn-secundario" title="Novo lançamento"><i class="fa-solid fa-plus"></i> Novo lançamento</button>

                    <dialog id="lancamento">

                        <div class="menu-dialog">
                            <h3>Novo Lançamento</h3>
                            <button type="button" onclick="document.getElementById('lancamento').close()" class="btn btn-primario" title="Fechar"><i class="fa-solid fa-xmark"></i></button>
                        </div>

                        <form action="<?= $base ?>process/processar_lancamento.php" method="post" class="form-dialog">

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
                                <select name="tipo" id="tipo" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="entrada">Entrada</option>
                                    <option value="saida">Saída</option>
                                    <option value="investimento">Investimento</option>
                                </select>
                            </div>

                            <div class="form-grupo">
                                <label for="categoria_id">Categoria:</label>
                                <select name="categoria_id" id="categoria_id">
                                    <option value="" selected>Sem categoria</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-grupo">
                                <label for="data">Data:</label>
                                <input type="date" name="data" id="data" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="form-opcoes">
                                <button type="submit" class="btn btn-form" title="Adicionar">Adicionar</button>
                            </div>

                        </form>

                    </dialog>
                </div>
            </div>

            <div class="layout-display display-resumo">

                <div class="card card-destaque">
                    <h3 class="titulo-card saldo-atual">Saldo Atual</h3>
                    <p class="subtitulo-card">Valor líquido do mês:</p>
                    <p class="valor-card">R$ <?= number_format($saldo, 2, ',', '.') ?></p>
                </div>

                <div class="card">
                    <h3 class="titulo-card saldo-positivo">Entradas</h3>
                    <p class="subtitulo-card">Valor das receitas:</p>
                    <p class="valor-card">R$ <?= number_format($entradas, 2, ',', '.') ?></p>
                </div>

                <div class="card">
                    <h3 class="titulo-card saldo-negativo">Saídas</h3>
                    <p class="subtitulo-card">Valor dos gastos:</p>
                    <p class="valor-card">R$ <?= number_format($saidas, 2, ',', '.') ?></p>
                </div>

                <div class="card">
                    <h3 class="titulo-card saldo-investimento">Investimentos</h3>
                    <p class="subtitulo-card">Valor investido:</p>
                    <p class="valor-card">R$ <?= number_format($investimentos, 2, ',', '.') ?></p>
                </div>

            </div>

        </section>

        <hr>

        <!-- Últimas Movimentações -->
        <section id="ultimas-movimentacoes" class="container" data-aos="fade-right">

            <div class="layout-display menu-section">
                <div class="menu-section-titulo">
                    <h2 class="titulo-section">Últimas Movimentações</h2>
                </div>

                <div class="menu-section-btn">
                    <a href="extrato.php" class="btn btn-primario"><i class="fa-solid fa-sheet-plastic"></i> Extrato completo</a>
                </div>
            </div>

            <div class="layout-display">

                <table class="movimentacoes">
                    <thead>
                        <tr class="movimentacao movimentacao-resumo" data-aos="fade-right">

                            <td class="titulo-table" colspan="2">
                                <p>Últimas movimentações de <?= date("m/Y", strtotime($data_inicio)) ?>. Mostrando <?= count($ultimasMovimentacoes) ?> de até 5 registros.</p>
                            </td>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (!empty($ultimasMovimentacoes)): ?>
                            <?php foreach ($ultimasMovimentacoes as $movimentacao): ?>
                                <tr class="movimentacao movimentacao-resumo" data-aos="fade-right">
                                    <td class="titulo">
                                        <h3><?= htmlspecialchars($movimentacao['descricao'], ENT_QUOTES, 'UTF-8') ?></h3>
                                        <p><?= date('d/m/Y', strtotime($movimentacao['data_transacao'])) ?></p>
                                    </td>
                                    <td class="categoria">
                                        <?php if ($movimentacao['tipo'] === 'entrada'): ?>
                                            <p class="saldo-positivo">Entrada</p>
                                        <?php elseif ($movimentacao['tipo'] === 'investimento'): ?>
                                            <p class="saldo-investimento">Investimento</p>
                                        <?php else: ?>
                                            <p class="saldo-negativo">Saída</p>
                                        <?php endif; ?>
                                        <br>
                                        <p class="tipo-categoria"><?= $movimentacao['categoria_nome'] ?? 'Sem categoria' ?></p>

                                    </td>
                                    <td class="valor">
                                        <?php if ($movimentacao['tipo'] === 'saida'): ?>
                                            <p>- R$ <?= number_format($movimentacao['valor'], 2, ',', '.') ?></p>
                                        <?php else: ?>
                                            <p>R$ <?= number_format($movimentacao['valor'], 2, ',', '.') ?></p>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>
                            <tr class="movimentacao" data-aos="fade-right">
                                <td class="titulo">
                                    <p>Ainda não há movimentações nesse mês.</p>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>

            </div>

        </section>

    </main>

    <!-- Footer -->
    <?php require '../templates/footer.php' ?>

    <!-- Javascript -->
    <?php require '../templates/bibliotecas_js.php' ?>

</body>

</html>