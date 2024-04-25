<?php
// Подключение к базе данных
$db_host = 'localhost';
$db_username = 'root';
$db_password = ' ';
$db_name = 'delivery';

$connection = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($connection->connect_error) {
    die("Ошибка подключения к базе данных: " . $connection->connect_error);
}

// Запрос к базе данных для получения последнего статуса
$query = "SELECT status FROM Orders ORDER BY id DESC LIMIT 1";
$result = $connection->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = $row['status'];

    // Возвращаем статус в формате JSON
    echo json_encode(array("status" => $status));
} else {
    echo json_encode(array("error" => "Статус не найден"));
}

$connection->close();
?>
