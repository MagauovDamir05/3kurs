<?php
require '../db.php';
session_start();

if (!isset($_SESSION['admin'])) exit;

$user_id = $_GET['user_id'] ?? 0;

// 1. Пометить входящие сообщения от пользователя как прочитанные
$markRead = $pdo->prepare("
  UPDATE messages 
  SET is_read = 1 
  WHERE sender_id = ? AND is_admin_sender = 0 AND is_read = 0
");
$markRead->execute([$user_id]);

// 2. Получить всю переписку между админом и этим пользователем
$stmt = $pdo->prepare("
  SELECT * FROM messages
  WHERE (sender_id = :uid AND receiver_id = 0)
     OR (receiver_id = :uid AND sender_id = 0)
  ORDER BY sent_at ASC
");
$stmt->execute(['uid' => $user_id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
