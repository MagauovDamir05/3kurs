<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) exit;

$stmt = $pdo->prepare("DELETE FROM accounts WHERE account_id = ?");
$stmt->execute([$_SESSION['user_id']]);
session_destroy();
echo "Аккаунт успешно удален";
