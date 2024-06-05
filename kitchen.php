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
                <tbody>
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
                            $_GET['status'] = $new_status; // Сохраняем новый статус в параметрах URL
                            echo "<div class='alert alert-success' role='alert'>Статус заказа изменен успешно</div>";
                        } else {
                            echo "<div class='alert alert-danger' role='alert'>Ошибка при изменении статуса заказа: " . $conn->error . "</div>";
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
                            
                            $dishes = explode(', ', $row["dishes_name"]);
                            echo "<td>";
                            foreach ($dishes as $dish) {
                                $dish_name = explode(' x', $dish)[0];
                                $sql_dish_ingredients = "SELECT ingredients_name FROM dishes WHERE name = '$dish_name'";
                                $result_dish_ingredients = $conn->query($sql_dish_ingredients);

                                // Вывод ингредиентов
                                if ($result_dish_ingredients->num_rows > 0) {
                                    while ($ingredient_row = $result_dish_ingredients->fetch_assoc()) {
                                        echo "<strong>$dish_name:</strong> " . $ingredient_row["ingredients_name"] . "<br>";
                                    }
                                } else {
                                    echo "<strong>$dish_name:</strong> нет ингредиентов<br>";
                                }
                            }
                            echo "</td>";
                            
                            echo "<td>".$row["time"]."</td>";
                            
                            // Кнопки для изменения статуса заказа
                            echo "<td>";
                            echo "<form action='kitchen.php' method='post' class='d-inline'>";
                            echo "<input type='hidden' name='order_id' value='".$row["id"]."'>";
                            if ($row["status"] == 'Обрабатывается') {
                                echo "<button type='submit' name='new_status' value='На кухне' class='btn btn-warning btn-sm'>На кухне</button>";
                            } elseif ($row["status"] == 'На кухне') {
                                echo "<button type='submit' name='new_status' value='Готов, ожидает курьера' class='btn btn-success btn-sm'>Готов, ожидает курьера</button>";
                            } elseif ($row["status"] == 'Готов, ожидает курьера') {
                                echo "<button type='button' class='btn btn-secondary btn-sm' disabled>Нет действий</button>";
                            } 
                            echo "</form>";
                            echo "</td>";
                            
                            echo "<td>".$row["courier_login"]."</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>результатов не найдено</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
