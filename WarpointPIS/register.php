<?php
session_start();
require 'db.php';

$lastName = trim($_POST['last_name']);
$firstName = trim($_POST['first_name']);
$phone = trim($_POST['phone']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

if (empty($lastName) || empty($firstName) || empty($phone) || empty($_POST['password'])) {
    exit('Пожалуйста, заполните все поля.');
}

$stmt = $pdo->prepare("SELECT * FROM accounts WHERE phone_number = ?");
$stmt->execute([$phone]);

if ($stmt->fetch()) {
    exit('Пользователь с таким номером уже существует.');
}

// Регистрация пользователя
$stmt = $pdo->prepare("INSERT INTO accounts (first_name, last_name, phone_number, password) VALUES (?, ?, ?, ?)");
$stmt->execute([$firstName, $lastName, $phone, $password]);

// Получаем только что созданного пользователя
$userId = $pdo->lastInsertId();
$_SESSION['user_id'] = $userId;
$_SESSION['user_name'] = $firstName;

header('Location: index.php');
exit;
?>
