document.addEventListener("DOMContentLoaded", () => {
  const filterInput = document.getElementById("filter-input");
  const loginModal = document.getElementById("login-modal");
  const openLoginModalButton = document.getElementById("open-login-modal");
  const closeModalButton = document.getElementById("close-modal");
  const modalTitle = document.getElementById("modal-title");
  const actionButton = document.getElementById("actionButton");
  const toggleText = document.getElementById("toggleText");
  const toggleLink = document.getElementById("toggleLink");
  const usernameInput = document.getElementById("usernameInput");
  const passwordInput = document.getElementById("passwordInput");
  const userNameDisplay = document.getElementById("user-name");
  const userSaldoDisplay = document.getElementById("user-saldo");
  const logoutBtn = document.getElementById("logout-btn");
  const profilePic = document.getElementById("profile-pic");
  const gridContainer = document.querySelector(".grid-container");
  let currentUser = JSON.parse(localStorage.getItem("currentUser"));
  let isLoginMode = true;

  // Definição global de carros (substitui as caixas)
  const carros = [
    { name: "Volkswagen Fusca 1968", price: 15000.00, id: "fusca-1968", image: "img/carros/fusca.jpg" },
    { name: "Chevrolet Chevette 1991", price: 8000.00, id: "chevette-1991", image: "img/carros/chevette.jpg" },
    { name: "Ford Mustang 1967", price: 120000.00, id: "mustang-1967", image: "img/carros/mustang.jpg" },
    { name: "Fiat Uno 1995", price: 6500.00, id: "uno-1995", image: "img/carros/uno.jpg" },
    { name: "Dodge Charger 1970", price: 180000.00, id: "charger-1970", image: "img/carros/charger.jpg" },
  ];

  // Alternar entre login e cadastro
  if (toggleLink) {
    toggleLink.addEventListener("click", () => {
      isLoginMode = !isLoginMode;
      modalTitle.textContent = isLoginMode ? "Entrar" : "Cadastrar";
      actionButton.textContent = isLoginMode ? "Entrar" : "Cadastrar";
      toggleText.innerHTML = isLoginMode
        ? 'Não tem conta? <span style="cursor: pointer; color: #4CAF50;">Cadastre-se</span>'
        : 'Já tem conta? <span style="cursor: pointer; color: #4CAF50;">Entre</span>';
    });
  }

  // Configurar eventos de clique para o perfil
  [userNameDisplay, profilePic].forEach((element) => {
    element.addEventListener("click", () => {
      window.location.href = "./profile.html";
    });
    element.style.cursor = "pointer";
  });

  // Ação de login/cadastro
  if (actionButton) {
    actionButton.addEventListener("click", () => {
      if (!usernameInput || !passwordInput) return;

      const username = usernameInput.value.trim();
      const password = passwordInput.value.trim();

      if (!username || !password) {
        alert("Por favor, preencha todos os campos.");
        return;
      }

      const users = JSON.parse(localStorage.getItem("users")) || [];

      if (isLoginMode) {
        const user = users.find((u) => u.username === username && u.password === password);
        if (user) {
          currentUser = user;
          localStorage.setItem("currentUser", JSON.stringify(currentUser));
          alert(`Bem-vindo, ${user.username}!`);
          if (loginModal) loginModal.style.display = "none";
          updateUserInterface();
        } else {
          alert("Usuário ou senha incorretos.");
        }
      } else {
        if (users.some((u) => u.username === username)) {
          alert("Usuário já cadastrado.");
        } else {
          users.push({ username, password, saldo: 1000.00 }); // Começa com R$1000 para lances
          localStorage.setItem("users", JSON.stringify(users));
          alert("Usuário cadastrado com sucesso!");
          if (toggleLink) toggleLink.click();
        }
      }
    });
  }

  // Atualiza a interface do usuário
  function updateUserInterface() {
    if (currentUser) {
      userNameDisplay.textContent = currentUser.username;
      userSaldoDisplay.textContent = `Saldo: R$${currentUser.saldo.toFixed(2)}`;
      logoutBtn.style.display = "block";
      openLoginModalButton.style.display = "none";
      profilePic.src = "img/avatar-user.png";
      profilePic.style.display = "block";
      profilePic.addEventListener("click", () => {
        window.location.href = "./profile.html";
      });
    } else {
      userNameDisplay.textContent = "Guest";
      userSaldoDisplay.textContent = "Saldo: R$0.00";
      logoutBtn.style.display = "none";
      openLoginModalButton.style.display = "block";
      profilePic.style.display = "none";
    }
    renderGrid(carros);
    populateCarros();
  }

  // Popula os carros no grid (substitui as caixas)
  function populateCarros() {
    if (!gridContainer) return;
  
    gridContainer.innerHTML = "";
    carros.forEach((carro) => {
      const carroElement = document.createElement("div");
      carroElement.className = "grid-item";
      carroElement.innerHTML = `
        <img src="${carro.image}" alt="${carro.name}">
        <h3>${carro.name}</h3>
        <p>R$${carro.price.toFixed(2)}</p>
        <button class="enter-car-btn" 
                data-id="${carro.id}" 
                data-name="${carro.name}" 
                data-image="${carro.image}">Ver Detalhes</button>
      `;
      gridContainer.appendChild(carroElement);
    });
    
    // Adiciona eventos aos botões de cada carro
    document.querySelectorAll(".enter-car-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const carroId = e.target.dataset.id;
        const carroName = e.target.dataset.name;
        const carroImage = e.target.dataset.image;
    
        if (!currentUser) {
          alert("Você precisa fazer login para ver detalhes deste carro!");
          return;
        }
    
        // Redirecionar para a página de detalhes com os parâmetros
        window.location.href = `detalhes-carro.php?carroId=${carroId}&carroName=${encodeURIComponent(carroName)}&carroImage=${encodeURIComponent(carroImage)}`;
      });
    });
  }
  
  // Renderiza os carros no grid (versão filtrada)
  function renderGrid(filteredCarros) {
    gridContainer.innerHTML = ""; // Limpa a grade
    filteredCarros.forEach((carro) => {
      const carroElement = document.createElement("div");
      carroElement.className = "grid-item";
      carroElement.innerHTML = `
        <img src="${carro.image}" alt="${carro.name}">
        <h3>${carro.name}</h3>
        <p>R$${carro.price.toFixed(2)}</p>
        <button class="enter-car-btn" 
                data-id="${carro.id}" 
                data-name="${carro.name}" 
                data-image="${carro.image}">Ver Detalhes</button>
      `;
      gridContainer.appendChild(carroElement);
    });
    
    // Re-adiciona eventos após renderizar
    document.querySelectorAll(".enter-car-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const carroId = e.target.dataset.id;
        const carroName = e.target.dataset.name;
        const carroImage = e.target.dataset.image;
    
        if (!currentUser) {
          alert("Você precisa fazer login para ver detalhes deste carro!");
          return;
        }
    
        window.location.href = `detalhes-carro.php?carroId=${carroId}&carroName=${encodeURIComponent(carroName)}&carroImage=${encodeURIComponent(carroImage)}`;
      });
    });
  }

  // Filtrar dinamicamente com base no texto do input
  if (filterInput) {
    filterInput.addEventListener("input", () => {
      const searchText = filterInput.value.toLowerCase();
      const filteredCarros = carros.filter((carro) =>
        carro.name.toLowerCase().includes(searchText)
      );
      renderGrid(filteredCarros);
    });
  }

  // Inicializa a interface do usuário
  updateUserInterface();

  // Sair
  if (logoutBtn) {
    logoutBtn.addEventListener("click", () => {
      if (currentUser) {
        const users = JSON.parse(localStorage.getItem("users")) || [];
        const userIndex = users.findIndex((u) => u.username === currentUser.username);
        if (userIndex !== -1) {
          users[userIndex].saldo = currentUser.saldo;
          localStorage.setItem("users", JSON.stringify(users));
        }
        localStorage.removeItem("currentUser");
        currentUser = null;
        alert("Você saiu com sucesso.");
        updateUserInterface();
      }
    });
  }

  // Abrir modal de login
  if (openLoginModalButton) {
    openLoginModalButton.addEventListener("click", () => {
      if (loginModal) loginModal.style.display = "flex";
    });
  }

  // Fechar modal de login
  if (closeModalButton) {
    closeModalButton.addEventListener("click", () => {
      if (loginModal) loginModal.style.display = "none";
    });
  }

  // Fechar modal ao clicar fora
  window.addEventListener("click", (event) => {
    if (event.target === loginModal) {
      loginModal.style.display = "none";
    }
  });

  // Inicializa a interface
  updateUserInterface();

  // Adicione este código ao seu evento DOMContentLoaded no carsHome.js

// Configuração dos dropdowns de filtro
const dropdowns = document.querySelectorAll('.dropdown');
let activeFilters = {
  ano: null,
  preco: null,
  categoria: null
};

// Configurar cada dropdown
dropdowns.forEach(dropdown => {
  const button = dropdown.querySelector('.filter-button');
  const content = dropdown.querySelector('.dropdown-content');
  
  // Mostrar/esconder dropdown ao clicar no botão
  button.addEventListener('click', function() {
    // Fechar outros dropdowns abertos
    document.querySelectorAll('.dropdown-content.show').forEach(openContent => {
      if (openContent !== content) {
        openContent.classList.remove('show');
      }
    });
    
    // Alternar visibilidade deste dropdown
    content.classList.toggle('show');
  });
  
  // Configurar links dentro do dropdown
  const links = content.querySelectorAll('a');
  links.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Remove classe ativa de todos os links no mesmo dropdown
      content.querySelectorAll('a').forEach(a => a.classList.remove('active'));
      
      // Adiciona classe ativa ao link clicado
      this.classList.add('active');
      
      // Atualiza o texto do botão para mostrar a seleção atual
      if (button.id === 'ano-filter') {
        const min = this.dataset.min;
        const max = this.dataset.max;
        button.textContent = `${min} - ${max}`;
        button.classList.add('active');
        activeFilters.ano = { min, max };
      } 
      else if (button.id === 'preco-filter') {
        const min = this.dataset.min;
        const max = this.dataset.max;
        if (min == 0) {
          button.textContent = `Até R$ ${parseInt(max).toLocaleString('pt-BR')}`;
        } else if (max == 999999999) {
          button.textContent = `Acima de R$ ${parseInt(min).toLocaleString('pt-BR')}`;
        } else {
          button.textContent = `R$ ${parseInt(min).toLocaleString('pt-BR')} - R$ ${parseInt(max).toLocaleString('pt-BR')}`;
        }
        button.classList.add('active');
        activeFilters.preco = { min, max };
      } 
      else if (button.id === 'categoria-filter') {
        const categoria = this.dataset.categoria;
        button.textContent = categoria;
        button.classList.add('active');
        activeFilters.categoria = categoria;
      }
      
      // Esconde o dropdown após a seleção
      content.classList.remove('show');
      
      // Aplica os filtros
      aplicarFiltros();
    });
  });
});

// Fechar os dropdowns quando clicar fora deles
window.addEventListener('click', function(event) {
  if (!event.target.matches('.filter-button')) {
    document.querySelectorAll('.dropdown-content').forEach(content => {
      if (content.classList.contains('show')) {
        content.classList.remove('show');
      }
    });
  }
});

// Função para aplicar filtros
function aplicarFiltros() {
  // Filtrar os carros com base nos critérios selecionados
  let carrosFiltrados = [...carros]; // Clone do array original
  
  // Filtrar por ano
  if (activeFilters.ano) {
    carrosFiltrados = carrosFiltrados.filter(carro => {
      const anoMatch = carro.name.match(/(\d{4})/); // Extrai o ano do nome do carro
      if (anoMatch) {
        const ano = parseInt(anoMatch[0]);
        return ano >= parseInt(activeFilters.ano.min) && ano <= parseInt(activeFilters.ano.max);
      }
      return false;
    });
  }
  
  // Filtrar por preço
  if (activeFilters.preco) {
    carrosFiltrados = carrosFiltrados.filter(carro => {
      return carro.price >= parseInt(activeFilters.preco.min) && carro.price <= parseInt(activeFilters.preco.max);
    });
  }
  
  // Filtrar por categoria
  if (activeFilters.categoria) {
    carrosFiltrados = carrosFiltrados.filter(carro => {
      // Se você tem a categoria como propriedade do carro, use isso:
      return carro.categoria === activeFilters.categoria;
      
      // Se não, você pode adaptar para usar outra lógica de filtragem
    });
  }
  
  // Renderizar os carros filtrados
  renderGrid(carrosFiltrados);
  
  // Se não houver resultados, mostrar mensagem
  if (carrosFiltrados.length === 0) {
    const gridContainer = document.querySelector('.grid-container');
    gridContainer.innerHTML = `
      <div class="no-results">
        <h3>Nenhum carro encontrado</h3>
        <p>Tente outros filtros ou <button id="clear-filters" class="clear-filters-btn">limpar filtros</button></p>
      </div>
    `;
    
    document.getElementById('clear-filters').addEventListener('click', limparFiltros);
  }
}

// Função para limpar todos os filtros
function limparFiltros() {
  activeFilters = {
    ano: null,
    preco: null,
    categoria: null
  };
  
  // Resetar texto e classe ativa dos botões
  document.getElementById('ano-filter').textContent = 'Ano X a Y';
  document.getElementById('ano-filter').classList.remove('active');
  
  document.getElementById('preco-filter').textContent = 'Preço X a Y';
  document.getElementById('preco-filter').classList.remove('active');
  
  document.getElementById('categoria-filter').textContent = 'Categoria';
  document.getElementById('categoria-filter').classList.remove('active');
  
  // Remover seleção ativa em todos os links de dropdown
  document.querySelectorAll('.dropdown-content a').forEach(link => {
    link.classList.remove('active');
  });
  
  // Recarregar todos os carros
  renderGrid(carros);
}

// Adicione um botão para limpar filtros no topo
function adicionarBotaoLimparFiltros() {
  const filterSection = document.querySelector('.filter-section');
  
  // Verificar se o botão já existe
  if (!document.getElementById('clear-all-filters')) {
    const clearButton = document.createElement('button');
    clearButton.id = 'clear-all-filters';
    clearButton.className = 'filter-button clear-button';
    clearButton.textContent = 'Limpar Filtros';
    clearButton.style.backgroundColor = '#ef4444';
    clearButton.style.color = 'white';
    clearButton.style.display = 'none';
    
    clearButton.addEventListener('click', limparFiltros);
    
    filterSection.appendChild(clearButton);
  }
}

// Mostrar/ocultar o botão limpar filtros com base no estado dos filtros
function atualizarBotaoLimparFiltros() {
  const clearButton = document.getElementById('clear-all-filters');
  if (!clearButton) return;
  
  const temFiltroAtivo = activeFilters.ano || activeFilters.preco || activeFilters.categoria;
  clearButton.style.display = temFiltroAtivo ? 'block' : 'none';
}

// Chamar essa função após a inicialização
adicionarBotaoLimparFiltros();
});