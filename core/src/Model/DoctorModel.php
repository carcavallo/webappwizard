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
        $sql = "INSERT INTO doctors (anrede, titel, vorname, nachname, email, arbeitsstelle_name, arbeitsstelle_adresse, arbeitsstelle_stadt, arbeitsstelle_plz, arbeitsstelle_land, taetigkeitsbereich, taetigkeitsbereich_sonstiges) VALUES (:anrede, :titel, :vorname, :nachname, :email, :arbeitsstelle_name, :arbeitsstelle_adresse, :arbeitsstelle_stadt, :arbeitsstelle_plz, :arbeitsstelle_land, :taetigkeitsbereich, :taetigkeitsbereich_sonstiges)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':anrede' => $doctorData['anrede'],
                ':titel' => $doctorData['titel'],
                ':vorname' => $doctorData['vorname'],
                ':nachname' => $doctorData['nachname'],
                ':email' => $doctorData['email'],
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
    
    public function activateDoctor($doctorId) {
        $sql = "UPDATE doctors SET activated = 1 WHERE id = :id AND activated = 0";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $doctorId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log('PDOException in activateDoctor: ' . $e->getMessage());
            return false;
        }
    }
    /*
    public function getDoctorById($doctorId) {
        $sql = "SELECT * FROM doctors WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $doctorId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle exception
            return false;
        }
    }

    public function updateDoctor($doctorId, $doctorData) {
        $sql = "UPDATE doctors SET anrede = :anrede, titel = :titel, vorname = :vorname, nachname = :nachname, email = :email, arbeitsstelle_name = :arbeitsstelle_name, arbeitsstelle_adresse = :arbeitsstelle_adresse, arbeitsstelle_stadt = :arbeitsstelle_stadt, arbeitsstelle_plz = :arbeitsstelle_plz, arbeitsstelle_land = :arbeitsstelle_land, taetigkeitsbereich = :taetigkeitsbereich, taetigkeitsbereich_sonstiges = :taetigkeitsbereich_sonstiges WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $doctorData['id'] = $doctorId; // Add the ID to the doctorData array
            $stmt->execute($doctorData);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            // Handle exception
            return false;
        }
    }

    public function deleteDoctor($doctorId) {
        $sql = "DELETE FROM doctors WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $doctorId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            // Handle exception
            return false;
        }
    }
    */
}
