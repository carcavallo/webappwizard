<?php
namespace PR24\Controller;

use PR24\Model\PatientModel;

/**
 * PatientController handles actions related to patient management.
 */
class PatientController {
    protected $patientModel;

    /**
     * Constructor for the PatientController class.
     *
     * @param PatientModel $patientModel The model handling patient data.
     */
    public function __construct(PatientModel $patientModel) {
        $this->patientModel = $patientModel;
    }

    /**
     * Creates a new patient record.
     *
     * @return array Result of the patient creation process.
     */
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

    /**
     * Prepares patient data from the request.
     *
     * @param array $request The request data.
     * @return array The prepared patient data.
     */
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

    /**
     * Retrieves patient data based on patient ID.
     *
     * @param int $patientId The ID of the patient.
     * @return array The result of the read operation.
     */    
    public function readPatient($patientId) {
        $patientData = $this->patientModel->getPatientById($patientId);
        if ($patientData) {
            return ['status' => 'success', 'patientData' => $patientData];
        } else {
            return ['status' => 'error', 'message' => 'Patient not found'];
        }
    }

    /**
     * Updates patient data based on patient ID.
     *
     * @param int $patientId The ID of the patient.
     * @return array The result of the update operation.
     */    
    public function updatePatient($patientId) {
        $request = json_decode(file_get_contents('php://input'), true);
        $patientData = $this->preparePatientData($request);

        if ($this->patientModel->updatePatient($patientId, $patientData)) {
            return ['status' => 'success', 'message' => 'Patient update successful'];
        } else {
            return ['status' => 'error', 'message' => 'Patient update failed'];
        }
    }

    /**
     * Deletes a patient record based on patient ID.
     *
     * @param int $patientId The ID of the patient.
     * @return array The result of the delete operation.
     */    
    public function deletePatient($patientId) {
        if ($this->patientModel->deletePatient($patientId)) {
            return ['status' => 'success', 'message' => 'Patient deletion successful'];
        } else {
            return ['status' => 'error', 'message' => 'Patient deletion failed'];
        }
    }

    /**
     * Retrieves all patients for a specific doctor.
     *
     * @param int $userId The ID of the doctor.
     * @return array The list of patients for the doctor.
     */
    public function getPatientsByDoctor($userId) {
        $patients = $this->patientModel->getPatientsByDoctorId($userId);

        if ($patients) {
            return ['status' => 'success', 'data' => $patients];
        } else {
            return ['status' => 'error', 'message' => 'No patients found'];
        }
    }

    public function getBisherigeTherapien($patientId) {
        $result = $this->patientModel->getBisherigeTherapien($patientId);
        if ($result !== false) {
            return ['status' => 'success', 'data' => $result];
        } else {
            return ['status' => 'error', 'message' => 'Failed to retrieve Bisherige Therapien'];
        }
    }

    public function getAktuelleTherapien($patientId) {
        $result = $this->patientModel->getAktuelleTherapien($patientId);
        if ($result !== false) {
            return ['status' => 'success', 'data' => $result];
        } else {
            return ['status' => 'error', 'message' => 'Failed to retrieve Aktuelle Therapien'];
        }
    }

    public function updateBisherigeTherapien($patientId) {
        $request = json_decode(file_get_contents('php://input'), true);
        $lokaleTherapieIds = $request['lokaleTherapie'] ?? [];
        $systemTherapieIds = $request['systemtherapie'] ?? [];

        $lokaleResult = $this->patientModel->addBisherigeTherapie($patientId, $lokaleTherapieIds);
        $systemResult = $this->patientModel->addBisherigeSystemtherapie($patientId, $systemTherapieIds);

        if ($lokaleResult && $systemResult) {
            return ['status' => 'success', 'message' => 'Bisherige Therapie updated successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update Bisherige Therapie'];
        }
    }

    public function updateAktuelleTherapien($patientId) {
        $request = json_decode(file_get_contents('php://input'), true);
        $lokaleTherapieIds = $request['lokaleTherapie'] ?? [];
        $systemTherapieIds = $request['systemtherapie'] ?? [];

        $lokaleResult = $this->patientModel->addAktuelleTherapie($patientId, $lokaleTherapieIds);
        $systemResult = $this->patientModel->addAktuelleSystemtherapie($patientId, $systemTherapieIds);

        if ($lokaleResult && $systemResult) {
            return ['status' => 'success', 'message' => 'Aktuelle Therapie updated successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update Aktuelle Therapie'];
        }
    }

    /**
     * Validates the patient data.
     *
     * @param array $data The patient data to validate.
     * @return bool True if valid, false otherwise.
     */    
    private function validatePatientData($data) {
        return !empty($data['doctor_id']) && !empty($data['geburtsdatum']) && !empty($data['geschlecht']);
    }
}
