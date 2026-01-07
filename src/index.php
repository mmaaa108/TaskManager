<?php
include 'db.php';

$stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة المهام</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>إدارة المهام</h1>
    <a href="add_task.php">إضافة مهمة جديدة</a>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
                <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                <p><?php echo htmlspecialchars($task['description']); ?></p>
                <p>الحالة: <?php echo $task['status']; ?></p>
                <a href="edit_task.php?id=<?php echo $task['id']; ?>">تحديث</a>
                <a href="delete_task.php?id=<?php echo $task['id']; ?>" onclick="return confirm('هل أنت متأكد؟')">حذف</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
