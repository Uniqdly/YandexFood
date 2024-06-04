<h3>Добавить новое блюдо</h3>
<form action="add_dish.php" method="post" enctype="multipart/form-data">
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

<?php
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

    echo "Блюдо успешно добавлено!";
}
?>