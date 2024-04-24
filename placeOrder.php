<?php
// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

// Получаем id_user из сессии
session_start();
$user_id = $_SESSION['user_id']; 

$data = json_decode(file_get_contents('php://input'), true);

foreach ($data['cart'] as $item) {
    $dishName = $item['name'];
    
    // Выполняем запрос INSERT с указанием id_user
    $stmt = $pdo->prepare('INSERT INTO Orders (user_id, dishes_name) VALUES (:user_id, :dishes_name)');
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':dishes_name', $dishName);
    $stmt->execute();
}

echo json_encode(['success' => true]);
?>
