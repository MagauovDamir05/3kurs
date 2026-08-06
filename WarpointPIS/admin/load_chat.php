<?php
require '../db.php';
session_start();

$admin_id = $_SESSION['admin'] ?? null;
$user_id = $_GET['user_id'] ?? 0;

$stmt = $pdo->prepare("
  SELECT * FROM messages
  WHERE (sender_id = :uid AND is_admin_sender = 0)
     OR (receiver_id = :uid AND is_admin_sender = 1)
  ORDER BY sent_at ASC
");
$stmt->execute(['uid' => $user_id]);
$messages = $stmt->fetchAll();

echo json_encode($messages);
