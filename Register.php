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
</head>
<body>
    <h1>Регистрация</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <p><?php echo $_SESSION['message']; ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <form method="post">
        <label for="name">Имя:</label>
        <input type="text" name="name" required><br>
        <label for="password">Пароль:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Зарегистрироваться</button>
        <a href="login.php">Уже есть аккаунт?</a>
    </form>
</body>
</html>
