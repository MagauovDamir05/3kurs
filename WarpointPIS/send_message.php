<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || empty($_POST['message'])) {
    die('Ошибка отправки');
}

$stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, is_admin_sender) VALUES (?, 0, ?, 0)");
$stmt->execute([$_SESSION['user_id'], $_POST['message']]);

header('Location: messages.php');
