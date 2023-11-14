<?php

namespace PR24\Controller;

class UserController {
    public function register($request) {
        $response = [
            'message' => 'Registration successful'
        ];
        echo json_encode($response);
    }

    public function activateUser($userId) {
        $response = [
            'message' => 'User activated'
        ];
        echo json_encode($response);
    }
}
