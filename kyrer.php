<!DOCTYPE html>
<html>
<head>
    <title>Курьер</title>
    <script>
        function toggleSelectButton(orderId) {
            // Скрыть кнопку "Выбрать"
            document.getElementById('select_button_' + orderId).style.display = 'none';
            // Отобразить форму изменения статуса заказа
            document.getElementById('status_form_' + orderId).style.display = 'inline';
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            // Функция для проверки статуса заказа
            function checkOrderStatus() {
                $.ajax({
                    url: 'check_status.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // Обновляем статус на странице
                        $('#order-status').text('Статус: ' + response.status);
                    },
                    error: function(xhr, status, error) {
                        console.error('Ошибка при получении статуса заказа:', error);
                    }
                });
            }

            // Вызываем функцию для проверки статуса при загрузке страницы
            checkOrderStatus();

            // Устанавливаем интервал для автоматической проверки статуса каждые 5 секунд
            setInterval(checkOrderStatus, 5000);
        });
    </script>
</head>
<body>
    <h1>Список заказов</h1>
    <h2>Доступные заказы</h2>
    <table>
        <tr>
            <th>Заказ</th>
            <th>Статус заказа</th>
            <th>Блюда</th>
            <th>Адрес</th>
            <th>Доставка к</th>
            <th>Действия</th>
        </tr>
        <?php
        // Подключение к базе данных
        $conn = new mysqli("localhost", "root", "", "delivery");

        // Проверка соединения
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        session_start();
        if (isset($_SESSION['role']) && $_SESSION['role'] !== 'courier') {
            // Роль пользователя не является "courier", перенаправляем на страницу логина
            header('Location: login.php');
            exit();
        }
        $courier_id = $_SESSION['user_id'];
        // Обработка изменения статуса заказа
        if ($_SERVER["REQUEST_METHOD"] == "POST") 
        {
            
            $order_id = $_POST["order_id"];
            $new_status = $_POST["new_status"];
            $sql_update = "UPDATE Orders SET status='$new_status', courier_login=(SELECT login FROM users WHERE id=$courier_id) WHERE id=$order_id";
            if ($conn->query($sql_update) === TRUE) 
            {
                echo " ";
            } 
            else 
            {
                echo "Ошибка при изменении статуса заказа: " . $conn->error;
            }
        }
        // Запрос на получение списка заказов на кухне
        $sql = "SELECT id, status, dishes_name, address, time FROM Orders";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Вывод каждого заказа
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row["id"]."</td>";
                echo "<td>".$row["status"]."</td>";
                echo "<td>".$row["dishes_name"]."</td>";
                echo "<td>".$row["address"]."</td>";
                echo "<td>".$row["time"]."</td>";
                // Кнопка "Выбрать" и форма для изменения статуса заказа
                echo "<td>";
                echo "<button id='select_button_".$row["id"]."' onclick='toggleSelectButton(".$row["id"].")'>Выбрать</button>";
                echo "<form id='status_form_".$row["id"]."' action='kyrer.php' method='post' style='display:none;'>";
                echo "<input type='hidden' name='order_id' value='".$row["id"]."'>";
                echo "<select name='new_status'>";
                echo "<option value='Готов забрать заказ'>Готов забрать заказ</option>";
                echo "<option value='Забрал заказ'>Забрал заказ</option>";
                echo "<option value='Заказ доставлен'>Заказ доставлен</option>";
                echo "</select>";
                echo "<input type='submit' value='Изменить статус'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>результатов не найдено</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>