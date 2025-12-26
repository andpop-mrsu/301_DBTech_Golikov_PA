<?php
require_once 'config.php';

try {
    $pdo->exec('PRAGMA foreign_keys = ON;');
    
    // Удаление таблиц
    $pdo->exec('DROP TABLE IF EXISTS credits;');
    $pdo->exec('DROP TABLE IF EXISTS exams;');
    $pdo->exec('DROP TABLE IF EXISTS students;');
    $pdo->exec('DROP TABLE IF EXISTS groups;');
    $pdo->exec('DROP TABLE IF EXISTS curriculum;');
    $pdo->exec('DROP TABLE IF EXISTS subjects;');
    $pdo->exec('DROP TABLE IF EXISTS directions;');
    
    // Создание таблиц
    $pdo->exec("CREATE TABLE directions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(200) NOT NULL UNIQUE,
        degree_level VARCHAR(50) NOT NULL CHECK (degree_level IN ('Бакалавриат', 'Магистратура', 'Специалитет'))
    );");
    
    $pdo->exec("CREATE TABLE subjects (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(200) NOT NULL UNIQUE,
        total_hours INTEGER NOT NULL CHECK (total_hours > 0),
        lecture_hours INTEGER NOT NULL DEFAULT 0 CHECK (lecture_hours >= 0),
        practice_hours INTEGER NOT NULL DEFAULT 0 CHECK (practice_hours >= 0),
        assessment_type VARCHAR(20) NOT NULL CHECK (assessment_type IN ('зачет', 'экзамен')),
        CHECK (lecture_hours + practice_hours <= total_hours)
    );");
    
    $pdo->exec("CREATE TABLE curriculum (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        direction_id INTEGER NOT NULL REFERENCES directions(id) ON DELETE CASCADE,
        subject_id INTEGER NOT NULL REFERENCES subjects(id) ON DELETE CASCADE,
        semester INTEGER NOT NULL CHECK (semester >= 1 AND semester <= 12),
        UNIQUE(direction_id, subject_id, semester)
    );");
    
    $pdo->exec("CREATE TABLE groups (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(50) NOT NULL,
        direction_id INTEGER NOT NULL REFERENCES directions(id),
        study_year INTEGER NOT NULL CHECK (study_year >= 1 AND study_year <= 6),
        academic_year VARCHAR(20) NOT NULL,
        UNIQUE(name, direction_id, study_year, academic_year)
    );");
    
    $pdo->exec("CREATE TABLE students (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        last_name VARCHAR(100) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        middle_name VARCHAR(100),
        birth_date DATE NOT NULL,
        gender CHAR(1) NOT NULL CHECK (gender IN ('M', 'F')),
        group_id INTEGER NOT NULL REFERENCES groups(id),
        enrollment_date DATE NOT NULL DEFAULT (date('now'))
    );");
    
    $pdo->exec("CREATE TABLE exams (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        student_id INTEGER NOT NULL REFERENCES students(id) ON DELETE CASCADE,
        subject_id INTEGER NOT NULL REFERENCES subjects(id),
        grade INTEGER NOT NULL CHECK (grade >= 2 AND grade <= 5),
        exam_date DATE NOT NULL DEFAULT (date('now')),
        academic_year VARCHAR(20) NOT NULL,
        UNIQUE(student_id, subject_id, academic_year)
    );");
    
    $pdo->exec("CREATE TABLE credits (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        student_id INTEGER NOT NULL REFERENCES students(id) ON DELETE CASCADE,
        subject_id INTEGER NOT NULL REFERENCES subjects(id),
        passed INTEGER NOT NULL DEFAULT 0,
        credit_date DATE NOT NULL DEFAULT (date('now')),
        academic_year VARCHAR(20) NOT NULL,
        UNIQUE(student_id, subject_id, academic_year)
    );");
    
    // Вставка данных
    $pdo->exec("INSERT INTO directions (name, degree_level) VALUES
        ('Прикладная математика и информатика', 'Бакалавриат'),
        ('Математика и компьютерные науки', 'Бакалавриат'),
        ('Прикладная информатика', 'Бакалавриат'),
        ('Математика', 'Магистратура'),
        ('Информатика и вычислительная техника', 'Бакалавриат');");
    
    $pdo->exec("INSERT INTO subjects (name, total_hours, lecture_hours, practice_hours, assessment_type) VALUES
        ('Математический анализ', 144, 72, 72, 'экзамен'),
        ('Алгебра и геометрия', 108, 54, 54, 'экзамен'),
        ('Дискретная математика', 108, 54, 54, 'экзамен'),
        ('Программирование', 144, 36, 108, 'экзамен'),
        ('Базы данных', 108, 36, 72, 'экзамен'),
        ('Операционные системы', 72, 36, 36, 'зачет'),
        ('Компьютерные сети', 72, 36, 36, 'зачет'),
        ('Теория вероятностей и математическая статистика', 108, 72, 36, 'экзамен'),
        ('Машинное обучение', 72, 36, 36, 'экзамен'),
        ('Веб-программирование', 108, 36, 72, 'экзамен'),
        ('Алгоритмы и структуры данных', 108, 54, 54, 'экзамен'),
        ('Физика', 108, 72, 36, 'экзамен'),
        ('Иностранный язык', 72, 36, 36, 'зачет'),
        ('Философия', 72, 72, 0, 'зачет'),
        ('История', 72, 72, 0, 'зачет');");
    
    $pdo->exec("INSERT INTO curriculum (direction_id, subject_id, semester) VALUES
        (1, 1, 1), (1, 1, 2), (1, 2, 1), (1, 3, 2), (1, 4, 1), (1, 4, 2),
        (1, 5, 3), (1, 6, 3), (1, 7, 4), (1, 8, 3), (1, 9, 5), (1, 10, 4),
        (1, 11, 3), (1, 12, 1), (1, 13, 1), (1, 13, 2), (1, 14, 2), (1, 15, 1),
        (2, 1, 1), (2, 1, 2), (2, 2, 1), (2, 3, 2), (2, 4, 1), (2, 5, 3), (2, 8, 3), (2, 11, 3);");
    
    $pdo->exec("INSERT INTO groups (name, direction_id, study_year, academic_year) VALUES
        ('303', 1, 3, '2020/2021'), ('304', 1, 3, '2020/2021'),
        ('403', 1, 4, '2021/2022'), ('404', 1, 4, '2021/2022'),
        ('501', 1, 1, '2022/2023'), ('502', 1, 1, '2022/2023'),
        ('201', 2, 2, '2020/2021'), ('202', 2, 2, '2020/2021'), ('301', 2, 3, '2021/2022');");
    
    $pdo->exec("INSERT INTO students (last_name, first_name, middle_name, birth_date, gender, group_id, enrollment_date) VALUES
        ('Иванов', 'Иван', 'Иванович', '2001-05-15', 'M', 1, '2018-09-01'),
        ('Петрова', 'Мария', 'Сергеевна', '2001-08-22', 'F', 1, '2018-09-01'),
        ('Сидоров', 'Алексей', 'Владимирович', '2001-03-10', 'M', 1, '2018-09-01'),
        ('Козлова', 'Анна', 'Дмитриевна', '2001-11-30', 'F', 1, '2018-09-01'),
        ('Морозов', 'Дмитрий', 'Александрович', '2001-07-05', 'M', 1, '2018-09-01'),
        ('Волкова', 'Елена', 'Игоревна', '2001-04-18', 'F', 2, '2018-09-01'),
        ('Новиков', 'Сергей', 'Петрович', '2001-09-12', 'M', 2, '2018-09-01'),
        ('Федорова', 'Ольга', 'Николаевна', '2001-12-25', 'F', 2, '2018-09-01'),
        ('Смирнов', 'Андрей', 'Викторович', '2001-06-08', 'M', 2, '2018-09-01'),
        ('Лебедева', 'Татьяна', 'Александровна', '2001-02-14', 'F', 2, '2018-09-01'),
        ('Соколов', 'Павел', 'Игоревич', '2000-10-20', 'M', 3, '2017-09-01'),
        ('Михайлова', 'Екатерина', 'Сергеевна', '2000-01-28', 'F', 3, '2017-09-01'),
        ('Кузнецов', 'Роман', 'Дмитриевич', '2000-05-03', 'M', 3, '2017-09-01'),
        ('Орлова', 'Наталья', 'Владимировна', '2000-08-15', 'F', 4, '2017-09-01'),
        ('Попов', 'Игорь', 'Александрович', '2000-03-22', 'M', 4, '2017-09-01'),
        ('Васильев', 'Александр', 'Николаевич', '2004-07-10', 'M', 5, '2022-09-01'),
        ('Романова', 'Виктория', 'Андреевна', '2004-11-05', 'F', 5, '2022-09-01'),
        ('Григорьев', 'Максим', 'Сергеевич', '2004-02-18', 'M', 5, '2022-09-01'),
        ('Титова', 'Дарья', 'Ивановна', '2004-09-30', 'F', 6, '2022-09-01'),
        ('Белов', 'Никита', 'Дмитриевич', '2004-04-12', 'M', 6, '2022-09-01'),
        ('Семенов', 'Артем', 'Владимирович', '2002-06-20', 'M', 7, '2019-09-01'),
        ('Антонова', 'Юлия', 'Сергеевна', '2002-08-14', 'F', 7, '2019-09-01');");
    
    // Вставка экзаменов
    $exams = [
        [1, 1, 5, '2021-01-15', '2020/2021'], [1, 2, 4, '2021-01-20', '2020/2021'],
        [1, 3, 5, '2021-06-10', '2020/2021'], [1, 4, 5, '2021-06-15', '2020/2021'],
        [1, 5, 4, '2021-12-20', '2021/2022'], [1, 8, 5, '2021-12-25', '2021/2022'],
        [1, 11, 4, '2021-12-18', '2021/2022'],
        [2, 1, 4, '2021-01-15', '2020/2021'], [2, 2, 4, '2021-01-20', '2020/2021'],
        [2, 3, 4, '2021-06-10', '2020/2021'], [2, 4, 5, '2021-06-15', '2020/2021'],
        [2, 5, 5, '2021-12-20', '2021/2022'], [2, 8, 4, '2021-12-25', '2021/2022'],
        [2, 11, 5, '2021-12-18', '2021/2022'],
        [3, 1, 3, '2021-01-15', '2020/2021'], [3, 2, 3, '2021-01-20', '2020/2021'],
        [3, 3, 4, '2021-06-10', '2020/2021'], [3, 4, 3, '2021-06-15', '2020/2021'],
        [3, 5, 4, '2021-12-20', '2021/2022'], [3, 8, 3, '2021-12-25', '2021/2022'],
        [3, 11, 4, '2021-12-18', '2021/2022'],
        [4, 1, 5, '2021-01-15', '2020/2021'], [4, 2, 5, '2021-01-20', '2020/2021'],
        [4, 3, 5, '2021-06-10', '2020/2021'], [4, 4, 5, '2021-06-15', '2020/2021'],
        [4, 5, 5, '2021-12-20', '2021/2022'], [4, 8, 5, '2021-12-25', '2021/2022'],
        [4, 11, 5, '2021-12-18', '2021/2022'],
        [5, 1, 2, '2021-01-15', '2020/2021'], [5, 2, 3, '2021-01-20', '2020/2021'],
        [5, 3, 2, '2021-06-10', '2020/2021'], [5, 4, 3, '2021-06-15', '2020/2021'],
        [5, 5, 3, '2021-12-20', '2021/2022'], [5, 8, 2, '2021-12-25', '2021/2022'],
        [5, 11, 3, '2021-12-18', '2021/2022'],
        [6, 1, 4, '2021-01-15', '2020/2021'], [6, 2, 5, '2021-01-20', '2020/2021'],
        [6, 3, 4, '2021-06-10', '2020/2021'], [6, 4, 4, '2021-06-15', '2020/2021'],
        [6, 5, 5, '2021-12-20', '2021/2022'], [6, 8, 4, '2021-12-25', '2021/2022'],
        [6, 11, 4, '2021-12-18', '2021/2022'],
        [7, 1, 5, '2021-01-15', '2020/2021'], [7, 2, 5, '2021-01-20', '2020/2021'],
        [7, 3, 5, '2021-06-10', '2020/2021'], [7, 4, 5, '2021-06-15', '2020/2021'],
        [7, 5, 5, '2021-12-20', '2021/2022'], [7, 8, 5, '2021-12-25', '2021/2022'],
        [7, 11, 5, '2021-12-18', '2021/2022'],
        [8, 1, 3, '2021-01-15', '2020/2021'], [8, 2, 4, '2021-01-20', '2020/2021'],
        [8, 3, 3, '2021-06-10', '2020/2021'], [8, 4, 4, '2021-06-15', '2020/2021'],
        [8, 5, 4, '2021-12-20', '2021/2022'], [8, 8, 4, '2021-12-25', '2021/2022'],
        [8, 11, 4, '2021-12-18', '2021/2022'],
        [11, 5, 5, '2021-12-20', '2021/2022'], [11, 9, 4, '2022-06-15', '2021/2022'],
        [11, 10, 5, '2022-06-20', '2021/2022'],
        [12, 5, 5, '2021-12-20', '2021/2022'], [12, 9, 5, '2022-06-15', '2021/2022'],
        [12, 10, 4, '2022-06-20', '2021/2022'],
        [16, 1, 4, '2023-01-20', '2022/2023'], [16, 2, 5, '2023-01-25', '2022/2023'],
        [16, 4, 4, '2023-06-15', '2022/2023'], [16, 12, 4, '2023-01-30', '2022/2023'],
        [17, 1, 5, '2023-01-20', '2022/2023'], [17, 2, 5, '2023-01-25', '2022/2023'],
        [17, 4, 5, '2023-06-15', '2022/2023'], [17, 12, 5, '2023-01-30', '2022/2023'],
        [19, 1, 4, '2021-01-15', '2020/2021'], [19, 2, 4, '2021-01-20', '2020/2021'],
        [19, 3, 5, '2021-06-10', '2020/2021'], [19, 4, 4, '2021-06-15', '2020/2021']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO exams (student_id, subject_id, grade, exam_date, academic_year) VALUES (?, ?, ?, ?, ?)");
    foreach ($exams as $exam) {
        $stmt->execute($exam);
    }
    
    // Индексы
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_students_group ON students(group_id);');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_exams_student ON exams(student_id);');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_exams_subject ON exams(subject_id);');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_exams_academic_year ON exams(academic_year);');
    
    echo "База данных успешно инициализирована!\n";
} catch (PDOException $e) {
    echo "Ошибка при инициализации базы данных: " . $e->getMessage() . "\n";
}
?>
