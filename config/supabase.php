<?php

namespace Config;

class SupabaseClient {
    private static $instance = null;
    private $url;
    private $key;
    private $serviceKey;

    private function __construct() {
        // Load configuration directly from .env.supabase
        $configFile = __DIR__ . '/../.env.supabase';
        if (file_exists($configFile)) {
            $lines = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, 'SUPABASE_URL=') === 0) {
                    $this->url = trim(substr($line, 13));
                } elseif (strpos($line, 'SUPABASE_ANON_KEY=') === 0) {
                    $this->key = trim(substr($line, 18));
                } elseif (strpos($line, 'SUPABASE_SERVICE_KEY=') === 0) {
                    $this->serviceKey = trim(substr($line, 21));
                }
            }
        }

        if (!$this->url || !$this->key) {
            throw new \Exception('Supabase configuration missing. Please check your .env.supabase file.');
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Database operations
    public function select($table, $columns = '*', $filters = [], $options = []) {
        try {
            $url = $this->url . "/rest/v1/$table?select=$columns";
            
            // Add filters
            foreach ($filters as $column => $value) {
                if (is_array($value)) {
                    // Handle operators like ['gte' => '2024-01-01']
                    foreach ($value as $op => $val) {
                        $url .= "&$column=$op.$val";
                    }
                } else {
                    $url .= "&$column=eq.$value";
                }
            }
            
            // Add options like order and limit
            if (isset($options['order'])) {
                $url .= "&order=" . $options['order'];
            }
            if (isset($options['limit'])) {
                $url .= "&limit=" . $options['limit'];
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . $this->serviceKey,
                'Authorization: Bearer ' . $this->serviceKey
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                return json_decode($response, true) ?? [];
            } else {
                return [];
            }
        } catch (\Exception $e) {
            throw new \Exception('Supabase select failed: ' . $e->getMessage());
        }
    }

    public function insert($table, $data) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url . "/rest/v1/$table");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . $this->serviceKey,
                'Authorization: Bearer ' . $this->serviceKey,
                'Content-Type: application/json',
                'Prefer: return=minimal'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 201) {
                // Return the ID if possible
                $responseData = json_decode($response, true);
                return $responseData[0]['id'] ?? null;
            } else {
                throw new \Exception("Insert failed with HTTP $httpCode: $response");
            }
        } catch (\Exception $e) {
            throw new \Exception('Supabase insert failed: ' . $e->getMessage());
        }
    }

    public function update($table, $data, $filters = []) {
        try {
            $url = $this->url . "/rest/v1/$table";
            
            // Add filters to URL
            $filterStrings = [];
            foreach ($filters as $column => $value) {
                $filterStrings[] = "$column=eq.$value";
            }
            if (!empty($filterStrings)) {
                $url .= "?" . implode('&', $filterStrings);
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . $this->serviceKey,
                'Authorization: Bearer ' . $this->serviceKey,
                'Content-Type: application/json',
                'Prefer: return=minimal'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 204 || $httpCode === 200) {
                return true;
            } else {
                throw new \Exception("Update failed with HTTP $httpCode: $response");
            }
        } catch (\Exception $e) {
            throw new \Exception('Supabase update failed: ' . $e->getMessage());
        }
    }

    public function delete($table, $filters = []) {
        try {
            $url = $this->url . "/rest/v1/$table";
            
            // Add filters to URL
            $filterStrings = [];
            foreach ($filters as $column => $value) {
                $filterStrings[] = "$column=eq.$value";
            }
            if (!empty($filterStrings)) {
                $url .= "?" . implode('&', $filterStrings);
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . $this->serviceKey,
                'Authorization: Bearer ' . $this->serviceKey
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 204 || $httpCode === 200) {
                return true;
            } else {
                throw new \Exception("Delete failed with HTTP $httpCode: $response");
            }
        } catch (\Exception $e) {
            throw new \Exception('Supabase delete failed: ' . $e->getMessage());
        }
    }
}
