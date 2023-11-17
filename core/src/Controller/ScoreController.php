<?php
namespace PR24\Controller;

use PR24\Model\ScoreModel;

class ScoreController {
    protected $scoreModel;

    public function __construct(ScoreModel $scoreModel) {
        $this->scoreModel = $scoreModel;
    }

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
            if (!isset($request[$criteriaKey]) || !is_bool($request[$criteriaKey])) {
                $request[$criteriaKey] = false;
            }
        }
    
        if ($this->scoreModel->insertNewScoreRecord($patientId, $request, $totalScore)) {
            return ['status' => 'success', 'message' => 'Score calculated and record inserted', 'score' => $totalScore];
        } else {
            return ['status' => 'error', 'message' => 'Failed to insert score record'];
        }
    }

    public function getScores($patientId) {
        $scores = $this->scoreModel->getScoresByPatientId($patientId);
        if ($scores) {
            return ['status' => 'success', 'message' => 'Scores retrieved successfully', 'scores' => $scores];
        } else {
            return ['status' => 'error', 'message' => 'No scores found for this patient'];
        }
    }
    
    public function updateScore($scoreId) {
        $data = json_decode(file_get_contents('php://input'), true);
    
        $updateResult = $this->scoreModel->updateScoreRecord($scoreId, $data);
        if ($updateResult) {
            return ['status' => 'success', 'message' => 'Score updated successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update score record'];
        }
    }
    
    public function deleteScore($scoreId) {
        $deleteResult = $this->scoreModel->deleteScoreRecord($scoreId);
        if ($deleteResult) {
            return ['status' => 'success', 'message' => 'Score deleted successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete score record'];
        }
    }    
}   
