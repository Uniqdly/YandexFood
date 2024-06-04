<form action="users.php" method="post">
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

<?php
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