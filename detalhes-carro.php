<?php
// Obtem os parâmetros da URL
$carroId = isset($_GET['carroId']) ? $_GET['carroId'] : '';
$carroName = isset($_GET['carroName']) ? $_GET['carroName'] : 'Carro Desconhecido';
$carroImage = isset($_GET['carroImage']) ? $_GET['carroImage'] : 'img/carro-default.jpg';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($carroName); ?> - Carros e Lances</title>
  <link rel="stylesheet" href="Home.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  
  <style>
    .car-details {
      background-color: #1e2430;
      border-radius: 12px;
      padding: 30px;
      max-width: 800px;
      margin: 30px auto;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }
    
    .car-image {
      width: 100%;
      max-height: 400px;
      object-fit: contain;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    
    .car-info h1 {
      color: #7dd3fc;
      margin-bottom: 20px;
    }
    
    .car-info p {
      font-size: 18px;
      margin: 10px 0;
    }
    
    .specs-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
      margin: 30px 0;
    }
    
    .spec-item {
      background-color: #0f172a;
      padding: 15px;
      border-radius: 8px;
      text-align: center;
    }
    
    .bid-section {
      margin-top: 30px;
      text-align: center;
    }
    
    .bid-input {
      width: 80%;
      max-width: 300px;
      padding: 12px;
      margin-bottom: 20px;
      background-color: #2d3748;
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 18px;
      text-align: center;
    }
    
    .bid-button {
      background: linear-gradient(145deg, #3b82f6, #2563eb);
      color: white;
      font-size: 18px;
      padding: 15px 30px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .bid-button:hover {
      background: linear-gradient(145deg, #2563eb, #1d4ed8);
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    
    .current-bids {
      margin-top: 30px;
      padding: 20px;
      background-color: #1a202c;
      border-radius: 8px;
    }
    
    .current-bids h3 {
      color: #7dd3fc;
      margin-bottom: 15px;
    }
    
    .bid-item {
      display: flex;
      justify-content: space-between;
      padding: 10px 0;
      border-bottom: 1px solid #2d3748;
    }
    
    .bid-user {
      font-weight: bold;
      color: #e2e8f0;
    }
    
    .bid-amount {
      color: #10b981;
    }
    
    .bid-time {
      color: #94a3b8;
      font-size: 0.9em;
    }
    
    .back-button {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #4b5563;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      transition: background-color 0.3s;
    }
    
    .back-button:hover {
      background-color: #374151;
    }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="logo-container">
      <a href="CarsAndBids.php">
        <img src="img/logo-cars.png" alt="Logo" />
      </a>
      <h1>Carros e Lances</h1>
    </div>

    <div class="user-info">
      <img id="profile-pic" src="img/avatar-user.png" alt="Avatar do Usuário" style="display: none;" />
      <span id="user-name">Guest</span>
      <span id="user-saldo">Saldo: R$0</span>
      <button id="open-login-modal" class="login-btn">Entrar/Cadastrar</button>
      <button id="logout-btn" class="logout-btn" style="display: none;">Sair</button>
    </div>
  </header>

  <main>
    <div class="car-details">
      <h1><?php echo htmlspecialchars($carroName); ?></h1>
      
      <img class="car-image" src="<?php echo htmlspecialchars($carroImage); ?>" alt="<?php echo htmlspecialchars($carroName); ?>">
      
      <div class="car-info">
        <p><strong>Preço inicial:</strong> <span id="preco-inicial">R$ 15.000,00</span></p>
        <p><strong>Lance atual:</strong> <span id="lance-atual">R$ 16.500,00</span></p>
        <p><strong>Tempo restante:</strong> <span id="tempo-restante">1 dia, 6 horas</span></p>
      </div>
      
      <div class="specs-grid">
        <div class="spec-item">
          <h3>Motor</h3>
          <p>1.6</p>
        </div>
        <div class="spec-item">
          <h3>Quilometragem</h3>
          <p>85.000 km</p>
        </div>
        <div class="spec-item">
          <h3>Câmbio</h3>
          <p>Manual</p>
        </div>
        <div class="spec-item">
          <h3>Combustível</h3>
          <p>Gasolina</p>
        </div>
      </div>
      
      <!-- Seção de lances (como se fosse a roleta de items) -->
      <div class="bid-section">
        <input type="number" id="bid-value" class="bid-input" placeholder="Digite seu lance (R$)" min="16600" step="100">
        <button id="make-bid" class="bid-button">Fazer Lance</button>
      </div>
      
      <!-- Lances atuais (similar aos itens obtidos) -->
      <div class="current-bids">
        <h3>Lances Atuais</h3>
        <div class="bid-item">
          <span class="bid-user">usuario123</span>
          <span class="bid-amount">R$ 16.500,00</span>
          <span class="bid-time">há 2 horas</span>
        </div>
        <div class="bid-item">
          <span class="bid-user">colecionador</span>
          <span class="bid-amount">R$ 16.000,00</span>
          <span class="bid-time">há 4 horas</span>
        </div>
        <div class="bid-item">
          <span class="bid-user">carfan27</span>
          <span class="bid-amount">R$ 15.500,00</span>
          <span class="bid-time">há 5 horas</span>
        </div>
      </div>
      
      <div style="text-align: center; margin-top: 30px;">
        <a href="CarsAndBids.php" class="back-button">Voltar para Listagem</a>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 Carros e Lances</p>
  </footer>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Recuperar o usuário atual do localStorage
      const currentUser = JSON.parse(localStorage.getItem("currentUser"));
      const userNameDisplay = document.getElementById("user-name");
      const userSaldoDisplay = document.getElementById("user-saldo");
      const logoutBtn = document.getElementById("logout-btn");
      const openLoginModalButton = document.getElementById("open-login-modal");
      const profilePic = document.getElementById("profile-pic");
      const bidButton = document.getElementById("make-bid");
      const bidInput = document.getElementById("bid-value");
      
      // Atualizar interface do usuário
      if (currentUser) {
        userNameDisplay.textContent = currentUser.username;
        userSaldoDisplay.textContent = `Saldo: R$${currentUser.saldo.toFixed(2)}`;
        logoutBtn.style.display = "block";
        openLoginModalButton.style.display = "none";
        profilePic.style.display = "block";
        
        // Habilitar lance
        bidButton.addEventListener('click', function() {
          const bidValue = parseFloat(bidInput.value);
          if (!bidValue || bidValue < 16600) {
            alert("Por favor, digite um valor válido maior que R$ 16.600,00");
            return;
          }
          
          if (bidValue > currentUser.saldo) {
            alert("Saldo insuficiente para este lance!");
            return;
          }
          
          // Simular o lance bem sucedido
          alert(`Lance de R$ ${bidValue.toFixed(2)} realizado com sucesso!`);
          
          // Atualizar a lista de lances
          const currentBids = document.querySelector('.current-bids');
          const newBid = document.createElement('div');
          newBid.className = 'bid-item';
          newBid.innerHTML = `
            <span class="bid-user">${currentUser.username}</span>
            <span class="bid-amount">R$ ${bidValue.toFixed(2)}</span>
            <span class="bid-time">agora mesmo</span>
          `;
          
          // Inserir o novo lance no topo
          currentBids.insertBefore(newBid, currentBids.children[1]);
          
          // Atualizar o lance atual exibido
          document.getElementById('lance-atual').textContent = `R$ ${bidValue.toFixed(2)}`;
          
          // Atualizar o placeholder para o próximo lance mínimo
          const nextMinBid = bidValue + 100;
          bidInput.min = nextMinBid;
          bidInput.placeholder = `Digite seu lance (mín: R$ ${nextMinBid.toFixed(2)})`;
          bidInput.value = "";
        });
      } else {
        userNameDisplay.textContent = "Guest";
        userSaldoDisplay.textContent = "Saldo: R$0.00";
        logoutBtn.style.display = "none";
        openLoginModalButton.style.display = "block";
        profilePic.style.display = "none";
        
        // Desabilitar lance para usuários não logados
        bidButton.addEventListener('click', function() {
          alert("Você precisa estar logado para dar lances!");
        });
      }
      
      // Adicionar evento de clique para logout
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
          localStorage.removeItem("currentUser");
          window.location.reload();
        });
      }
    });
  </script>
</body>
</html>