<?php
namespace PR24\Controller;

use PR24\Model\DoctorModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class DoctorController {
    protected $doctorModel;

    public function __construct(DoctorModel $doctorModel) {
        $this->doctorModel = $doctorModel;
    }

    public function authenticate() {
        $credentials = json_decode(file_get_contents('php://input'), true);

        if ($this->doctorModel->validateCredentials($credentials['email'], $credentials['password'])) {
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600;  // Token valid for 1 hour
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'email' => $credentials['email']
            ];

            $jwt = JWT::encode($payload, 'your_secret_key', 'HS256');  // Replace 'your_secret_key' with your actual secret key

            return ['status' => 'success', 'token' => $jwt];
        } else {
            return ['status' => 'error', 'message' => 'Invalid credentials'];
        }
    }

    public function register() {

        $requestBody = json_decode(file_get_contents('php://input'), true);

        // Extract data from the decoded JSON
        $registrationData = [
            'anrede' => $requestBody['anrede'] ?? null,
            'titel' => $requestBody['titel'] ?? null,
            'vorname' => $requestBody['vorname'] ?? null,
            'nachname' => $requestBody['nachname'] ?? null,
            'email' => $requestBody['email'] ?? null,
            'arbeitsstelle_name' => $requestBody['arbeitsstelle_name'] ?? null,
            'arbeitsstelle_adresse' => $requestBody['arbeitsstelle_adresse'] ?? null,
            'arbeitsstelle_stadt' => $requestBody['arbeitsstelle_stadt'] ?? null,
            'arbeitsstelle_plz' => $requestBody['arbeitsstelle_plz'] ?? null,
            'arbeitsstelle_land' => $requestBody['arbeitsstelle_land'] ?? null,
            'taetigkeitsbereich' => $requestBody['taetigkeitsbereich'] ?? null,
            'taetigkeitsbereich_sonstiges' => $requestBody['taetigkeitsbereich_sonstiges'] ?? null
        ];
        
        // Data validation
        if (!$this->validateRegistrationData($registrationData)) {
            return ['status' => 'error', 'message' => 'Invalid registration data'];
        }

        // Create doctor in database
        $doctorId = $this->doctorModel->createDoctor($registrationData);
        if ($doctorId) {
            return ['status' => 'success', 'message' => 'Registration successful', 'doctorId' => $doctorId];
        } else {
            return ['status' => 'error', 'message' => 'Registration failed'];
        }
    }

    public function activateUser($userId) {
        // Check if the doctor is already activated
        $isActivated = $this->doctorModel->isDoctorActivated($userId);
        if ($isActivated === true) {
            return ['status' => 'error', 'message' => 'Doctor already activated'];
        } elseif ($isActivated === null) {
            return ['status' => 'error', 'message' => 'Doctor not found'];
        }
    
        // Activate the doctor
        $result = $this->doctorModel->activateDoctor($userId);
        if ($result) {
            return ['status' => 'success', 'message' => 'Doctor activated'];
        } else {
            return ['status' => 'error', 'message' => 'Activation failed'];
        }
    }
    

    private function validateRegistrationData($data) {
        // Basic validation logic
        return filter_var($data['email'], FILTER_VALIDATE_EMAIL) && !empty($data['vorname']) && !empty($data['nachname']);
    }
}
