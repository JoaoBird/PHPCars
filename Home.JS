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
  