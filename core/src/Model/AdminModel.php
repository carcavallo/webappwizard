<?php

namespace PR24\Model;

use PDO;
use PDOException;

/**
 * AdminModel handles database interactions related to admin functionalities.
 */
class AdminModel {
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
     * Authenticates an admin user.
     *
     * @param string $email Admin's email.
     * @param string $password Admin's password.
     * @return array|null Admin data if authentication is successful, null otherwise.
     */
    public function authenticateAdmin($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admins WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                return $admin;
            }
            return null;
        } catch (PDOException $e) {
            error_log('PDOException in authenticateAdmin: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves patient data and their associated scores.
     *
     * @return array Patient data and scores.
     */
    public function getPatientsAndScores() {
        try {
            $stmt = $this->db->prepare("
                SELECT p.id, p.geschlecht, p.geburtsdatum, s.total_score 
                FROM patients p 
                LEFT JOIN patient_scores s ON p.id = s.patient_id
                ORDER BY p.id
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('PDOException in getPatientsAndScores: ' . $e->getMessage());
            return [];
        }
    }
}
