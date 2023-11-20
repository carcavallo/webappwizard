<?php

namespace PR24\Controller;

use PR24\Model\AdminModel;
use Firebase\JWT\JWT;
use Exception;

/**
 * AdminController handles administrative actions such as authentication and data export.
 */
class AdminController {
    protected $adminModel;

    /**
     * Constructor to initialize AdminModel.
     * 
     * @param AdminModel $adminModel The model handling admin data.
     */
    public function __construct(AdminModel $adminModel) {
        $this->adminModel = $adminModel;
    }

    /**
     * Authenticates admin user and generates a JWT token.
     *
     * @return array Result of authentication with JWT token if successful.
     */
    public function authenticate() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
    
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
    
        if ($admin = $this->adminModel->authenticateAdmin($email, $password)) {
            try {
                $token = [
                    "iss" => $_ENV['BASEDOMAIN'],
                    "iat" => time(),
                    "exp" => time() + 3600,
                    "data" => [
                        "email" => $email
                    ]
                ];
    
                $jwt = JWT::encode($token, $_ENV['SECRET_KEY'], 'HS256');
    
                return ['success' => true, 'message' => 'Authorized', 'token' => $jwt];
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'JWT encoding failed'];
            }
        } else {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
    }

    /**
     * Exports all patients data in CSV format.
     */
    public function exportPatientData() {
        try {
            $patients = $this->adminModel->getPatientsAndScores();
    
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="patienten_daten.csv"');
    
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Patienten-ID', 'Geschlecht', 'Geburtsdatum', 'Flip-Flop-Scores']);
    
            $patientRows = [];
    
            foreach ($patients as $patient) {
                if (!array_key_exists($patient['id'], $patientRows)) {
                    $patientRows[$patient['id']] = [
                        'id' => $patient['id'],
                        'geschlecht' => $patient['geschlecht'],
                        'geburtsdatum' => $patient['geburtsdatum'],
                        'scores' => []
                    ];
                }
                $patientRows[$patient['id']]['scores'][] = $patient['total_score'];
            }
    
            foreach ($patientRows as $patientId => $data) {
                $scoresString = implode(", ", $data['scores']);
                fputcsv($output, [$data['id'], $data['geschlecht'], $data['geburtsdatum'], $scoresString]);
            }
    
            fclose($output);
        } catch (Exception $e) {
            logError("Error exporting patient data: " . $e->getMessage());
            return false;
        }
    }
}
