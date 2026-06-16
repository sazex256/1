CREATE DATABASE IF NOT EXISTS library_min CHARACTER SET utf8mb4;
USE library_min;
CREATE TABLE readers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    is_deleted INT DEFAULT 0
);
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    author VARCHAR(100) NOT NULL,
    is_deleted INT DEFAULT 0
);
CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reader_id INT NOT NULL,
    book_id INT NOT NULL,
    loan_date DATE NOT NULL,
    is_deleted INT DEFAULT 0,
    FOREIGN KEY (reader_id) REFERENCES readers(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);
INSERT INTO readers (full_name, phone) VALUES 
('Иванов И.И.', '89991112233'), 
('Петрова А.С.', '89161234567'), 
('Сидоров П.А.', '89009876543');
INSERT INTO books (title, author) VALUES 
('Война и мир', 'Л. Толстой'), 
('Мастер и Маргарита', 'М. Булгаков'), 
('Преступление и наказание', 'Ф. Достоевский');
INSERT INTO loans (reader_id, book_id, loan_date) VALUES 
(1, 1, CURDATE());
