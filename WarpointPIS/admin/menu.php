<?php
session_start();

// Проверка авторизации администратора
if (!isset($_SESSION['admin'])) {
    header('Location: ../index.php'); // если не админ — редирект на главную
    exit();
}
require '../db.php'; 

$imagePath = null; // переменная для хранения пути к изображению

// Загрузка данных для редактирования 
$editItem = null;
if (isset($_GET['edit'])) {
    // Если передан параметр edit — получаем данные нужного блюда
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
    $stmt->execute([$_GET['edit']]);
    $editItem = $stmt->fetch(); // сохраняем в переменную для отображения в форме
}

// Обработка отправки формы 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];         // Категория блюда
    $item_name = $_POST['item_name'];       // Название блюда
    $description = $_POST['description'];   // Описание
    $price = $_POST['price'];               // Цена
    $menu_id = $_POST['menu_id'] ?? null;   // Если редактируем — будет ID

    //  Загрузка изображения 
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/'; // Путь к папке на сервере
        $webPath = 'uploads/';                 // Путь для ссылки в БД
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true); // Создаем папку если не существует

        $imageName = basename($_FILES['image']['name']);       // Имя файла
        $targetPath = $uploadDir . $imageName;                 // Полный путь

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $webPath . $imageName; // Путь для хранения в БД
        }
    }

    //  Обновление существующей записи 
    if ($menu_id) {
        if ($imagePath) {
            // Если новое изображение — обновляем с картинкой
            $stmt = $pdo->prepare("UPDATE menu SET category = ?, item_name = ?, description = ?, price = ?, image_path = ? WHERE menu_id = ?");
            $stmt->execute([$category, $item_name, $description, $price, $imagePath, $menu_id]);
        } else {
            // Без нового изображения
            $stmt = $pdo->prepare("UPDATE menu SET category = ?, item_name = ?, description = ?, price = ? WHERE menu_id = ?");
            $stmt->execute([$category, $item_name, $description, $price, $menu_id]);
        }

    //  Добавление нового блюда 
    } else {
        $stmt = $pdo->prepare("INSERT INTO menu (category, item_name, description, price, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$category, $item_name, $description, $price, $imagePath]);
    }

    header('Location: menu.php');
    exit();
}

//  Удаление блюда
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM menu WHERE menu_id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: menu.php');
    exit();
}

//  Получение меню с фильтром категории 
$category_filter = $_GET['category'] ?? ''; // фильтр по категории из GET-запроса
$sql = "SELECT * FROM menu";
$params = [];

if (!empty($category_filter)) {
    $sql .= " WHERE category = ?";
    $params[] = $category_filter;
}

// Получение всех (или отфильтрованных) блюд
$menu = $pdo->prepare($sql);
$menu->execute($params);
$menu = $menu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Меню</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .content {
            padding: 30px;
            margin-left: 250px;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            min-height: 100vh;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .menu-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .menu-form input,
        .menu-form textarea {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-family: inherit;
        }

        .menu-form input[type="text"],
        .menu-form input[type="number"] {
            width: 200px;
        }

        .menu-form textarea {
            width: 300px;
            height: 40px;
            resize: none;
        }

        .menu-form button {
            background-color: #fdd835;
            border: none;
            padding: 8px 15px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
        }

        input[type="file"] {
            display: none;
        }

.file-name-preview {
    margin-left: 10px;
    font-size: 14px;
    color: #333;
    font-style: italic;
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

        .add-btn,
        .add-btn-label {
            background: #fdd835;
            color: black;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
            text-align: center;
            text-decoration: none;
        }
        
        .add-btn:hover,
        .add-btn-label:hover {
            background: #e6c200;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
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

        .delete-btn {
            background-color: #ef4444;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        img {
            border-radius: 5px;
        }
        
        .filter-select {
          padding: 8px 12px;
          border: 1px solid #ccc;
          border-radius: 6px;
          font-size: 14px;
          font-family: inherit;
          height: 40px;
          min-width: 150px;
        }
        
        .filter-btn {
          background-color: #fdd835;
          color: black;
          font-weight: bold;
          padding: 8px 20px;
          border-radius: 6px;
          border: none;
          cursor: pointer;
          text-decoration: none;
          text-align: center;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          height: 40px;
          transition: background-color 0.3s ease;
        }
        
        .filter-btn:hover {
          background-color: #e6c200;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
  <h1>Меню</h1>

  <!-- Форма добавления / редактирования -->
  <form method="post" class="menu-form" enctype="multipart/form-data">
    <?php if ($editItem): ?>
      <input type="hidden" name="menu_id" value="<?= $editItem['menu_id'] ?>">
    <?php endif; ?>
    <input name="category" placeholder="Категория" value="<?= $editItem['category'] ?? '' ?>" required>
    <input name="item_name" placeholder="Название" value="<?= $editItem['item_name'] ?? '' ?>" required>
    <textarea name="description" placeholder="Описание"><?= $editItem['description'] ?? '' ?></textarea>
    <input name="price" type="number" step="0.01" placeholder="Цена" value="<?= $editItem['price'] ?? '' ?>" required>

    <label for="fileUpload" class="add-btn-label">Выбрать файл</label>
    <input type="file" id="fileUpload" name="image" style="display: none;" onchange="showFileName(this)">
    <span id="fileName" class="file-name-preview">
      <?= !empty($editItem['image_path']) ? basename($editItem['image_path']) : '' ?>
    </span>

    <button type="submit" class="add-btn"><?= $editItem ? 'Сохранить' : 'Добавить' ?></button>
  </form>

<form method="GET" style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
  <select name="category" id="category_filter" class="filter-select" onchange="this.form.submit()">
    <option value="">Категория</option>
    <?php
    $categories = $pdo->query("SELECT DISTINCT category FROM menu")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($categories as $cat):
    ?>
      <option value="<?= htmlspecialchars($cat) ?>" <?= ($_GET['category'] ?? '') === $cat ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <a href="menu.php" class="filter-btn">Сбросить</a>
</form>


  <table>
    <thead>
      <tr>
        <th>Фото</th>
        <th>Категория</th>
        <th>Название</th>
        <th>Описание</th>
        <th>Цена (₸)</th>
        <th>Действие</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($menu as $item): ?>
        <tr>
          <td>
            <?php if (!empty($item['image_path'])): ?>
              <img src="../<?= htmlspecialchars($item['image_path']) ?>" alt="Фото" width="50">
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($item['category']) ?></td>
          <td><?= htmlspecialchars($item['item_name']) ?></td>
          <td><?= htmlspecialchars($item['description']) ?></td>
          <td><?= number_format($item['price'], 0, '', ' ') ?> ₸</td>
          <td>
            <div class="action-buttons">
              <a href="?edit=<?= $item['menu_id'] ?>" class="action-btn edit-btn" title="Редактировать">
                <i class="fas fa-edit"></i>
              </a>
              <a href="javascript:void(0);" 
                class="action-btn delete-btn" 
                title="Удалить" 
                onclick="deleteMenuItem(<?= $item['menu_id'] ?>, this)">
                <i class="fas fa-trash-alt"></i>
              </a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
function showFileName(input) {
  const fileName = input.files[0]?.name || '';
  document.getElementById('fileName').innerText = fileName;
}

function deleteMenuItem(menuId, element) {
  if (!confirm('Удалить это блюдо?')) return;

  fetch('delete_menu.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'menu_id=' + encodeURIComponent(menuId)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const row = element.closest('tr');
      row.remove();
    } else {
      alert('Ошибка при удалении.');
    }
  })
  .catch(() => alert('Ошибка при соединении с сервером.'));
}
</script>

</body>
</html>
