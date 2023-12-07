-- Create database if it does not exist
CREATE DATABASE IF NOT EXISTS `webappwizard`;
USE `webappwizard`;

-- Create `admins` table
CREATE TABLE `admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create `doctors` table
CREATE TABLE `doctors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `anrede` ENUM('Herr','Frau') DEFAULT NULL,
  `titel` VARCHAR(50) DEFAULT NULL,
  `vorname` VARCHAR(100) NOT NULL,
  `nachname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255),
  `arbeitsstelle_name` VARCHAR(255) DEFAULT NULL,
  `arbeitsstelle_adresse` VARCHAR(255) DEFAULT NULL,
  `arbeitsstelle_stadt` VARCHAR(100) DEFAULT NULL,
  `arbeitsstelle_plz` VARCHAR(20) DEFAULT NULL,
  `arbeitsstelle_land` VARCHAR(100) DEFAULT NULL,
  `taetigkeitsbereich` ENUM('Patientenversorgung','Forschung','Arzneimittelentwicklung','Sonstiges') NOT NULL,
  `taetigkeitsbereich_sonstiges` VARCHAR(255) DEFAULT NULL,
  `activated` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create `patients` table
CREATE TABLE `patients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `patient_id` VARCHAR(255) NOT NULL,
  `doctor_id` INT NOT NULL,
  `geburtsdatum` DATE NOT NULL,
  `geschlecht` ENUM('Männlich','Weiblich','Divers') NOT NULL,
  `ethnie` VARCHAR(255),
  `vermutete_diagnose` ENUM('AD', 'Psoriasis', 'Flip-Flop') NOT NULL,
  `histopathologische_untersuchung` ENUM('Ja', 'Nein') NOT NULL,
  `histopathologie_ergebnis` TEXT,
  `bisherige_lokaltherapie_sonstiges` TEXT,
  `bisherige_systemtherapie_sonstiges` TEXT,
  `aktuelle_lokaltherapie_sonstiges` TEXT,
  `aktuelle_systemtherapie_sonstiges` TEXT,
  `jucken_letzte_24_stunden` TINYINT NOT NULL,
  `saved` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Therapy options tables
CREATE TABLE `lokale_therapie_optionen` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `option_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `systemtherapie_optionen` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `option_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tables for patient's bisherige (previous) therapy selections
CREATE TABLE `patient_bisherige_lokale_therapie` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `patient_id` INT NOT NULL,
  `therapie_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`),
  FOREIGN KEY (`therapie_id`) REFERENCES `lokale_therapie_optionen`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `patient_bisherige_systemtherapie` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `patient_id` INT NOT NULL,
  `therapie_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`),
  FOREIGN KEY (`therapie_id`) REFERENCES `systemtherapie_optionen`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tables for patient's aktuelle (current) therapy selections
CREATE TABLE `patient_aktuelle_lokale_therapie` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `patient_id` INT NOT NULL,
  `therapie_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`),
  FOREIGN KEY (`therapie_id`) REFERENCES `lokale_therapie_optionen`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `patient_aktuelle_systemtherapie` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `patient_id` INT NOT NULL,
  `therapie_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`),
  FOREIGN KEY (`therapie_id`) REFERENCES `systemtherapie_optionen`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Create `patient_scores` table
CREATE TABLE `patient_scores` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `patient_id` INT NOT NULL,
  `criteria_1` BOOLEAN,
  `criteria_2` BOOLEAN,
  `criteria_3` BOOLEAN,
  `criteria_4` BOOLEAN,
  `criteria_5` BOOLEAN,
  `criteria_6` BOOLEAN,
  `criteria_7` BOOLEAN,
  `criteria_8` BOOLEAN,
  `criteria_9` BOOLEAN,
  `criteria_10` BOOLEAN,
  `criteria_11` BOOLEAN,
  `criteria_12` BOOLEAN,
  `criteria_13` BOOLEAN,
  `criteria_14` BOOLEAN,
  `criteria_15` BOOLEAN,
  `criteria_16` BOOLEAN,
  `criteria_17` BOOLEAN,
  `criteria_18` BOOLEAN,
  `criteria_19` BOOLEAN,
  `criteria_20` BOOLEAN,
  `total_score` DECIMAL(5,2),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `saved` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO lokale_therapie_optionen (option_name) VALUES ('topische Glukokortikosteroide'), ('topische Calcineurininhibitoren');
INSERT INTO systemtherapie_optionen (option_name) VALUES ('orale Antihistaminika'), ('orale Glukokortikosteroide'), ('Ciclosporin A'), ('Methotrexat'), ('Azathioprin'), ('Dupilumab'), ('Tralokinukmab'), ('Baricitinib'), ('Upadacitinib'), ('Abrocitinib'), ('Lebrikizumab');

