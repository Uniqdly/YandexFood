<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Менеджер блюд</title>
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
    <h1>Менеджер блюд</h1>
    <h2>Добавить новое блюдо</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <label for="name">Название блюда:</label>
        <input type="text" id="name" name="name" required><br><br>
        
        <label for="ingredients_name">Ингредиенты (через запятую):</label>
        <input type="text" id="ingredients_name" name="ingredients_name" required><br><br>
        
        <label for="description">Описание:</label><br>
        <textarea id="description" name="description" rows="4" cols="50"></textarea><br><br>
        
        <label for="price">Цена:</label>
        <input type="text" id="price" name="price" required><br><br>
        
        <label for="photo">Фото:</label>
        <input type="file" id="photo" name="photo"><br><br>
        
        <input type="submit" name="submit" value="Добавить блюдо">
    </form>

    <h2>Текущее меню</h2>
    <table border="1">
        <tr>
            <th>Название блюда</th>
            <th>Ингредиенты</th>
            <th>Описание</th>
            <th>Цена</th>
            <th>Действия</th>
        </tr>
        <?php
        // Отображение текущего меню блюд с возможностью редактирования и удаления
        $conn = new mysqli("localhost", "root", "", "delivery");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (isset($_POST['submit'])) {
            // Обработка формы добавления нового блюда
            $dish_name = $_POST["name"];
            $ingredients = $_POST["ingredients_name"];
            $description = $_POST["description"];
            $price = $_POST["price"];
            $photo = $_FILES["photo"]["tmp_name"];

            // Вставка данных о блюде в таблицу Dishes
            $sql = "INSERT INTO Dishes (name, ingredients_name, description, price, photo) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssis", $dish_name, $ingredients, $description, $price, $photo);
            $stmt->execute();
            $stmt->close();

            // Получаем ID добавленного блюда
            $dish_id = $conn->insert_id;

            // Разделение списка ингредиентов на отдельные записи
            $ingredient_names = explode(",", $ingredients);
            foreach ($ingredient_names as $ingredient_name) {
                // Вставка данных об ингредиенте в таблицу ingredients
                $ingredient_sql = "INSERT INTO Ingredients (name, name_dishes) VALUES (?, ?)";
                $ingredient_stmt = $conn->prepare($ingredient_sql);
                $ingredient_stmt->bind_param("ss", $ingredient_name, $dish_name);
                $ingredient_stmt->execute();
                $ingredient_stmt->close();
            }

            echo "<meta http-equiv='refresh' content='0'>";
        }

        $sql = "SELECT * FROM Dishes";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><input type='text' value='" . $row["name"] . "'></td>";
                
                // Получение списка ингредиентов для текущего блюда
                $ingredients_sql = "SELECT name FROM Ingredients WHERE name_dishes = '" . $row["name"] . "'";
                $ingredients_result = $conn->query($ingredients_sql);
                $ingredients_list = array();
                if ($ingredients_result->num_rows > 0) {
                    while ($ingredient_row = $ingredients_result->fetch_assoc()) {
                        $ingredients_list[] = $ingredient_row["name"];
                    }
                }
                $ingredients_str = implode(", ", $ingredients_list);
                
                // Обработка нажатия кнопки "Удалить" для каждой строки в таблице блюд
                if(isset($_POST["delete_dish"])) {
                    $dish_id = $_POST["dish_id"];
                    $sql_delete_dish = "DELETE FROM Dishes WHERE id=$dish_id";
                    if ($conn->query($sql_delete_dish) === TRUE) {
                        echo "";
                    } else {
                        echo "Ошибка при удалении блюда: " . $conn->error;
                    }
                }

                // Обработка нажатия кнопки "Сохранить" для каждой строки в таблице блюд
                if(isset($_POST["save_dish"])) {
                    $dish_id = $_POST["dish_id"];
                    $new_name = $_POST["new_name"];
                    $new_ingredients = $_POST["new_ingredients"];
                    $new_description = $_POST["new_description"];
                    $new_price = $_POST["new_price"];
                    // Дополнительно получите новые значения для других полей, если они могут изменяться
                    // Например, $new_ingredients = $_POST["new_ingredients"];
                    // и так далее...

                    // Выполните SQL-запрос для обновления данных блюда
                    $sql_update_dish = "UPDATE Dishes SET name='$new_name' WHERE id=$dish_id";
                    $sql_update_ingredients = "UPDATE Dishes SET name='$new_ingredients' WHERE id=$dish_id";
                    $sql_update_description = "UPDATE Dishes SET name='$new_description' WHERE id=$dish_id";
                    $sql_update_price = "UPDATE Dishes SET name='$new_price' WHERE id=$dish_id";
                    // Дополнительно добавьте другие поля для обновления
                    // Например, $sql_update_dish = "UPDATE Dishes SET name='$new_name', ingredients='$new_ingredients' WHERE id=$dish_id";

                    if ($conn->query($sql_update_dish) === TRUE) {
                        echo "Изменения сохранены успешно.";
                    } else {
                        echo "Ошибка при сохранении изменений: " . $conn->error;
                    }
                }

                echo "<td><input type='text' value='" . $ingredients_str . "'></td>";
                
                echo "<td><input type='text' value='" . $row["description"] . "'></td>";
                echo "<td><input type='text' value='" . $row["price"] . "'></td>";
                echo "<td>";
                echo "<td>";
                echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
                echo "<input type='hidden' name='dish_id' value='" . $row["id"] . "'>";
                echo "<button type='submit' name='save_dish'>Сохранить</button>";
                echo "</form>";
                echo "</td>";

                echo "<td>";
                echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
                echo "<input type='hidden' name='dish_id' value='" . $row["id"] . "'>";
                echo "<button type='submit' name='delete_dish'>Удалить</button>";
                echo "</form>";
                echo "</td>";

                echo "</tr>";
                echo "</td>";
                echo "</tr>";
                
            }
        } else {
            echo "<tr><td colspan='5'>Нет доступных блюд в меню.</td></tr>";
        }
        $conn->close();
        ?>
    </table>

    <h2>Заказы</h2>
    <table border="1">
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
        <?php
        // Отображение всех заказов с возможностью изменения статуса
        $conn = new mysqli("localhost", "root", "", "delivery");

        if ($conn->connect_error) {
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
                echo " ";
            } 
            else 
            {
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
                echo "<form action='meneger.php' method='post'>";
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
            }
        } else {
            echo "<tr><td colspan='9'>Нет доступных заказов.</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>

