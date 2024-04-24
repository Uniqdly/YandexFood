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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    
    <h1>Меню</h1>
    <div class="container">
    <div class="row">
        <?php foreach ($dishes as $dish): ?>
            <div class="card" style="width: 18rem;">
                <img src="<?php echo $dish['image_url']; ?>" class="card-img-top" alt="<?php echo $dish['name']; ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $dish['name']; ?></h5>
                    <p class="card-text"><?php echo $dish['description']; ?></p>
                    <button onclick="addToCart(<?php echo $dish['id']; ?>, '<?php echo $dish['name']; ?>')" class="btn btn-primary">Добавить в корзину</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

    <button onclick="redirectToRegister()">Register</button>
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
        function redirectToRegister() {
    window.location.href = 'register.php';
}

function placeOrder() {
    // Подготовка списка блюд для передачи в URL
    const selectedDishesIds = cart.map(item => item.id).join(',');
    
    // Перенаправление на страницу заказа с передачей списка блюд через параметр URL
    window.location.href = 'order.php?dishes=' + selectedDishesIds;
}


        

        function addToCart(dishId, dishName) {
    fetch('getDishInfo.php?id=' + dishId)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error:', data.error);
            } else {
                selectedDish = data;
                document.getElementById('confirmDishName').textContent = selectedDish.name;
                document.getElementById('confirmDishDescription').textContent = selectedDish.description;
                document.getElementById('confirmModal').style.display = 'block';
            }
        })
        .catch(error => console.error('Error:', error));
}


let cart = [];
let cartItems = [];

function confirmPurchase() {
    if (selectedDish) {
        // Проверяем, есть ли выбранный продукт уже в корзине
        const existingItemIndex = cart.findIndex(item => item.id === selectedDish.id);
        
        if (existingItemIndex !== -1) {
            // Если продукт уже есть в корзине, увеличиваем его количество
            cart[existingItemIndex].quantity += 1;
            cartItems[existingItemIndex].quantity += 1;
        } else {
            // Если продукта нет в корзине, добавляем его
            selectedDish.quantity = 1;
            cart.push(selectedDish);
            cartItems.push({ id: selectedDish.id, name: selectedDish.name, quantity: 1 });
        }
        
        updateCart();
        closeModal('confirmModal');
        selectedDish = null; // Сброс выбранного блюда после добавления в корзину
    }
}

function removeFromCart(index) {
    cart[index].quantity -= 1;
    
    if (cart[index].quantity === 0) {
        cart.splice(index, 1);
    }
    
    updateCart();
}


function updateCart() {
    const cartItemsElement = document.getElementById('cartItems');
    cartItemsElement.innerHTML = '';
    
    cartItems.forEach((item, index) => {
        const li = document.createElement('li');
        li.classList.add('burger');
        
        const itemName = document.createElement('span');
        itemName.textContent = item.name + ' x' + item.quantity;
        
        const removeButton = document.createElement('span');
        removeButton.textContent = '-';
        removeButton.classList.add('remove');
        removeButton.onclick = () => removeFromCart(index);
        
        li.appendChild(itemName);
        li.appendChild(removeButton);
        
        cartItemsElement.appendChild(li);
    });
}н


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