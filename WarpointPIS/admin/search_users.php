<?php
require '../db.php';

$query = trim($_GET['query'] ?? '');

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$parts = explode(' ', $query);
$params = [];
$sql = "SELECT first_name, last_name, phone_number FROM accounts WHERE ";

if (count($parts) >= 2) {
    // Поиск по "Фамилия Имя" или "Имя Фамилия"
    $sql .= "(first_name LIKE :first AND last_name LIKE :last)
             OR (first_name LIKE :last AND last_name LIKE :first)";
    $params = [
        'first' => "%{$parts[0]}%",
        'last' => "%{$parts[1]}%",
    ];
} else {
    // Одно слово — ищем в имени или фамилии
    $sql .= "(first_name LIKE :query OR last_name LIKE :query)";
    $params = ['query' => "%$query%"];
}

$sql .= " LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($results);
