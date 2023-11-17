<?php
namespace PR24\Controller;

use PR24\Model\AdminModel;

class AdminController {
    protected $adminModel;

    public function __construct(AdminModel $adminModel) {
        $this->adminModel = $adminModel;
    }

    public function authenticate() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $admin = $this->adminModel->authenticateAdmin($email, $password);

        if ($admin) {
            return ['success' => true, 'message' => 'Authentication successful'];
        }

        return ['success' => false, 'message' => 'Authentication failed'];
    }

    public function exportPatientData() {
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
        exit;
    }
}
