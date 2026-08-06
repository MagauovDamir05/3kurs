<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../index.php');
    exit;
}

// Обработка формы добавления бронирования
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'], $_POST['time'], $_POST['name'], $_POST['tariff'])) {
  $date = $_POST['date'];
  $time = $_POST['time'];
  $account_id = $_POST['name']; 
  $phone = $_POST['phone'];
  $tariffName = $_POST['tariff'];
  $players = $_POST['players'] ?? null;

  // Получаем имя пользователя по account_id
  $stmt = $pdo->prepare("SELECT first_name FROM accounts WHERE account_id = ? LIMIT 1");
  $stmt->execute([$account_id]);
  $account = $stmt->fetch();
  $user_name = $account ? $account['first_name'] : '';

  // Получаем tariff_id по названию
  $stmt = $pdo->prepare("SELECT tariff_id FROM tariffs WHERE tariff_name = ? LIMIT 1");
  $stmt->execute([$tariffName]);
  $tariff = $stmt->fetch();

  if ($tariff) {
      $tariff_id = $tariff['tariff_id'];

      // Добавление бронирования
      $stmt = $pdo->prepare("
        INSERT INTO reservations 
        (reservation_date, reservation_time, reservation_name, reservation_phone, tariff_id, players_count, account_id, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'в ожидании', NOW())
    ");
    $stmt->execute([$date, $time, $user_name, $phone, $tariff_id, $players, $account_id]);

      header('Location: bookings.php');
      exit;
  }
}

// Удаление бронирования
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE reservation_id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: bookings.php');
    exit;
}

// Получение данных для редактирования
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE reservation_id = ?");
    $stmt->execute([$_GET['edit']]);
    $editData = $stmt->fetch();
}

// Обработка фильтров
$date_filter = $_GET['date'] ?? '';
$tariff_filter = $_GET['tariff'] ?? '';
$players_filter = $_GET['players'] ?? '';

$sql = "SELECT r.*, t.tariff_name 
        FROM reservations r 
        LEFT JOIN tariffs t ON r.tariff_id = t.tariff_id";

$where = [];
$params = [];

if (!empty($date_filter)) {
    $where[] = "r.reservation_date = ?";
    $params[] = $date_filter;
}
if (!empty($tariff_filter)) {
    $where[] = "t.tariff_id = ?";
    $params[] = $tariff_filter;
}
if (!empty($players_filter)) {
    $where[] = "r.players_count = ?";
    $params[] = $players_filter;
}

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получение тарифов
$tariffs = $pdo->query("SELECT * FROM tariffs")->fetchAll(PDO::FETCH_ASSOC);
// Получение пользователей
$users = $pdo->query("SELECT account_id, first_name, last_name FROM accounts")->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Бронирования</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .filters {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        .filter-group select, 
        .filter-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        button, .add-btn {
            background: #FFD700; /* Ярко-жёлтый фон */
            color: #000;          /* Чёрный текст */
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover, .add-btn:hover {
            background: #e6c200; /* Более насыщённый жёлтый при наведении */
            transform: translateY(-2px);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #111; /* Чёрный или почти чёрный фон вокруг таблицы */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 12px rgba(0,0,0,0.4);
        }

        th {
            background-color: #000; /* Чёрный заголовок */
            color: #fff;             /* Белый текст */
            font-weight: bold;
            padding: 14px 20px;
            text-align: left;
            font-size: 16px;
        }

        td {
            background-color: #fff; /* БЕЛЫЙ фон ячеек */
            color: #000;             /* ЧЁРНЫЙ текст в ячейках */
            padding: 14px 20px;
            border-bottom: 2px solid #ddd;
            font-size: 15px;
        }

        tr:hover td {
            background-color: #f5f5f5; /* Лёгкая подсветка строки при наведении */
            transition: background-color 0.3s ease;
        }

        
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .edit-btn {
            background: #3b82f6;
            color: white;
        }
        
        .delete-btn {
            background: #ef4444;
            color: white;
        }
        
        .add-btn {
            background: #e6c200;
            color: #000;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        /* Общие стили для модальных окон */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 60px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.8);
  }
  
  /* Контент модального окна */
  .modal-content {
    background-color: #1c1c1c;
    margin: auto;
    padding: 20px 30px;
    border: 1px solid #888;
    width: 100%;
    max-width: 400px;
    border-radius: 8px;
    color: white;
    position: relative;
  }
  
  /* Закрыть окно (крестик) */
  .modal-content .close {
    position: absolute;
    top: 15px;
    right: 20px;
    color: white;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
  }
  
  /* Заголовки в модалках */
  .modal-content h2 {
    text-align: center;
    color: #FFD700;
    margin-bottom: 20px;
    font-size: 22px;
  }
  
  /* Стили для форм в модалках */
  .modal-content form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }
  
  .modal-content form input,
  .modal-content form select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: none;
    border-radius: 6px;
    background: #2c2c2c;
    color: white;
  }
  
  .modal-content form input::placeholder {
    color: #999;
  }
  
  /* Кнопка отправки формы */
  .modal-content form button {
    background-color: #FFD700;
    color: black;
    padding: 10px;
    width: 100%;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }
  
  .modal-content form button:hover {
    background-color: #e6c200;
  }

  .status-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
}

/* Иконка-цветочек возле текста статуса */
.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

/* Цвета для состояний */
.status-pending {
    background-color: #ffffff; /* БЕЛЫЙ кружок для "в ожидании" */
    border: 1px solid #ccc; /* Тонкая серая обводка, чтобы белый кружок был виден */
}

.status-approved {
    background-color: #10b981; /* светло-зелёный цвет для принято */
}

.status-rejected {
    background-color: #ef4444; /* красный цвет для отклонено */
}

/* Выпадающий список статуса */
.status-select {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-weight: bold;
    background-color: #fff;
    color: #000; /* Базовый цвет текста (чёрный) */
}

/* Цвет options — только для раскрытия */
.status-select option[value="в ожидании"] {
    color: #999999; /* Серый текст */
}

.status-select option[value="принято"] {
    color: #10b981; /* Светло-зелёный текст */
}

.status-select option[value="отклонено"] {
    color: #ef4444; /* Красный текст */
}



    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
  <div class="header-actions">
    <h1>Бронирования</h1>
    <button class="add-btn" onclick="openModal()">Добавить</button>
  </div>

  <form method="GET" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
  <div class="filter-group">
    <label for="date">Дата</label>
    <input type="date" id="date" name="date" value="<?= htmlspecialchars($date_filter) ?>">
  </div>

  <div class="filter-group">
    <label for="tariff">Тариф</label>
    <select id="tariff" name="tariff">
      <option value="">Все</option>
      <?php foreach ($tariffs as $tariff): ?>
        <option value="<?= $tariff['tariff_id'] ?>" <?= $tariff_filter == $tariff['tariff_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($tariff['tariff_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="filter-group">
    <label for="players">Кол-во игроков</label>
    <select id="players" name="players">
      <option value="">Любое</option>
      <?php for ($i = 1; $i <= 10; $i++): ?>
        <option value="<?= $i ?>" <?= $players_filter == $i ? 'selected' : '' ?>><?= $i ?></option>
      <?php endfor; ?>
    </select>
  </div>

  <button type="submit">Фильтровать</button>
  <button type="button" onclick="window.location.href='bookings.php'">Сброс</button>
</form>

  <!-- Таблица бронирований -->
  <table>
    <thead>
      <tr>
      <th>Дата</th><th>Время</th><th>Имя</th><th>Телефон</th><th>Тариф</th><th>Игроков</th><th>Статус</th><th>Создано</th><th>Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bookings as $b): ?>
        <tr>
          <td><?= htmlspecialchars(date('d.m.Y', strtotime($b['reservation_date']))) ?></td>
          <td><?= htmlspecialchars(date('H:i', strtotime($b['reservation_time']))) ?></td>
          <td><?= htmlspecialchars($b['reservation_name']) ?></td>
          <td><?= htmlspecialchars($b['reservation_phone']) ?></td>
          <td><?= htmlspecialchars($b['tariff_name']) ?></td>
          <td><?= htmlspecialchars($b['players_count']) ?></td>
          <td>
            <div class="status-label">
              <span class="status-dot 
                <?= $b['status'] === 'принято' ? 'status-approved' : ($b['status'] === 'отклонено' ? 'status-rejected' : 'status-pending') ?>">
              </span>
              <?= htmlspecialchars($b['status']) ?>
            </div>
          </td>
          <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($b['created_at']))) ?></td>
          <td>
            <div class="action-buttons">
            <div class="status-dropdown">
              <form method="POST" action="update_status.php">
                <input type="hidden" name="id" value="<?= $b['reservation_id'] ?>">
                <select name="status" class="status-select" onchange="this.form.submit()">
                  <option value="в ожидании" <?= $b['status'] === 'в ожидании' ? 'selected' : '' ?>>⚪ в ожидании</option>
                  <option value="принято" <?= $b['status'] === 'принято' ? 'selected' : '' ?>>🟢 принято</option>
                  <option value="отклонено" <?= $b['status'] === 'отклонено' ? 'selected' : '' ?>>🔴 отклонено</option>
                </select>
              </form>
            </div>
              <a href="?edit=<?= $b['reservation_id'] ?>" class="action-btn edit-btn" title="Редактировать"><i class="fas fa-edit"></i></a>
              <a href="?delete=<?= $b['reservation_id'] ?>" class="action-btn delete-btn" title="Удалить" onclick="return confirm('Удалить бронирование?')"><i class="fas fa-trash-alt"></i></a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Модальное окно бронирования -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Забронировать игру</h2>
    <form id="bookingForm" action="bookings.php" method="POST">
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

      <label for="user">Пользователь:</label>
      <select id="user" name="name" required>
        <option value="">Выберите пользователя</option>
        <?php foreach ($users as $user): ?>
          <option value="<?= $user['account_id'] ?>">
            <?= htmlspecialchars($user['last_name'] . ' ' . $user['first_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="phone">Телефон:</label>
      <input type="text" id="phone" name="phone" placeholder="Введите телефон" required>

      <label for="tariff">Тариф:</label>
      <select id="tariffAdd" name="tariff" required>
        <option value="">Выберите тариф</option>
        <option value="Открытая игра">Открытая игра</option>
        <option value="Lite">Тариф Lite</option>
        <option value="Standart">Тариф Standart</option>
        <option value="Max">Тариф Max</option>
      </select>

      <div id="additionalOptionsAdd">
        <label for="players">Количество игроков:</label>
        <select id="players" name="players">
          <option value="">Выберите количество</option>
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

<?php if ($editData): ?>
<div id="editModal" class="modal" style="display:block;">
  <div class="modal-content">
    <span class="close" onclick="closeEditModal()">&times;</span>
    <h2>Редактировать бронирование</h2>
    <form action="edit_booking.php" method="POST">
  <input type="hidden" name="id" value="<?= $editData['reservation_id'] ?>">

  <label for="edit_date">Дата:</label>
  <input type="date" name="date" value="<?= $editData['reservation_date'] ?>" required>

  <label for="edit_time">Время:</label>
  <select name="time" required>
    <?php
    $times = ["11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00"];
    foreach ($times as $time) {
      $selected = ($editData['reservation_time'] === $time) ? 'selected' : '';
      echo "<option value=\"$time\" $selected>$time</option>";
    }
    ?>
  </select>

  <label for="edit_name">Имя:</label>
  <input type="text" name="name" value="<?= htmlspecialchars($editData['reservation_name']) ?>" required>

  <label for="edit_phone">Телефон:</label>
  <input type="text" name="phone" value="<?= htmlspecialchars($editData['reservation_phone']) ?>" required>

  <label for="tariff">Тариф:</label>
  <select name="tariff" required>
    <?php foreach ($tariffs as $tariff): ?>
      <option value="<?= $tariff['tariff_id'] ?>" <?= ($tariff['tariff_id'] == $editData['tariff_id']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($tariff['tariff_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label for="edit_players">Игроков:</label>
  <input type="number" name="players" min="1" max="10" value="<?= $editData['players_count'] ?>">

  <button type="submit">Изменить</button>
</form>
  </div>
</div>
<?php endif; ?>

<script>
// Открытие окна бронирования
function openModal() {
  document.getElementById('addModal').style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function () {
  const dateInputs = document.querySelectorAll('input[type="date"]');

  dateInputs.forEach(input => {
    input.addEventListener('click', function() {
      this.showPicker(); // Открыть календарь при клике на input
    });
  });
});

document.addEventListener('DOMContentLoaded', function () {
  const tariffSelectAdd = document.getElementById('tariffAdd');
  const additionalOptionsAdd = document.getElementById('additionalOptionsAdd');

  // Закрытие модалок по кнопке
  document.querySelectorAll('.modal .close').forEach(btn => {
    btn.onclick = function () {
      this.closest('.modal').style.display = 'none';
    };
  });

  // Закрытие при клике вне окна
  window.onclick = function (event) {
    document.querySelectorAll('.modal').forEach(modal => {
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    });
  };
});

function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
  window.history.pushState({}, document.title, "bookings.php"); // убрать ?edit из URL
}

function toggleOptions() {
  const value = document.getElementById('tariffSelect').selectedOptions[0]?.text;
  document.getElementById('additionalOptions').style.display = (value === 'Открытая игра') ? 'block' : 'none';
}
window.onclick = function(event) {
  const modal = document.getElementById('addModal');
  if (event.target == modal) modal.style.display = 'none';
}

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.status-select').forEach(select => {
    setSelectColor(select);
    select.addEventListener('change', () => setSelectColor(select));
  });

  function setSelectColor(select) {
    const value = select.value;
    if (value === "в ожидании") {
      select.style.color = "#999999"; // Серый
    } else if (value === "принято") {
      select.style.color = "#10b981"; // Светло-зелёный
    } else if (value === "отклонено") {
      select.style.color = "#ef4444"; // Красный
    } else {
      select.style.color = "#000"; // Чёрный по умолчанию
    }
  }
});
</script>

</script>
</body>
</html>