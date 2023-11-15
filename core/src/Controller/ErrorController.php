<?php
namespace PR24\Controller;

class ErrorController {
    public function handleNotFound() {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['message' => 'Not Found']);
    }

    public function handleServerError($e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['message' => 'Internal Server Error', 'error' => $e->getMessage()]);
    }
}
