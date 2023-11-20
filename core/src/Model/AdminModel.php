<?php
namespace PR24\Model;
use PDO;

class AdminModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function authenticateAdmin($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        return null;
    }

    public function getPatientsAndScores() {
        $stmt = $this->db->prepare("
            SELECT p.id, p.geschlecht, p.geburtsdatum, s.total_score 
            FROM patients p 
            LEFT JOIN patient_scores s ON p.id = s.patient_id
            ORDER BY p.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}