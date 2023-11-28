<?php

namespace PR24\Dependencies;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

/**
 * JWTMiddleware handles JWT token validation.
 */
class JWTMiddleware {
    
    /**
     * Validates a JWT token.
     *
     * @param string $token The JWT token to validate.
     * @return array Status of the validation with data or error message.
     */
    public static function validateToken($token) {
        try {
            $key = $_ENV['SECRET_KEY'];
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return ['status' => 'success', 'data' => $decoded->data];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
