<?php
require_once 'config.php';

$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

if ($student_id <= 0):
    header('Location: index.php');
    exit;
endif;

// Получение данных студента
$stmt = $pdo->prepare("SELECT s.*, g.name as group_name, g.direction_id 
                       FROM students s 
                       JOIN groups g ON s.group_id = g.id 
                       WHERE s.id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student):
    header('Location: index.php');
    exit;
endif;

// Получение всех дисциплин для направления студента
$subjectsStmt = $pdo->prepare("SELECT DISTINCT s.id, s.name 
                               FROM subjects s
                               JOIN curriculum c ON s.id = c.subject_id
                               WHERE c.direction_id = ? AND s.assessment_type = 'экзамен'
                               ORDER BY s.name");
$subjectsStmt->execute([$student['direction_id']]);
$subjects = $subjectsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить экзамен</title>
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
            color: #333;
        }
        .student-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-submit {
            background: #28a745;
            color: white;
        }
        .btn-submit:hover {
            background: #218838;
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
        <h1>Добавить экзамен</h1>
        
        <div class="student-info">
            <p><strong>Студент:</strong> <?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? '')) ?></p>
            <p><strong>Группа:</strong> <?= htmlspecialchars($student['group_name']) ?></p>
        </div>
        
        <form method="POST" action="exam_save.php">
            <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
            
            <div class="form-group">
                <label for="subject_id">Дисциплина *</label>
                <select id="subject_id" name="subject_id" required>
                    <option value="">Выберите дисциплину</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= htmlspecialchars($subject['id']) ?>">
                            <?= htmlspecialchars($subject['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="grade">Оценка *</label>
                <select id="grade" name="grade" required>
                    <option value="">Выберите оценку</option>
                    <option value="2">2 (Неудовлетворительно)</option>
                    <option value="3">3 (Удовлетворительно)</option>
                    <option value="4">4 (Хорошо)</option>
                    <option value="5">5 (Отлично)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="exam_date">Дата экзамена *</label>
                <input type="date" id="exam_date" name="exam_date" required>
            </div>
            
            <div class="form-group">
                <label for="academic_year">Учебный год *</label>
                <input type="text" id="academic_year" name="academic_year" 
                       placeholder="например, 2020/2021" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-submit">Сохранить</button>
                <a href="exams.php?student_id=<?= $student_id ?>" class="btn btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>

