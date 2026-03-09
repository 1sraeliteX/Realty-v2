<?php

namespace Config;

class Config {
    private static $instance = null;
    private $data = [];

    private function __construct() {
        // $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        // $dotenv->load();
        
        $this->data = [
            'database' => [
                'host' => 'localhost',
                'name' => 'real_estate_db',
                'user' => 'root',
                'password' => '',
                'use_supabase' => true, // Enable Supabase for production
                'supabase_url' => 'https://ducwcodegciekralkrqd.supabase.co',
                'supabase_key' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImR1Y3djb2RlZ2NpZWtyYWxrcnFkIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3MjgzNjczNiwiZXhwIjoyMDg4NDEyNzM2fQ.VKZUKgEtkrJWhE1UlHzHNm_fIZe4gdrOGYfFyHlQ22Y'
            ],
            'jwt' => [
                'secret' => 'your-secret-key-change-in-production',
                'expire' => 86400
            ],
            'app' => [
                'url' => 'http://localhost',
                'env' => 'development',
                'debug' => true
            ],
            'mail' => [
                'host' => '',
                'port' => 587,
                'username' => '',
                'password' => '',
                'encryption' => 'tls'
            ],
            'upload' => [
                'max_size' => 5242880,
                'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf']
            ]
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
