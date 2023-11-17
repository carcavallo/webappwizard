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
}
