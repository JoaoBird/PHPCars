<?php
session_start();
include_once './usuarios.php';

// Verificar se já está logado
if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    header('Location: index.php');
    exit;
}

$erro = '';

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Buscar usuário
    $usuario = buscarUsuario($username);
    
    if ($usuario && password_verify($password, $usuario['password'])) {
        // Login bem-sucedido
        $_SESSION['usuario_logado'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['saldo'] = $usuario['saldo'];
        
        header('Location: index.php');
        exit;
    } else {
        $erro = 'Usuário ou senha incorretos.';
    }
}

include_once 'header.php';
?>
<link rel="stylesheet" href="./css/Login.css">
<div class="login-container">
    <div class="login-box">
        <h2>Login</h2>
        
        <?php if ($erro): ?>
            <div class="login-error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <form method="post" action="login.php" class="login-form">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Entrar</button>
    </form>
</div>

<?php include_once 'footer.php'; ?>