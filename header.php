<?php 
// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detectar a página atual
$pagina_atual = basename($_SERVER['PHP_SELF']);
$e_pagina_login = ($pagina_atual == 'login.php');
$e_pagina_protegido = ($pagina_atual == 'protegido.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carros e Lances</title>
  <link rel="stylesheet" href="./css/Home.css">
  <link rel="stylesheet" href="./css/Filtros.css">
  <link rel="stylesheet" href="./css/styleindex.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <header class="site-header">
    <div class="logo-container">
      <a href="index.php">
        <img src="img/logo-cars.png" alt="Logo Carros e Lances" />
      </a>
      <h1>Carros e Lances</h1>
    </div>
    

    <div class="user-info">
      <?php if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true): ?>
        <img id="profile-pic" src="img/avatar-user.png" alt="Avatar do Usuário" />
        <span id="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <span id="user-saldo">Saldo: R$<?php echo number_format($_SESSION['saldo'], 2, ',', '.'); ?></span>
        <?php if(!$e_pagina_protegido): ?>
        <a href="protegido.php" class="btn">Minha Área</a>
        <?php endif; ?>
        <a href="logout.php" class="logout-btn">Sair</a>
      <?php else: ?>
        <span id="user-name">Visitante</span>
        <?php if(!$e_pagina_login): ?>
        <a href="login.php" class="login-btn">Entrar</a>
        <?php endif; ?>
      <?php endif; ?>
    </div>

  </header>
  
  <main>