<?php
require 'db.php';
$msg = '';

// 1. Добавление читателя
if (isset($_POST['add_reader'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    
    if (empty($name) || empty($phone)) {
        $msg = "<div class='error'>Ошибка: Заполните все поля!</div>";
    } elseif (!preg_match('/^(\+7\(\d{3}\)\d{3}-\d{2}-\d{2}|8\d{10})$/', $phone)) {
        $msg = "<div class='error'>Ошибка: Неверный формат телефона! Используйте +7(XXX)XXX-XX-XX или 8XXXXXXXXXX</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO readers (full_name, phone) VALUES (?, ?)");
        try {
            $stmt->execute([$name, $phone]);
            $msg = "<div class='success'>Успех: Читатель зарегистрирован!</div>";
        } catch (PDOException $e) {
            $msg = "<div class='error'>Ошибка: Такой телефон уже существует!</div>";
        }
    }
}

// 2. Бронирование книги
if (isset($_POST['book_loan'])) {
    $rid = (int)$_POST['reader_id'];
    $bid = (int)$_POST['book_id'];
    
    if ($rid == 0 || $bid == 0) {
        $msg = "<div class='error'>Ошибка: Выберите читателя и книгу!</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO loans (reader_id, book_id, loan_date) VALUES (?, ?, CURDATE())");
        $stmt->execute([$rid, $bid]);
        $msg = "<div class='success'>Успех: Книга забронирована!</div>";
    }
}

// 3. Мягкое удаление
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("UPDATE readers SET is_deleted = 1 WHERE id = ?");
    $stmt->execute([$id]);
    $msg = "<div class='success'>Успех: Читатель архивирован.</div>";
}

// Получение данных
$readers = $pdo->query("SELECT id, full_name FROM readers WHERE is_deleted = 0")->fetchAll();
$books = $pdo->query("SELECT id, title FROM books WHERE is_deleted = 0")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Библиотека ПМ.05</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 30px auto; padding: 20px; background: #f4f6f8; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h2 { text-align: center; color: #2c3e50; }
        h3 { color: #2980b9; border-bottom: 2px solid #2980b9; padding-bottom: 5px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #2980b9; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #1f6391; }
        .error { color: #c0392b; background: #fadbd8; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .success { color: #27ae60; background: #d5f5e3; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        #err { color: red; font-size: 13px; margin-top: -10px; margin-bottom: 10px; display: none; }
        ul { list-style: none; padding: 0; }
        li { padding: 10px; background: #fff; margin-bottom: 5px; border-radius: 4px; display: flex; justify-content: space-between; }
        .del { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Система управления библиотекой</h2>
        <?= $msg ?>

        <h3>Регистрация читателя</h3>
        <!-- ДОБАВЛЕНЫ name и id для работы PHP и JS -->
        <form method="POST" onsubmit="return checkPhone()">
            <label>ФИО:</label>
            <input type="text" name="name" id="name" required placeholder="Иванов Иван Иванович">
            
            <label>Телефон:</label>
            <input type="text" name="phone" id="phone" required placeholder="+7(XXX)XXX-XX-XX">
            <small id="err">Неверный формат! Используйте +7(XXX)XXX-XX-XX или 8XXXXXXXXXX</small>
            
            <button type="submit" name="add_reader">Добавить</button>
        </form>

        <h3>Бронирование книги</h3>
        <form method="POST">
            <label>Читатель:</label>
            <!-- ДОБАВЛЕН name и цикл PHP для вывода -->
            <select name="reader_id" required>
                <option value="0">-- Выберите --</option>
                <?php foreach($readers as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <label>Книга:</label>
            <select name="book_id" required>
                <option value="0">-- Выберите --</option>
                <?php foreach($books as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" name="book_loan">Забронировать</button>
        </form>

        <h3>Список читателей (Мягкое удаление)</h3>
        <ul>
            <!-- ИСПРАВЛЕНА ссылка удаления и добавлен цикл -->
            <?php foreach($readers as $r): ?>
                <li>
                    <?= htmlspecialchars($r['full_name']) ?>
                    <a href="?delete=<?= $r['id'] ?>" class="del" onclick="return confirm('Архивировать?')">[Удалить]</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script>
        // Маска ввода
        document.getElementById('phone').addEventListener('input', function (e) {
            let val = e.target.value.replace(/\D/g, '');
            if (val.length > 0) {
                if (val[0] === '8' || val[0] === '7') val = '7' + val.substring(1);
                else val = '7' + val;
            }
            let formatted = '';
            if (val.length > 0) formatted += '+' + val[0];
            if (val.length > 1) formatted += '(' + val.substring(1, 4);
            if (val.length >= 5) formatted += ')' + val.substring(4, 7);
            if (val.length >= 8) formatted += '-' + val.substring(7, 9);
            if (val.length >= 10) formatted += '-' + val.substring(9, 11);
            e.target.value = formatted;
        });

        // Валидация
        function checkPhone() {
            const phoneInput = document.getElementById('phone');
            const errorMsg = document.getElementById('err');
            const regex = /^(\+7\(\d{3}\)\d{3}-\d{2}-\d{2}|8\d{10})$/;
            
            if (!regex.test(phoneInput.value.trim())) {
                errorMsg.style.display = 'block';
                phoneInput.style.border = '1px solid red';
                return false;
            }
            errorMsg.style.display = 'none';
            phoneInput.style.border = '1px solid #ccc';
            return true;
        }
    </script>
</body>
</html>