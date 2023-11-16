<?php
namespace PR24\Model;

use PDO;
use PDOException;

class PatientModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function createPatient($patientData) {
        $sql = "INSERT INTO patients (patient_id, doctor_id, geburtsdatum, geschlecht, ethnie, vermutete_diagnose, histopathologische_untersuchung, histopathologie_ergebnis, bisherige_lokaltherapie_sonstiges, bisherige_systemtherapie_sonstiges, aktuelle_lokaltherapie_sonstiges, aktuelle_systemtherapie_sonstiges, jucken_letzte_24_stunden) VALUES (:patient_id, :doctor_id, :geburtsdatum, :geschlecht, :ethnie, :vermutete_diagnose, :histopathologische_untersuchung, :histopathologie_ergebnis, :bisherige_lokaltherapie_sonstiges, :bisherige_systemtherapie_sonstiges, :aktuelle_lokaltherapie_sonstiges, :aktuelle_systemtherapie_sonstiges, :jucken_letzte_24_stunden)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($patientData);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("PDOException in createPatient: " . $e->getMessage());
            return false;
        }
    }
    /*
    public function getPatientById($patientId) {
        $sql = "SELECT * FROM patients WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $patientId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error or handle exception
            return false;
        }
    }

    public function updatePatient($patientId, $patientData) {
        $sql = "UPDATE patients SET patient_id = :patient_id, doctor_id = :doctor_id, geburtsdatum = :geburtsdatum, geschlecht = :geschlecht, ethnie = :ethnie, vermutete_diagnose = :vermutete_diagnose, histopathologische_untersuchung = :histopathologische_untersuchung, histopathologie_ergebnis = :histopathologie_ergebnis, bisherige_lokaltherapie_sonstiges = :bisherige_lokaltherapie_sonstiges, bisherige_systemtherapie_sonstiges = :bisherige_systemtherapie_sonstiges, aktuelle_lokaltherapie_sonstiges = :aktuelle_lokaltherapie_sonstiges, aktuelle_systemtherapie_sonstiges = :aktuelle_systemtherapie_sonstiges, jucken_letzte_24_stunden = :jucken_letzte_24_stunden WHERE id = :id";
    
        try {
            $stmt = $this->db->prepare($sql);
            $patientData['id'] = $patientId; // Ensure 'id' is included in the data array
            $stmt->execute($patientData);
            return $stmt->rowCount(); // Returns the number of affected rows
        } catch (PDOException $e) {
            // Log error or handle exception
            return false;
        }
    }
    

    public function deletePatient($patientId) {
        $sql = "DELETE FROM patients WHERE id = :id";
    
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $patientId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount(); // Returns the number of affected rows
        } catch (PDOException $e) {
            // Log error or handle exception
            return false;
        }
    }
    */

}
