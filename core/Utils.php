<?php

namespace PR24\Dependencies;

class Utils {
    public static function sendJsonResponse($response, $code = 200) {
        http_response_code($code);
        echo json_encode($response);
        exit();
    }
}