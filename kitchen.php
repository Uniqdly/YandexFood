<!DOCTYPE html>
<html>
<head>
    <title>Кухня</title>
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
    <h2>Заказы в стадии подготовки</h2>
    <table>
        <tr>
            <th>Заказ</th>
            <th>Статус заказа</th>
            <th>Блюда</th>
            <th>Ингредиенты</th>
            <th>Доставка к</th>
            <th>Действия</th>
            <th>Курьер который забирает заказ</th>
        </tr>
        <?php
        // Подключение к базе данных
        $conn = new mysqli("localhost", "root", "", "delivery");

        // Проверка соединения
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'cook') {
    // Роль пользователя не является "cook", перенаправляем на страницу логина
    header('Location: login.php');
    exit();
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

        // Запрос на получение списка заказов
        $sql = "SELECT id, status, dishes_name, courier_login, time FROM Orders"; 
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Вывод каждого заказа
            while($row = $result->fetch_assoc()) {
                
                echo "<tr>";
                echo "<td>".$row["id"]."</td>";
                echo "<td>".$row["status"]."</td>";
                echo "<td>".$row["dishes_name"]."</td>";
                
                
                // Получение ингредиентов для каждого блюда
                $order_id = $row["id"];
                $sql_dish_ingredients = "SELECT ingredients_name
                                        FROM orders
                                        WHERE id = $order_id";
                $result_dish_ingredients = $conn->query($sql_dish_ingredients);

                // Вывод ингредиентов
                echo "<td>";
                if ($result_dish_ingredients->num_rows > 0) {
                    while ($ingredient_row = $result_dish_ingredients->fetch_assoc()) {
                        echo $ingredient_row["ingredients_name"] . "<br>";
                    }
                } else {
                    echo "нет ингредиентов";
                }
                echo "</td>";
                
                echo "<td>".$row["time"]."</td>";
                
                // Кнопки для изменения статуса заказа
                echo "<td>";
                echo "<form action='kitchen.php' method='post'>";
                echo "<input type='hidden' name='order_id' value='".$row["id"]."'>";
                echo "<select name='new_status'>";
                echo "<option value='Обрабатывается'>Обрабатывается</option>";
                echo "<option value='На кухне'>На кухне</option>";
                echo "<option value='Ожидает курьера'>Ожидает курьера</option>";
                echo "<option value='Передано курьеру'>Передано курьеру</option>";
                echo "</select>";
                echo "<input type='submit' value='Изменить статус'>";
                echo "</form>";
                echo "<td>".$row["courier_login"]."</td>";
                echo "</td>";
                echo "</tr>";
                
            }
        } else {
            echo "<tr><td colspan='7'>результатов не найдено</td></tr>";
        }
        $conn->close();
        ?>

    </table>
</body>
</html>