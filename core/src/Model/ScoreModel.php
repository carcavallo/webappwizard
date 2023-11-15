<?php
namespace PR24\Model;

use PDO;

class ScoreModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function createScore($patientId, $scoreData) {
        // Logic to insert a new score record into the database
        // Use PDO prepared statements for security
    }

    public function getScoreByPatientId($patientId) {
        // Logic to retrieve a score record by patient ID
    }

    public function updateScore($patientId, $scoreData) {
        // Logic to update an existing score record
    }

    public function deleteScore($patientId) {
        // Logic to delete a score record
    }

    public function calculateTotalScore($scoreData) {
        // Logic to calculate the total score based on the criteria
        // Return the calculated score
    }
}
