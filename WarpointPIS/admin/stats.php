<?php
session_start();
require '../db.php';

// Проверка авторизации администратора
if (!isset($_SESSION['admin'])) exit;

//  ФИЛЬТРЫ ДЛЯ СТАТИСТИКИ 
// Получаем значения фильтров из URL
$date    = $_GET['date'] ?? '';     // Фильтр по конкретной дате
$tariff  = $_GET['tariff'] ?? '';   // Фильтр по тарифу
$players = $_GET['players'] ?? '';  // Фильтр по количеству игроков
$from    = $_GET['from'] ?? '';     // Начальная дата диапазона
$to      = $_GET['to'] ?? '';       // Конечная дата диапазона

$where = [];    // Массив условий для SQL WHERE
$params = [];   // Массив значений для подстановки в запрос

// Добавляем условия в зависимости от наличия фильтров
if ($date) {
    $where[] = 'r.reservation_date = ?';
    $params[] = $date;
}
if ($tariff) {
    $where[] = 't.tariff_name = ?';
    $params[] = $tariff;
}
if ($players) {
    $where[] = 'r.players_count = ?';
    $params[] = $players;
}
if ($from) {
    $where[] = 'r.reservation_date >= ?';
    $params[] = $from;
}
if ($to) {
    $where[] = 'r.reservation_date <= ?';
    $params[] = $to;
}

// Собираем финальный SQL-фрагмент WHERE
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ЗАПРОС ДЛЯ ВЫВОДА ТАБЛИЦЫ 
// Получаем бронирования с тарифами и фильтрами
$stmt = $pdo->prepare("
    SELECT r.*, t.tariff_name
    FROM reservations r
    JOIN tariffs t ON r.tariff_id = t.tariff_id
    $where_sql
    ORDER BY r.reservation_date DESC
");
$stmt->execute($params);
$reservations = $stmt->fetchAll();  // Все данные для отображения в таблице

// ЗАПРОС ДЛЯ ГРАФИКА 
// Кол-во бронирований на каждую дату
$stmt = $pdo->prepare("
    SELECT r.reservation_date, COUNT(*) as count
    FROM reservations r
    JOIN tariffs t ON r.tariff_id = t.tariff_id
    $where_sql
    GROUP BY r.reservation_date
    ORDER BY r.reservation_date
");
$stmt->execute($params);
$dayReservations = $stmt->fetchAll(); // Для построения диаграммы количества

//  ЗАПРОС ДЛЯ ГРАФИКА №2 
// Среднее количество игроков на каждую дату
$stmt = $pdo->prepare("
    SELECT r.reservation_date, AVG(r.players_count) as avg_players
    FROM reservations r
    JOIN tariffs t ON r.tariff_id = t.tariff_id
    $where_sql
    GROUP BY r.reservation_date
    ORDER BY r.reservation_date
");
$stmt->execute($params);
$dayPlayersAvg = $stmt->fetchAll(); // Для построения диаграммы средней загрузки
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Статистика</title>
  <style>
    body {
  font-family: 'Segoe UI', sans-serif;
  background: #f4f4f4;
  color: #000;
  margin: 0;
  display: flex;
}

.sidebar {
  width: 220px;
  background-color: #1b1b1b;
  color: white;
  height: 100vh;
  padding: 20px;
  position: fixed;
}

.sidebar h2 {
  color: #fdd835;
}

.sidebar ul {
  list-style: none;
  padding: 0;
  margin-top: 30px;
}

.sidebar ul li {
  margin: 20px 0;
}

.sidebar ul li a {
  color: white;
  text-decoration: none;
  font-size: 16px;
  transition: 0.3s;
}

.sidebar ul li a:hover {
  color: #fdd835;
}

.content {
  margin-left: 240px;
  padding: 30px;
  width: 100%;
  min-height: 100vh;
  background-color: #f4f4f4;
}

h1 {
  color: #333;
  font-size: 28px;
  margin-bottom: 20px;
}

.filters {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  align-items: center;
  margin-bottom: 20px;
}

.filters input,
.filters select {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
  background-color: #fff;
  color: #000;
}

.filters button {
  padding: 8px 16px;
  background: #fdd835;
  border: none;
  border-radius: 5px;
  font-weight: bold;
  color: #000;
  cursor: pointer;
}

.filters button:hover {
  background: #e6c300;
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

.filters h2 {
  margin-bottom: 5px;
  color: #fdd835;
}

.filters input[type="date"] {
  min-width: 180px;
}

.charts {
  display: flex;
  flex-wrap: wrap;
  gap: 40px;
  justify-content: center;
  margin-top: 40px;
}

.charts canvas {
  background: #fff;
  border-radius: 12px;
  padding: 10px;
  width: 750px !important;
  height: 400px !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

  </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
  <h1>Статистика бронирований</h1>

  <!-- ФИЛЬТРЫ -->
  <form class="filters" method="GET" id="filterForm">
  <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
  <select name="tariff">
    <option value="">Тариф</option>
    <?php foreach ($pdo->query("SELECT * FROM tariffs") as $t): ?>
      <option value="<?= $t['tariff_name'] ?>" <?= $tariff === $t['tariff_name'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($t['tariff_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <select name="players">
    <option value="">Игроков</option>
    <?php for ($i = 2; $i <= 10; $i++): ?>
      <option value="<?= $i ?>" <?= $players == $i ? 'selected' : '' ?>><?= $i ?></option>
    <?php endfor; ?>
  </select>

  <button type="submit">Фильтровать</button>
  <button type="button" onclick="resetFilters()">Сбросить</button>
</form>


  <!-- ТАБЛИЦА -->
  <table>
    <tr>
      <th>Дата</th>
      <th>Время</th>
      <th>Имя</th>
      <th>Телефон</th>
      <th>Тариф</th>
      <th>Игроков</th>
    </tr>
    <?php foreach ($reservations as $r): ?>
      <tr>
        <td><?= date('d.m.Y', strtotime($r['reservation_date'])) ?></td>
        <td><?= date('H:i', strtotime($r['reservation_time'])) ?></td>
        <td><?= htmlspecialchars($r['reservation_name']) ?></td>
        <td><?= htmlspecialchars($r['reservation_phone']) ?></td>
        <td><?= htmlspecialchars($r['tariff_name']) ?></td>
        <td><?= htmlspecialchars($r['players_count']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <!-- ФИЛЬТРЫ ДЛЯ ДИАГРАММ -->
  <h2 style="margin-top: 40px; color: #333;">Фильтрация диаграмм</h2>
<p style="font-size: 16px; color: #555; margin-bottom: 10px;">
  Выберите <strong>период с</strong> и <strong>по</strong>, чтобы отфильтровать данные:
</p>
<form method="GET" class="filters" style="margin-top: 10px;" id="chartFilterForm">
  <input type="date" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
  <input type="date" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
  <button type="submit">Фильтровать</button>
  <button type="button" onclick="window.location.href='<?= basename($_SERVER["PHP_SELF"]) ?>'">Сбросить</button>
</form>

  <!-- ДИАГРАММЫ -->
<div class="charts">
  <canvas id="dailyRequests"></canvas>
  <canvas id="averagePlayers"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  function resetFilters() {
    const form = document.getElementById('filterForm');
    form.reset(); // сбрасывает значения в форме
    window.location.href = '<?= basename($_SERVER["PHP_SELF"]) ?>'; // перезагружает страницу без параметров
  }

  //  Формируем данные для графика "Количество заявок в день" 
// Формируем массив меток (дат) в формате dd.mm.yyyy
const dailyRequestsData = {
  labels: <?= json_encode(array_map(function($r) {
    return date('d.m.Y', strtotime($r['reservation_date']));
  }, $dayReservations)) ?>, // Получаем все даты бронирований

  datasets: [{
    label: 'Заявки', // Название графика
    data: <?= json_encode(array_column($dayReservations, 'count')) ?>, // Количество заявок на каждую дату
    borderColor: '#fdd835', // Цвет линии
    backgroundColor: '#fdd835', // Цвет точек (совпадает с линией)
    fill: false, // Не закрашивать под линией
    tension: 0.3 // Сглаженность кривой
  }]
};

// Формируем данные для графика "Среднее число игроков в день" 
const averagePlayersData = {
  labels: <?= json_encode(array_map(function($r) {
    return date('d.m.Y', strtotime($r['reservation_date']));
  }, $dayPlayersAvg)) ?>, // Все даты для среднего значения

  datasets: [{
    label: 'Среднее число игроков', // Подпись графика
    data: <?= json_encode(array_map('floatval', array_column($dayPlayersAvg, 'avg_players'))) ?>, // Массив средних значений
    backgroundColor: '#4bc0c0' // Цвет колонок
  }]
};

//  Построение линейного графика "Заявки в день" 
new Chart(document.getElementById('dailyRequests'), {
  type: 'line', // Тип графика: линия
  data: dailyRequestsData,
  options: {
    plugins: {
      title: {
        display: true,
        text: 'Количество заявок в день' // Заголовок графика
      }
    },
    responsive: true, // График адаптивный
    maintainAspectRatio: false, // Не сохранять исходное соотношение сторон
    scales: {
      x: {
        ticks: {
          autoSkip: false, // Не пропускать метки
          maxRotation: 45, // Максимальный угол поворота текста
          minRotation: 45  // Минимальный угол поворота текста
        }
      }
    }
  }
});

//  Построение столбчатого графика "Среднее количество игроков" 
new Chart(document.getElementById('averagePlayers'), {
  type: 'bar', // Тип графика: столбчатый
  data: averagePlayersData,
  options: {
    plugins: {
      title: {
        display: true,
        text: 'Среднее количество игроков в день' // Заголовок графика
      }
    },
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      x: {
        ticks: {
          autoSkip: false,
          maxRotation: 45,
          minRotation: 45
        }
      }
    }
  }
});
</script>

</body>
</html>
