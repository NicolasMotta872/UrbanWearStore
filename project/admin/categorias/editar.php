<?php
require_once '../../includes/admin-header.php';

// Verificar se o ID da categoria foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "ID de categoria inválido";
    $_SESSION['flash_type'] = "danger";
    header('Location: /project/admin/index.php');
    exit;
}

$categoria_id = $_GET['id'];

// Buscar categoria
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$categoria_id]);
$categoria = $stmt->fetch();

// Se a categoria não existe, redirecionar
if (!$categoria) {
    $_SESSION['flash_message'] = "Categoria não encontrada";
    $_SESSION['flash_type'] = "danger";
    header('Location: /project/admin/index.php');
    exit;
}

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    
    $erro = false;
    $mensagens = [];
    
    // Validações
    if (empty($nome)) {
        $erro = true;
        $mensagens[] = "O nome da categoria é obrigatório";
    }
    
    // Se não há erros, atualizar categoria
    if (!$erro) {
        try {
            $stmt = $pdo->prepare("UPDATE categorias SET nome = ? WHERE id = ?");
            $stmt->execute([$nome, $categoria_id]);
            
            $_SESSION['flash_message'] = "Categoria atualizada com sucesso!";
            $_SESSION['flash_type'] = "success";
            
            header("Location: /project/admin/categorias/index.php");
            exit;
        } catch (PDOException $e) {
            $erro = true;
            $mensagens[] = "Erro ao atualizar categoria: " . $e->getMessage();
        }
    }
}
?>

<div class="admin-header">
    <h1 class="admin-title">Editar Categoria</h1>
    <a href="http://localhost/project/admin/categorias/index.php" class="btn btn-secondary">Voltar</a>
</div>

<?php if (isset($mensagens) && !empty($mensagens)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($mensagens as $mensagem): ?>
                <li><?= $mensagem ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-form">
    <form action="" method="post">
        <div class="form-group">
            <label for="nome" class="form-label">Nome da Categoria *</label>
            <input type="text" id="nome" name="nome" class="form-control" required value="<?= $categoria['nome'] ?>">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Atualizar Categoria</button>
        </div>
    </form>
</div>

<?php require_once '../../includes/admin-footer.php'; ?>