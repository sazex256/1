CREATE DATABASE IF NOT EXISTS library_exam CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_exam;
CREATE TABLE readers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE, -- Ограничение UNIQUE добавлено
    is_deleted INT DEFAULT 0           -- Мягкое удаление
);
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(100) NOT NULL,
    is_deleted INT DEFAULT 0           -- Мягкое удаление
);
CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reader_id INT NOT NULL,
    book_id INT NOT NULL,
    loan_date DATE NOT NULL,
    is_deleted INT DEFAULT 0,          -- Мягкое удаление
    FOREIGN KEY (reader_id) REFERENCES readers(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);
INSERT INTO readers (full_name, phone) VALUES 
('Иванов Иван Иванович', '89991112233'), 
('Петрова Анна Сергеевна', '89161234567'), 
('Сидоров Петр Алексеевич', '89009876543');
INSERT INTO books (title, author) VALUES 
('Война и мир', 'Лев Толстой'), 
('Мастер и Маргарита', 'Михаил Булгаков'), 
('Преступление и наказание', 'Федор Достоевский');
INSERT INTO loans (reader_id, book_id, loan_date) VALUES 
(1, 1, CURDATE()),
(2, 2, CURDATE()),
(3, 3, CURDATE());
