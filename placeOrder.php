<?php
// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

// Получаем id_user из сессии
session_start();
$user_id = $_SESSION['user_id']; 

$data = json_decode(file_get_contents('php://input'), true);

foreach ($data['cart'] as $item) {
    $dishName = $item['name'];
    $quantity = $item['quantity'];
    
    // Добавляем каждый экземпляр блюда в базу данных
    for ($i = 0; $i < $quantity; $i++) {
        $stmt = $pdo->prepare('INSERT INTO Orders (user_id, dishes_name, status) VALUES (:user_id, :dishes_name, :status)');
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':dishes_name', $dishName);
        $status = 'Обрабатывается';
        $stmt->bindParam(':status', $status);
        $stmt->execute();
    }
}

echo json_encode(['success' => true]);
?>
