<?php

namespace PR24\Controller;

class PatientController {
    public function createPatient($request) {
        $response = [
            'message' => 'Registration successful'
        ];
        echo json_encode($response);
    }
}