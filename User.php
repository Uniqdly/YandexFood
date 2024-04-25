<?php
session_start();
// Подключение к базе данных
// Проверка авторизации пользователя
$loggedIn = isset($_SESSION['user_id']);

$pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

// Получение всех блюд из базы данных
$stmt = $pdo->query('SELECT * FROM Dishes');
$dishes = $stmt->fetchAll();

// Получение корзины из сессии
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    foreach ($cart as $item) {
        $dishName = $item['name'];
        $stmt = $pdo->prepare('INSERT INTO Orders (dishes_name) VALUES (:dishes_name)');
        $stmt->bindParam(':dishes_name', $dishName);
        $stmt->execute();
    }
}

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
                <img src="<?php echo $dish['photo']; ?>" class="card-img-top" alt="<?php echo $dish['name']; ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $dish['name']; ?></h5>
                    <p class="card-text"><?php echo $dish['description']; ?></p>
                    <button onclick="addToCart(<?php echo $dish['id']; ?>, '<?php echo $dish['name']; ?>')" class="btn btn-primary">Добавить в корзину</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($loggedIn): ?>
        <!-- Кнопка "Logout" -->
        <button onclick="logout()">Logout</button>
        <!-- Кнопка "Мои заказы" -->
        <button onclick="redirectToMyOrders()">Мои заказы</button>
    <?php else: ?>
        <!-- Кнопка "Register" -->
        <button onclick="redirectToRegister()">Регистрация</button>
        <!-- Кнопка "Login" -->
        <button onclick="redirectToLogin()">Вход</button>
    <?php endif; ?>
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
        <?php if ($loggedIn): ?> <button onclick="confirmPurchase()">Добавить в корзину</button>
            <?php else: ?>
    <p>Чтобы добавить в корзину авторизуйтесь</p>
<?php endif; ?>
    </div>
</div>

   
    <?php if ($loggedIn): ?>
    <button id="cartButton" onclick="openCart()">Корзина</button>
<?php endif; ?>

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
    let selectedDish = null;
    let cart = [];
    let cartItems = [];

    function redirectToRegister() {
        window.location.href = 'register.php';
    }
    function redirectToLogin() {
        window.location.href = 'Login.php';
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

    function confirmPurchase() {
    if (selectedDish) {
        const existingItemIndex = cart.findIndex(item => item.id === selectedDish.id);
        
        if (existingItemIndex !== -1) {
            cart[existingItemIndex].quantity += 1;
            cartItems[existingItemIndex].quantity += 1;
        } else {
            selectedDish.quantity = 1;
            cart.push(selectedDish);
            cartItems.push({ id: selectedDish.id, name: selectedDish.name, quantity: 1, price: selectedDish.price });
        }
        
        updateCart();
        closeModal('confirmModal');
        selectedDish = null;
        // Сохраняем корзину в сессию
        sessionStorage.setItem('cart', JSON.stringify(cart));
    }
}


    function removeFromCart(index) {
    cart[index].quantity -= 1;
    
    if (cart[index].quantity === 0) {
        cart.splice(index, 1);
        cartItems.splice(index, 1);
    }
    
    // Сохраняем обновленную корзину в сессию
    sessionStorage.setItem('cart', JSON.stringify(cart));
    
    updateCart();
}




function updateCart() {
    const cartItemsElement = document.getElementById('cartItems');
    cartItemsElement.innerHTML = '';
    
    let totalAmount = 0;
    
    cartItems.forEach((item, index) => {
        const li = document.createElement('li');
        li.classList.add('cartItem');
        
        const itemName = document.createElement('span');
        itemName.textContent = item.name + ' x' + item.quantity + ' - $' + (item.price * item.quantity);
        
        totalAmount += item.price * item.quantity;
        
        const removeButton = document.createElement('span');
        removeButton.textContent = '-';
        removeButton.classList.add('remove');
        removeButton.onclick = () => removeFromCart(index);
        
        li.appendChild(itemName);
        li.appendChild(removeButton);
        
        cartItemsElement.appendChild(li);
    });
    
    // Отображение общей суммы
    const totalElement = document.createElement('p');
    totalElement.textContent = 'Итог: ' + totalAmount + '₽';
    cartItemsElement.appendChild(totalElement);
}


function openCart() {
    if (cart.length > 0) {
        document.getElementById('cartModal').style.display = 'block';
        updateCart();
    } else {
        alert('Корзина пуста. Добавьте товары перед оформлением заказа.');
    }
}


    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    function redirectToOrder() {
        window.location.href = 'Order.php';
    }
    function placeOrder() {
    fetch('placeOrder.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ cart: cart }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Очистка корзины и обновление отображения
            cart = [];
            cartItems = [];
            updateCart();
            closeModal('cartModal');
            redirectToOrder();
            
        } else {
            alert('Произошла ошибка при размещении заказа.');
        }
    })
    .catch(error => console.error('Error:', error));
    $_SESSION['cart'] = [];
}

function logout() {
    // Очистка сессии и перенаправление на страницу выхода
    fetch('logout.php')
        .then(() => {
            window.location.href = 'logout.php';
        })
        .catch(error => console.error('Error:', error));
}

function redirectToMyOrders() {
    window.location.href = 'MyOrders.php';
}

</script>

</body>
</html>
