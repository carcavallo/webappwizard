<?php
namespace PR24\Model;

use PDO;
use PDOException;

class ScoreModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function canCalculateNewScore($patientId) {
        $sql = "SELECT COUNT(*) FROM patient_scores WHERE patient_id = :patient_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':patient_id' => $patientId]);
            $count = $stmt->fetchColumn();

            return $count < 4;
        } catch (PDOException $e) {
            error_log("PDOException in canCalculateNewScore: " . $e->getMessage());
            return false;
        }
    }

    public function calculateScore($criteria) {
        $criteriaScores = [
            'criteria_1' => 4.18, 'criteria_2' => 0.35, 'criteria_3' => 0.24,
            'criteria_4' => -0.63, 'criteria_5' => -0.20, 'criteria_6' => 0.23,
            'criteria_7' => 0.28, 'criteria_8' => 0.33, 'criteria_9' => -0.79,
            'criteria_10' => -0.11, 'criteria_11' => 0.41, 'criteria_12' => -0.59,
            'criteria_13' => 1.20, 'criteria_14' => -0.16, 'criteria_15' => -2.86,
            'criteria_16' => -0.19, 'criteria_17' => 0.19, 'criteria_18' => -7.74,
            'criteria_19' => 5.87, 'criteria_20' => -0.84
        ];
    
        $totalScore = -0.65;
        foreach ($criteriaScores as $key => $value) {
            $criteria[$key] = isset($criteria[$key]) && $criteria[$key];
            if ($criteria[$key]) {
                $totalScore += $value;
            }
        }
        return $totalScore;
    }

    public function insertNewScoreRecord($patientId, $criteria, $totalScore) {
        $sql = "INSERT INTO patient_scores (patient_id, criteria_1, criteria_2, criteria_3, criteria_4, criteria_5, criteria_6, criteria_7, criteria_8, criteria_9, criteria_10, criteria_11, criteria_12, criteria_13, criteria_14, criteria_15, criteria_16, criteria_17, criteria_18, criteria_19, criteria_20, total_score)
                VALUES (:patient_id, :criteria_1, :criteria_2, :criteria_3, :criteria_4, :criteria_5, :criteria_6, :criteria_7, :criteria_8, :criteria_9, :criteria_10, :criteria_11, :criteria_12, :criteria_13, :criteria_14, :criteria_15, :criteria_16, :criteria_17, :criteria_18, :criteria_19, :criteria_20, :total_score)";
        try {
            $defaultCriteria = array_fill_keys(array_map(function($i) { return 'criteria_' . $i; }, range(1, 20)), false);
            foreach ($criteria as $key => $value) {
                $defaultCriteria[$key] = $value ? 1 : 0;
            }
            $parameters = array_merge([':patient_id' => $patientId, ':total_score' => $totalScore], $defaultCriteria);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($parameters);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("PDOException in insertNewScoreRecord: " . $e->getMessage());
            error_log("SQL Query: " . $sql);
            error_log("Query Parameters: " . print_r($parameters, true));
            return false;
        }
    }

    public function getScoresByPatientId($patientId) {
        try {
            $sql = "SELECT * FROM patient_scores WHERE patient_id = :patient_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':patient_id' => $patientId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDOException in getScoresByPatientId: " . $e->getMessage());
            return [];
        }
    }

    public function updateScoreRecord($scoreId, $data) {
        try {
            $criteriaData = array_filter($data, function($key) {
                return strpos($key, 'criteria_') === 0;
            }, ARRAY_FILTER_USE_KEY);
    
            $parameters = [':id' => $scoreId];
            $criteriaSet = [];
            foreach ($criteriaData as $key => $value) {
                $criteriaSet[] = "$key = :$key";
                $parameters[":$key"] = $value ? 1 : 0;
            }
            $criteriaSetString = implode(', ', $criteriaSet);
            $sql = "UPDATE patient_scores SET $criteriaSetString WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($parameters);
    
            $totalScore = $this->calculateScore($criteriaData);
    
            $sql = "UPDATE patient_scores SET total_score = :total_score WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':total_score' => $totalScore, ':id' => $scoreId]);
    
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("PDOException in updateScoreRecord: " . $e->getMessage());
            return false;
        }
    }
    

    public function deleteScoreRecord($scoreId) {
        try {
            $sql = "DELETE FROM patient_scores WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $scoreId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("PDOException in deleteScoreRecord: " . $e->getMessage());
            return false;
        }
    }    
}
