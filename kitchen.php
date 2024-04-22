<!DOCTYPE html>
<html>
<head>
    <title>Кухня</title>
</head>
<body>
    <h1>Список заказов</h1>
    <h2>Заказы в стадии подготовки</h2>
    <table>
        <tr>
            <th>Заказ</th>
            <th>Статус заказа</th>
            <th>Время создания</th>
            <th>Действия</th>
        </tr>
        <?php
        // Подключение к базе данных
        $conn = new mysqli("localhost", "root", "", "delivery");

        // Проверка соединения
        if ($conn->connect_error) 
        {
            die("Connection failed: " . $conn->connect_error);
        }

        // Обработка изменения статуса заказа
        if ($_SERVER["REQUEST_METHOD"] == "POST") 
        {
            $order_id = $_POST["order_id"];
            $new_status = $_POST["new_status"];
            $sql_update = "UPDATE Orders SET status='$new_status' WHERE id=$order_id";
            if ($conn->query($sql_update) === TRUE) 
            {
                echo "Статус заказа успешно изменен";
            } 
            else 
            {
                echo "Ошибка при изменении статуса заказа: " . $conn->error;
            }
        }

        // Запрос на получение списка заказов
        $sql = "SELECT id, status, time FROM Orders";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) 
        {
            // Вывод каждого заказа
            while($row = $result->fetch_assoc()) 
            {
                echo "<tr>";
                echo "<td>".$row["id"]."</td>";
                // Вывод статуса заказа из базы данных
                echo "<td>".$row["status"]."</td>";
                echo "<td>".$row["time"]."</td>";
                // Кнопки для изменения статуса заказа
                echo "<td>";
                echo "<form action='kitchen.php' method='post'>";
                echo "<input type='hidden' name='order_id' value='".$row["id"]."'>";
                echo "<select name='new_status'>";
                echo "<option value='На кухне'>На кухне</option>";
                echo "<option value='Ожидает курьера'>Ожидает курьера</option>";
                echo "<option value='Обрабатывается'>Обрабатывается</option>";
                echo "</select>";
                echo "<input type='submit' value='Изменить статус'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } 
        else 
        {
            echo "результатов не найдено";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>
