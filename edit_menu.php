<?php
ob_start(); // Включение буферизации вывода в самом начале файла
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование меню</title>
    <!-- Подключение Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        
        
        h2 {
            color: #343a40;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary, .btn-warning, .btn-danger {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mt-5">Текущее меню</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Название блюда</th>
                    <th>Ингредиенты</th>
                    <th>Описание</th>
                    <th>Цена</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            // Подключение к базе данных
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "delivery";

            // Создание подключения
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Проверка подключения
            if ($conn->connect_error) {
                die("Ошибка подключения: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM Dishes";
            $result = $conn->query($sql);

            if(isset($_POST["edit_dish"])) {
                $edit_dish_id = $_POST["edit_dish_id"];
                $edit_dish_sql = "SELECT * FROM Dishes WHERE id = ?";
                $edit_stmt = $conn->prepare($edit_dish_sql);
                $edit_stmt->bind_param("i", $edit_dish_id);
                $edit_stmt->execute();
                $edit_dish_result = $edit_stmt->get_result();
                
                if ($edit_dish_result->num_rows > 0) {
                    $edit_dish_row = $edit_dish_result->fetch_assoc();
                    // Отобразите форму с существующими данными блюда для редактирования
                    echo "<h3>Редактировать блюдо</h3>";
                    echo "<form action='edit_menu.php' method='post'>";
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
                $edit_stmt->close();
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
                if($update_stmt->execute()) {
                    echo "<div class='alert alert-success'>Блюдо успешно обновлено!</div>";
                    header("Location: meneger.php?page=edit_menu"); // Перенаправление на страницу редактирования меню
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Ошибка при обновлении блюда: " . $conn->error . "</div>";
                }
                $update_stmt->close();
            }

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><input type='text' class='form-control' value='" . $row["name"] . "' readonly></td>";

                    // Получение списка ингредиентов для текущего блюда
                    $ingredients_sql = "SELECT name FROM Ingredients WHERE name_dishes = ?";
                    $ingredients_stmt = $conn->prepare($ingredients_sql);
                    $ingredients_stmt->bind_param("s", $row["name"]);
                    $ingredients_stmt->execute();
                    $ingredients_result = $ingredients_stmt->get_result();
                    $ingredients_list = array();
                    if ($ingredients_result->num_rows > 0) {
                        while ($ingredient_row = $ingredients_result->fetch_assoc()) {
                            $ingredients_list[] = $ingredient_row["name"];
                        }
                    }
                    $ingredients_str = implode(", ", $ingredients_list);

                    echo "<td><input type='text' class='form-control' value='" . $ingredients_str . "' readonly></td>";
                    echo "<td><input type='text' class='form-control' value='" . $row["description"] . "' readonly></td>";
                    echo "<td><input type='text' class='form-control' value='" . $row["price"] . "' readonly></td>";
                    echo "<td>";

                    echo "<form action='edit_menu.php' method='post' style='display:inline-block;'>";
                    echo "<input type='hidden' name='edit_dish_id' value='" . $row["id"] . "'>";
                    echo "<button type='submit' name='edit_dish' class='btn btn-warning'>Редактировать</button>";
                    echo "</form>";

                    echo "<form action='edit_menu.php' method='post' style='display:inline-block;'>";
                    echo "<input type='hidden' name='dish_id' value='" . $row["id"] . "'>";
                    echo "<button type='submit' name='delete_dish' class='btn btn-danger'>Удалить</button>";
                    echo "</form>";

                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Нет доступных блюд в меню.</td></tr>";
            }

            if(isset($_POST["delete_dish"])) {
                $dish_id = $_POST["dish_id"];
                $sql_delete_dish = "DELETE FROM Dishes WHERE id=?";
                $delete_stmt = $conn->prepare($sql_delete_dish);
                $delete_stmt->bind_param("i", $dish_id);
                if ($delete_stmt->execute()) {
                    echo "<div class='alert alert-success'>Блюдо успешно удалено.</div>";
                    header("Location: meneger.php?page=edit_menu"); // Перенаправление на страницу редактирования меню
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Ошибка при удалении блюда: " . $conn->error . "</div>";
                }
                $delete_stmt->close();
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Подключение Bootstrap JS и зависимостей -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
ob_end_flush(); // Отправка содержимого буфера вывода и отключение буферизации
?>