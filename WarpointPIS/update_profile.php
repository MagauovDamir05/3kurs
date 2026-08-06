<?php
session_start();
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($_SESSION['user_id'])) exit;

$stmt = $pdo->prepare("UPDATE accounts SET first_name = ?, last_name = ?, phone_number = ?" . 
                      ($data['password'] ? ", password = ?" : "") . 
                      " WHERE account_id = ?");

$params = [
    $data['first_name'],
    $data['last_name'],
    $data['phone'],
];

if ($data['password']) {
    $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
}

$params[] = $_SESSION['user_id'];
$stmt->execute($params);
echo "Данные успешно обновлены";
