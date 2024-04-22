<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кухня</title>
</head>
<body>
    <h1>Кухня</h1>
    <h2>Заказы в стадии подготовки</h2>
    <table>
        <tr>
            <th>Заказ</th>
            <th>Статус заказа</th>
            <th>Действие</th>
        </tr>
        <?php
            // Подключение к бд
            $conn = mysqli_connect("localhost", "root", "", "delivery");
            if (!$conn) 
            {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Получение заказов 
            $sql = "SELECT id, status FROM Orders WHERE status = 'В процессе'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) 
            {
                while ($row = mysqli_fetch_assoc($result)) 
                {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td><a href='kitchen.php?orders_id=" . $row['id'] . "'>Начало готовки</a></td>";
                    echo "</tr>";
                }
            } 
            
            else 
            {
                echo "<tr><td colspan='3'> Нет заказов </td></tr>";
            }

            mysqli_close($conn);
        ?>
    </table>
</body>
</html>
