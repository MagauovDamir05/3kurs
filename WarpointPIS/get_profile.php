<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stmt = $pdo->prepare("SELECT first_name, last_name, phone_number FROM accounts WHERE account_id = ?");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
