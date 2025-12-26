<?php
require_once 'config.php';

// Получение списка групп
$groupsStmt = $pdo->query("SELECT id, name FROM groups ORDER BY name");
$groups = $groupsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить студента</title>
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
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .radio-group {
            display: flex;
            gap: 20px;
        }
        .radio-group label {
            font-weight: normal;
            display: flex;
            align-items: center;
        }
        .radio-group input[type="radio"] {
            width: auto;
            margin-right: 5px;
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
        <h1>Добавить студента</h1>
        
        <form method="POST" action="student_save.php">
            <div class="form-group">
                <label for="last_name">Фамилия *</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            
            <div class="form-group">
                <label for="first_name">Имя *</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            
            <div class="form-group">
                <label for="middle_name">Отчество</label>
                <input type="text" id="middle_name" name="middle_name">
            </div>
            
            <div class="form-group">
                <label for="birth_date">Дата рождения *</label>
                <input type="date" id="birth_date" name="birth_date" required>
            </div>
            
            <div class="form-group">
                <label>Пол *</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="M" required> Мужской
                    </label>
                    <label>
                        <input type="radio" name="gender" value="F" required> Женский
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="group_id">Группа *</label>
                <select id="group_id" name="group_id" required>
                    <option value="">Выберите группу</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= htmlspecialchars($group['id']) ?>">
                            <?= htmlspecialchars($group['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="enrollment_date">Дата поступления *</label>
                <input type="date" id="enrollment_date" name="enrollment_date" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-submit">Сохранить</button>
                <a href="index.php" class="btn btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>

