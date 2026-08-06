<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin'])) exit;
$user_id = $_GET['user_id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM messages WHERE sender_id = :id OR receiver_id = :id ORDER BY sent_at ASC");
$stmt->execute(['id' => $user_id]);
$messages = $stmt->fetchAll();
?>

<h2>Чат с пользователем <?= $user_id ?></h2>

<div style="max-height:300px; overflow:auto; border:1px solid #ccc; padding:10px;">
<?php foreach ($messages as $m): ?>
  <div style="margin-bottom:10px;">
    <strong><?= $m['is_admin_sender'] ? 'Админ' : "Пользователь $user_id" ?>:</strong>
    <?= htmlspecialchars($m['message']) ?><br>
    <small><?= $m['sent_at'] ?></small>
  </div>
<?php endforeach; ?>
</div>

<form action="send_admin_message.php" method="POST">
  <input type="hidden" name="receiver_id" value="<?= $user_id ?>">
  <textarea name="message" required placeholder="Сообщение пользователю" style="width:100%; height:60px;"></textarea><br>
  <button type="submit">Отправить</button>
</form>
