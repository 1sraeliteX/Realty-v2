-- Create Super Admin Account in Supabase
-- Run this in your Supabase SQL Editor

INSERT INTO admins (
    id, 
    name, 
    email, 
    password, 
    role, 
    created_at, 
    updated_at
) 
VALUES (
    uuid_generate_v4(),
    'Super Admin',
    'superadmin@cornerstone.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'super_admin',
    NOW(),
    NOW()
);

-- Also create a regular admin for testing
INSERT INTO admins (
    id, 
    name, 
    email, 
    password, 
    role, 
    created_at, 
    updated_at
) 
VALUES (
    uuid_generate_v4(),
    'Test Admin',
    'admin@cornerstone.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin',
    NOW(),
    NOW()
);

-- Verify the accounts were created
SELECT id, name, email, role, created_at FROM admins WHERE deleted_at IS NULL ORDER BY created_at DESC;
