<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

if ($id <= 0 || $student_id <= 0):
    header('Location: index.php');
    exit;
endif;

// Получение данных экзамена
$stmt = $pdo->prepare("SELECT e.*, s.name as subject_name, st.last_name, st.first_name, st.middle_name
                       FROM exams e
                       JOIN subjects s ON e.subject_id = s.id
                       JOIN students st ON e.student_id = st.id
                       WHERE e.id = ? AND e.student_id = ?");
$stmt->execute([$id, $student_id]);
$exam = $stmt->fetch();

if (!$exam):
    header('Location: index.php');
    exit;
endif;

// Обработка удаления
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])):
    try {
        $deleteStmt = $pdo->prepare("DELETE FROM exams WHERE id = ?");
        $deleteStmt->execute([$id]);
        header('Location: exams.php?student_id=' . $student_id);
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
    <title>Удалить экзамен</title>
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
        <h1>Удалить экзамен</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="info">
            <p><strong>Вы уверены, что хотите удалить следующий экзамен?</strong></p>
            <p><strong>Студент:</strong> <?= htmlspecialchars($exam['last_name'] . ' ' . $exam['first_name'] . ' ' . ($exam['middle_name'] ?? '')) ?></p>
            <p><strong>Дисциплина:</strong> <?= htmlspecialchars($exam['subject_name']) ?></p>
            <p><strong>Оценка:</strong> <?= htmlspecialchars($exam['grade']) ?></p>
            <p><strong>Дата:</strong> <?= htmlspecialchars($exam['exam_date']) ?></p>
            <p><strong>Учебный год:</strong> <?= htmlspecialchars($exam['academic_year']) ?></p>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" class="btn btn-danger">Да, удалить</button>
            <a href="exams.php?student_id=<?= $student_id ?>" class="btn btn-cancel">Отмена</a>
        </form>
    </div>
</body>
</html>

