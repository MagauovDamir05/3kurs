<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../index.php');
    exit;
}

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

$allowed = ['в ожидании', 'принято', 'отклонено'];

if ($id && in_array($status, $allowed)) {
    $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE reservation_id = ?");
    $stmt->execute([$status, $id]);
}

header('Location: bookings.php');
exit;
