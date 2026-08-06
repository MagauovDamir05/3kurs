<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Тарифы</title>
  <!-- Подключаем Inputmask -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/bindings/inputmask.binding.min.js"></script>
</head>
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

  .tariff-section {
  padding: 60px 20px;
  background-color: #111;
}

.tariff-container {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  justify-content: center;
  max-width: 1200px;
  margin: auto;
}

.tariff-card {
  background-color: #1a1a1a;
  border-radius: 16px;
  overflow: hidden;
  width: 320px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
  display: flex;
  flex-direction: column;
  position: relative;
  padding-top: 50px; /* чтобы не обрезался баннер */
}

.tariff-image {
  position: relative;
  height: 260px; /* увеличено */
  overflow: hidden;
  border-bottom: 5px solid #e52328; /* красная декоративная полоса */
  border-radius: 10px 10px 0 0;
}

.tariff-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.tariff-banner {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: #e52328;
  color: white;
  padding: 8px 16px;
  font-weight: bold;
  font-size: 16px;
  transform: skewX(-10deg);
  z-index: 2;
  box-shadow: 0 4px 6px rgba(0,0,0,0.3);
  border-radius: 4px;
}

.tariff-info {
  padding: 20px;
  color: #fff;
  font-size: 14px;
  line-height: 1.5;
}

.tariff-info h3 {
  color: #fdd835;
  font-size: 18px;
  margin-bottom: 10px;
}

.tariff-price {
  font-weight: bold;
  color: #fff;
  margin: 15px 0;
}

.tariff-btn {
  background-color: #3366ff;
  border: none;
  color: white;
  padding: 10px;
  width: 100%;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s;
}

.tariff-btn:hover {
  background-color: #264fd1;
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

<section class="tariff-section">
  <div class="tariff-container">
    
    <div class="tariff-card">
      <div class="tariff-image">
        <img src="images/Offer1.png" alt="Lite">
        <div class="tariff-banner">ТАРИФ “LITE”</div>
      </div>
      <div class="tariff-info">
        <h3>ТАРИФ "LITE"</h3>
        <p>Откройте для себя мир виртуальной реальности вместе с нашим стартовым пакетом, который отлично подходит для небольших компаний</p>
        <p>1 час 45 минут<br>до 10 человек</p>
        <p class="tariff-price">ОТ 59 000 ТНГ.</p>
        <button class="tariff-btn">Оставить заявку</button>
      </div>
    </div>

    <div class="tariff-card">
      <div class="tariff-image">
        <img src="images/Offer2.png" alt="Standard">
        <div class="tariff-banner">ТАРИФ “STANDARD”</div>
      </div>
      <div class="tariff-info">
        <h3>ТАРИФ "STANDARD"</h3>
        <p>Оптимальный тариф для небольших компаний. Полное погружение в игровую атмосферу и незабываемые эмоции для всех гостей гарантированы!</p>
        <p>2 часа 45 минут<br>до 15 человек</p>
        <p class="tariff-price">ОТ 79 000 ТНГ.</p>
        <button class="tariff-btn">Оставить заявку</button>
      </div>
    </div>

    <div class="tariff-card">
      <div class="tariff-image">
        <img src="images/Offer3.png" alt="Max">
        <div class="tariff-banner">ТАРИФ “MAX”</div>
      </div>
      <div class="tariff-info">
        <h3>ТАРИФ "MAX"</h3>
        <p>Самый популярный тариф в нашей линейке, для тех, кто хочет отправиться в путешествие по виртуальным мирам по полной!</p>
        <p>3 часа 45 минут<br>до 20 человек</p>
        <p class="tariff-price">ОТ 99 000 ТНГ.</p>
        <button class="tariff-btn">Оставить заявку</button>
      </div>
    </div>

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
