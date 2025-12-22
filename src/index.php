<?php
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'taskdb';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: 'rootpass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $pdo->prepare("INSERT INTO tasks (title, description) VALUES (?, ?)");
                $stmt->execute([$_POST['title'], $_POST['description']]);
                break;
            case 'toggle':
                $stmt = $pdo->prepare("UPDATE tasks SET status = IF(status='pending', 'completed', 'pending') WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                break;
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                break;
        }
        header('Location: index.php');
        exit;
    }
}

$tasks = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدير المهام - Task Manager</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5em;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s;
        }
        input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
        .task-list {
            margin-top: 40px;
        }
        .task-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s;
        }
        .task-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .task-item.completed {
            opacity: 0.6;
            background: #e8f5e9;
        }
        .task-item.completed .task-title {
            text-decoration: line-through;
        }
        .task-content {
            flex: 1;
        }
        .task-title {
            font-size: 1.3em;
            color: #333;
            margin-bottom: 5px;
        }
        .task-desc {
            color: #666;
            margin-bottom: 5px;
        }
        .task-date {
            color: #999;
            font-size: 0.9em;
        }
        .task-actions {
            display: flex;
            gap: 10px;
        }
        .btn-small {
            padding: 8px 15px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-toggle {
            background: #4caf50;
            color: white;
        }
        .btn-toggle:hover {
            background: #45a049;
        }
        .btn-delete {
            background: #f44336;
            color: white;
        }
        .btn-delete:hover {
            background: #da190b;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85em;
            font-weight: bold;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 مدير المهام</h1>
        
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>عنوان المهمة:</label>
                <input type="text" name="title" required placeholder="مثال: إنهاء Assignment 2">
            </div>
            <div class="form-group">
                <label>الوصف:</label>
                <textarea name="description" placeholder="تفاصيل المهمة..."></textarea>
            </div>
            <button type="submit" class="btn">➕ إضافة مهمة جديدة</button>
        </form>

        <div class="task-list">
            <h2 style="margin-bottom: 20px;">قائمة المهام (<?= count($tasks) ?>)</h2>
            
            <?php if (empty($tasks)): ?>
                <div class="empty-state">
                    <p>لا توجد مهام حالياً. أضف مهمتك الأولى! 🎯</p>
                </div>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task-item <?= $task['status'] === 'completed' ? 'completed' : '' ?>">
                        <div class="task-content">
                            <div class="task-title">
                                <?= htmlspecialchars($task['title']) ?>
                                <span class="status-badge status-<?= $task['status'] ?>">
                                    <?= $task['status'] === 'completed' ? '✓ مكتملة' : '⏳ قيد التنفيذ' ?>
                                </span>
                            </div>
                            <?php if ($task['description']): ?>
                                <div class="task-desc"><?= htmlspecialchars($task['description']) ?></div>
                            <?php endif; ?>
                            <div class="task-date">📅 <?= date('Y-m-d H:i', strtotime($task['created_at'])) ?></div>
                        </div>
                        <div class="task-actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                <button type="submit" class="btn-small btn-toggle">
                                    <?= $task['status'] === 'completed' ? '↩️ إلغاء' : '✓ إنجاز' ?>
                                </button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                <button type="submit" class="btn-small btn-delete" onclick="return confirm('هل أنت متأكد من الحذف؟')">🗑️ حذف</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
