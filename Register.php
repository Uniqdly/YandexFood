<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['name'];
    $password = $_POST['password'];
    $role = 'user';

    $pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

    // Проверка существования пользователя с таким логином
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE login = :login');
    $stmt->execute(['login' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['message'] = 'Пользователь с таким логином уже существует.';
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
                <label for="name">Имя:</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            <a href="login.php" class="ml-2">Уже есть аккаунт?</a>
        </form>
    </div>
</body>
</html>

