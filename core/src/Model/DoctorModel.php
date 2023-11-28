<?php

namespace PR24\Model;

use PDO;
use PDOException;

/**
 * DoctorModel handles database interactions related to doctor functionalities.
 */
class DoctorModel {
    protected $db;

    /**
     * Constructor to initialize the database connection.
     * 
     * @param PDO $db Database connection object.
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Creates a new doctor record in the database.
     *
     * @param array $doctorData The doctor's data.
     * @return int|false The newly created doctor ID or false on failure.
     */
    public function createDoctor($doctorData) {
        if (isset($doctorData['password']) && !empty($doctorData['password'])) {
            $hashedPassword = password_hash($doctorData['password'], PASSWORD_DEFAULT);
            $doctorData['password'] = $hashedPassword;
        } else {
            $doctorData['password'] = NULL;
        }
        
        $sql = "INSERT INTO doctors (anrede, titel, vorname, nachname, email, password, arbeitsstelle_name, arbeitsstelle_adresse, arbeitsstelle_stadt, arbeitsstelle_plz, arbeitsstelle_land, taetigkeitsbereich, taetigkeitsbereich_sonstiges) VALUES (:anrede, :titel, :vorname, :nachname, :email, :password, :arbeitsstelle_name, :arbeitsstelle_adresse, :arbeitsstelle_stadt, :arbeitsstelle_plz, :arbeitsstelle_land, :taetigkeitsbereich, :taetigkeitsbereich_sonstiges)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':anrede' => $doctorData['anrede'],
                ':titel' => $doctorData['titel'],
                ':vorname' => $doctorData['vorname'],
                ':nachname' => $doctorData['nachname'],
                ':email' => $doctorData['email'],
                ':password' => $doctorData['password'],
                ':arbeitsstelle_name' => $doctorData['arbeitsstelle_name'],
                ':arbeitsstelle_adresse' => $doctorData['arbeitsstelle_adresse'],
                ':arbeitsstelle_stadt' => $doctorData['arbeitsstelle_stadt'],
                ':arbeitsstelle_plz' => $doctorData['arbeitsstelle_plz'],
                ':arbeitsstelle_land' => $doctorData['arbeitsstelle_land'],
                ':taetigkeitsbereich' => $doctorData['taetigkeitsbereich'],
                ':taetigkeitsbereich_sonstiges' => $doctorData['taetigkeitsbereich_sonstiges']
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('PDOException in createDoctor: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks if a doctor is activated.
     *
     * @param int $doctorId The ID of the doctor.
     * @return bool|null True if activated, false if not, null if doctor doesn't exist.
     */
    public function isDoctorActivated($doctorId) {
        $sql = "SELECT activated FROM doctors WHERE id = :id";
    
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $doctorId, PDO::PARAM_INT);
            $stmt->execute();
    
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return (bool)$row['activated'];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log('PDOException in isDoctorActivated: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Activates a doctor and sets a new password.
     *
     * @param int $doctorId The ID of the doctor.
     * @return string|false The new password or false on failure.
     */
    public function activateDoctorAndSetPassword($doctorId) {
        $newPassword = $this->generateRandomPassword();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE doctors SET activated = 1, password = :password WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $doctorId, ':password' => $hashedPassword]);

            if ($stmt->rowCount() > 0) {
                return $newPassword;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log('PDOException in activateDoctorAndSetPassword: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generates a random password.
     *
     * @param int $length Length of the password.
     * @return string The generated password.
     */
    private function generateRandomPassword($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomPassword = '';

        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomPassword;
    }

    /**
     * Retrieves the ID of a doctor by their email.
     *
     * @param string $email The email of the doctor.
     * @return int|null The ID of the doctor or null if not found.
     */
    public function getDoctorIdByEmail($email) {
        $stmt = $this->db->prepare("SELECT id FROM doctors WHERE email = :email");
        $stmt->execute([':email' => $email]);

        return $stmt->fetchColumn();
    }

    /**
     * Retrieves the email of a doctor by their ID.
     *
     * @param int $doctorId The ID of the doctor.
     * @return string|null The email of the doctor or null if not found.
     */
    public function getDoctorEmailById($doctorId) {
        $stmt = $this->db->prepare("SELECT email FROM doctors WHERE id = :id");
        $stmt->execute([':id' => $doctorId]);

        return $stmt->fetchColumn();
    }    

    /**
     * Validates the credentials of a doctor.
     *
     * @param string $email The email of the doctor.
     * @param string $password The password of the doctor.
     * @return bool True if credentials are valid, false otherwise.
     */
    public function validateCredentials($email, $password) {
        $stmt = $this->db->prepare("SELECT password FROM doctors WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $storedPassword = $stmt->fetchColumn();

        if ($storedPassword && password_verify($password, $storedPassword)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieves the email of a doctor associated with a patient ID.
     *
     * @param int $patientId The ID of the patient.
     * @return string|null The email of the doctor or null if not found.
     */
    public function getDoctorEmailByPatientId($patientId) {
        $sql = "SELECT email FROM doctors WHERE id = (SELECT doctor_id FROM patients WHERE id = :patient_id)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('PDOException in getDoctorEmailByPatientId: ' . $e->getMessage());
            return null;
        }
    }
}