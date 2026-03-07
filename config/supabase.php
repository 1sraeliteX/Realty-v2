<?php

namespace Config;

use Supabase\Supabase;

class SupabaseClient {
    private static $instance = null;
    private $client;
    private $url;
    private $key;
    private $serviceKey;

    private function __construct() {
        $config = Config::getInstance();
        
        $this->url = $config->get('supabase.url');
        $this->key = $config->get('supabase.anon_key');
        $this->serviceKey = $config->get('supabase.service_key');

        if (!$this->url || !$this->key) {
            throw new \Exception('Supabase configuration missing. Please check your .env file.');
        }

        $this->client = new Supabase($this->url, $this->key);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getClient() {
        return $this->client;
    }

    public function getServiceClient() {
        return new Supabase($this->url, $this->serviceKey);
    }

    // Database operations
    public function select($table, $columns = '*', $filters = []) {
        try {
            $query = $this->client->from($table)->select($columns);
            
            foreach ($filters as $column => $value) {
                $query = $query->eq($column, $value);
            }
            
            $result = $query->execute();
            return $result['data'] ?? [];
        } catch (\Exception $e) {
            if (Config::getInstance()->get('app.debug')) {
                throw new \Exception('Supabase query failed: ' . $e->getMessage());
            } else {
                throw new \Exception('Database query failed.');
            }
        }
    }

    public function insert($table, $data) {
        try {
            $result = $this->client->from($table)->insert($data)->execute();
            return $result['data'][0] ?? null;
        } catch (\Exception $e) {
            if (Config::getInstance()->get('app.debug')) {
                throw new \Exception('Supabase insert failed: ' . $e->getMessage());
            } else {
                throw new \Exception('Database insert failed.');
            }
        }
    }

    public function update($table, $data, $filters = []) {
        try {
            $query = $this->client->from($table)->update($data);
            
            foreach ($filters as $column => $value) {
                $query = $query->eq($column, $value);
            }
            
            $result = $query->execute();
            return $result['data'][0] ?? null;
        } catch (\Exception $e) {
            if (Config::getInstance()->get('app.debug')) {
                throw new \Exception('Supabase update failed: ' . $e->getMessage());
            } else {
                throw new \Exception('Database update failed.');
            }
        }
    }

    public function delete($table, $filters = []) {
        try {
            $query = $this->client->from($table)->delete();
            
            foreach ($filters as $column => $value) {
                $query = $query->eq($column, $value);
            }
            
            $result = $query->execute();
            return $result['data'] ?? [];
        } catch (\Exception $e) {
            if (Config::getInstance()->get('app.debug')) {
                throw new \Exception('Supabase delete failed: ' . $e->getMessage());
            } else {
                throw new \Exception('Database delete failed.');
            }
        }
    }

    // Authentication operations
    public function signUp($email, $password, $metadata = []) {
        try {
            $result = $this->client->auth->signUp([
                'email' => $email,
                'password' => $password,
                'options' => [
                    'data' => $metadata
                ]
            ]);
            
            return $result;
        } catch (\Exception $e) {
            throw new \Exception('Registration failed: ' . $e->getMessage());
        }
    }

    public function signIn($email, $password) {
        try {
            $result = $this->client->auth->signInWithPassword([
                'email' => $email,
                'password' => $password
            ]);
            
            return $result;
        } catch (\Exception $e) {
            throw new \Exception('Login failed: ' . $e->getMessage());
        }
    }

    public function signOut() {
        try {
            $this->client->auth->signOut();
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Logout failed: ' . $e->getMessage());
        }
    }

    public function getUser() {
        try {
            return $this->client->auth->getUser();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getSession() {
        try {
            return $this->client->auth->getSession();
        } catch (\Exception $e) {
            return null;
        }
    }

    // Storage operations
    public function uploadFile($bucket, $path, $file) {
        try {
            $serviceClient = $this->getServiceClient();
            $result = $serviceClient->storage->from($bucket)->upload($path, $file);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception('File upload failed: ' . $e->getMessage());
        }
    }

    public function getPublicUrl($bucket, $path) {
        try {
            $serviceClient = $this->getServiceClient();
            $result = $serviceClient->storage->from($bucket)->getPublicUrl($path);
            return $result['publicUrl'];
        } catch (\Exception $e) {
            throw new \Exception('Failed to get public URL: ' . $e->getMessage());
        }
    }

    public function deleteFile($bucket, $path) {
        try {
            $serviceClient = $this->getServiceClient();
            $result = $serviceClient->storage->from($bucket)->remove([$path]);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception('File deletion failed: ' . $e->getMessage());
        }
    }
}
