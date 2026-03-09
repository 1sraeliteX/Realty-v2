<?php

// Production Configuration
return [
    'database' => [
        'use_supabase' => true, // Use Supabase for production
        'supabase_url' => 'https://ducwcodegciekralkrqd.supabase.co',
        'supabase_key' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImR1Y3djb2RlZ2NpZWtyYWxrcnFkIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3MjgzNjczNiwiZXhwIjoyMDg4NDEyNzM2fQ.VKZUKgEtkrJWhE1UlHzHNm_fIZe4gdrOGYfFyHlQ22Y',
        // Fallback to MySQL if needed
        'host' => 'localhost',
        'name' => 'real_estate_db',
        'user' => 'root',
        'password' => ''
    ],
    'app' => [
        'env' => 'production',
        'debug' => false, // Disable debug in production
        'url' => 'https://your-domain.com', // Update with your domain
        'force_https' => true
    ],
    'security' => [
        'csrf_protection' => true,
        'rate_limiting' => true,
        'max_attempts' => 5,
        'rate_limit_window' => 300, // 5 minutes
        'session_timeout' => 3600, // 1 hour
        'password_policy' => [
            'min_length' => 8,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_numbers' => true,
            'require_symbols' => false
        ]
    ],
    'jwt' => [
        'secret' => 'your-super-secure-jwt-secret-change-this-in-production',
        'expire' => 86400 // 24 hours
    ],
    'upload' => [
        'max_size' => 5242880, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf'],
        'secure_upload' => true
    ],
    'logging' => [
        'enabled' => true,
        'level' => 'error',
        'file' => 'production.log',
        'security_log' => 'security.log'
    ]
];
