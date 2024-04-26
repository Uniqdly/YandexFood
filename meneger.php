<?php 
session_start(); // Переместил вызов session_start() в самое начало

$conn = new mysqli("localhost", "root", "", "delivery");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit();
}

if (isset($_POST['submit'])) {
    // Обработка формы добавления нового блюда
    $dish_name = $_POST["name"];
    $ingredients = $_POST["ingredients_name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $photo_link = $_POST['photo']; // Получаем ссылку на фото из формы

// Добавляем значение ссылки на фото в запрос для вставки данных в таблицу Dishes
$sql = "INSERT INTO Dishes (name, ingredients_name, description, price, photo) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssis", $dish_name, $ingredients, $description, $price, $photo_link);
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
if (isset($_POST['assign_role'])) {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];

    // Проверка наличия пользователя с указанным ID и обновление его роли
    $update_user_sql = "UPDATE Users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($update_user_sql);
    $stmt->bind_param("si", $role, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "Роль пользователя с ID $user_id успешно обновлена на $role.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Менеджер блюд</title>
    <!-- Подключение Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        /* Дополнительные стили */
        .btn-toggle {
            margin-bottom: 5px;
        }
        .status-form {
            display: none;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function logout() {
            // Очистка сессии и перенаправление на страницу выхода
            fetch('logout.php')
                .then(() => {
                    window.location.href = 'logout.php';
                })
                .catch(error => console.error('Error:', error));
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
                    error: function(xhr, status) {
                        console.error(' ');
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
        <h2 class="text-center">Менеджер блюд</h2>
        <button class="btn btn-danger float-right mb-3" onclick="logout()">Выход</button>
        <h3>Управление пользователями</h3>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <div class="form-group">
        <label for="user_id">ID пользователя:</label>
        <input type="text" class="form-control" id="user_id" name="user_id" required>
    </div>
    <div class="form-group">
        <label for="role">Выберите роль:</label>
        <select class="form-control" id="role" name="role" required>
            <option value="courier">Курьер</option>
            <option value="manager">Менеджер</option>
            <option value="cook">Повар</option>
        </select>
    </div>
    <button type="submit" name="assign_role" class="btn btn-primary">Назначить роль</button>
</form>

        <h3>Добавить новое блюдо</h3>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Название блюда:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="ingredients_name">Ингредиенты (через запятую):</label>
                <input type="text" class="form-control" id="ingredients_name" name="ingredients_name" required>
            </div>
            <div class="form-group">
                <label for="description">Описание:</label>
                <textarea class="form-control" id="description" name="description" rows="4" cols="50"></textarea>
            </div>
            <div class="form-group">
                <label for="price">Цена:</label>
                <input type="text" class="form-control" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="photo">Ссылка на фото:</label>
                <input type="text" class="form-control-file" id="photo" name="photo">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Добавить блюдо</button>
        </form>
        <h2 class="mt-5">Текущее меню</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Название блюда</th>
                        <th>Ингредиенты</
                        <th>Ингредиенты</th>
        <th>Описание</th>
        <th>Цена</th>
        <th>Действия</th>
        </tr>
        </thead>
        <tbody>

        <?php 
        $sql = "SELECT * FROM Dishes";
        $result = $conn->query($sql);

        if(isset($_POST["edit_dish"])) {
            $edit_dish_id = $_POST["edit_dish_id"];
            $edit_dish_sql = "SELECT * FROM Dishes WHERE id = $edit_dish_id";
            $edit_dish_result = $conn->query($edit_dish_sql);
            if ($edit_dish_result->num_rows > 0) {
                $edit_dish_row = $edit_dish_result->fetch_assoc();
                // Отобразите форму с существующими данными блюда для редактирования
                echo "<h3>Редактировать блюдо</h3>";
                echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
                echo "<input type='hidden' name='edit_dish_id' value='".$edit_dish_row["id"]."'>";
                echo "<div class='form-group'>";
                echo "<label for='name'>Название:</label>";
                echo "<input type='text' class='form-control' name='edit_name' value='".$edit_dish_row["name"]."'>";
                echo "</div>";
                echo "<div class='form-group'>";
                echo "<label for='ingredients_name'>Ингредиенты (через запятую):</label>";
                echo "<input type='text' class='form-control' name='edit_ingredients_name' value='".$edit_dish_row["ingredients_name"]."'>";
                echo "</div>";
                echo "<div class='form-group'>";
                echo "<label for='description'>Описание:</label>";
                echo "<textarea class='form-control' name='edit_description'>".$edit_dish_row["description"]."</textarea>";
                echo "</div>";
                echo "<div class='form-group'>";
                echo "<label for='price'>Цена:</label>";
                echo "<input type='text' class='form-control' name='edit_price' value='".$edit_dish_row["price"]."'>";
                echo "</div>";
                echo "<div class='form-group'>";
                echo "<label for='photo'>Ссылка на фото:</label>";
                echo "<input type='text' class='form-control' name='edit_photo' value='".$edit_dish_row["photo"]."'>";
                echo "</div>";
                echo "<button type='submit' name='save_edited_dish' class='btn btn-primary'>Сохранить изменения</button>";
                echo "</form>";
            }
        }
        if(isset($_POST["save_edited_dish"])) {
            $edit_dish_id = $_POST["edit_dish_id"];
            $edit_name = $_POST["edit_name"];
            $edit_ingredients = $_POST["edit_ingredients_name"];
            $edit_description = $_POST["edit_description"];
            $edit_price = $_POST["edit_price"];
            $edit_photo = $_POST["edit_photo"];
            
            // Обновите информацию о блюде в базе данных
            $update_dish_sql = "UPDATE Dishes SET name=?, ingredients_name=?, description=?, price=?, photo=? WHERE id=?";
            $update_stmt = $conn->prepare($update_dish_sql);
            $update_stmt->bind_param("sssssi", $edit_name, $edit_ingredients, $edit_description, $edit_price, $edit_photo, $edit_dish_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            // Перенаправление или вывод сообщения об успешном обновлении блюда
            echo "Блюдо успешно обновлено!";
        }
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
                

                echo "<td><input type='text' value='" . $ingredients_str . "'></td>";

                echo "<td><input type='text' value='" . $row["description"] . "'></td>";
                echo "<td><input type='text' value='" . $row["price"] . "'></td>";
                echo "<td>";

                echo "<td>";
                echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
                echo "<input type='hidden' name='edit_dish_id' value='" . $row["id"] . "'>";
                echo "<button type='submit' name='edit_dish'>Редактировать</button>";
                echo "</form>";


                echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
                echo "<input type='hidden' name='dish_id' value='" . $row["id"] . "'>";
                echo "<button type='submit' name='delete_dish'>Удалить</button>";
                echo "</form>";

                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Нет доступных блюд в меню.</td></tr>";
        }
        $conn->close();
        ?>
        </tbody>
        </table>
        </div>
        <h2 class="mt-5">Заказы</h2>
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
        </div>
        </body>
        </html>
