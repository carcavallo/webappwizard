<?php
namespace PR24\Controller;

use PR24\Model\DoctorModel;

class DoctorController {
    protected $doctorModel;

    public function __construct(DoctorModel $doctorModel) {
        $this->doctorModel = $doctorModel;
    }

    public function register($request) {

        $registrationData = [
            'anrede' => $request['anrede'] ?? null,
            'titel' => $request['titel'] ?? null,
            'vorname' => $request['vorname'] ?? null,
            'nachname' => $request['nachname'] ?? null,
            'email' => $request['email'] ?? null,
            'arbeitsstelle_name' => $request['arbeitsstelle_name'] ?? null,
            'arbeitsstelle_adresse' => $request['arbeitsstelle_adresse'] ?? null,
            'arbeitsstelle_stadt' => $request['arbeitsstelle_stadt'] ?? null,
            'arbeitsstelle_plz' => $request['arbeitsstelle_plz'] ?? null,
            'arbeitsstelle_land' => $request['arbeitsstelle_land'] ?? null,
            'taetigkeitsbereich' => $request['taetigkeitsbereich'] ?? null,
            'taetigkeitsbereich_sonstiges' => $request['taetigkeitsbereich_sonstiges'] ?? null
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
        // Activate the doctor
        $result = $this->doctorModel->activateDoctor($userId);
        if ($result) {
            return ['status' => 'success', 'message' => 'User activated'];
        } else {
            return ['status' => 'error', 'message' => 'Activation failed'];
        }
    }

    private function validateRegistrationData($data) {
        // Basic validation logic
        return filter_var($data['email'], FILTER_VALIDATE_EMAIL) && !empty($data['vorname']) && !empty($data['nachname']);
    }
}
