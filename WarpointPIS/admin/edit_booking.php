<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $players = $_POST['players'];
    $tariff = $_POST['tariff'];

    $stmt = $pdo->prepare("UPDATE reservations SET reservation_date = ?, reservation_time = ?, reservation_name = ?, reservation_phone = ?, players_count = ?, tariff_id = ? WHERE reservation_id = ?");
    $stmt->execute([$date, $time, $name, $phone, $players, $tariff, $id]);


    header('Location: bookings.php');
    exit;
}
?>
