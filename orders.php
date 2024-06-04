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
                echo " ";
            } else {
                echo "Ошибка при изменении статуса заказа: " . $conn->error;
            }
        }

        $sql = "SELECT * FROM Orders";
        $result = $conn->query($sql);

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
                echo "<form action='orders.php' method='post'>";
                echo "<input type='hidden' name='order_id' value='".$row["id"]."'>";
                echo "<select name='new_status'>";
                echo "<option value='Обрабатывается'>Обрабатывается</option>";
                echo "<option value='На кухне'>На кухне</option>";
                echo "<option value='Нашелся курьер'>Нашелся курьер</option>";
                echo "<option value='Ожидает курьера'>Ожидает курьера</option>";
                echo "<option value='Передано курьеру'>Передано курьеру</option>";
                echo "<option value='Заказ доставлен'>Заказ доставлен</option>";
                echo "</select>";
                echo "<input type='submit' value='Изменить статус'>";
                echo "</form>";

                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>Нет доступных заказов.</td></tr>";
        }
        $conn->close();
        ?>
        </tbody>
    </table>
</div>