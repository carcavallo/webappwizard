<?php
namespace PR24\Dependencies;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class JWTMiddleware {
    public static function validateToken($token) {
        try {
            $key = "4BPGK7keKm";
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return ['status' => 'success', 'data' => $decoded->data];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
