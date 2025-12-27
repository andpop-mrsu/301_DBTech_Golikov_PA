<?php
require_once 'config.php';

// Получение фильтра по группе
$filterGroupId = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;

// Получение списка всех групп для фильтра
$groupsStmt = $pdo->query("SELECT id, name FROM groups ORDER BY name");
$groups = $groupsStmt->fetchAll();

// Формирование запроса для получения студентов
$sql = "SELECT s.id, s.last_name, s.first_name, s.middle_name, s.birth_date, s.gender, 
               g.name as group_name, g.id as group_id
        FROM students s
        JOIN groups g ON s.group_id = g.id";
        
$params = [];

if ($filterGroupId > 0):
    $sql .= " WHERE s.group_id = ?";
    $params[] = $filterGroupId;
endif;

$sql .= " ORDER BY g.name, s.last_name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список студентов</title>
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
        .filter {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .filter label {
            margin-right: 10px;
            font-weight: bold;
        }
        .filter select {
            padding: 5px 10px;
            font-size: 14px;
        }
        .filter button {
            padding: 5px 15px;
            margin-left: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .filter button:hover {
            background: #0056b3;
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
        .btn-exams {
            background: #17a2b8;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Список студентов</h1>
        
        <div class="filter">
            <form method="GET" action="">
                <label for="group_id">Фильтр по группе:</label>
                <select name="group_id" id="group_id">
                    <option value="0">Все группы</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= htmlspecialchars($group['id']) ?>" 
                                <?= $filterGroupId == $group['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($group['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Применить</button>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Группа</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Дата рождения</th>
                    <th>Пол</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Студенты не найдены</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['group_name']) ?></td>
                            <td><?= htmlspecialchars($student['last_name']) ?></td>
                            <td><?= htmlspecialchars($student['first_name']) ?></td>
                            <td><?= htmlspecialchars($student['middle_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($student['birth_date']) ?></td>
                            <td><?= $student['gender'] == 'M' ? 'М' : 'Ж' ?></td>
                            <td class="actions">
                                <a href="student_edit.php?id=<?= $student['id'] ?>" class="btn-edit">Редактировать</a>
                                <a href="student_delete.php?id=<?= $student['id'] ?>" class="btn-delete">Удалить</a>
                                <a href="exams.php?student_id=<?= $student['id'] ?>" class="btn-exams">Результаты экзаменов</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <a href="student_add.php" class="btn-add">Добавить студента</a>
    </div>
</body>
</html>

