<?php 
session_start();

$conn = new mysqli("localhost", "root", "", "delivery");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Менеджер</title>
    <!-- Подключение Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .nav-tabs {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function logout() {
            fetch('logout.php')
                .then(() => {
                    window.location.href = 'logout.php';
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Менеджер</h2>
    <button class="btn btn-danger float-right mb-3" onclick="logout()">Выход</button>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="meneger.php?page=users">Управление пользователями</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="meneger.php?page=add_dish">Добавить блюдо</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="meneger.php?page=edit_menu">Редактировать меню</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="meneger.php?page=orders">Управление заказами</a>
        </li>
    </ul>

    <div>
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'users';
        switch ($page) {
            case 'add_dish':
                include 'add_dish.php';
                break;
            case 'edit_menu':
                include 'edit_menu.php';
                break;
            case 'orders':
                include 'orders.php';
                break;
            case 'users':
            default:
                include 'users.php';
                break;
        }
        ?>
    </div>
</div>
</body>
</html>