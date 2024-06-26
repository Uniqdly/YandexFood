<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "delivery";

$conn = new mysqli($servername, $username, $password, $dbname);
session_start();


// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['user_id'])) {
    // Пользователь не авторизован, перенаправляем на страницу авторизации
    header('Location: Login.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $address = $_POST['address'];
    $deliveryTime = $_POST['delivery-time'];
    $phone = $_POST['phone'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    // Преобразование времени в нужный формат (если требуется)
    $deliveryDateTime = date("Y-m-d H:i:s", strtotime(date("Y-m-d") . " " . $deliveryTime));

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    
        // SQL запрос для выбора заказов пользователя с указанным статусом
        $sql_check = "SELECT * FROM orders WHERE user_id = $user_id AND status = 'Обрабатывается' ORDER BY id DESC LIMIT 1";


        $result_check = $conn->query($sql_check);
    
        if ($result_check->num_rows > 0) {
            // Если найдены заказы с указанным статусом, обновляем их данные
            while ($row = $result_check->fetch_assoc()) {
                $sql_update = "UPDATE orders SET address = '$address', time = '$deliveryDateTime', phone_number = '$phone', comment = '$comment' WHERE id = " . $row['id'];
                $success_message = "";

                if ($conn->query($sql_update) === TRUE) {
                    $success_message = "Заказ успешно отправлен!";
                    ?>
                    <div class="container mt-3"> 
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    </div>
                    <?php
                } else {
                    echo "Ошибка при обновлении данных заказа: " . $conn->error;
                }
                
                
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ доставки</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Подключение стилей -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        h1 {
            text-align: center;
        }
        
        form {
            max-width: 500px;
            margin: 0 auto;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
        }
        
        input[type="text"],
        input[type="time"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        
    </style>
</head>
<body>
    <h1>Оформление заказа</h1>
    
    <form id="order-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

        <label for="address">Адрес (Улица, дом, квартира):</label>
        <input type="text" id="address" name="address" required><br><br>
        
        <label for="delivery-time">Время доставки:</label>
        <input type="time" id="delivery-time" name="delivery-time" min="00:00" max="23:59" required><br><br>
    
        <label for="phone">Номер телефона (11 чисел):</label>
<input type="tel" id="phone" name="phone" required maxlength="11"><br><br>

        
        <label for="comment">Комментарий к заказу:</label><br>
        <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br><br>
        
        <div class="button-container">
            <button type="submit">Заказать</button>
            <button onclick="redirectToMyOrders()">Мои заказы</button>
            
            <button onclick="logout()">Выход</button>
        </div>
    </form>
    <script>
    function validateForm() {
        var phoneInput = document.getElementById('phone');
        if (phoneInput.value.length !== 11) {
            alert('Номер телефона должен содержать 11 чисел.');
            return false; // Отменить отправку формы
        }
        return true; // Продолжить отправку формы
    }

    document.getElementById('order-form').addEventListener('submit', function(event) {
        if (!validateForm()) {
            event.preventDefault(); // Отменить отправку формы
        }
    });
</script>

    <script>
        function redirectToMyOrders() {
            window.location.href = 'MyOrders.php';
        }
        
        //function redirectToMenu() {
        //    window.location.href = 'user.php';
        //}
        
        function logout() {
            // Очистка сессии и перенаправление на страницу выхода
            fetch('logout.php')
                .then(() => {
                    window.location.href = 'logout.php';
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Ваши скрипты здесь
    </script>
</body>
</html>

