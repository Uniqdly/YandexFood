<?php
// Подключение к базе данных
$conn = new mysqli("localhost", "root", "", "delivery");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Обработка изменения статуса заказа
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST["order_id"];
    $new_status = $_POST["new_status"];
    $sql_update = "UPDATE Orders SET status='$new_status' WHERE id=$order_id";
    if ($conn->query($sql_update) === TRUE) {
        $message = urlencode("статус успешно изменен!");
        header("Location: meneger.php?message=$message&page=orders");
        exit();
    } else {
        echo "Ошибка при изменении статуса заказа: " . $conn->error;
    }
}

$sql = "SELECT * FROM Orders";
$result = $conn->query($sql);

?>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Статус</th>
                <th>Состав</th>
                <th>Общая стоимость</th>
                <th>Адрес</th>
                <th>Время</th>
                <th>Номер телефона</th>
                <th>Комментарий</th>
                <th>Изменить статус</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["status"] . "</td>";
                echo "<td>" . $row["dishes_name"] . "</td>";
                echo "<td>" . $row["total_price"] . "</td>";
                echo "<td>" . $row["address"] . "</td>";
                echo "<td>" . $row["time"] . "</td>";
                echo "<td>" . $row["phone_number"] . "</td>";
                echo "<td>" . $row["comment"] . "</td>";

                // Кнопки для изменения статуса заказа
                echo "<td>";
                if ($row["status"] == 'Обрабатывается') {
                    echo "<form action='orders.php' method='post'>";
                    echo "<input type='hidden' name='order_id' value='".$row["id"]."'>";
                    echo "<button type='submit' name='new_status' value='На кухне' class='btn btn-warning btn-sm'>На кухне</button>";
                    echo "</form>";
                } elseif ($row["status"] == 'На кухне') {
                    echo "<form action='orders.php' method='post'>";
                    echo "<input type='hidden' name='order_id' value='".$row["id"]."'>";
                    echo "<button type='submit' name='new_status' value='Готов, ожидает курьера' class='btn btn-success btn-sm'>Готов, ожидает курьера</button>";
                    echo "</form>";
                } elseif ($row["status"] == 'Готов, ожидает курьера') {
                    echo "<form action='orders.php' method='post'>";
                    echo "<input type='hidden' name='order_id' value='".$row["id"]."'>";
                    echo "<button type='submit' name='new_status' value='Забрал заказ' class='btn btn-primary btn-sm'>Забрал заказ</button>";
                    echo "</form>";
                } elseif ($row["status"] == 'Забрал заказ') {
                    echo "<form action='orders.php' method='post'>";
                    echo "<input type='hidden' name='order_id' value='".$row["id"]."'>";
                    echo "<button type='submit' name='new_status' value='Заказ доставлен' class='btn btn-info btn-sm'>Заказ доставлен</button>";
                    echo "</form>";
                } elseif ($row["status"] == 'Заказ доставлен') {
                    echo "<button type='button' class='btn btn-secondary btn-sm' disabled>Нет действий</button>";
                } 
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>Нет доступных заказов.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
?>
