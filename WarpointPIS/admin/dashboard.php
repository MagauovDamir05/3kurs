<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ../index.php');
    exit();
}

// AJAX-запрос на цитату
if (isset($_GET['get_quote'])) {
    $quote = file_get_contents('https://favqs.com/api/qotd');
    header('Content-Type: application/json');
    echo $quote;
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Admin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <h1>Добро пожаловать в админ-панель Warpoint</h1>
    <p>Выберите раздел слева для управления системой.</p>

    <!-- Цитата дня -->
    <div id="quote-box" style="
        margin-top: 30px;
        background: transparent;
        color: #000;
        padding: 20px;
        border-radius: 8px;
        max-width: 600px;
        font-family: 'Segoe UI', sans-serif;
    ">
        <h3 style="margin-bottom: 10px; font-size: 18px;">💬 Цитата дня:</h3>
        <p id="quote-text" style="font-style: italic; font-size: 16px;">Загрузка...</p>
        <p id="quote-author" style="text-align: right; margin-top: 10px; font-weight: 500;">—</p>
    </div>
</div>

<script>
function loadQuote() {
  fetch('dashboard.php?get_quote=1')
    .then(res => res.json())
    .then(data => {
      document.getElementById('quote-text').textContent = data.quote.body;
      document.getElementById('quote-author').textContent = '— ' + data.quote.author;
    })
    .catch(() => {
      document.getElementById('quote-text').textContent = "Failed to load quote.";
      document.getElementById('quote-author').textContent = "";
    });
}

loadQuote();
</script>

</body>
</html>
