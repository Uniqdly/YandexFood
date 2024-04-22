<?php
$pdo = new PDO('mysql:host=localhost;dbname=delivery', 'root', '');

$dishId = $_GET['id'];

$stmt = $pdo->prepare('SELECT * FROM Dishes WHERE id = :id');
$stmt->bindParam(':id', $dishId);
$stmt->execute();

$dish = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($dish);
?>
