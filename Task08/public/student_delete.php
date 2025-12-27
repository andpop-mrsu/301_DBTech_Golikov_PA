<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0):
    header('Location: index.php');
    exit;
endif;

// Получение данных студента
$stmt = $pdo->prepare("SELECT s.*, g.name as group_name 
                       FROM students s 
                       JOIN groups g ON s.group_id = g.id 
                       WHERE s.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student):
    header('Location: index.php');
    exit;
endif;

// Обработка удаления
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])):
    try {
        $deleteStmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $deleteStmt->execute([$id]);
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $error = "Ошибка при удалении: " . $e->getMessage();
    }
endif;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить студента</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #dc3545;
        }
        .info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        .btn-cancel:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Удалить студента</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="info">
            <p><strong>Вы уверены, что хотите удалить следующего студента?</strong></p>
            <p><strong>Группа:</strong> <?= htmlspecialchars($student['group_name']) ?></p>
            <p><strong>ФИО:</strong> <?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? '')) ?></p>
            <p><strong>Дата рождения:</strong> <?= htmlspecialchars($student['birth_date']) ?></p>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" class="btn btn-danger">Да, удалить</button>
            <a href="index.php" class="btn btn-cancel">Отмена</a>
        </form>
    </div>
</body>
</html>

