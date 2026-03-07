-- Create test super admin account
-- Run this in Supabase SQL Editor to create a test super admin

-- Insert super admin (password: admin123)
INSERT INTO admins (id, name, email, password, role, created_at, updated_at) 
VALUES (
    uuid_generate_v4(),
    'Super Admin',
    'superadmin@cornerstone.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'super_admin',
    NOW(),
    NOW()
) ON CONFLICT (email) DO NOTHING;

-- Insert regular admin for testing (password: admin123)
INSERT INTO admins (id, name, email, password, role, created_at, updated_at) 
VALUES (
    uuid_generate_v4(),
    'Test Admin',
    'admin@cornerstone.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin',
    NOW(),
    NOW()
) ON CONFLICT (email) DO NOTHING;

-- Verify the accounts were created
SELECT id, name, email, role, created_at FROM admins WHERE deleted_at IS NULL ORDER BY created_at DESC;
