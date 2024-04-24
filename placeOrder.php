<?php
// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

$data = json_decode(file_get_contents('php://input'), true);

foreach ($data['cart'] as $item) {
    $dishName = $item['name'];
    $stmt = $pdo->prepare('INSERT INTO Orders (dishes_name) VALUES (:dishes_name)');
    $stmt->bindParam(':dishes_name', $dishName);
    $stmt->execute();
}

echo json_encode(['success' => true]);
?>
