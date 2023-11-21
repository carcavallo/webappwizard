<?php

namespace PR24\Model;

use PDO;
use PDOException;

class TherapyOptionsModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

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
