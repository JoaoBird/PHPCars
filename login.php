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
        $_SESSION['user_id'] = md5($username); 
        
        header('Location: index.php');
        exit;
    } else {
        $erro = 'Usuário ou senha incorretos.';
    }
}

include_once 'header.php';
?>

<style>

</style>
<link rel="stylesheet" href="./css/Login.css">

<div class="login-container">
    <!-- Elementos decorativos -->
    <div class="decoration decoration-1"></div>
    <div class="decoration decoration-2"></div>
    
    <div class="login-card">
        <!-- Logo opcional - substitua pelo seu logo -->
        <div class="login-logo">
            <img src="img/car-logo.png" alt="Carros e Lances" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZ2h0PSI4MCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM3NzQ5RjgiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cmVjdCB4PSIzIiB5PSI2IiB3aWR0aD0iMTgiIGhlaWdodD0iNyIgcnk9IjEiPjwvcmVjdD48Y2lyY2xlIGN4PSI3IiBjeT0iMTciIHI9IjIiPjwvY2lyY2xlPjxjaXJjbGUgY3g9IjE3IiBjeT0iMTciIHI9IjIiPjwvY2lyY2xlPjxwYXRoIGQ9Ik0zIDE3aDQuMm01LjggMGg0LjIiPjwvcGF0aD48cGF0aCBkPSJNMTQgNmwtMy0zSDRNMTQgNmg3TTcgMTN2NC4yTTE0IDEzdjQuMiI+PC9wYXRoPjwvc3ZnPg==';">
        </div>
        
        <h2 class="login-title">Login</h2>
        
        <?php if ($erro): ?>
            <div class="login-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="login.php" class="login-form">
            <div class="form-group username">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" placeholder="Digite seu nome de usuário" required>
            </div>
            
            <div class="form-group password">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
            </div>
            
            <button type="submit" class="login-btn">Entrar</button>
        </form>
        
        <div class="login-links">
        </div>
    </div>
</div>

<script>
// Efeito de animação nos campos de input
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.login-form input');
    
    inputs.forEach(input => {
        // Adiciona classe quando o campo ganha foco
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        // Remove classe quando o campo perde foco e está vazio
        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Verifica se o campo já tem valor ao carregar
        if (input.value !== '') {
            input.parentElement.classList.add('focused');
        }
    });
});
</script>

<?php include_once 'footer.php'; ?>