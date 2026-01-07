<?php
include 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, status = ? WHERE id = ?");
    $stmt->execute([$title, $description, $status, $id]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تحديث مهمة</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>تحديث المهمة</h1>
    <form method="POST">
        <label>العنوان: <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required></label><br>
        <label>الوصف: <textarea name="description"><?php echo htmlspecialchars($task['description']); ?></textarea></label><br>
        <label>الحالة: 
            <select name="status">
                <option value="pending" <?php if ($task['status'] == 'pending') echo 'selected'; ?>>معلق</option>
                <option value="completed" <?php if ($task['status'] == 'completed') echo 'selected'; ?>>مكتمل</option>
            </select>
        </label><br>
        <button type="submit">تحديث</button>
    </form>
    <a href="index.php">العودة</a>
</body>
</html>
