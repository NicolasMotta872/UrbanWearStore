<?php
session_start();

// Destruir todas as variáveis de sessão
$_SESSION = [];

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
header('Location: /project/admin/login.php');
exit;
?>