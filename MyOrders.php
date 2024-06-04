<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Пользователь не авторизован, перенаправляем на страницу авторизации
    header('Location: Login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

$stmt = $pdo->prepare('SELECT * FROM Orders WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Мои заказы</title>
    <!-- Подключение стилей -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>Статус</th>
            <th>Блюда</th>
            <th>Сумма</th>
            <th>Адрес</th>
            <th>Время</th>
            <th>Номер телефона</th>
            <th>Комментарий</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo $order['status']; ?></td>
                <td><?php echo $order['dishes_name']; ?></td>
                <td><?php echo $order['total_price']; ?></td>
                <td><?php echo $order['address']; ?></td>
                <td><?php echo $order['time']; ?></td>
                <td><?php echo $order['phone_number']; ?></td>
                <td><?php echo $order['comment']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Кнопка "Logout" -->
    <button onclick="GoMenu()">В меню</button>
    <button onclick="logout()">Выход</button>
    <script>
        function GoMenu() {
            // Перенаправление на страницу меню
            window.location.href = 'User.php';
        }

        function logout() {
            // Очистка сессии и перенаправление на страницу выхода
            fetch('logout.php')
                .then(() => {
                    window.location.href = 'logout.php';
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>

