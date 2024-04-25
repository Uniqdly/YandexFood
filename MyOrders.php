<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Пользователь не авторизован, перенаправляем на страницу авторизации
    header('Location: Login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

$stmt = $pdo->prepare('SELECT DISTINCT address, phone_number, status FROM Orders WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
</head>
<body>
    <h1>Мои заказы</h1>
    <table>
        <tr>
            <th>Блюда</th>
            <th>Статус</th>
            <th>Адрес</th>
            <th>Время</th>
            <th>Номер телефона</th>
            <th>Комментарий</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>
                    <?php
                    $stmt = $pdo->prepare('SELECT dishes_name FROM Orders WHERE user_id = :user_id AND address = :address AND phone_number = :phone_number AND status = :status');
                    $stmt->bindParam(':user_id', $userId);
                    $stmt->bindParam(':address', $order['address']);
                    $stmt->bindParam(':phone_number', $order['phone_number']);
                    $stmt->bindParam(':status', $order['status']);
                    $stmt->execute();
                    $dishes = $stmt->fetchAll();

                    foreach ($dishes as $dish) {
                        echo $dish['dishes_name'] . '<br>';
                    }
                    ?>
                </td>
                <td><?php echo $order['status']; ?></td>
                <td><?php echo $order['address']; ?></td>
                <td><?php echo $order['time']; ?></td>
                <td><?php echo $order['phone_number']; ?></td>
                <td><?php echo $order['comment']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
