<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка данных формы регистрации
    $username = $_POST['name']; // Используем введенное имя как логин
    $password = $_POST['password'];
    $role = 'user'; // Устанавливаем роль по умолчанию
    // Дополнительные данные пользователя, если необходимо

    // Подключение к базе данных
    $pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

    // Подготовка и выполнение запроса на добавление нового пользователя
    $stmt = $pdo->prepare('INSERT INTO Users (login, password, role) VALUES (:login, :password, :role)'); 
    $stmt->execute(['login' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT), 'role' => $role]); 

    $_SESSION['message'] = 'Регистрация прошла успешно. Теперь вы можете войти.';
    header('Location: login.php');
    exit();
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
        <!-- Дополнительные поля для данных пользователя -->
        <button type="submit">Зарегистрироваться</button>
    </form>
</body>
</html>
