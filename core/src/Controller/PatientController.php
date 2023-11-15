<?php
namespace PR24\Controller;

use PR24\Model\PatientModel;

class PatientController {
    protected $patientModel;

    public function __construct(PatientModel $patientModel) {
        $this->patientModel = $patientModel;
    }

    public function createPatient($request) {
        // Extracting patient data from the request
        $patientData = [
            'patient_id' => $request['patient_id'] ?? null,
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

    private function validatePatientData($data) {
        // Implement validation logic
        // Example: check if mandatory fields are present and correctly formatted
        return !empty($data['patient_id']) && !empty($data['doctor_id']) && !empty($data['geburtsdatum']);
    }

    // Implement additional CRUD methods for patient management
}
