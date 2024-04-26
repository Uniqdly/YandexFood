<!DOCTYPE html>
<html>
<head>
    <title>Кухня</title>
    <!-- Подключение Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script> 
        function logout() {
        // Очистка сессии и перенаправление на страницу выхода
            fetch('logout.php').then(() => {
                window.location.href = 'logout.php';
            })
                .catch(error => console.error('Error:', error));
            }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Список заказов</h1>
        <div class="text-right mb-3">
            <button class="btn btn-danger" onclick="logout()">Выход</button>
        </div>
        <h2>Заказы в стадии подготовки</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Заказ</th>
                        <th scope="col">Статус заказа</th>
                        <th scope="col">Блюда</th>
                        <th scope="col">Ингредиенты</th>
                        <th scope="col">Доставка к</th>
                        <th scope="col">Действия</th>
                        <th scope="col">Курьер который забирает заказ</th>
                    </tr>
                </thead>
<body>
    
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
                
                
                $order_id = $row["id"];
                $sql_dish_ingredients = "SELECT name
                                        FROM Ingredients
                                        WHERE name_dishes IN (
                                            SELECT dishes_name
                                            FROM Orders
                                            WHERE id = $order_id
                                        )";
                $result_dish_ingredients = $conn->query($sql_dish_ingredients);

                // Вывод ингредиентов
                echo "<td>";
                if ($result_dish_ingredients->num_rows > 0) {
                    while ($ingredient_row = $result_dish_ingredients->fetch_assoc()) {
                        echo $ingredient_row["name"] . "<br>";
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