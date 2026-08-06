<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die('Вы не авторизованы');
}

$stmt = $pdo->prepare("SELECT * FROM messages WHERE sender_id = :id OR receiver_id = :id ORDER BY sent_at ASC");
$stmt->execute(['id' => $user_id]);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Чат с админом</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #121212;
      color: #fff;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 800px;
      margin: 40px auto;
      padding: 20px;
      background: #1e1e1e;
      border-radius: 8px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.4);
    }

    h2 {
      color: #fdd835;
      margin-bottom: 20px;
    }

    .message-box {
      max-height: 400px;
      overflow-y: auto;
      padding: 10px;
      border-radius: 5px;
      background: #2b2b2b;
      margin-bottom: 20px;
    }

    .message {
      margin-bottom: 12px;
      padding: 10px;
      border-radius: 5px;
      background-color: #3a3a3a;
      color: #fff;
    }

    .message small {
      display: block;
      color: #bbb;
      margin-top: 5px;
      font-size: 12px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    textarea {
      resize: none;
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-family: inherit;
      font-size: 14px;
      margin-bottom: 10px;
      background: #333;
      color: #fff;
    }

    button {
      padding: 10px;
      background: #fdd835;
      border: none;
      font-weight: bold;
      color: #000;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.2s;
    }

    button:hover {
      background: #e6c200;
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 15px;
      background: #333;
      color: #fdd835;
      padding: 8px 14px;
      text-decoration: none;
      border-radius: 5px;
      transition: background 0.2s;
    }

    .back-btn:hover {
      background: #444;
    }
  </style>
</head>
<body>

<div class="container">
  <a class="back-btn" href="index.php">← Назад</a>

  <h2>Чат с админом</h2>

  <div class="message-box">
    <?php foreach ($messages as $m): ?>
      <div class="message">
        <strong><?= $m['is_admin_sender'] ? 'Админ' : 'Вы' ?>:</strong> <?= htmlspecialchars($m['message']) ?>
        <small><?= date('Y-m-d H:i:s', strtotime($m['sent_at'])) ?></small>
      </div>
    <?php endforeach; ?>
  </div>

  <form action="send_message.php" method="POST">
    <textarea name="message" required placeholder="Сообщение администратору"></textarea>
    <button type="submit">Отправить</button>
  </form>
</div>

</body>
</html>
