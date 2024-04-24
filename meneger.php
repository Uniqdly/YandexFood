<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Менеджер блюд</title>
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
        // Отображение текущего меню блюд с возможностью редактирования
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
                
                echo "<td><input type='text' value='" . $ingredients_str . "'></td>";
                
                echo "<td><input type='text' value='" . $row["description"] . "'></td>";
                echo "<td><input type='text' value='" . $row["price"] . "'></td>";
                echo "<td><button>Сохранить</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Нет доступных блюд в меню.</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>
