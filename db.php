<?php
$host = '127.0.0.1';
$db   = 'library_exam';
$user = 'root'; // Если у вас другой пользователь, измените здесь
$pass = '';     // Если у вас есть пароль, укажите его здесь
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Настоящие подготовленные выражения
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Ошибка подключения к БД: " . htmlspecialchars($e->getMessage()));
}
?>