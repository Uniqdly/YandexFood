<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ доставки</title>
</head>
<body>
    <h1>Оформление заказа</h1>
    
    <form action="/submit_order" method="post">
        <label for="address">Адрес:</label>
        <input type="text" id="address" name="address" required><br><br>
        
        <label for="street">Улица:</label>
        <input type="text" id="street" name="street" required><br><br>
        
        <label for="house">Дом:</label>
        <input type="text" id="house" name="house" required><br><br>
        
        <label for="apartment">Квартира:</label>
        <input type="text" id="apartment" name="apartment" required><br><br>
        
        <label for="delivery-time">Время доставки:</label>
        <input type="time" id="delivery-time" name="delivery-time" min="00:00" max="23:59" required><br><br>
    
        
        <label for="phone">Номер телефона:</label>
        <input type="tel" id="phone" name="phone" required><br><br>
        
        <label for="comment">Комментарий к заказу:</label><br>
        <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br><br>
        <h2>Выбранные блюда:</h2>
        <ul id="selectedDishes"></ul>

        
        <button type="submit">Заказать</button>
    </form>
</body>
</html>
