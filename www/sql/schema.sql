-- ============================================================
-- Эмнэлгийн цаг захиалгын систем — өгөгдлийн сангийн бүтэц
-- Нэмэлт: users, email, price, status enum-үүд
-- ============================================================

-- Кирилл үсэг зөв хадгалагдахын тулд клиент холболтын кодчилолыг
-- заавал utf8mb4 болгоно (энэ мөр байхгүй бол зарим mysql client
-- анхдагч latin1-ээр уншиж, өгөгдлийг давхар кодлож гэмтээж болно).
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS hospital_appointment
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hospital_appointment;

-- Админ / ажилтны эрх
CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  email         VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name     VARCHAR(120) NOT NULL,
  role          ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  status        ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Эмч нар
CREATE TABLE doctors (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(120) NOT NULL,
  phone       VARCHAR(20),
  email       VARCHAR(120),
  specialty   VARCHAR(120) NOT NULL,
  photo       VARCHAR(255),
  bio         TEXT,
  status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Үйлчилгээ
CREATE TABLE services (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(150) NOT NULL,
  description TEXT,
  duration    INT NOT NULL COMMENT 'минутаар',
  price       DECIMAL(12,2) NOT NULL DEFAULT 0,
  status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Эмч <-> Үйлчилгээ (M:N), эмч тус бүрийн онцгой үнэ
CREATE TABLE doctor_services (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id   INT NOT NULL,
  service_id  INT NOT NULL,
  price       DECIMAL(12,2) NULL COMMENT 'NULL бол services.price ашиглана',
  status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_doctor_service (doctor_id, service_id),
  FOREIGN KEY (doctor_id)  REFERENCES doctors(id)  ON DELETE CASCADE,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Эмчийн долоо хоногийн байнгын хуваарь
CREATE TABLE doctor_schedules (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id   INT NOT NULL,
  day_of_week TINYINT NOT NULL COMMENT '0=Ням,1=Даваа...6=Бямба',
  start_time  TIME NOT NULL,
  end_time    TIME NOT NULL,
  status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
  FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Хуваарийн онцгой өөрчлөлт (амралт, чөлөө, нэмэлт цаг)
CREATE TABLE schedule_exceptions (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id      INT NOT NULL,
  exception_date DATE NOT NULL,
  start_time     TIME NULL,
  end_time       TIME NULL,
  type           ENUM('day_off','extra_hours') NOT NULL DEFAULT 'day_off',
  reason         VARCHAR(255),
  FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Өвчтөн
CREATE TABLE patients (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120) NOT NULL,
  phone         VARCHAR(20)  NOT NULL,
  email         VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  register_no   VARCHAR(20)  UNIQUE COMMENT 'РД',
  birth_date    DATE,
  gender        ENUM('male','female','other'),
  status        ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Цаг захиалга
CREATE TABLE appointments (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id         INT NOT NULL,
  patient_id        INT NOT NULL,
  service_id        INT NOT NULL,
  appointment_date  DATE NOT NULL,
  start_time        TIME NOT NULL,
  end_time          TIME NOT NULL,
  status            ENUM('pending','confirmed','completed','cancelled','no_show') NOT NULL DEFAULT 'pending',
  note              TEXT,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_doctor_slot (doctor_id, appointment_date, start_time),
  FOREIGN KEY (doctor_id)  REFERENCES doctors(id),
  FOREIGN KEY (patient_id) REFERENCES patients(id),
  FOREIGN KEY (service_id) REFERENCES services(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Анхны өгөгдөл
-- ------------------------------------------------------------

-- Нэвтрэх нэр: admin   Нууц үг: admin123
INSERT INTO users (username, email, password_hash, full_name, role)
VALUES ('admin', 'admin@clinic.mn', '$2b$10$clJez0BAn6bAnPW6j/HkVuK1FyEIjI3/tS2XM/gejZlJGEEX24apW', 'Систем администратор', 'admin');

INSERT INTO services (name, description, duration, price) VALUES
('Ерөнхий үзлэг', 'Дотрын ерөнхий эмчийн үзлэг', 20, 15000),
('Шүдний үзлэг', 'Шүдний эмчийн анхан шатны үзлэг', 30, 25000),
('Нүдний үзлэг', 'Харааны шинжилгээ, үзлэг', 25, 20000),
('Арьс арчилгаа', 'Арьс өвчний эмчийн зөвлөгөө', 20, 18000);

INSERT INTO doctors (name, phone, email, specialty, status) VALUES
('Б. Оюунчимэг', '99110011', 'oyunchimeg@clinic.mn', 'Дотрын эмч', 'active'),
('Д. Ганбаатар', '99223344', 'ganbaatar@clinic.mn', 'Шүдний эмч', 'active'),
('С. Мөнхзул', '99887766', 'munkhzul@clinic.mn', 'Нүдний эмч', 'active');

INSERT INTO doctor_services (doctor_id, service_id) VALUES
(1, 1), (2, 2), (3, 3), (1, 4);

INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time) VALUES
(1, 1, '09:00:00', '17:00:00'),
(1, 2, '09:00:00', '17:00:00'),
(1, 3, '09:00:00', '17:00:00'),
(1, 4, '09:00:00', '17:00:00'),
(1, 5, '09:00:00', '13:00:00'),
(2, 1, '10:00:00', '18:00:00'),
(2, 3, '10:00:00', '18:00:00'),
(2, 5, '10:00:00', '18:00:00'),
(3, 2, '09:00:00', '16:00:00'),
(3, 4, '09:00:00', '16:00:00');
