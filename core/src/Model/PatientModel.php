<?php
namespace PR24\Model;

use PDO;
use PDOException;

/**
 * PatientModel handles database interactions related to patient functionalities.
 */
class PatientModel {
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
     * Creates a new patient record in the database.
     *
     * @param array $patientData Data for the new patient.
     * @return int|false The ID of the newly created patient or false on failure.
     */
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

    /**
     * Retrieves a patient record by ID.
     *
     * @param int $patientId The ID of the patient.
     * @return array|null Patient data if found, null otherwise.
     */
    public function getPatientById($patientId) {
        $sql = "SELECT * FROM patients WHERE id = :patient_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['patient_id' => $patientId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates a patient record.
     *
     * @param int $patientId The ID of the patient to update.
     * @param array $patientData Updated data for the patient.
     * @return bool True on successful update, false on failure.
     */    
    public function updatePatient($patientId, $patientData) {
        $sql = "UPDATE patients SET 
                    doctor_id = :doctor_id, 
                    geburtsdatum = :geburtsdatum, 
                    geschlecht = :geschlecht, 
                    ethnie = :ethnie, 
                    vermutete_diagnose = :vermutete_diagnose, 
                    histopathologische_untersuchung = :histopathologische_untersuchung, 
                    histopathologie_ergebnis = :histopathologie_ergebnis, 
                    bisherige_lokaltherapie_sonstiges = :bisherige_lokaltherapie_sonstiges, 
                    bisherige_systemtherapie_sonstiges = :bisherige_systemtherapie_sonstiges, 
                    aktuelle_lokaltherapie_sonstiges = :aktuelle_lokaltherapie_sonstiges, 
                    aktuelle_systemtherapie_sonstiges = :aktuelle_systemtherapie_sonstiges, 
                    jucken_letzte_24_stunden = :jucken_letzte_24_stunden 
                WHERE id = :patient_id";
    
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':doctor_id', $patientData['doctor_id']);
            $stmt->bindParam(':geburtsdatum', $patientData['geburtsdatum']);
            $stmt->bindParam(':geschlecht', $patientData['geschlecht']);
            $stmt->bindParam(':ethnie', $patientData['ethnie']);
            $stmt->bindParam(':vermutete_diagnose', $patientData['vermutete_diagnose']);
            $stmt->bindParam(':histopathologische_untersuchung', $patientData['histopathologische_untersuchung']);
            $stmt->bindParam(':histopathologie_ergebnis', $patientData['histopathologie_ergebnis']);
            $stmt->bindParam(':bisherige_lokaltherapie_sonstiges', $patientData['bisherige_lokaltherapie_sonstiges']);
            $stmt->bindParam(':bisherige_systemtherapie_sonstiges', $patientData['bisherige_systemtherapie_sonstiges']);
            $stmt->bindParam(':aktuelle_lokaltherapie_sonstiges', $patientData['aktuelle_lokaltherapie_sonstiges']);
            $stmt->bindParam(':aktuelle_systemtherapie_sonstiges', $patientData['aktuelle_systemtherapie_sonstiges']);
            $stmt->bindParam(':jucken_letzte_24_stunden', $patientData['jucken_letzte_24_stunden']);
    
            $stmt->bindParam(':patient_id', $patientId);
    
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("PDOException in updatePatient: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Deletes a patient record from the database.
     *
     * @param int $patientId The ID of the patient to delete.
     * @return bool True on successful deletion, false on failure.
     */
    public function deletePatient($patientId) {
        $sql = "DELETE FROM patients WHERE id = :patient_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['patient_id' => $patientId]);
    }
}
