document.addEventListener('DOMContentLoaded', function () {
    console.log('auth.js загружен');
  
    const accountBtn = document.getElementById('accountBtn');
    const authModal = document.getElementById('authModal');
    const closeAuthModal = authModal.querySelector('.close');
  
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
  
    if (!accountBtn) {
      console.error('Кнопка accountBtn не найдена!');
      return;
    }
  
    accountBtn.onclick = () => {
      console.log('Кнопка accountBtn нажата');
      authModal.style.display = 'block';
    };
  
    closeAuthModal.onclick = () => authModal.style.display = 'none';
    window.onclick = (event) => { if (event.target === authModal) authModal.style.display = 'none'; };
  
    loginTab.onclick = () => {
      loginTab.classList.add('active');
      registerTab.classList.remove('active');
      loginForm.style.display = 'block';
      registerForm.style.display = 'none';
    };
  
    registerTab.onclick = () => {
      registerTab.classList.add('active');
      loginTab.classList.remove('active');
      registerForm.style.display = 'block';
      loginForm.style.display = 'none';
    };
  
    // Инициализация маски телефона
    Inputmask("+7 (999)-999-99-99").mask(document.querySelectorAll('input[type="tel"]'));
  
    // Отправка формы регистрации
    registerForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const data = {
        last_name: registerForm.registerLastName.value,
        first_name: registerForm.registerFirstName.value,
        phone: registerForm.registerPhone.value,
        password: registerForm.registerPassword.value
      };
  
      fetch('php/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          authModal.style.display = 'none';
          location.reload();
        }
      });
    });
  
    // Отправка формы входа
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const data = {
        phone: loginForm.loginPhone.value,
        password: loginForm.loginPassword.value
      };
  
      fetch('php/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          authModal.style.display = 'none';
          location.reload();
        }
      });
    });
  });
  