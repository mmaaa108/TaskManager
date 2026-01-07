<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, status) VALUES (?, ?, ?)");
    $stmt->execute([$title, $description, $status]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إضافة مهمة</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>إضافة مهمة جديدة</h1>
    <form method="POST">
        <label>العنوان: <input type="text" name="title" required></label><br>
        <label>الوصف: <textarea name="description"></textarea></label><br>
        <label>الحالة: 
            <select name="status">
                <option value="pending">معلق</option>
                <option value="completed">مكتمل</option>
            </select>
        </label><br>
        <button type="submit">إضافة</button>
    </form>
    <a href="index.php">العودة</a>
</body>
</html>
