<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Главная</title>
  <!-- Подключаем Inputmask -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/bindings/inputmask.binding.min.js"></script>
    <style>
        body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #111;
    color: #fff;
  }
  
  /* Хэдер */
  .main-header {
    background-color: #1b1b1b;
    padding: 15px 40px;
  }
  
  .nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .nav-menu {
    display: flex;
    list-style: none;
    gap: 30px;
    padding: 0;
    margin: 0;
  }
  
  .nav-menu li a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
  }
  
  .nav-menu li a:hover {
    color: #fdd835;
  }
  
  .nav-right {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  
  .book-btn {
    background-color: #3366ff;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 14px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .book-btn:hover {
    background-color: #264fd1;
  }

  .account-btn {
  background-color: transparent;
  color: #fdd835;
  border: 1px solid #fdd835;
  padding: 10px 20px;
  font-size: 14px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s, color 0.3s;
}

.account-btn:hover {
  background-color: #fdd835;
  color: #111;
}
  
  .hero-block {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #111;
    padding: 60px 20px;
    flex-wrap: wrap;
    gap: 40px;
  }
  
  .hero-image img {
    width: 400px;
    max-width: 90vw;
    transform: skewY(-3deg);
    border: 2px solid #005eff;
    box-shadow: 0 0 20px rgba(0, 0, 255, 0.1);
    border-radius: 10px;
  }
  
  .hero-text {
    color: white;
    font-size: 36px;
    font-weight: bold;
    line-height: 1.3;
    text-transform: uppercase;
    max-width: 600px;
  }
  
  .hero-text p {
    margin: 0 0 10px 0;
  }
  
  .hero-text span {
    background-color: #e53939;
    padding: 5px 15px;
    display: inline-block;
    color: #fff;
    transform: skewX(-10deg);
    margin-left: 10px;
  }

  .site-footer {
  background-color: #121212;
  color: white;
  padding: 60px 40px 20px;
  font-family: sans-serif;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  max-width: 1200px;
  margin: 0 auto;
}

.footer-left,
.footer-menu,
.footer-contacts {
  flex: 1;
  min-width: 220px;
  margin-bottom: 30px;
}

.footer-left h3 {
  font-size: 24px;
  margin-bottom: 15px;
}

.footer-left p {
  font-size: 16px;
  line-height: 1.5;
}

.footer-menu h4,
.footer-contacts h4 {
  font-size: 18px;
  margin-bottom: 15px;
}

.footer-menu ul {
  list-style: none;
  padding: 0;
}

.footer-menu ul li {
  margin-bottom: 10px;
}

.footer-menu a {
  color: white;
  text-decoration: none;
  transition: color 0.3s;
}

.footer-menu a:hover {
  color: #e52328;
}

.footer-contacts p {
  font-size: 14px;
  margin-bottom: 8px;
}

.footer-bottom {
  text-align: center;
  border-top: 1px solid #333;
  margin-top: 40px;
  padding-top: 20px;
  font-size: 14px;
  color: #888;
}

/* Скрыть бургер на десктопе */
.burger-btn {
  display: none;
  font-size: 28px;
  background: none;
  border: none;
  color: #fff;
  cursor: pointer;
}

/* Показывать бургер и скрывать нав-меню на мобилках */
@media (max-width: 768px) {
  .nav-menu {
    display: none;
    flex-direction: column;
    background-color: #1b1b1b;
    position: absolute;
    top: 60px;
    right: 0;
    width: 200px;
    z-index: 1000;
    padding: 20px;
    border-radius: 8px;
  }

  .nav-menu.active {
    display: flex;
  }

  .burger-btn {
    display: block;
  }

  .nav-right {
    display: none;
  }
}

</style>
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

<!-- КОНТАКТНАЯ ИНФОРМАЦИЯ -->
<section class="hero-block">
  <div class="hero-text">
    <p>УЗНАЙТЕ БОЛЬШЕ</p>
    <p><span>О ВСЕЛЕННОЙ WARPOINT</span></p>
  </div>

</section>

<section class="contact-section" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; padding: 40px 20px;">
  <div style="flex: 1; min-width: 320px;">
  <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A72dcac4fbd2d17ae95c4d37c0106df7dcbccba9797b828a9de4c6a6baf37c392&amp;source=constructor" width="100%" height="300" frameborder="0"></iframe>
  </div>
  <div style="flex: 1; min-width: 320px; color: white; font-size: 18px; max-width: 400px;">
    <p><strong>+7 (705) 123-45-67</strong></p>
    <p>г. Петропавловск, ул. Партизанская, 48Б<br> ТД "Галерея"</p>
    <p>ПН-ВС: 10:00–22:00</p>
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
