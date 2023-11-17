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
        $patientData = $this->preparePatientData($request);

        if (!$this->validatePatientData($patientData)) {
            return ['status' => 'error', 'message' => 'Invalid patient data'];
        }

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

    public function readPatient($patientId) {
        $patientData = $this->patientModel->getPatientById($patientId);
        if ($patientData) {
            return ['status' => 'success', 'patientData' => $patientData];
        } else {
            return ['status' => 'error', 'message' => 'Patient not found'];
        }
    }

    public function updatePatient($patientId) {
        $request = json_decode(file_get_contents('php://input'), true);
        $patientData = $this->preparePatientData($request);

        if (!$this->validatePatientData($patientData)) {
            return ['status' => 'error', 'message' => 'Invalid patient data'];
        }

        if ($this->patientModel->updatePatient($patientId, $patientData)) {
            return ['status' => 'success', 'message' => 'Patient update successful'];
        } else {
            return ['status' => 'error', 'message' => 'Patient update failed'];
        }
    }

    public function deletePatient($patientId) {
        if ($this->patientModel->deletePatient($patientId)) {
            return ['status' => 'success', 'message' => 'Patient deletion successful'];
        } else {
            return ['status' => 'error', 'message' => 'Patient deletion failed'];
        }
    }

    private function validatePatientData($data) {
        return !empty($data['doctor_id']) && !empty($data['geburtsdatum']) && !empty($data['geschlecht']);
    }
}
