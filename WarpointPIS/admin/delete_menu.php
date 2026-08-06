<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id = $_POST['menu_id'] ?? null;

    if ($menu_id) {
        $stmt = $pdo->prepare("DELETE FROM menu WHERE menu_id = ?");
        $stmt->execute([$menu_id]);
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false]);
