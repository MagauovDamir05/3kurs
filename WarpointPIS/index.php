<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Главная</title>
  <link rel="stylesheet" href="css/indexstyle.css?v=1.4" />
  <!-- Подключаем Inputmask -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/bindings/inputmask.binding.min.js"></script>
</head>
<body>

<!-- ХЭДЕР -->
<header class="main-header">
  <nav class="nav">
    <ul class="nav-menu">
      <li><a href="index.php">Главная</a></li>
      <li><a href="tariff.php">Тарифы</a></li>
      <li><a href="menu.php">Наше меню</a></li>
      <li><a href="contact.php">Контакты</a></li>
    </ul>
    <button id="burgerBtn" class="burger-btn">&#9776;</button>
    <div class="nav-right">
      <button id="openModal" class="book-btn">Забронировать</button>
      <button id="accountBtn" class="account-btn">
        <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Вход'; ?>
      </button>
    </div>
  </nav>
</header>

<!-- Модальное окно бронирования -->
<div id="bookingModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Забронировать игру</h2>
    <form id="bookingForm" action="booking.php" method="POST">
      <label for="date">Дата:</label>
      <input type="date" id="date" name="date" required>

      <label for="time">Время:</label>
      <select id="time" name="time" required>
        <option value="">Выберите время</option>
        <option value="11:00">11:00</option>
        <option value="12:00">12:00</option>
        <option value="13:00">13:00</option>
        <option value="14:00">14:00</option>
        <option value="15:00">15:00</option>
        <option value="16:00">16:00</option>
        <option value="17:00">17:00</option>
        <option value="18:00">18:00</option>
        <option value="19:00">19:00</option>
        <option value="20:00">20:00</option>
        <option value="21:00">21:00</option>
      </select>

      <label for="tariff">Тариф:</label>
      <select id="tariff" name="tariff" required>
        <option value="">Выберите тариф</option>
        <option value="Открытая игра">Открытая игра</option>
        <option value="Lite">Тариф Lite</option>
        <option value="Standart">Тариф Standart</option>
        <option value="Max">Тариф Max</option>
      </select>

      <div id="additionalOptions">
        <label for="players">Количество игроков:</label>
        <select id="players" name="players">
          <option value="">Выберите количество игроков</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="8">8</option>
          <option value="9">9</option>
          <option value="10">10</option>
        </select>
      </div>

      <button type="submit">Забронировать</button>
    </form>
  </div>
</div>


<!-- Модальное окно для входа и регистрации -->
<div id="authModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div class="auth-tabs">
      <button id="loginTab" class="active">Вход</button>
      <button id="registerTab">Регистрация</button>
    </div>

    <!-- Форма входа -->
    <form id="loginForm" action="login.php" method="POST">
      <label for="loginPhone">Номер телефона:</label>
      <input type="tel" id="loginPhone" name="phone" required />

      <label for="loginPassword">Пароль:</label>
      <input type="password" id="loginPassword" name="password" required />

      <button type="submit">Войти</button>
    </form>

    <!-- Форма регистрации -->
    <form id="registerForm" action="register.php" method="POST">
  <label>Фамилия:</label>
  <input type="text" name="last_name" required>

  <label>Имя:</label>
  <input type="text" name="first_name" required>

  <label>Номер телефона:</label>
  <input type="tel" name="phone" required>

  <label>Пароль:</label>
  <input type="password" name="password" required>

  <button type="submit">Зарегистрироваться</button>
</form>

  </div>
</div>

<div id="accountMenu" class="account-menu" style="display: none;">
  <ul>
    <li id="profileBtn">Личный кабинет</li>
    <li><a href="messages.php">Сообщения</a></li>
    <li><a href="logout.php">Выход</a></li>
  </ul>
</div>

<!-- Модалка личного кабинета -->
<div id="profileModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Личный кабинет</h2>

    <!-- Имя -->
    <div class="field-group">
      <div class="field-label">Имя:
        <button type="button" class="edit-btn" onclick="toggleEdit('firstName')">Изменить</button>
      </div>
      <div id="firstNameDisplay">Имя</div>
      <form id="firstNameForm" style="display: none;" onsubmit="saveField(event, 'firstName')">
        <input type="text" id="firstNameInput" required placeholder="Новое имя">
        <button type="submit" class="save-btn">Сохранить</button>
      </form>
    </div>

    <!-- Фамилия -->
    <div class="field-group">
      <div class="field-label">Фамилия:
        <button type="button" class="edit-btn" onclick="toggleEdit('lastName')">Изменить</button>
      </div>
      <div id="lastNameDisplay">Фамилия</div>
      <form id="lastNameForm" style="display: none;" onsubmit="saveField(event, 'lastName')">
        <input type="text" id="lastNameInput" required placeholder="Новая фамилия">
        <button type="submit" class="save-btn">Сохранить</button>
      </form>
    </div>

    <!-- Телефон -->
    <div class="field-group">
      <div class="field-label">Телефон:
        <button type="button" class="edit-btn" onclick="toggleEdit('phone')">Изменить</button>
      </div>
      <div id="phoneDisplay">Телефон</div>
      <form id="phoneForm" style="display: none;" onsubmit="saveField(event, 'phone')">
        <input type="tel" id="phoneInput" required placeholder="Новый телефон">
        <button type="submit" class="save-btn">Сохранить</button>
      </form>
    </div>

    <!-- Пароль -->
    <div class="field-group">
      <div class="field-label">Пароль:
        <button type="button" class="edit-btn" onclick="toggleEdit('password')">Изменить</button>
      </div>
      <div id="passwordDisplay">********</div>
      <form id="passwordForm" style="display: none;" onsubmit="saveField(event, 'password')">
        <input type="password" id="passwordInput" required placeholder="Новый пароль">
        <button type="submit" class="save-btn">Сохранить</button>
      </form>
    </div>

    <button type="button" id="deleteAccountBtn" style="background-color: #e74c3c; margin-top: 20px;">Удалить аккаунт</button>
  </div>
</div>


<!-- Основной контент -->
<section class="hero-block">
  <div class="hero-image">
    <img src="images/Glav2.png" alt="VR Player" />
  </div>
  <div class="hero-text">
    <p>В КОМПЬЮТЕРНОЙ ИГРЕ</p>
    <p>ВЫ ИГРАЕТЕ ЗА ГЕРОЯ,</p>
    <p>А В WARPOINT <span>— ВЫ И ЕСТЬ ГЕРОЙ!</span></p>
  </div>
</section>

<section class="arena-block">
  <div class="arena-content">
    <div class="arena-text">
      <h2>WARPPOINT ARENA</h2>
      <p>Аналог игры Counter-Strike только в формате VR, где две команды сражаются друг против друга.</p>
      <p>20 уникальных карт, 15 видов оружия и 3 игровых режима — выбери свой стиль сражения!</p>
      <ul class="arena-details">
        <li>Возраст: 8+</li>
        <li>Более 20-ти игровых карт</li>
        <li>2–10 игроков</li>
        <li>PvP-шутер</li>
      </ul>
      <a href="#" class="arena-more">Подробнее →</a>
    </div>

    <div class="arena-image-wrapper">
      <div class="arena-image">
        <img src="images/Glav3.png" alt="WARPPOINT Arena" />
      </div>
      <div class="arena-banner"></div>
    </div>
  </div>
</section>

<section class="reviews-section">
  <h2 class="reviews-title">Отзывы наших гостей</h2>
  <div class="review-grid">
    <?php if (isset($reviews)) : ?>
      <?php foreach ($reviews as $review): ?>
        <div class="review-card">
          <div class="review-name"><?= htmlspecialchars($review['name']) ?></div>
          <div class="review-rating">
            <?= str_repeat('★', $review['rating']) ?>
            <?= str_repeat('☆', 5 - $review['rating']) ?>
          </div>
          <div class="review-comment"><?= nl2br(htmlspecialchars($review['comment'])) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<footer class="site-footer">
  <div class="footer-content">
    <div class="footer-left">
      <h3>WARPPOINT</h3>
      <p>VR-баталии нового поколения — погрузись в мир футуристических сражений уже сегодня.</p>
    </div>
    <div class="footer-menu">
      <h4>Меню</h4>
      <ul>
        <li><a href="#">Главная</a></li>
        <li><a href="#">Игры</a></li>
        <li><a href="#">Цены</a></li>
        <li><a href="#">Контакты</a></li>
      </ul>
    </div>
    <div class="footer-contacts">
      <h4>Контакты</h4>
      <p>Email: info@warpoint.ru</p>
      <p>Тел: +7 (705) 123-45-67</p>
      <p>Адрес: Петропавловск, ул. Партизанская 48б</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2025 WARPOINT. Все права защищены.</p>
  </div>
</footer>

<script>

// Открытие окна бронирования
document.getElementById('openModal').onclick = function () {
  document.getElementById('bookingModal').style.display = 'block';
};

document.addEventListener('DOMContentLoaded', function () {
  const tariffSelect = document.getElementById('tariff');
  const additionalOptions = document.getElementById('additionalOptions');

  const phoneInputs = document.querySelectorAll('input[type="tel"]');
  phoneInputs.forEach(function(input) {
    Inputmask({ 
      mask: '+7 (999) 999-99-99',
      showMaskOnHover: false
    }).mask(input);
  });
});

// Открытие окна авторизации
document.getElementById('accountBtn').onclick = function () {
  if (!<?php echo isset($_SESSION['user_name']) ? 'true' : 'false'; ?>) {
    document.getElementById('authModal').style.display = 'block';
  }
};

// Закрытие всех модальных окон
document.querySelectorAll('.modal .close').forEach(btn => {
  btn.onclick = function () {
    this.closest('.modal').style.display = 'none';
  };
});

window.onclick = function (event) {
  document.querySelectorAll('.modal').forEach(modal => {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  });
};

// Переключение вкладок: Вход / Регистрация
const loginTab = document.getElementById('loginTab');
const registerTab = document.getElementById('registerTab');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');

// Показываем только форму входа по умолчанию
loginForm.style.display = 'block';
registerForm.style.display = 'none';

loginTab.onclick = () => {
  loginTab.classList.add('active');
  registerTab.classList.remove('active');
  loginForm.style.display = 'block';
  registerForm.style.display = 'none';
};

registerTab.onclick = () => {
  registerTab.classList.add('active');
  loginTab.classList.remove('active');
  loginForm.style.display = 'none';
  registerForm.style.display = 'block';
};

document.addEventListener('DOMContentLoaded', function () {

const isAuthenticated = <?php echo isset($_SESSION['user_name']) ? 'true' : 'false'; ?>;

const accountBtn = document.getElementById('accountBtn');
const accountMenu = document.getElementById('accountMenu');
const authModal = document.getElementById('authModal');

if (accountBtn && accountMenu) {
  accountBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    if (isAuthenticated) {
      // Показать / скрыть меню
      accountMenu.style.display = accountMenu.style.display === 'block' ? 'none' : 'block';

      // Добавить обработчик на кнопку "Личный кабинет" ТОЛЬКО если меню появилось
      if (accountMenu.style.display === 'block') {
        const profileBtn = document.getElementById('profileBtn');
        const profileModal = document.getElementById('profileModal');
        const profileContent = document.getElementById('profileContent');
        const closeProfileModal = document.getElementById('closeProfileModal');

        if (profileBtn && profileModal) {
          profileBtn.addEventListener('click', function () {
            accountMenu.style.display = 'none'; // закрываем меню
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_profile.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
              if (xhr.status === 200) {
                profileContent.innerHTML = xhr.responseText;
                profileModal.style.display = 'block';
                attachProfileFormHandlers();
              } else {
                alert('Ошибка при загрузке профиля');
              }
            };
            xhr.send();
          });
        }

        if (closeProfileModal) {
          closeProfileModal.addEventListener('click', function () {
            profileModal.style.display = 'none';
          });
        }

        window.addEventListener('click', function (event) {
          if (event.target === profileModal) {
            profileModal.style.display = 'none';
          }
        });
      }

    } else {
      authModal.style.display = 'block';
    }
  });

  window.addEventListener('click', function (event) {
    if (!accountMenu.contains(event.target) && event.target !== accountBtn) {
      accountMenu.style.display = 'none';
    }
  });
}

function attachProfileFormHandlers() {
  const profileForms = document.querySelectorAll('.profile-form');
  profileForms.forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(form);
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'update_profile.php', true);
      xhr.onload = function () {
        alert(this.responseText);
        if (xhr.status === 200 && this.responseText.includes('успешно')) {
          location.reload();
        }
      };
      xhr.send(new URLSearchParams(formData).toString());
    });
  });

  const deleteAccountBtn = document.getElementById('deleteAccountBtn');
  if (deleteAccountBtn) {
    deleteAccountBtn.addEventListener('click', function (e) {
      e.preventDefault();
      if (confirm('Вы уверены, что хотите удалить свой аккаунт?')) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_account.php', true);
        xhr.onload = function () {
          alert(this.responseText);
          if (xhr.status === 200 && this.responseText.includes('успешно')) {
            location.href = 'index.php';
          }
        };
        xhr.send();
      }
    });
  }
}

});

document.addEventListener('DOMContentLoaded', function () {
    const burger = document.getElementById('burgerBtn');
    const navMenu = document.querySelector('.nav-menu');

    if (burger && navMenu) {
      burger.addEventListener('click', function () {
        navMenu.classList.toggle('active');
      });

      window.addEventListener('click', function (e) {
        if (!navMenu.contains(e.target) && e.target !== burger) {
          navMenu.classList.remove('active');
        }
      });
    }
  });
</script>


</body>
</html>
