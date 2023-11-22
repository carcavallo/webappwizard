<?php
namespace PR24\Controller;

use PR24\Model\ScoreModel;

/**
 * ScoreController manages the scoring-related actions.
 */
class ScoreController {
    protected $scoreModel;

    /**
     * Constructor to initialize the ScoreModel.
     *
     * @param ScoreModel $scoreModel The model handling score data.
     */    
    public function __construct(ScoreModel $scoreModel) {
        $this->scoreModel = $scoreModel;
    }

    /**
     * Creates a new score record.
     *
     * @return array Status of score creation with message, success or failure.
     */    
    public function createScore() {
        $request = json_decode(file_get_contents('php://input'), true);
        $patientId = $request['patient_id'];
        unset($request['patient_id']);
    
        if (!$this->scoreModel->canCalculateNewScore($patientId)) {
            return ['status' => 'error', 'message' => 'Score calculation limit reached for this patient'];
        }
    
        $totalScore = $this->scoreModel->calculateScore($request);
        if ($totalScore === false) {
            return ['status' => 'error', 'message' => 'Error calculating score'];
        }
    
        for ($i = 1; $i <= 20; $i++) {
            $criteriaKey = 'criteria_' . $i;
            if (!isset($request[$criteriaKey])) {
                $request[$criteriaKey] = NULL;
            }
        }
        
        $insertResult = $this->scoreModel->insertNewScoreRecord($patientId, $request, $totalScore);
        if ($insertResult && $allCriteriaSet) {
            $lastInsertedScoreId = $this->db->lastInsertId();
            $this->scoreModel->setScoreSaved($lastInsertedScoreId);
            return ['status' => 'success', 'message' => 'Score calculated and record inserted', 'score' => $totalScore];
        } else {
            return ['status' => 'error', 'message' => 'Failed to insert score record'];
        }
    }

    /**
     * Retrieves scores for a specific patient.
     *
     * @param int $patientId The ID of the patient.
     * @return array Status of the retrieval operation with scores if successful.
     */    
    public function getScores($patientId) {
        $scores = $this->scoreModel->getScoresByPatientId($patientId);
        if ($scores) {
            return ['status' => 'success', 'message' => 'Scores retrieved successfully', 'scores' => $scores];
        } else {
            return ['status' => 'error', 'message' => 'No scores found for this patient'];
        }
    }
    
    /**
     * Updates a score record.
     *
     * @param int $scoreId The ID of the score record.
     * @return array Status of the update operation with message, success or failure.
     */    
    public function updateScore($scoreId) {
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (!is_array($data)) {
            return ['status' => 'error', 'message' => 'Invalid input data'];
        }
    
        $allCriteriaSet = true;
        for ($i = 1; $i <= 20; $i++) {
            $criteriaKey = 'criteria_' . $i;
            if (!isset($data[$criteriaKey]) || $data[$criteriaKey] === NULL) {
                $allCriteriaSet = false;
                break;
            }
        }
    
        $updateResult = $this->scoreModel->updateScoreRecord($scoreId, $data);
        if ($updateResult && $allCriteriaSet) {
            $this->scoreModel->setScoreSaved($scoreId);
        }
    
        if ($updateResult) {
            return ['status' => 'success', 'message' => 'Score updated successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update score record'];
        }
    }
    
    
    /**
     * Deletes a score record.
     *
     * @param int $scoreId The ID of the score record.
     * @return array Status of the deletion operation with message, success or failure.
     */    
    public function deleteScore($scoreId) {
        $deleteResult = $this->scoreModel->deleteScoreRecord($scoreId);
        if ($deleteResult) {
            return ['status' => 'success', 'message' => 'Score deleted successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete score record'];
        }
    }    
}   
