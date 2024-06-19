<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['login'];
    $password = $_POST['password'];
    $role = 'user';

    // Проверка формата электронной почты
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = 'Неверный формат электронной почты.';
        header('Location: register.php'); // Предполагается, что ваш файл называется register.php
        exit();
    }

    $pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

    // Проверка существования пользователя с таким логином
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE login = :login');
    $stmt->execute(['login' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['message'] = 'Пользователь с таким email уже существует.';
        header('Location: register.php'); // Перенаправление обратно на страницу регистрации
        exit();
    } else {
        // Добавление нового пользователя
        $stmt = $pdo->prepare('INSERT INTO Users (login, password, role) VALUES (:login, :password, :role)');
        $stmt->execute(['login' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT), 'role' => $role]);

        $_SESSION['message'] = 'Регистрация прошла успешно. Теперь вы можете войти.';
        header('Location: login.php');
        exit();
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <!-- Подключение Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Регистрация</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <form method="post">
        <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" class="form-control" name="login" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
</div>



            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            <a href="login.php" class="ml-2">Уже есть аккаунт?</a>
            <a href="user.php" class="ml-2">Вернуться в меню</a>
        </form>
    </div>
</body>
</html>

