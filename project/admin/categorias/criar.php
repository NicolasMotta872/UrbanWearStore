<?php
require_once '../../includes/admin-header.php';

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
    
    // Se não há erros, inserir categoria
    if (!$erro) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
            $stmt->execute([$nome]);
            
            $_SESSION['flash_message'] = "Categoria criada com sucesso!";
            $_SESSION['flash_type'] = "success";
            
            header("Location: /project/admin/categorias/index.php");
            exit;
        } catch (PDOException $e) {
            $erro = true;
            $mensagens[] = "Erro ao criar categoria: " . $e->getMessage();
        }
    }
}
?>

<div class="admin-header">
    <h1 class="admin-title">Nova Categoria</h1>
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
            <input type="text" id="nome" name="nome" class="form-control" required value="<?= isset($nome) ? $nome : '' ?>">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Salvar Categoria</button>
        </div>
    </form>
</div>

<?php require_once '../../includes/admin-footer.php'; ?>