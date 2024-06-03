<?php
// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

// Получаем id_user из сессии
session_start();
$user_id = $_SESSION['user_id']; 

$data = json_decode(file_get_contents('php://input'), true);

$total_price = 0;
$ingredients_list = [];

foreach ($data['cart'] as $item) {
    $dishName = $item['name'];
    $quantity = $item['quantity'];
    
    // Получаем информацию о блюде из таблицы dishes
    $stmt_dish = $pdo->prepare('SELECT * FROM dishes WHERE name = :name');
    $stmt_dish->bindParam(':name', $dishName);
    $stmt_dish->execute();
    $dish = $stmt_dish->fetch();

    // Увеличиваем общую цену заказа
    $total_price += $dish['price'] * $quantity;

    // Добавляем ингредиенты в общий список
    $ingredients = explode(',', $dish['ingredients_name']);
    $ingredients_list = array_merge($ingredients_list, $ingredients);
}

// Удаляем дублирующиеся ингредиенты
$ingredients_list = array_unique($ingredients_list);
$ingredients_names = implode(', ', $ingredients_list);

$stmt = $pdo->prepare('INSERT INTO Orders (user_id, dishes_name, total_price, ingredients_name, status) VALUES (:user_id, :dishes_name, :total_price, :ingredients_name, :status)');
$stmt->bindParam(':user_id', $user_id);
$dishes_names = implode(', ', array_map(function($item) { return $item['name'] . ' x' . $item['quantity']; }, $data['cart']));
$stmt->bindParam(':dishes_name', $dishes_names);
$stmt->bindParam(':total_price', $total_price);
$stmt->bindParam(':ingredients_name', $ingredients_names);
$status = 'Обрабатывается';
$stmt->bindParam(':status', $status);
$stmt->execute();

echo json_encode(['success' => true]);
?>