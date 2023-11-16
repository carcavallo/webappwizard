-- Create database if it does not exist
CREATE DATABASE IF NOT EXISTS `webappwizard`;
USE `webappwizard`;

-- Create `admins` table
CREATE TABLE `admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create `doctors` table
CREATE TABLE `doctors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `anrede` ENUM('Herr','Frau') NOT NULL,
  `titel` VARCHAR(50) DEFAULT NULL,
  `vorname` VARCHAR(100) NOT NULL,
  `nachname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
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
  PRIMARY KEY (`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create tables for therapy options
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

-- Junction tables for patient selections
CREATE TABLE `patient_lokale_therapie` (
  `patient_id` INT NOT NULL,
  `therapie_id` INT NOT NULL,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`),
  FOREIGN KEY (`therapie_id`) REFERENCES `lokale_therapie_optionen`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `patient_systemtherapie` (
  `patient_id` INT NOT NULL,
  `therapie_id` INT NOT NULL,
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
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data into doctors
INSERT INTO `doctors` (`anrede`, `titel`, `vorname`, `nachname`, `email`, `arbeitsstelle_name`, `arbeitsstelle_adresse`, `arbeitsstelle_stadt`, `arbeitsstelle_plz`, `arbeitsstelle_land`, `taetigkeitsbereich`, `taetigkeitsbereich_sonstiges`) 
VALUES ('Herr', 'Dr.', 'Max', 'Mustermann', 'max.mustermann@email.com', 'Klinikum Beispielstadt', 'Musterstrasse 1', 'Beispielstadt', '12345', 'Deutschland', 'Patientenversorgung', NULL),
('Frau', NULL, 'Anna', 'Schmidt', 'anna.schmidt@email.com', 'Forschungsinstitut Musterstadt', 'Forschungsweg 2', 'Musterstadt', '54321', 'Deutschland', 'Forschung', NULL);

-- Insert sample data into lokale_therapie_optionen
INSERT INTO `lokale_therapie_optionen` (`option_name`) VALUES ('topische Glukokortikosteroide'), ('topische Calcineurininhibitoren'), ('Sonstiges');

-- Insert sample data into systemtherapie_optionen
INSERT INTO `systemtherapie_optionen` (`option_name`) VALUES ('orale Antihistaminika'), ('orale Glukokortikosteroide'), ('Ciclosporin A'), ('Methotrexat'), ('Azathioprin'), ('Dupilumab'), ('Tralokinukmab'), ('Baricitinib'), ('Upadacitinib'), ('Abrocitinib'), ('Sonstiges');

-- Note: The following patient_id values are placeholders and should be generated by the application logic
-- Insert sample data into patients
INSERT INTO `patients` (`patient_id`, `doctor_id`, `geburtsdatum`, `geschlecht`, `ethnie`, `vermutete_diagnose`, `histopathologische_untersuchung`, `histopathologie_ergebnis`, `bisherige_lokaltherapie_sonstiges`, `bisherige_systemtherapie_sonstiges`, `aktuelle_lokaltherapie_sonstiges`, `aktuelle_systemtherapie_sonstiges`, `jucken_letzte_24_stunden`) VALUES ('m1980zr3s1', 1, '1980-05-15', 'Männlich', 'Europäisch', 'AD', 'Ja', 'Normal', 'keine', 'keine', 'keine', 'keine', 5), ('w1995kf4s1', 2, '1995-07-20', 'Weiblich', 'Asiatisch', 'Psoriasis', 'Nein', NULL, 'keine', 'keine', 'keine', 'keine', 3);

-- Note: The following insertions assume the existence of appropriate ids from the patients and therapy option tables
-- Insert sample data into patient_lokale_therapie
INSERT INTO `patient_lokale_therapie` (`patient_id`, `therapie_id`) VALUES ('m1980-zr3s1', 1), ('m1980zr3s1', 2), ('w1995kf4s1', 1);

-- Insert sample data into patient_systemtherapie
INSERT INTO `patient_systemtherapie` (`patient_id`, `therapie_id`) VALUES ('m1980-zr3s1', 1), ('w1995kf4s1', 2), ('w1995kf4s1', 3);

-- Insert sample data into patient_scores
INSERT INTO `patient_scores` (`patient_id`, `criteria_1`, `criteria_2`, `criteria_3`, `criteria_4`, `criteria_5`, `criteria_6`, `criteria_7`, `criteria_8`, `criteria_9`, `criteria_10`, `criteria_11`, `criteria_12`, `criteria_13`, `criteria_14`, `criteria_15`, `criteria_16`, `criteria_17`, `criteria_18`, `criteria_19`, `criteria_20`, `total_score`)
VALUES 
(1, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, -1.21),
(2, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, FALSE, TRUE, 2.34);