<?php

namespace Config;

class ConfigSimple {
    private static $instance = null;
    private $data = [];

    private function __construct() {
        // Load environment variables from .env file if it exists
        $this->loadEnvFile();
        
        $this->data = [
            'database' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'name' => $_ENV['DB_NAME'] ?? 'real_estate_db',
                'user' => $_ENV['DB_USER'] ?? 'root',
                'password' => $_ENV['DB_PASSWORD'] ?? ''
            ],
            'jwt' => [
                'secret' => $_ENV['JWT_SECRET'] ?? 'default-secret-key-change-in-production',
                'expire' => $_ENV['JWT_EXPIRE'] ?? 86400
            ],
            'app' => [
                'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
                'env' => $_ENV['APP_ENV'] ?? 'development',
                'debug' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN)
            ],
            'mail' => [
                'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
                'port' => $_ENV['MAIL_PORT'] ?? 587,
                'username' => $_ENV['MAIL_USERNAME'] ?? '',
                'password' => $_ENV['MAIL_PASSWORD'] ?? '',
                'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls'
            ],
            'upload' => [
                'max_size' => $_ENV['UPLOAD_MAX_SIZE'] ?? 5242880,
                'allowed_types' => explode(',', $_ENV['UPLOAD_ALLOWED_TYPES'] ?? 'jpg,jpeg,png,pdf')
            ]
        ];
    }

    private function loadEnvFile() {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '#') === 0) {
                    continue;
                }
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->data;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }

    public function set($key, $value) {
        $keys = explode('.', $key);
        $current = &$this->data;
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        
        $current = $value;
    }
}
