<?php

namespace Config;

class Config {
    private static $instance = null;
    private $data = [];

    private function __construct() {
        // Load .env file if it exists
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') === false) continue;
                [$key, $value] = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }

        $this->data = [
            'database' => [
                'host'        => $_ENV['DB_HOST'] ?? 'localhost',
                'name'        => $_ENV['DB_NAME'] ?? 'real_estate_db',
                'user'        => $_ENV['DB_USER'] ?? 'real_estate_db_user',
                'password'    => $_ENV['DB_PASSWORD'] ?? '',
                'use_supabase'=> false,
                'supabase_url'=> '',
                'supabase_key'=> '',
            ],
            'jwt' => [
                'secret' => $_ENV['JWT_SECRET'] ?? '',
                'expire' => (int)($_ENV['JWT_EXPIRE'] ?? 86400),
            ],
            'app' => [
                'url'   => $_ENV['APP_URL'] ?? 'http://localhost:8080',
                'env'   => $_ENV['APP_ENV'] ?? 'production',
                'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
            ],
            'mail' => [
                'host'       => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
                'port'       => (int)($_ENV['MAIL_PORT'] ?? 587),
                'username'   => $_ENV['MAIL_USERNAME'] ?? '',
                'password'   => $_ENV['MAIL_PASSWORD'] ?? '',
                'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
            ],
            'upload' => [
                'max_size'     => 5242880,
                'allowed_types'=> ['jpg', 'jpeg', 'png', 'pdf'],
            ],
        ];
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
