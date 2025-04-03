<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carros e Lances</title>
  <script src="Home.js" defer></script>
  <link rel="stylesheet" href="Home.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <header class="site-header">
    <div class="logo-container">
      <a href="#">
        <img src="#" alt="Logo" />
      </a>
      <h1>Carros e Lances</h1>
    </div>
    <div>
      <div id="filter-area">
        <input type="text" id="filter-input" placeholder="Pesquisar...">
      </div>
    </div>

    <div class="user-info">
      <img id="profile-pic" src="#" alt="Avatar" style="display: none;" />
      <button id="open-login-modal" class="login-btn">Entrar/Cadastrar</button>
      <button id="logout-btn" class="logout-btn" style="display: none;">Sair</button>
    </div>
  </header>
  
  <!-- Modal de Login -->
  <div id="login-modal" class="modal">
    <div class="modal-content">
      <span class="close-button" id="close-modal">&times;</span>
      <h2 id="modal-title">Entrar</h2>
      <input type="text" id="usernameInput" placeholder="Usuário" />
      <input type="password" id="passwordInput" placeholder="Senha" />
      <button id="actionButton">Entrar</button>
      <p id="toggleText">Não tem conta? <span style="cursor: pointer; color: #4CAF50;" id="toggleLink">Cadastre-se</span></p>
    </div>
  </div>

  <main>
    <div class="grid-container">
      <!-- Caixas serão carregadas dinamicamente aqui -->
    </div>
  </main>

  <footer>
    <p>&copy; 2025 Carros e Lances </p>
  </footer>
</body>
</html>
