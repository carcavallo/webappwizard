<?php

namespace PR24\Dependencies;

/**
 * Utils class provides utility functions.
 */
class Utils {

    /**
     * Sends a JSON response to the client.
     *
     * @param array $response The response data to be sent as JSON.
     * @param int $code The HTTP response code (default is 200).
     */
    public static function sendJsonResponse($response, $code = 200) {
        http_response_code($code);
        echo json_encode($response);
        exit();
    }
}