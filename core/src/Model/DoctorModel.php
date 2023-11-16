<?php
namespace PR24\Model;

use PDO;
use PDOException;

class DoctorModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

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
            // Handle exception
            return false;
        }
    }

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
            // Handle exception
            return false;
        }
    }

    private function generateRandomPassword($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomPassword = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomPassword;
    }

    public function getDoctorEmailById($doctorId) {
        $stmt = $this->db->prepare("SELECT email FROM doctors WHERE id = :id");
        $stmt->execute([':id' => $doctorId]);
        return $stmt->fetchColumn();
    }    

    public function validateCredentials($email, $password) {
        $stmt = $this->db->prepare("SELECT password FROM doctors WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $storedPassword = $stmt->fetchColumn();

        if ($storedPassword && password_verify($password, $storedPassword)) {
            return true;
        }
        return false;
    }
}
