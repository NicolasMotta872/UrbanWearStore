<?php
require_once '../config/database.php';
session_start();

// Redirecionar se já estiver logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: /project/admin/dashboard.php');
    exit;
}

$erro = null;

// Processar o login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
    
    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos";
    } else {
        // Buscar usuário pelo e-mail
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            
            // Redirecionar para o dashboard
            header('Location: /project/admin/dashboard.php');
            exit;
        } else {
            $erro = "E-mail ou senha incorretos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StoreScobars Admin</title>
    <link rel="stylesheet" href="http://localhost/project/assets/css/admin.css">
    <style>
        body {
            background-color: var(--gray-100);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 32px;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 24px;
        }
        
        .login-logo a {
            color: var(--primary);
            font-size: 24px;
            font-weight: 700;
            text-decoration: none;
        }
        
        .login-title {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <a href="http://localhost/project/">StoreScobars</a>
        </div>
        
        <h1 class="login-title">Acesso ao Painel Admin</h1>
        
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>
        
        <form action="" method="post">
            <div class="form-group">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" id="senha" name="senha" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>
        
        <div style="text-align: center; margin-top: 16px;">
            <a href="http://localhost/project/">Voltar para a loja</a>
        </div>
    </div>
</body>
</html>