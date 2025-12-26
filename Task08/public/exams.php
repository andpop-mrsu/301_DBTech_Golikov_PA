<?php
require_once 'config.php';

$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

if ($student_id <= 0):
    header('Location: index.php');
    exit;
endif;

// Получение данных студента
$stmt = $pdo->prepare("SELECT s.*, g.name as group_name, g.direction_id, g.study_year, g.academic_year as current_academic_year
                       FROM students s 
                       JOIN groups g ON s.group_id = g.id 
                       WHERE s.id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student):
    header('Location: index.php');
    exit;
endif;

// Получение всех экзаменов студента в хронологическом порядке
$examsStmt = $pdo->prepare("SELECT e.*, s.name as subject_name 
                            FROM exams e
                            JOIN subjects s ON e.subject_id = s.id
                            WHERE e.student_id = ?
                            ORDER BY e.exam_date ASC, e.academic_year ASC");
$examsStmt->execute([$student_id]);
$exams = $examsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты экзаменов</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
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
        .student-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .actions {
            white-space: nowrap;
        }
        .actions a {
            display: inline-block;
            padding: 5px 10px;
            margin: 0 2px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
        }
        .btn-edit {
            background: #28a745;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-add {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-add:hover {
            background: #218838;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            margin-right: 10px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-back:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Результаты экзаменов</h1>
        
        <div class="student-info">
            <p><strong>Студент:</strong> <?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? '')) ?></p>
            <p><strong>Группа:</strong> <?= htmlspecialchars($student['group_name']) ?></p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Дисциплина</th>
                    <th>Оценка</th>
                    <th>Дата экзамена</th>
                    <th>Учебный год</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($exams)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Экзамены не найдены</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($exams as $exam): ?>
                        <tr>
                            <td><?= htmlspecialchars($exam['subject_name']) ?></td>
                            <td><?= htmlspecialchars($exam['grade']) ?></td>
                            <td><?= htmlspecialchars($exam['exam_date']) ?></td>
                            <td><?= htmlspecialchars($exam['academic_year']) ?></td>
                            <td class="actions">
                                <a href="exam_edit.php?id=<?= $exam['id'] ?>&student_id=<?= $student_id ?>" class="btn-edit">Редактировать</a>
                                <a href="exam_delete.php?id=<?= $exam['id'] ?>&student_id=<?= $student_id ?>" class="btn-delete">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <a href="index.php" class="btn-back">Назад к списку</a>
        <a href="exam_add.php?student_id=<?= $student_id ?>" class="btn-add">Добавить экзамен</a>
    </div>
</body>
</html>

