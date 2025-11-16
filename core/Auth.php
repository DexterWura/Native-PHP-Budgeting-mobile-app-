<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function register($email, $password, $name) {
        // Validate input
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            throw new Exception("Password must be at least " . PASSWORD_MIN_LENGTH . " characters");
        }
        
        // Check if user exists
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            throw new Exception("Email already registered");
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Insert user
        $stmt = $this->conn->prepare(
            "INSERT INTO users (email, password, name, created_at) VALUES (?, ?, ?, NOW())"
        );
        
        if ($stmt->execute([$email, $hashedPassword, $name])) {
            $userId = $this->conn->lastInsertId();
            return $this->generateToken($userId);
        }
        
        throw new Exception("Registration failed");
    }
    
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT id, password, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid credentials");
        }
        
        return $this->generateToken($user['id']);
    }
    
    public function validateToken($token) {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return false;
            }
            
            $payload = json_decode(base64_decode($parts[1]), true);
            
            if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
                return false;
            }
            
            // Verify signature
            $signature = hash_hmac('sha256', $parts[0] . '.' . $parts[1], JWT_SECRET, true);
            $expectedSignature = base64_encode($signature);
            
            if (!hash_equals($expectedSignature, $parts[2])) {
                return false;
            }
            
            return $payload['user_id'];
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function generateToken($userId) {
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode([
            'user_id' => $userId,
            'exp' => time() + JWT_EXPIRATION,
            'iat' => time()
        ]));
        
        $signature = hash_hmac('sha256', $header . '.' . $payload, JWT_SECRET, true);
        $signature = base64_encode($signature);
        
        return [
            'token' => $header . '.' . $payload . '.' . $signature,
            'expires_in' => JWT_EXPIRATION
        ];
    }
    
    public function getCurrentUser($token) {
        $userId = $this->validateToken($token);
        if (!$userId) {
            return null;
        }
        
        $stmt = $this->conn->prepare("SELECT id, email, name, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}

