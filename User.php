<?php
// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

// Получение всех блюд из базы данных
$stmt = $pdo->query('SELECT * FROM Dishes');
$dishes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Menu</title>
    <style>
        /* Стили для модального окна */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Меню</h1>
    <ul id="menuList">
        <?php foreach ($dishes as $dish): ?>
            <li>
                <strong><?php echo $dish['name']; ?></strong>
                <button onclick="addToCart(<?php echo $dish['id']; ?>, '<?php echo $dish['name']; ?>')">В корзину</button>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Модальное окно с информацией о блюде -->
    <div id="dishModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('dishModal')">&times;</span>
            <h2 id="dishName"></h2>
            <p id="dishDescription"></p>
        </div>
    </div>
    <!-- Модальное окно подтверждения покупки -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('confirmModal')">&times;</span>
        <h2>Вы точно хотите добавить это в корзину?</h2>
        <h3 id="confirmDishName"></h3>
        <p id="confirmDishDescription"></p>
        <button onclick="confirmPurchase()">Добавить в корзину</button>
    </div>
</div>




    <!-- Кнопка "Корзина" -->
    <button id="cartButton" onclick="openCart()">Корзина</button>

    <!-- Модальное окно с корзиной -->
<div id="cartModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('cartModal')">&times;</span>
        <h2>Корзина</h2>
        <ul id="cartItems"></ul>
        <button id="orderButton" onclick="placeOrder()">Заказать</button>
    </div>
</div>


    <script>
        function placeOrder() {
    // Перенаправление на страницу заказа
    window.location.href = 'order.php';
}

        let cart = [];

        function addToCart(dishId, dishName) {
    selectedDish = { id: dishId, name: dishName };
    // Запрос к базе данных для получения информации о выбранном блюде
    fetch('getDishInfo.php?id=' + dishId)
        .then(response => response.json())
        .then(data => {
            selectedDish = data;
            document.getElementById('confirmDishName').textContent = selectedDish.name;
            document.getElementById('confirmDishDescription').textContent = selectedDish.description;
            document.getElementById('confirmModal').style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}



function confirmPurchase() {
    if (selectedDish) {
        cart.push(selectedDish);
        updateCart();
        closeModal('confirmModal');
        selectedDish = null; // Сброс выбранного блюда после добавления в корзину
    }
}



        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCart();
        }

        function updateCart() {
            const cartItemsElement = document.getElementById('cartItems');
            cartItemsElement.innerHTML = '';
            cart.forEach((item, index) => {
                const li = document.createElement('li');
                li.classList.add('burger');
                li.textContent = item.name;
                const removeButton = document.createElement('span');
                removeButton.textContent = '-';
                removeButton.classList.add('remove');
                removeButton.onclick = () => removeFromCart(index);
                li.appendChild(removeButton);
                cartItemsElement.appendChild(li);
            });
        }

        function openCart() {
            document.getElementById('cartModal').style.display = 'block';
            updateCart();
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
    <!-- Модальное окно подтверждения покупки -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('confirmModal')">&times;</span>
        <h2>Confirm Purchase</h2>
        <p>Are you sure you want to add this item to your cart?</p>
        <button onclick="confirmPurchase()">Confirm</button>
    </div>
</div>

</body>
</html>