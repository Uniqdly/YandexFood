<!DOCTYPE html>
<html>
<head>
    <title>Курьер</title>
    <!-- Подключение Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .btn-action {
            margin: 2px;
        }
    </style>
    <script>
        function logout() {
            // Очистка сессии и перенаправление на страницу выхода
            fetch('logout.php')
                .then(() => {
                    window.location.href = 'logout.php';
                })
                .catch(error => console.error('Error:', error));
        }

        function changeOrderStatus(orderId, newStatus) {
            $.ajax({
                url: 'kyrer.php',
                type: 'POST',
                data: { order_id: orderId, new_status: newStatus },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Ошибка при изменении статуса заказа:', error);
                }
            });
        }

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
<div class="container mt-5">
    <h1 class="text-center">Список заказов</h1>
    <h2 class="mt-4">Доступные заказы</h2>
    <div class="text-right mb-3">
        <button class="btn btn-danger" onclick="logout()">Выход</button>
    </div>
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Заказ</th>
                <th>Статус заказа</th>
                <th>Блюда</th>
                <th>Адрес</th>
                <th>Доставка к</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $order_id = $_POST["order_id"];
            $new_status = $_POST["new_status"];
            $sql_update = "UPDATE Orders SET status='$new_status', courier_login=(SELECT login FROM users WHERE id=$courier_id) WHERE id=$order_id";
            if ($conn->query($sql_update) === TRUE) {
                exit();
            } else {
                echo "Ошибка при изменении статуса заказа: " . $conn->error;
            }
        }

        // Запрос на получение списка заказов с нужными статусами
        $sql = "SELECT id, status, dishes_name, address, time FROM Orders WHERE status IN ('Готов, ожидает курьера', 'Забрал заказ', 'Заказ доставлен')";
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
                // Кнопки для изменения статуса заказа
                echo "<td>";
                if ($row["status"] == 'Готов, ожидает курьера') {
                    echo "<button class='btn btn-primary btn-action' onclick=\"changeOrderStatus(".$row["id"].", 'Забрал заказ')\">Забрал заказ</button>";
                }
                elseif ($row["status"] == 'Забрал заказ') {
                    echo "<button class='btn btn-success btn-action' onclick=\"changeOrderStatus(".$row["id"].", 'Заказ доставлен')\">Заказ доставлен</button>";
                } elseif ($row["status"] == 'Заказ доставлен') {
                    echo "<button type='button' class='btn btn-secondary btn-sm' disabled>Нет действий</button>";
                }
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Результатов не найдено</td></tr>";
        }
        $conn->close();
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
