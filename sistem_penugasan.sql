CREATE DATABASE sistem_penugasan;
USE sistem_penugasan;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    PASSWORD VARCHAR(255) NOT NULL,
    ROLE ENUM('admin', 'mahasiswa') NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE classes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    class_name VARCHAR(150) NOT NULL,
    class_code VARCHAR(20) UNIQUE NOT NULL,
    DESCRIPTION TEXT,
    admin_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    class_id INT NOT NULL,
    student_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (class_id, student_id)
);

CREATE TABLE tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    class_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    DESCRIPTION TEXT,
    deadline DATETIME NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT NOT NULL,
    student_id INT NOT NULL,
    file_path VARCHAR(500),
    submission_text TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_late BOOLEAN DEFAULT FALSE,
    grade DECIMAL(5,2),
    feedback TEXT,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_submission (task_id, student_id)
);

ALTER TABLE submissions
ADD COLUMN file_name VARCHAR(255) NULL,
ADD COLUMN file_type VARCHAR(100) NULL;

ALTER TABLE submissions
ADD notes TEXT NULL;

ALTER TABLE tasks
ADD publish_at DATETIME NULL;

ALTER TABLE users
ADD first_name VARCHAR(100) AFTER id,
ADD last_name VARCHAR(100) AFTER first_name;

UPDATE users
SET
first_name = SUBSTRING_INDEX(full_name, ' ', 1),
last_name = SUBSTRING(full_name, LENGTH(SUBSTRING_INDEX(full_name, ' ', 1)) + 2);

INSERT INTO users (username, email, PASSWORD, ROLE, full_name) VALUES
('admin', 'admin@system.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator'),
('mahasiswa1', 'student1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', 'Budi Santoso');

INSERT INTO classes (class_name, class_code, DESCRIPTION, admin_id) VALUES
('Pemrograman Web Dasar', 'WEB2024', 'Kelas pemrograman web menggunakan PHP', 1),
('Database Sistem', 'DB2024', 'Belajar database MySQL', 1);

SELECT ROLE FROM users;

ALTER TABLE users
MODIFY ROLE ENUM('admin','dosen','mahasiswa') NOT NULL;

UPDATE users
SET ROLE = 'dosen'
WHERE ROLE = 'admin';

ALTER TABLE users
MODIFY ROLE ENUM('dosen','mahasiswa') NOT NULL;

INSERT INTO users (
    username,
    email,
    PASSWORD,
    ROLE,
    full_name
)

VALUES (
    'dosen1',
    'dosen@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'dosen',
    'Nama Dosen'
);

ALTER TABLE users
MODIFY ROLE ENUM('dosen','mahasiswa') NOT NULL;

UPDATE users
SET ROLE='dosen'
WHERE username='admin';

SELECT username, ROLE
FROM users
WHERE username='dosen1';

SELECT id, username, ROLE
FROM users;

ALTER TABLE classes
CHANGE admin_id teacher_id INT NOT NULL;

ALTER TABLE classes
ADD archived TINYINT(1) DEFAULT 0;

SELECT id, class_name, teacher_id, archived
FROM classes;

UPDATE users
SET full_name = 'Dosen Sistem Informasi'
WHERE email = 'dosen@gmail.com';

ALTER TABLE tasks
ADD material_file VARCHAR(255) NULL;

ALTER TABLE submissions
ADD submission_link TEXT NULL;