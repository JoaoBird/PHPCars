<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carros e Lances</title>
  <script src="CarsHome.js" defer></script>
  <link rel="stylesheet" href="Home.css">
  <link rel="stylesheet" href="Filtros.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <header class="site-header">
    <div class="logo-container">
      <a href="#">
        <img src="img/logo-cars.png" alt="Logo Carros e Lances" />
      </a>
      <h1>Carros e Lances</h1>
    </div>
    <div>
      <div id="filter-area">
        <input type="text" id="filter-input" placeholder="Pesquisar carro...">
      </div>
    </div>

    <div class="user-info">
      <img id="profile-pic" src="img/avatar-user.png" alt="Avatar do Usuário" style="display: none;" />
      <span id="user-name">Guest</span>
      <span id="user-saldo">Saldo: R$0</span>
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
  <!-- Seção de filtros -->
  <div class="filter-section">
    <div class="dropdown">
      <button class="filter-button" id="ano-filter">Ano X a Y</button>
      <div class="dropdown-content" id="ano-dropdown">
        <a href="#" data-min="1960" data-max="1969">1960 - 1969</a>
        <a href="#" data-min="1970" data-max="1979">1970 - 1979</a>
        <a href="#" data-min="1980" data-max="1989">1980 - 1989</a>
        <a href="#" data-min="1990" data-max="1999">1990 - 1999</a>
        <a href="#" data-min="2000" data-max="2009">2000 - 2009</a>
        <a href="#" data-min="2010" data-max="2023">2010 - 2023</a>
      </div>
    </div>
    
    <div class="dropdown">
      <button class="filter-button" id="preco-filter">Preço X a Y</button>
      <div class="dropdown-content" id="preco-dropdown">
        <a href="#" data-min="0" data-max="10000">Até R$ 10.000</a>
        <a href="#" data-min="10000" data-max="30000">R$ 10.000 - R$ 30.000</a>
        <a href="#" data-min="30000" data-max="60000">R$ 30.000 - R$ 60.000</a>
        <a href="#" data-min="60000" data-max="100000">R$ 60.000 - R$ 100.000</a>
        <a href="#" data-min="100000" data-max="999999999">Acima de R$ 100.000</a>
      </div>
    </div>
    
    <div class="dropdown">
      <button class="filter-button" id="categoria-filter">Categoria</button>
      <div class="dropdown-content" id="categoria-dropdown">
        <?php
        // Incluir o arquivo de dados para obter as categorias
        include_once 'dados.php';
        foreach($categorias as $categoria) {
          echo "<a href=\"#\" data-categoria=\"$categoria\">$categoria</a>";
        }
        ?>
      </div>
    </div>
  </div>

  <!-- Grid de carros (já existente) -->
  <div class="grid-container">
    <!-- Carros serão carregados dinamicamente aqui pelo JavaScript -->
  </div>
</main>

  <footer>
    <p>&copy; 2025 Carros e Lances - Leilões online</p>
  </footer>
</body>
</html>