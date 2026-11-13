<?php

session_start();

$base = '../';

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

// 1. Se o usuário enviou o formulário de filtro via GET
if (isset($_GET['data_inicio']) || isset($_GET['data_fim']) || isset($_GET['tipo_filtro'])) {
    $_SESSION['data_inicio'] = $_GET['data_inicio'] ?? date('Y-m-01');
    $_SESSION['data_fim'] = $_GET['data_fim'] ?? date('Y-m-t');
    $_SESSION['tipo_filtro'] = $_GET['tipo_filtro'] ?? 'todos';
}

// 2. Recupera da SESSÃO (ou define o padrão para o primeiro acesso)
$data_inicio = $_SESSION['data_inicio'] ?? date('Y-m-01');
$data_fim = $_SESSION['data_fim'] ?? date('Y-m-t');
$tipo_filtro = $_SESSION['tipo_filtro'] ?? 'todos';

try {
    // 1. Lista de categorias (para o select da edição)
    $sql = "SELECT id, nome FROM categorias ORDER BY nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Movimentações do usuário no período, já com o nome da categoria
    $sql = "SELECT t.id, t.descricao, t.valor, t.tipo, t.data_transacao, t.categoria_id,
                   c.nome AS categoria_nome
            FROM transacoes t
            LEFT JOIN categorias c ON c.id = t.categoria_id
            WHERE t.usuario_id = :usuario_id
            AND t.data_transacao BETWEEN :data_inicio AND :data_fim";
    $parametros = [
        'usuario_id'  => $usuarioId,
        'data_inicio' => $data_inicio,
        'data_fim'    => $data_fim,
    ];

    // 3. Se escolheu um tipo, acrescenta o filtro (na consulta e na lista)
    if (in_array($tipo_filtro, ['entrada', 'saida', 'investimento'])) {
        $sql .= " AND t.tipo = :tipo";
        $parametros['tipo'] = $tipo_filtro;
    }

    $sql .= " ORDER BY t.data_transacao DESC, t.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);
    $transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $mensagem = "Erro ao carregar o extrato.";
    $categorias = [];
    $transacoes = [];
}

if (isset($_GET['baixar'])) {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="extrato.json"');
    echo json_encode($transacoes);
    exit;
}

?>

<?php require '../templates/head.php' ?>

<!-- CSS -->
<link rel="stylesheet" href="../css/style.css">

<title>Extrato - Diário Financeiro</title>
</head>

<body class="body-layout">

    <!-- HEADER -->
    <?php require '../templates/header_logado.php' ?>

    <main>

        <!-- Mensagem Toast -->
        <?php require '../templates/toast.php' ?>

        <!-- Extrato das movimentações -->
        <section id="extrato" class="container" data-aos="fade-right">

            <div class="layout-display menu-section">
                <div class="menu-section-titulo">
                    <h2 class="titulo-section">Extrato Financeiro</h2>
                </div>

                <div class="menu-section-btn">
                    <a href="?baixar=json" class="btn btn-primario"><i class="fa-solid fa-file-export"></i> JSON</a>
                </div>
            </div>

            <!-- Filtro de pesquisa -->
            <div class="card">

                <form action="extrato.php" method="get" class="form-filtro">

                    <div class="form-grupo">
                        <label for="tipo_filtro">Tipo:</label>
                        <select name="tipo_filtro" id="tipo_filtro">
                            <option value="todos" <?= $tipo_filtro === 'todos' ? 'selected' : '' ?>>Todos</option>
                            <option value="entrada" <?= $tipo_filtro === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                            <option value="saida" <?= $tipo_filtro === 'saida' ? 'selected' : '' ?>>Saída</option>
                            <option value="investimento" <?= $tipo_filtro === 'investimento' ? 'selected' : '' ?>>Investimento</option>
                        </select>
                    </div>

                    <div class="form-grupo">
                        <label for="data_inicio">De:</label>
                        <input type="date" name="data_inicio" id="data_inicio" value="<?= $data_inicio ?>">
                    </div>

                    <div class="form-grupo">
                        <label for="data_fim">Até:</label>
                        <input type="date" name="data_fim" id="data_fim" value="<?= $data_fim ?>">
                    </div>

                    <div>
                        <button type="submit" class="btn btn-form" title="Filtrar"><i class="fa-solid fa-filter"></i></button>
                    </div>

                </form>
            </div>

            <div class="layout-display">

                <table class="movimentacoes">
                    <thead>
                        <tr class="movimentacao movimentacao-extrato" data-aos="fade-right">

                            <td class="titulo-table" colspan="2">
                                <p>
                                    Mostrando dados de <?= date("d/m/Y", strtotime($data_inicio)) ?> até <?= date('d/m/Y', strtotime($data_fim)) ?>.
                                    <br>
                                    <?= count($transacoes) ?> registro(s) encontrado(s).
                                </p>
                            </td>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (!empty($transacoes)): ?>

                            <?php foreach ($transacoes as $transacao): ?>

                                <tr class="movimentacao movimentacao-extrato" data-aos="fade-right">
                                    <td class="titulo">
                                        <h3><?= htmlspecialchars($transacao['descricao'], ENT_QUOTES, 'UTF-8') ?></h3>
                                        <p><?= date('d/m/Y', strtotime($transacao['data_transacao'])) ?></p>
                                    </td>
                                    <td class="categoria">
                                        <?php if ($transacao['tipo'] === 'entrada'): ?>
                                            <p class="saldo-positivo">Entrada</p>
                                        <?php elseif ($transacao['tipo'] === 'investimento'): ?>
                                            <p class="saldo-investimento">Investimento</p>
                                        <?php else: ?>
                                            <p class="saldo-negativo">Saída</p>
                                        <?php endif; ?>
                                        <br>
                                        <p class="tipo-categoria"><?= htmlspecialchars($transacao['categoria_nome'] ?? 'Sem categoria') ?></p>
                                    </td>
                                    <td class="valor">
                                        <?php if ($transacao['tipo'] === 'saida'): ?>
                                            <p>- R$ <?= number_format($transacao['valor'], 2, ',', '.') ?></p>
                                        <?php else: ?>
                                            <p>R$ <?= number_format($transacao['valor'], 2, ',', '.') ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="opcoes">
                                        <button type="button" title="Editar" onclick="document.getElementById('editar-lancamento-<?= $transacao['id'] ?>').showModal()" class="btn btn-editar"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <dialog id="editar-lancamento-<?= $transacao['id'] ?>">

                                            <div class="menu-dialog">
                                                <h3>Editar Lançamento</h3>
                                                <button type="button" onclick="document.getElementById('editar-lancamento-<?= $transacao['id'] ?>').close()" class="btn btn-primario btn-cancelar"><i class="fa-solid fa-xmark"></i></button>
                                            </div>

                                            <form action="<?= $base ?>process/editar_transacao.php" method="post" class="form-dialog">

                                                <input type="hidden" name="id" value="<?= (int) $transacao['id'] ?>">

                                                <div class="form-grupo">
                                                    <label for="descricao-<?= (int) $transacao['id'] ?>">Descrição:</label>
                                                    <input type="text" name="descricao" id="descricao-<?= (int) $transacao['id'] ?>" value="<?= htmlspecialchars($transacao['descricao'], ENT_QUOTES, 'UTF-8') ?>" required>
                                                </div>

                                                <div class="form-grupo">
                                                    <label for="valor-<?= (int) $transacao['id'] ?>">Valor:</label>
                                                    <input type="number" name="valor" id="valor-<?= (int) $transacao['id'] ?>" step="0.01" min="0.01" value="<?= $transacao['valor'] ?>" required>
                                                </div>

                                                <div class="form-grupo">
                                                    <label for="tipo-<?= (int) $transacao['id'] ?>">Tipo de Transação:</label>
                                                    <select name="tipo" id="tipo-<?= (int) $transacao['id'] ?>" required>
                                                        <option value="entrada" <?= $transacao['tipo'] === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                                                        <option value="saida" <?= $transacao['tipo'] === 'saida' ? 'selected' : '' ?>>Saída</option>
                                                        <option value="investimento" <?= $transacao['tipo'] === 'investimento' ? 'selected' : '' ?>>Investimento</option>
                                                    </select>
                                                </div>

                                                <div class="form-grupo">
                                                    <label for="categoria-<?= (int) $transacao['id'] ?>">Categoria:</label>
                                                    <select name="categoria_id" id="categoria-<?= (int) $transacao['id'] ?>">
                                                        <option value="">Sem categoria</option>
                                                        <?php foreach ($categorias as $categoria): ?>
                                                            <option value="<?= $categoria['id'] ?>" <?= $categoria['id'] == $transacao['categoria_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($categoria['nome']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="form-grupo">
                                                    <label for="data-<?= (int) $transacao['id'] ?>">Data:</label>
                                                    <input type="date" name="data_transacao" id="data-<?= (int) $transacao['id'] ?>" value="<?= $transacao['data_transacao'] ?>" required>
                                                </div>

                                                <div class="form-opcoes">
                                                    <button type="submit" class="btn btn-form">Salvar</button>
                                                </div>
                                            </form>

                                        </dialog>

                                        <button type="button" title="Excluir" class="btn btn-excluir"
                                            onclick="document.getElementById('excluir-lancamento-<?= $transacao['id'] ?>').showModal()">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>

                                        <dialog id="excluir-lancamento-<?= $transacao['id'] ?>">
                                            <div class="menu-dialog">
                                                <h3>Excluir transação?</h3>
                                                <button type="button" onclick="document.getElementById('excluir-lancamento-<?= $transacao['id'] ?>').close()" class="btn btn-primario" title="Fechar"><i class="fa-solid fa-xmark"></i></button>

                                            </div>
                                            <form method="post" action="<?= $base ?>process/excluir_transacao.php">
                                                <input type="hidden" name="id" value="<?= $transacao['id'] ?>">

                                                <div class="form-opcoes">
                                                    <button type="submit" class="btn btn-excluir">Sim, excluir</button>
                                                </div>
                                            </form>
                                        </dialog>

                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>
                            <tr class="movimentacao" data-aos="fade-right">
                                <td class="titulo">
                                    <p>Ainda não há movimentações nesse período.</p>
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