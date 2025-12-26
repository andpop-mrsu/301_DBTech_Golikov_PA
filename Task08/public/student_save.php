<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST'):
    header('Location: index.php');
    exit;
endif;

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$last_name = trim($_POST['last_name'] ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$middle_name = trim($_POST['middle_name'] ?? '');
$birth_date = $_POST['birth_date'] ?? '';
$gender = $_POST['gender'] ?? '';
$group_id = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
$enrollment_date = $_POST['enrollment_date'] ?? '';

// Валидация
if (empty($last_name) || empty($first_name) || empty($birth_date) || 
    empty($gender) || $group_id <= 0 || empty($enrollment_date)):
    header('Location: index.php');
    exit;
endif;

try {
    if ($id > 0):
        // Обновление
        $stmt = $pdo->prepare("UPDATE students SET 
                               last_name = ?, first_name = ?, middle_name = ?, 
                               birth_date = ?, gender = ?, group_id = ?, enrollment_date = ?
                               WHERE id = ?");
        $stmt->execute([$last_name, $first_name, $middle_name, $birth_date, 
                       $gender, $group_id, $enrollment_date, $id]);
    else:
        // Вставка
        $stmt = $pdo->prepare("INSERT INTO students 
                             (last_name, first_name, middle_name, birth_date, gender, group_id, enrollment_date)
                             VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$last_name, $first_name, $middle_name, $birth_date, 
                       $gender, $group_id, $enrollment_date]);
    endif;
    
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    die('Ошибка при сохранении: ' . $e->getMessage());
}
?>

