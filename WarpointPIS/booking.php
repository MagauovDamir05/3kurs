<?php 
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Вы должны быть авторизованы для бронирования.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем имя и телефон из базы
    $stmt = $pdo->prepare("SELECT first_name, phone_number FROM accounts WHERE account_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "Пользователь не найден.";
        exit;
    }

    $name = $user['first_name'];
    $phone = $user['phone_number'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $tariff = $_POST['tariff'];
    $players = isset($_POST['players']) ? (int)$_POST['players'] : null;

    if (empty($date) || empty($time) || empty($tariff)) {
        echo "Пожалуйста, заполните все обязательные поля.";
        exit;
    }

    try {
        $tariffMap = [
            'Открытая игра' => 1,
            'Lite' => 2,
            'Standart' => 3,
            'Max' => 4
        ];
        $tariff_id = $tariffMap[$tariff] ?? 1;

        $stmt = $pdo->prepare("INSERT INTO reservations 
            (account_id, reservation_name, reservation_phone, tariff_id, reservation_date, reservation_time, players_count, created_at)
            VALUES (:account_id, :name, :phone, :tariff_id, :date, :time, :players, NOW())");

        $stmt->execute([
            'account_id' => $_SESSION['user_id'],
            'name' => $name,
            'phone' => $phone,
            'tariff_id' => $tariff_id,
            'date' => $date,
            'time' => $time,
            'players' => $players
        ]);

        header('Location: index.php');
        exit;

    } catch (Exception $e) {
        echo "Ошибка при сохранении брони: " . $e->getMessage();
        exit;
    }
}
?>
