<?php
session_start();
require 'db.php';

$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

// Проверка входа как админ
if ($phone === '87771234567' && $password === 'admin') {
    $_SESSION['admin'] = true;
    header('Location: admin/dashboard.php');
    exit();
}

// Проверка обычного пользователя
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE phone_number = ?");
$stmt->execute([$phone]);
$user = $stmt->fetch();

if ($user) {
    if ($user['status'] === 'blocked') {
        echo "<script>alert('Ваш аккаунт заблокирован администрацией.');window.location.href='index.php';</script>";
        exit();
    }

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['account_id'];
        $_SESSION['user_name'] = $user['first_name'];
        header('Location: index.php');
        exit();
    }
}

echo "<script>alert('Неверный номер телефона или пароль.');window.location.href='index.php';</script>";
exit();
