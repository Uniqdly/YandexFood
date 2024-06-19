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
    <!-- Подключение Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Вход</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="name">Email:</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Войти</button>
            <a href="register.php" class="ml-2">Ещё нет аккаунта?</a>
            <a href="user.php" class="ml-2">Вернуться в меню</a>
        </form>
    </div>
</body>
</html>
