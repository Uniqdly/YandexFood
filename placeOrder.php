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
    
    // Получаем цену блюда из таблицы dishes
    $stmt_dish = $pdo->prepare('SELECT price FROM dishes WHERE name = :name');
    $stmt_dish->bindParam(':name', $dishName);
    $stmt_dish->execute();
    $dish = $stmt_dish->fetch();
    $price = $dish['price'];

    // Добавляем каждый экземпляр блюда в базу данных
    for ($i = 0; $i < $quantity; $i++) {
        $stmt = $pdo->prepare('INSERT INTO Orders (user_id, dishes_name, total_price, status) VALUES (:user_id, :dishes_name, :total_price, :status)');
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':dishes_name', $dishName);
        $total_price = $price; // Используем цену блюда для расчета общей цены
        $stmt->bindParam(':total_price', $total_price);
        $status = 'Обрабатывается';
        $stmt->bindParam(':status', $status);
        $stmt->execute();
    }
}

echo json_encode(['success' => true]);
?>
