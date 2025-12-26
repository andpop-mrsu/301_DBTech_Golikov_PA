<?php
/**
 * Скрипт для проверки доступных расширений PHP
 */
echo "<h2>Проверка расширений PHP</h2>";
echo "<h3>PDO драйверы:</h3>";
$pdo_drivers = PDO::getAvailableDrivers();
if (empty($pdo_drivers)) {
    echo "<p style='color: red;'>❌ PDO драйверы не найдены!</p>";
} else {
    echo "<p style='color: green;'>✅ Найдены следующие PDO драйверы:</p>";
    echo "<ul>";
    foreach ($pdo_drivers as $driver) {
        $is_sqlite = ($driver === 'sqlite' || $driver === 'sqlite2');
        $icon = $is_sqlite ? '✅' : '•';
        echo "<li>{$icon} {$driver}</li>";
    }
    echo "</ul>";
    
    if (!in_array('sqlite', $pdo_drivers) && !in_array('sqlite2', $pdo_drivers)) {
        echo "<p style='color: red;'><strong>❌ PDO_SQLITE не установлен!</strong></p>";
    } else {
        echo "<p style='color: green;'><strong>✅ PDO_SQLITE доступен!</strong></p>";
    }
}

echo "<h3>Загруженные расширения PHP:</h3>";
$loaded_extensions = get_loaded_extensions();
$pdo_extensions = array_filter($loaded_extensions, function($ext) {
    return stripos($ext, 'pdo') !== false || stripos($ext, 'sqlite') !== false;
});

if (empty($pdo_extensions)) {
    echo "<p style='color: orange;'>⚠️ Расширения PDO/SQLite не найдены в списке загруженных</p>";
} else {
    echo "<ul>";
    foreach ($pdo_extensions as $ext) {
        echo "<li>✅ {$ext}</li>";
    }
    echo "</ul>";
}

echo "<h3>Информация о PHP:</h3>";
echo "<p><strong>Версия PHP:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Путь к php.ini:</strong> " . php_ini_loaded_file() . "</p>";
echo "<p><strong>Дополнительные ini файлы:</strong> " . php_ini_scanned_files() . "</p>";

echo "<h3>Рекомендации:</h3>";
if (!in_array('sqlite', $pdo_drivers) && !in_array('sqlite2', $pdo_drivers)) {
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 10px 0;'>";
    echo "<h4>Как исправить:</h4>";
    echo "<ol>";
    echo "<li>Найдите файл php.ini (обычно в директории PHP или в C:\\Windows)</li>";
    echo "<li>Откройте php.ini в текстовом редакторе</li>";
    echo "<li>Найдите строку <code>;extension=pdo_sqlite</code> или <code>;extension=sqlite3</code></li>";
    echo "<li>Удалите точку с запятой в начале строки (раскомментируйте):<br>";
    echo "<code>extension=pdo_sqlite</code><br>";
    echo "<code>extension=sqlite3</code></li>";
    echo "<li>Сохраните файл и перезапустите веб-сервер (Apache/Nginx) или PHP встроенный сервер</li>";
    echo "</ol>";
    echo "<p><strong>Примечание:</strong> Убедитесь, что файл php_sqlite3.dll существует в директории ext вашей установки PHP.</p>";
    echo "</div>";
} else {
    echo "<p style='color: green;'>✅ Все необходимые расширения установлены!</p>";
}
?>

