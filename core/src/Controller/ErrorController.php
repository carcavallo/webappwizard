<?php

namespace PR24\Controller;

class ErrorController {
    public function handleNotFound() {
        $response = [
            'message' => 'Not Found'
        ];
        http_response_code(404);
        echo json_encode($response);
    }

    public function handleServerError() {
        $response = [
            'message' => 'Internal Server Error'
        ];
        http_response_code(500);
        echo json_encode($response);
    }
}