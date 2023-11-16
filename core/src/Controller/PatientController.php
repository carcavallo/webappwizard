<?php
namespace PR24\Controller;

use PR24\Model\PatientModel;

class PatientController {
    protected $patientModel;

    public function __construct(PatientModel $patientModel) {
        $this->patientModel = $patientModel;
    }

    public function createPatient() {
        $request = json_decode(file_get_contents('php://input'), true);
        // Prepare patient data with generated patient_id
        $patientData = $this->preparePatientData($request);

        // Validate data before proceeding
        if (!$this->validatePatientData($patientData)) {
            return ['status' => 'error', 'message' => 'Invalid patient data'];
        }

        // Create patient in the database
        $patientId = $this->patientModel->createPatient($patientData);
        if ($patientId) {
            return ['status' => 'success', 'message' => 'Patient creation successful', 'patientId' => $patientId];
        } else {
            return ['status' => 'error', 'message' => 'Patient creation failed'];
        }
    }

    private function preparePatientData($request) {
        $birthYear = date('Y', strtotime($request['geburtsdatum'] ?? ''));
        $genderLetter = strtolower(substr($request['geschlecht'] ?? '', 0, 1));
        $randomString = substr(md5(mt_rand()), 0, 6);
        $patientId = $genderLetter . $birthYear . '-' . $randomString;

        $patientData = [
            'patient_id' => $patientId,
            'doctor_id' => $request['doctor_id'] ?? null,
            'geburtsdatum' => $request['geburtsdatum'] ?? null,
            'geschlecht' => $request['geschlecht'] ?? null,
            'ethnie' => $request['ethnie'] ?? null,
            'vermutete_diagnose' => $request['vermutete_diagnose'] ?? null,
            'histopathologische_untersuchung' => $request['histopathologische_untersuchung'] ?? null,
            'histopathologie_ergebnis' => $request['histopathologie_ergebnis'] ?? null,
            'bisherige_lokaltherapie_sonstiges' => $request['bisherige_lokaltherapie_sonstiges'] ?? null,
            'bisherige_systemtherapie_sonstiges' => $request['bisherige_systemtherapie_sonstiges'] ?? null,
            'aktuelle_lokaltherapie_sonstiges' => $request['aktuelle_lokaltherapie_sonstiges'] ?? null,
            'aktuelle_systemtherapie_sonstiges' => $request['aktuelle_systemtherapie_sonstiges'] ?? null,
            'jucken_letzte_24_stunden' => $request['jucken_letzte_24_stunden'] ?? null,
        ];

        return $patientData;
    }

    private function validatePatientData($data) {
        // Basic validation: Ensure mandatory fields are present
        return !empty($data['doctor_id']) && !empty($data['geburtsdatum']) && !empty($data['geschlecht']);
    }

    // Additional CRUD methods for patient management
}
