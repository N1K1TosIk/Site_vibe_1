CREATE DATABASE IF NOT EXISTS exam_inf_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE exam_inf_db;

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  source_id INT NOT NULL,
  task_number VARCHAR(50),
  category VARCHAR(255),
  theme VARCHAR(255),
  text TEXT,
  image_url VARCHAR(1024),
  answer VARCHAR(255),
  solution_html TEXT,
  difficulty VARCHAR(50),
  year INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
