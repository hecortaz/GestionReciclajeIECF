CREATE DATABASE IF NOT EXISTS reciclaje_aulas
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE reciclaje_aulas;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('jefe_proyecto', 'aprendiz_software', 'aprendiz_monitoreo') NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS measurements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  measurement_date DATE NOT NULL,
  grade VARCHAR(20) NOT NULL,
  classroom VARCHAR(80) NOT NULL,
  zone VARCHAR(80) NOT NULL,
  shift ENUM('Mañana', 'Tarde', 'Noche') NOT NULL,
  waste_type ENUM('Papel limpio', 'Papel sucio', 'PET limpio', 'PET sucio', 'Orgánico') NOT NULL,
  weight_kg DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL DEFAULT 0,
  students_count INT NOT NULL,
  waste_state ENUM('Aprovechable', 'No aprovechable') NOT NULL,
  classification VARCHAR(80) NOT NULL,
  user_id INT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_measurements_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
);

INSERT INTO users (full_name, username, password_hash, role) VALUES
('Aprendiz de software', 'software', SHA2('software123', 256), 'aprendiz_software'),
('Aprendiz de monitoreo', 'monitoreo', SHA2('monitoreo123', 256), 'aprendiz_monitoreo'),
('Jefe de proyecto', 'jefe', SHA2('jefe123', 256), 'jefe_proyecto')
ON DUPLICATE KEY UPDATE username = VALUES(username);

INSERT INTO measurements
(measurement_date, grade, classroom, zone, shift, waste_type, weight_kg, quantity, students_count, waste_state, classification, user_id)
VALUES
(CURDATE(), '8°', 'Salón 801', 'Bloque A', 'Mañana', 'PET limpio', 5.60, 92, 34, 'Aprovechable', 'Reciclable', 1),
(CURDATE(), '8°', 'Salón 802', 'Bloque A', 'Tarde', 'Papel sucio', 3.10, 0, 32, 'No aprovechable', 'No aprovechable', 2),
(CURDATE(), '9°', 'Salón 901', 'Bloque B', 'Mañana', 'Papel limpio', 4.30, 0, 36, 'Aprovechable', 'Reciclable', 1),
(DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Restaurante', 'Restaurante escolar', 'Zona común', 'Mañana', 'Orgánico', 9.20, 0, 120, 'Aprovechable', 'Compostaje', 2),
(DATE_SUB(CURDATE(), INTERVAL 3 DAY), '7°', 'Salón 701', 'Bloque C', 'Tarde', 'PET sucio', 2.80, 48, 31, 'No aprovechable', 'No aprovechable', 1);
