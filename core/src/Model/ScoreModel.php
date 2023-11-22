<?php
namespace PR24\Model;

use PDO;
use PDOException;
use TCPDF;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PR24\Model\DoctorModel;

/**
 * ScoreModel handles database interactions related to score functionalities.
 */
class ScoreModel {
    protected $db;
    protected $doctorModel;

    /**
     * Constructor to initialize database and DoctorModel.
     *
     * @param PDO $db Database connection object.
     * @param DoctorModel $doctorModel The model handling doctor data.
     */    
    public function __construct(PDO $db, DoctorModel $doctorModel) {
        $this->db = $db;
        $this->doctorModel = $doctorModel;
    }

    /**
     * Checks if a new score can be calculated for a patient.
     *
     * @param int $patientId The ID of the patient.
     * @return bool True if a new score can be calculated, false otherwise.
     */    
    public function canCalculateNewScore($patientId) {
        $sql = "SELECT COUNT(*) FROM patient_scores WHERE patient_id = :patient_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':patient_id' => $patientId]);
        $count = $stmt->fetchColumn();
        return $count < 4;
    }

    /**
     * Calculates the score based on given criteria.
     *
     * @param array $criteria Criteria for score calculation.
     * @return float Calculated score.
     */    
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

    /**
     * Inserts a new score record into the database.
     *
     * @param int $patientId The ID of the patient.
     * @param array $criteria Criteria used for the score calculation.
     * @param float $totalScore The calculated total score.
     * @return bool True on successful insertion, false on failure.
     */    
    public function insertNewScoreRecord($patientId, $criteria, $totalScore) {
        $allCriteriaSet = true;
        $defaultCriteria = array_fill_keys(array_map(function($i) { return 'criteria_' . $i; }, range(1, 20)), NULL);
    
        foreach ($criteria as $key => $value) {
            if (is_null($value)) {
                $allCriteriaSet = false;
                $defaultCriteria[$key] = NULL;
            } else {
                $defaultCriteria[$key] = $value ? 1 : 0;
            }
        }
    
        $sql = "INSERT INTO patient_scores (patient_id, criteria_1, criteria_2, criteria_3, criteria_4, criteria_5, criteria_6, criteria_7, criteria_8, criteria_9, criteria_10, criteria_11, criteria_12, criteria_13, criteria_14, criteria_15, criteria_16, criteria_17, criteria_18, criteria_19, criteria_20, total_score)
                VALUES (:patient_id, :criteria_1, :criteria_2, :criteria_3, :criteria_4, :criteria_5, :criteria_6, :criteria_7, :criteria_8, :criteria_9, :criteria_10, :criteria_11, :criteria_12, :criteria_13, :criteria_14, :criteria_15, :criteria_16, :criteria_17, :criteria_18, :criteria_19, :criteria_20, :total_score)";
    
        $parameters = array_merge([':patient_id' => $patientId, ':total_score' => $totalScore], $defaultCriteria);
    
        $stmt = $this->db->prepare($sql);
        $stmt->execute($parameters);
    
        if ($allCriteriaSet) {
            $scoreId = $this->db->lastInsertId();
            $this->setScoreSaved(intval($scoreId));
            $this->generateAndSendScoreReport($patientId, $totalScore);            
        }

        return $stmt->rowCount() > 0;
    }

    /**
     * Sets the 'saved' status of a score record to true.
     *
     * @param int $scoreId The ID of the score record.
     * @return bool True on successful update, false on failure.
     */
    public function setScoreSaved($scoreId) {
        $sql = "UPDATE patient_scores SET saved = 1 WHERE id = :score_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':score_id' => $scoreId]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Retrieves scores by patient ID.
     *
     * @param int $patientId The ID of the patient.
     * @return array Scores of the specified patient.
     */
    public function getScoresByPatientId($patientId) {
        $sql = "SELECT * FROM patient_scores WHERE patient_id = :patient_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves patient data by patient ID.
     *
     * @param int $patientId The ID of the patient.
     * @return array|null Patient data if found, null otherwise.
     */
    public function getPatientData($patientId) {
        $sql = "SELECT * FROM patients WHERE id = :patient_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates a score record in the database.
     *
     * @param int $scoreId The ID of the score record.
     * @param array $data Data for updating the score.
     * @return bool True on successful update, false on failure.
     */    
    public function updateScoreRecord($scoreId, $data) {
        $criteriaData = array_filter($data, function($key) {
            return strpos($key, 'criteria_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        $allCriteriaSet = true;

        foreach ($criteriaData as $key => &$value) {
            if (isset($value) && in_array($value, [0, 1])) {
                $value = (bool)$value;
            } else {
                $allCriteriaSet = false;
                $value = false;
            }
        }
        $parameters = [':id' => $scoreId];
        $criteriaSet = [];

        foreach ($criteriaData as $key => $value) {
            $criteriaSet[] = "$key = :$key";
            $parameters[":$key"] = $value ? 1 : 0;
        }

        $totalScore = $this->calculateScore($criteriaData);
        $parameters[':total_score'] = $totalScore;
        $criteriaSetString = implode(', ', $criteriaSet);
        $sql = "UPDATE patient_scores SET $criteriaSetString, total_score = :total_score WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($parameters);

        if ($allCriteriaSet) {
            $patientId = $this->getPatientIdByScoreId($scoreId);
            if ($patientId) {
                $this->generateAndSendScoreReport($patientId, $totalScore);
            }
        }
        return true;
    }

    /**
     * Deletes a score record from the database.
     *
     * @param int $scoreId The ID of the score record to delete.
     * @return bool True on successful deletion, false on failure.
     */    
    public function deleteScoreRecord($scoreId) {
        $sql = "DELETE FROM patient_scores WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $scoreId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Generates a PDF report for a patient.
     *
     * @param int $patientId The ID of the patient.
     * @return string File path of the generated PDF.
     */    
    public function generatePdf($patientId) {
        $patientIdStr = $this->getPatientIdStr($patientId);
        $scores = $this->getScoresByPatientId($patientId);
    
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
    
        $pdfContent = "Patienten ID: " . $patientIdStr . "\n";
    
        $scoreValues = array_column($scores, 'total_score');
        $pdfContent .= "Flip-Flop-Scores: " . implode(", ", $scoreValues);
    
        $pdf->Write(0, $pdfContent, '', 0, 'L', true, 0, false, false, 0);
    
        $pdfDirectory = __DIR__ . '/../pdf/';
        $pdfFileName = "PatientenScores_" . $patientIdStr . ".pdf";
        $pdfPath = $pdfDirectory . $pdfFileName;
    
        $pdf->Output($pdfPath, 'F');
        return $pdfPath;
    }
    
    /**
     * Retrieves the patient identifier string based on patient ID.
     *
     * @param int $patientId The ID of the patient.
     * @return string The patient identifier string.
     */
    private function getPatientIdStr($patientId) {
        $sql = "SELECT patient_id FROM patients WHERE id = :patient_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Generates and sends a score report via email.
     *
     * @param int $patientId The ID of the patient.
     * @param float $totalScore The total score of the patient.
     */    
    public function generateAndSendScoreReport($patientId, $totalScore) {
        $pdfPath = $this->generatePdf($patientId, $totalScore);
        $doctorEmail = $this->doctorModel->getDoctorEmailByPatientId($patientId);
        if ($doctorEmail) {
            $this->sendScoreEmail($doctorEmail, $pdfPath);
        }
    }

    /**
     * Sends an email containing the score report to the doctor.
     *
     * @param string $doctorEmail The email address of the doctor.
     * @param string $pdfPath The file path of the PDF score report.
     */    
    public function sendScoreEmail($doctorEmail, $pdfPath) {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['EMAIL_USERNAME'];
        $mail->Password = $_ENV['EMAIL_PASSWORD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($_ENV['EMAIL_FROM'], 'CK-Care Flip-Flop-Scores');
        $mail->addAddress($doctorEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Patient Flip-Flop-Scores Report';

        $mail->Body = 'Die berechneten Flip-Flop-Scores finden Sie im Anhang.';
        $mail->addAttachment($pdfPath);
        $mail->send();
    }

    /**
     * Retrieves the patient ID associated with a specific score record.
     *
     * @param int $scoreId The ID of the score record.
     * @return int The patient ID associated with the score.
     */    
    private function getPatientIdByScoreId($scoreId) {
        $sql = "SELECT patient_id FROM patient_scores WHERE id = :score_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':score_id' => $scoreId]);
        return $stmt->fetchColumn();
    }
}