<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin']) || empty($_POST['message']) || empty($_POST['receiver_id'])) {
    die('Ошибка отправки');
}

$stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, is_admin_sender) VALUES (0, ?, ?, 1)");
$stmt->execute([$_POST['receiver_id'], $_POST['message']]);

header('Location: messages.php');
exit();
