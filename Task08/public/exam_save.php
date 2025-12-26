<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST'):
    header('Location: index.php');
    exit;
endif;

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
$subject_id = isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : 0;
$grade = isset($_POST['grade']) ? (int)$_POST['grade'] : 0;
$exam_date = $_POST['exam_date'] ?? '';
$academic_year = trim($_POST['academic_year'] ?? '');

// Валидация
if ($student_id <= 0 || $subject_id <= 0 || $grade < 2 || $grade > 5 || 
    empty($exam_date) || empty($academic_year)):
    header('Location: index.php');
    exit;
endif;

try {
    if ($id > 0):
        // Обновление
        $stmt = $pdo->prepare("UPDATE exams SET 
                               subject_id = ?, grade = ?, exam_date = ?, academic_year = ?
                               WHERE id = ? AND student_id = ?");
        $stmt->execute([$subject_id, $grade, $exam_date, $academic_year, $id, $student_id]);
    else:
        // Вставка
        $stmt = $pdo->prepare("INSERT INTO exams 
                             (student_id, subject_id, grade, exam_date, academic_year)
                             VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $subject_id, $grade, $exam_date, $academic_year]);
    endif;
    
    header('Location: exams.php?student_id=' . $student_id);
    exit;
} catch (PDOException $e) {
    die('Ошибка при сохранении: ' . $e->getMessage());
}
?>

