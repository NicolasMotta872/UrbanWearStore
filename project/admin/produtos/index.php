<?php
require_once '../../includes/admin-header.php';

$where = "1=1";
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir' && isset($_POST['id'])) {
    $produto_id = (int)$_POST['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->execute([$produto_id]);
        
        $_SESSION['flash_message'] = "Produto excluído com sucesso!";
        $_SESSION['flash_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Erro ao excluir produto: " . $e->getMessage();
        $_SESSION['flash_type'] = "danger";
    }
    
    header("Location: /project/admin/produtos/index.php");
    exit;
}

$query = "
    SELECT p.*, c.nome as categoria_nome 
    FROM produtos p
    JOIN categorias c ON p.categoria_id = c.id
    WHERE $where
    ORDER BY p.nome
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$produtos = $stmt->fetchAll();

?>

<div class="admin-header">
    <h1 class="admin-title">Gerenciar Produtos</h1>
    <a href="http://localhost/project/admin/produtos/criar.php" class="btn btn-primary">Novo Produto</a>
</div>

<!-- Filtros removidos -->

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($produtos) > 0): ?>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= $produto['id'] ?></td>
                        <td><?= $produto['nome'] ?></td>
                        <td><?= $produto['categoria_nome'] ?></td>
                        <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                        <td>
                            <?php if ($produto['estoque'] <= 0): ?>
                                <span class="badge badge-danger">Esgotado</span>
                            <?php elseif ($produto['estoque'] < 5): ?>
                                <span class="badge badge-warning"><?= $produto['estoque'] ?></span>
                            <?php else: ?>
                                <span class="badge badge-success"><?= $produto['estoque'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="action-buttons">
                            <a href="http://localhost/project/admin/produtos/editar.php?id=<?= $produto['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                            <form action="" method="post" style="display: inline-block;">
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm delete-btn">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Nenhum produto encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../../includes/admin-footer.php'; ?>