<?php
require_once '../../includes/admin-header.php';

// Processar exclusão de categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir' && isset($_POST['id'])) {
    $categoria_id = (int)$_POST['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->execute([$categoria_id]);
        
        $_SESSION['flash_message'] = "Categoria excluída com sucesso!";
        $_SESSION['flash_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Erro ao excluir categoria: " . $e->getMessage();
        $_SESSION['flash_type'] = "danger";
    }
    
    header("Location: /admin/categorias/index.php");
    exit;
}

// Buscar todas as categorias
$stmt = $pdo->query("SELECT c.*, COUNT(p.id) as total_produtos FROM categorias c LEFT JOIN produtos p ON c.id = p.categoria_id GROUP BY c.id ORDER BY c.nome");
$categorias = $stmt->fetchAll();
?>

<div class="admin-header">
    <h1 class="admin-title">Gerenciar Categorias</h1>
    <a href="http://localhost/project/admin/categorias/criar.php" class="btn btn-primary">Nova Categoria</a>
</div>

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Produtos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($categorias) > 0): ?>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= $categoria['id'] ?></td>
                        <td><?= $categoria['nome'] ?></td>
                        <td><?= $categoria['total_produtos'] ?></td>
                        <td class="action-buttons">
                            <a href="http://localhost/project/admin/categorias/editar.php?id=<?= $categoria['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                            <form action="" method="post" style="display: inline-block;">
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm delete-btn">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Nenhuma categoria cadastrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../../includes/admin-footer.php'; ?>