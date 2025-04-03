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
  
    // Definição global de `boxes`
    const boxes = [
      { name: "White and Bright", price: 5.45, id: "white-and-bright", image: "/public/pages/img-pag/box1.png" },
      { name: "Green Wood Dragon", price: 0.20, id: "green-wood-dragon", image: "/public/pages/img-pag/box2.png" },
      { name: "Doppler Mining", price: 1.17, id: "doppler-mining", image: "/public/pages/img-pag/box3.png" },
      { name: "Pandora's Box", price: 26.03, id: "pandoras-box", image: "/public/pages/img-pag/box4.png" },
      { name: "Lazy Tiger", price: 58.99, id: "lazy-tiger", image: "/public/pages/img-pag/box5.png" },
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
            users.push({ username, password, saldo: 0 });
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
        profilePic.src = "/public/pages/img-pag/CS2G-user.png";
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
      renderGrid(boxes);
      populateBoxes();
    }
  
    // Popula as caixas no grid
    function populateBoxes() {
      const boxes = [
        { name: "White and Bright", price: 5.45, id: "white-and-bright", image: "/public/pages/img-pag/box1.png" },
        { name: "Green Wood Dragon", price: 0.20, id: "green-wood-dragon", image: "/public/pages/img-pag/box2.png" },
        { name: "Doppler Mining", price: 2.17, id: "doppler-mining", image: "/public/pages/img-pag/box3.png" },
        { name: "Pandora's Box", price: 26.03, id: "pandoras-box", image: "/public/pages/img-pag/box4.png" },
        { name: "Lazy Tiger", price: 58.99, id: "lazy-tiger", image: "/public/pages/img-pag/box5.png" },
      ];
    
      if (!gridContainer) return;
    
      gridContainer.innerHTML = "";
      boxes.forEach((box) => {
        const boxElement = document.createElement("div");
        boxElement.className = "grid-item";
        boxElement.innerHTML = `
          <img src="${box.image}" alt="${box.name}">
          <h3>${box.name}</h3>
          <p>R$${box.price.toFixed(2)}</p>
          <button class="enter-box-btn" 
                  data-id="${box.id}" 
                  data-name="${box.name}" 
                  data-image="${box.image}">Entrar na Caixa</button>
        `;
        gridContainer.appendChild(boxElement);
      });
      
      document.querySelectorAll(".enter-box-btn").forEach((btn) => {
        btn.addEventListener("click", (e) => {
          const boxId = e.target.dataset.id;
          const boxName = e.target.dataset.name;
          const boxImage = e.target.dataset.image;
      
          console.log("Box ID:", boxId);
          console.log("Box Name:", boxName);
          console.log("Box Image:", boxImage);
      
          if (!currentUser) {
            alert("Você precisa fazer login para acessar esta caixa!");
            return;
          }
      
          // Redirecionar para a página da caixa com os parâmetros
          window.location.href = `pages/white-and-bright.html?boxId=${boxId}&boxName=${encodeURIComponent(boxName)}&boxImage=${encodeURIComponent(boxImage)}`;
          console.log("Box Data:", box);
          console.log("URL Params:", window.location.search);
          alert("alerta");
        });
      });
      
      
    }
    // Renderiza as caixas no grid
    function renderGrid(filteredBoxes) {
      gridContainer.innerHTML = ""; // Limpa a grade
      filteredBoxes.forEach((box) => {
        const boxElement = document.createElement("div");
        boxElement.className = "grid-item";
        boxElement.innerHTML = `
          <img src="${box.image}" alt="${box.name}">
          <h3>${box.name}</h3>
          <p>R$${box.price.toFixed(2)}</p>
          <button class="enter-box-btn" data-id="${box.id}">Entrar na Caixa</button>
        `;
        gridContainer.appendChild(boxElement);
      });
    }
  
    // Filtrar dinamicamente com base no texto do input
    filterInput.addEventListener("input", () => {
      const searchText = filterInput.value.toLowerCase();
      const filteredBoxes = boxes.filter((box) =>
        box.name.toLowerCase().includes(searchText)
      );
      renderGrid(filteredBoxes);
    });
  
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
  
    if (openLoginModalButton) {
      openLoginModalButton.addEventListener("click", () => {
        if (loginModal) loginModal.style.display = "flex";
      });
    }
  
    if (closeModalButton) {
      closeModalButton.addEventListener("click", () => {
        if (loginModal) loginModal.style.display = "none";
      });
    }
  
    window.addEventListener("click", (event) => {
      if (event.target === loginModal) {
        loginModal.style.display = "none";
      }
    });
  
    updateUserInterface();
  });
  