-- Création de la base de données
CREATE DATABASE IF NOT EXISTS ensi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ensi_db;

-- Supprimer les tables si existent
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS subjects;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS professors;
DROP TABLE IF EXISTS users;

-- Table utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,  -- Taille réduite car pas de hash
    role ENUM('student', 'professor', 'admin') NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255) DEFAULT 'default.png',
    token VARCHAR(255) DEFAULT NULL,
    token_expires DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table professeurs
CREATE TABLE professors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    department VARCHAR(100) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    bio TEXT,
    office VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table étudiants
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    student_number VARCHAR(20) UNIQUE NOT NULL,
    level VARCHAR(20) NOT NULL,
    specialty VARCHAR(100),
    birth_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table matières
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    level VARCHAR(20) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table ressources
CREATE TABLE resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    type ENUM('cours', 'td', 'tp', 'devoir') NOT NULL,
    subject_id INT NOT NULL,
    professor_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    file_type VARCHAR(50),
    downloads INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (professor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table messages contact
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'read', 'replied') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table actualités
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    author_id INT,
    published BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ==================== DONNÉES DE TEST (Mots de passe EN CLAIR) ====================

-- Admin
INSERT INTO users (fullname, email, password, role, phone) VALUES
('Administrateur ENSI', 'admin@ensi.tn', 'admin', 'admin', '+216 71 600 444');

-- Professeurs (password: 1234)
INSERT INTO users (fullname, email, password, role, phone) VALUES
('Dr. Ahmed Ben Ali', 'a.benali@ensi.tn', '1234', 'professor', '+216 20 111 222'),
('Prof. Fatma Jaziri', 'f.jaziri@ensi.tn', '1234', 'professor', '+216 20 333 444'),
('Dr. Mohamed Trabelsi', 'm.trabelsi@ensi.tn', '1234', 'professor', '+216 20 555 666'),
('Prof. Sonia Kammoun', 's.kammoun@ensi.tn', '1234', 'professor', '+216 20 777 888'),
('Dr. Karim Bouazizi', 'k.bouazizi@ensi.tn', '1234', 'professor', '+216 20 999 000'),
('Prof. Leila Mansour', 'l.mansour@ensi.tn', '1234', 'professor', '+216 21 111 222');

-- Détails des professeurs
INSERT INTO professors (user_id, department, subject, bio, office) VALUES
(2, 'Informatique', 'Programmation Web', 'Expert en développement web et technologies modernes', 'B-201'),
(3, 'IA & Data Science', 'Intelligence Artificielle', 'Spécialiste en Machine Learning et Deep Learning', 'B-305'),
(4, 'Réseaux', 'Réseaux & Sécurité', 'Expert en sécurité informatique et cryptographie', 'A-102'),
(5, 'Mathématiques', 'Analyse Numérique', 'PhD en mathématiques appliquées', 'A-203'),
(6, 'Informatique', 'Bases de Données', 'Expert en SQL, NoSQL et Big Data', 'B-210'),
(7, 'IA & Data Science', 'Machine Learning', 'Chercheuse en apprentissage automatique', 'B-310');

-- Étudiants (password: 1234)
INSERT INTO users (fullname, email, password, role) VALUES
('Amine Khouaja', 'a.khouaja@ensi.tn', '1234', 'student'),
('Sarra Mejri', 's.mejri@ensi.tn', '1234', 'student'),
('Youssef Gharbi', 'y.gharbi@ensi.tn', '1234', 'student'),
('Ines Bouhlel', 'i.bouhlel@ensi.tn', '1234', 'student'),
('Mehdi Chakroun', 'm.chakroun@ensi.tn', '1234', 'student');

INSERT INTO students (user_id, student_number, level, specialty) VALUES
(8, 'ENSI2024001', '2A', 'Génie Logiciel'),
(9, 'ENSI2024002', '3A', 'Intelligence Artificielle'),
(10, 'ENSI2024003', '1A', 'Tronc Commun'),
(11, 'ENSI2024004', '2A', 'Réseaux'),
(12, 'ENSI2024005', '3A', 'Génie Logiciel');

-- Matières
INSERT INTO subjects (name, code, level, description) VALUES
('Programmation Web', 'WEB101', '2A', 'HTML, CSS, JavaScript, PHP'),
('Intelligence Artificielle', 'IA201', '3A', 'Machine Learning, Deep Learning'),
('Bases de Données', 'BD102', '2A', 'SQL, Modélisation, NoSQL'),
('Réseaux TCP/IP', 'RES201', '3A', 'Protocoles réseau et sécurité'),
('Algorithmique', 'ALG101', '1A', 'Structures de données et algorithmes'),
('Génie Logiciel', 'GL202', '2A', 'UML, Design Patterns, Agile');

-- Actualités
INSERT INTO news (title, content, author_id) VALUES
('Hackathon ENSI 2026', 'Les inscriptions sont ouvertes pour le hackathon annuel. Venez nombreux !', 1),
('Forum des Entreprises', 'Rencontrez nos partenaires pour trouver votre stage.', 1),
('Examens Semestre 1', 'Le calendrier des examens est disponible dans votre espace.', 1);