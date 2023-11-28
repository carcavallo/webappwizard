<?php

namespace PR24\Model;

use PDO;
use PDOException;

/**
 * TherapyOptionsModel handles database interactions related to therapy functionalities.
 */
class TherapyOptionsModel {
    protected $db;

    /**
     * Constructor to initialize database and DoctorModel.
     *
     * @param PDO $db Database connection object.
     */  
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Retrieves local therapy options from the database.
     *
     * @return array|null An array of local therapy options or null in case of an error.
     */
    public function getLokaleTherapyOptions() {
        $sql = "SELECT * FROM lokale_therapie_optionen";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDOException in getLokaleTherapyOptions: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves systemic therapy options from the database.
     *
     * @return array|null An array of systemic therapy options or null in case of an error.
     */
    public function getSystemtherapieOptions() {
        $sql = "SELECT * FROM systemtherapie_optionen";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDOException in getSystemtherapieOptions: " . $e->getMessage());
            return null;
        }
    }
}