<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "delivery";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение данных из POST запроса
$data = json_decode(file_get_contents("php://input"), true);

// Перебор полученных данных и добавление их в базу данных
foreach ($data as $item) {
    $name = $conn->real_escape_string($item['name']); // Предполагается, что данные приходят в виде ассоциативного массива

    // SQL запрос для добавления названия блюда в базу данных
    $sql = "INSERT INTO orders (dish_name) VALUES ('$name')";

    if ($conn->query($sql) === TRUE) {
        echo "Блюдо успешно добавлено в базу данных.";
    } else {
        echo "Ошибка при добавлении блюда в базу данных: " . $conn->error;
    }
}

// Закрытие соединения с базой данных
$conn->close();
?>
