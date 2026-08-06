<?php
session_start();
// Проверка авторизации администратора
if (!isset($_SESSION['admin'])) {
    header('Location: ../index.php'); 
    exit();
}
require '../db.php'; 

//  Блокировка / Разблокировка пользователя 
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action']; // "block" или "unblock"
    $id = $_GET['id'];         // ID пользователя
    $status = $action === 'block' ? 'blocked' : 'active'; // Статус на основе действия

    // Обновляем статус пользователя
    $stmt = $pdo->prepare("UPDATE accounts SET status = ? WHERE account_id = ?");
    $stmt->execute([$status, $id]);

    // После изменения статуса — редирект на страницу пользователей
    header('Location: users.php');
    exit();
}

// Обновление данных пользователя (форма редактирования) 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['user_id'];                 // ID пользователя
    $first_name = $_POST['first_name'];     // Имя
    $last_name = $_POST['last_name'];       // Фамилия
    $phone = $_POST['phone_number'];        // Телефон
    $password = $_POST['password'];         // Пароль (может быть пустым)

    if (!empty($password)) {
        // Если пароль введён — хешируем и обновляем
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE accounts SET first_name = ?, last_name = ?, phone_number = ?, password = ? WHERE account_id = ?");
        $stmt->execute([$first_name, $last_name, $phone, $hashedPassword, $id]);
    } else {
        // Если пароль не изменяется — обновляем только остальные поля
        $stmt = $pdo->prepare("UPDATE accounts SET first_name = ?, last_name = ?, phone_number = ? WHERE account_id = ?");
        $stmt->execute([$first_name, $last_name, $phone, $id]);
    }

    // После обновления — редирект
    header('Location: users.php');
    exit();
}

//  Поиск пользователей 
$search = $_GET['search'] ?? '';
$order = $_GET['order'] ?? 'asc';
$order = strtolower($order) === 'desc' ? 'DESC' : 'ASC';

$query = "SELECT * FROM accounts WHERE 1";

if (!empty($search)) {
  $parts = explode(' ', trim($search));

  if (count($parts) >= 2) {
      $query .= " AND ((first_name LIKE :first AND last_name LIKE :last) OR (first_name LIKE :last AND last_name LIKE :first))";
      $query .= " ORDER BY last_name $order";
      $stmt = $pdo->prepare($query);
      $stmt->execute([
          'first' => '%' . $parts[0] . '%',
          'last' => '%' . $parts[1] . '%'
      ]);
  } else {
      $query .= " AND (first_name LIKE :search OR last_name LIKE :search)";
      $query .= " ORDER BY last_name $order";
      $stmt = $pdo->prepare($query);
      $stmt->execute(['search' => "%$search%"]);
  }
} else {
    $query .= " ORDER BY last_name $order";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
}

// Получаем всех (или отфильтрованных) пользователей
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        button {
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
        
        button:hover {
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

        tr:nth-child(even) { background: #f8f9fa; }
        tr:nth-child(odd)  { background: #ffffff; }

        .status-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .status-active {
            background-color: green;
        }

        .status-blocked {
            background-color: red;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            color: white;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #3b82f6;
        }

        .block-btn {
            background-color: #ef4444;
        }

        .unblock-btn {
            background-color: #10b981;
        }

        .action-btn:hover {
            transform: scale(1.1);
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
    background: linear-gradient(135deg, #3b82f6 0%, #ef4444 100%);
    color: black;
    padding: 10px;
    width: 100%;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: transform 0.3s ease;
    }

    .modal-content form button:hover {
        transform: translateY(-2px);
    }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Пользователи</h1>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
  
  <!-- Кнопки сортировки слева -->
  <div style="display: flex; gap: 10px;">
    <a href="users.php?<?= http_build_query(array_merge($_GET, ['order' => 'asc'])) ?>">
      <button type="button">A–Я</button>
    </a>
    <a href="users.php?<?= http_build_query(array_merge($_GET, ['order' => 'desc'])) ?>">
      <button type="button">Я–A</button>
    </a>
  </div>

  <!-- Форма поиска справа -->
  <form method="GET" style="display: flex; gap: 10px; align-items: center;">
    <input type="text" name="search" placeholder="Поиск по имени, фамилии или телефону"
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
           style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; width: 250px;">

    <button type="submit">Найти</button>
    <button type="button" onclick="window.location.href='users.php'">Сброс</button>
  </form>

</div>

</form>
  <table>
    <thead>
      <tr>
        <th>Фамилия</th>
        <th>Имя</th>
        <th>Телефон</th>
        <th>Статус</th>
        <th>Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
      <tr>
        <td><?= htmlspecialchars($user['last_name']) ?></td>
        <td><?= htmlspecialchars($user['first_name']) ?></td>
        <td><?= htmlspecialchars($user['phone_number']) ?></td>
        <td>
            <span class="status-dot <?= $user['status'] === 'active' ? 'status-active' : 'status-blocked' ?>"></span>
            <?= $user['status'] === 'active' ? 'Активный' : 'Заблокирован' ?>
        </td>
        <td>
          <div class="action-buttons">
            <a href="javascript:void(0);" class="action-btn edit-btn" title="Редактировать"
           onclick="openEditModal(
             <?= $user['account_id'] ?>,
             '<?= htmlspecialchars($user['first_name'], ENT_QUOTES) ?>',
             '<?= htmlspecialchars($user['last_name'], ENT_QUOTES) ?>',
             '<?= htmlspecialchars($user['phone_number'], ENT_QUOTES) ?>',
             '<?= htmlspecialchars($user['password'], ENT_QUOTES) ?>'
           )">
          <i class="fas fa-edit"></i>
        </a>
            </a>
            <?php if ($user['status'] === 'active'): ?>
              <a href="?action=block&id=<?= $user['account_id'] ?>" class="action-btn block-btn" title="Заблокировать">
                <i class="fas fa-lock"></i>
              </a>
            <?php else: ?>
              <a href="?action=unblock&id=<?= $user['account_id'] ?>" class="action-btn unblock-btn" title="Разблокировать">
                <i class="fas fa-lock-open"></i>
              </a>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Модальное окно редактирования пользователя -->
<div id="editUserModal" class="modal" style="display:none;">
  <div class="modal-content" style="max-width: 400px;">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2>Редактировать пользователя</h2>
    <form method="POST">
      <input type="hidden" name="user_id" id="edit_user_id">
      <label>Имя:</label>
      <input type="text" name="first_name" id="edit_first_name" required>
      <label>Фамилия:</label>
      <input type="text" name="last_name" id="edit_last_name" required>
      <label>Телефон:</label>
      <input type="text" name="phone_number" id="edit_phone" required>
      <label>Пароль:</label>
      <input type="text" name="password" id="edit_password" placeholder="Новый пароль (необязательно)">
      <button type="submit" name="update_user">Сохранить</button>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const input = document.querySelector('input[name="search"]');

  const resultBox = document.createElement('div');
  resultBox.style.position = 'absolute';
  resultBox.style.zIndex = 1000;
  resultBox.style.backgroundColor = '#1c1c1c';
  resultBox.style.border = '1px solid #444';
  resultBox.style.width = input.offsetWidth + 'px';
  resultBox.style.maxHeight = '200px';
  resultBox.style.overflowY = 'auto';
  resultBox.style.boxShadow = '0 2px 10px rgba(0,0,0,0.5)';
  resultBox.style.display = 'none';
  resultBox.style.borderRadius = '4px';
  resultBox.style.color = 'white';
  resultBox.style.fontSize = '14px';

  document.body.appendChild(resultBox); // добавим к body, чтобы можно было задать абсолютную позицию

  input.addEventListener('input', () => {
    const query = input.value.trim();
    if (query.length < 2) {
      resultBox.style.display = 'none';
      return;
    }

    const rect = input.getBoundingClientRect(); // получаем координаты поля
    resultBox.style.top = window.scrollY + rect.bottom + 'px';
    resultBox.style.left = window.scrollX + rect.left + 'px';
    resultBox.style.width = rect.width + 'px';

    fetch(`search_users.php?query=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        resultBox.innerHTML = '';
        if (data.length > 0) {
          data.forEach(user => {
            const item = document.createElement('div');
            item.textContent = `${user.last_name} ${user.first_name}`;
            item.style.padding = '8px';
            item.style.cursor = 'pointer';
            item.style.borderBottom = '1px solid #333';
            item.addEventListener('click', () => {
              input.value = `${user.last_name} ${user.first_name}`;
              resultBox.style.display = 'none';
            });
            resultBox.appendChild(item);
          });
          resultBox.style.display = 'block';
        } else {
          resultBox.style.display = 'none';
        }
      });
  });

  document.addEventListener('click', (e) => {
    if (!resultBox.contains(e.target) && e.target !== input) {
      resultBox.style.display = 'none';
    }
  });
});
</script>


</body>
</html>
