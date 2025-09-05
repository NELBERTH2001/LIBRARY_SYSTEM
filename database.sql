-- Library DB schema (fixed) for the upgraded front-end build
SET NAMES utf8mb4;
DROP TABLE IF EXISTS borrow_transactions;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS admins;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_no VARCHAR(30) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  course VARCHAR(80),
  year_level VARCHAR(20),
  email VARCHAR(120),
  phone VARCHAR(30),
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  isbn VARCHAR(20),
  title VARCHAR(200) NOT NULL,
  author VARCHAR(200),
  publisher VARCHAR(200),
  year_pub VARCHAR(10),
  category VARCHAR(120),
  copies_total INT NOT NULL DEFAULT 1,
  copies_available INT NOT NULL DEFAULT 1,
  shelf_location VARCHAR(60),
  status ENUM('available','unavailable') NOT NULL DEFAULT 'available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE borrow_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  borrow_code VARCHAR(16) NOT NULL,
  student_id INT NOT NULL,
  book_id INT NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  status ENUM('requested','issued','returned','denied','overdue') NOT NULL DEFAULT 'requested',
  requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  issued_at DATETIME DEFAULT NULL,
  due_at DATETIME DEFAULT NULL,
  returned_at DATETIME DEFAULT NULL,
  notes TEXT,
  INDEX (borrow_code),
  INDEX (status),
  CONSTRAINT fk_borrow_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT fk_borrow_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed admin (admin / admin123)
INSERT INTO admins (username, password_hash)
VALUES ('admin', '$2y$10$1T5g9sD2Z9x0s1kQ9w9i4u5V5m3Gznxj1Z3xqYpXnC8M4U3Jr7mS6');

-- Sample books
INSERT INTO books (isbn, title, author, publisher, year_pub, category, copies_total, copies_available, shelf_location)
VALUES
('9780553386790', 'The Martian', 'Andy Weir', 'Crown', '2014', 'Fiction', 5, 5, 'FIC-WEI'),
('9780131103627', 'The C Programming Language', 'Kernighan & Ritchie', 'Prentice Hall', '1988', 'Computers', 3, 3, 'COMP-KR'),
('9780262033848', 'Introduction to Algorithms', 'Cormen et al.', 'MIT Press', '2009', 'Computers', 2, 2, 'COMP-CLRS');
