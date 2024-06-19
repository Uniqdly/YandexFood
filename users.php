<form action="users.php" method="post">
    <div class="form-group">
        <label for="user_email">Email пользователя:</label>
        <input type="email" class="form-control" id="user_email" name="user_email" required>
    </div>
    <div class="form-group">
        <label for="role">Выберите роль:</label>
        <select class="form-control" id="role" name="role" required>
            <option value="courier">Курьер</option>
            <option value="manager">Менеджер</option>
            <option value="cook">Повар</option>
        </select>
    </div>
    <button type="submit" name="assign_role" class="btn btn-primary">Назначить роль</button>
</form>

<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "delivery";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

if (isset($_POST['assign_role'])) {
    $user_email = $_POST['user_email'];
    $role = $_POST['role'];

    // Проверка наличия пользователя с указанным email
    $check_user_sql = "SELECT * FROM Users WHERE login = ?";
    $stmt = $conn->prepare($check_user_sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        // Пользователь найден, обновляем его роль
        $update_user_sql = "UPDATE Users SET role = ? WHERE login = ?";
        $stmt = $conn->prepare($update_user_sql);
        $stmt->bind_param("ss", $role, $user_email);
        $stmt->execute();
        $stmt->close();

        $message = urlencode("Роль пользователя с email $user_email успешно обновлена на $role.");
    } else {
        // Пользователь не найден, отправляем сообщение об ошибке
        $message = urlencode("Ошибка: Пользователь с email $user_email не найден.");
    }

    header("Location: meneger.php?message=$message");
    exit();
}
// Закрытие соединения с базой данных
$conn->close();
?>
