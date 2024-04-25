<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка данных формы авторизации
    $username = $_POST['name']; // Используем введенное имя как логин
    $password = $_POST['password'];

    // Подключение к базе данных
    $pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

    // Подготовка и выполнение запроса на получение пользователя по логину
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE login = :login');
    $stmt->execute(['login' => $username]); 
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Авторизация успешна, начинаем сессию
        $_SESSION['cart'] = [];
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id']; // Записываем ID пользователя в сессию
        $_SESSION['role'] = $user['role']; // Записываем роль пользователя в сессию

        // Проверка роли пользователя и перенаправление на соответствующую страницу
        switch ($user['role']) {
            case 'cook':
                header('Location: Kitchen.php');
                break;
            case 'courier':
                header('Location: kyrer.php');
                break;
            case 'manager':
                header('Location: meneger.php');
                break;
            default:
                header('Location: user.php');
                break;
        }
        exit();
    } else {
        $_SESSION['message'] = 'Неверный логин или пароль.';
        header('Location: login.php');
        exit();
    }
}



?>

<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
</head>
<body>
    <h1>Вход</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <p><?php echo $_SESSION['message']; ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <form method="post">
        <label for="name">Имя:</label>
        <input type="text" name="name" required><br>
        <label for="password">Пароль:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Войти</button>
    </form>
</body>
</html>
