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
    <title>My Orders</title>
</head>
<body>
<table>
    <tr>
        <th>Статус</th>
    </tr>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo $order['status']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

    <!-- Кнопка "Logout" -->
    <button onclick="logout()">Logout</button>
    <button onclick="GoMenu()">В меню</button>
        <script>

        function GoMenu() 
        {
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
