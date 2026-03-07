-- Create test admin accounts
-- Run this to create test users for development

-- Insert regular admin (password: admin123)
INSERT INTO admins (name, email, password, role, created_at, updated_at) 
VALUES (
    'Test Admin',
    'admin@cornerstone.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE email = email;

-- Insert super admin (password: admin123)
INSERT INTO admins (name, email, password, role, created_at, updated_at) 
VALUES (
    'Super Admin',
    'superadmin@cornerstone.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'super_admin',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE email = email;

-- Verify the accounts were created
SELECT id, name, email, role, created_at FROM admins WHERE deleted_at IS NULL ORDER BY created_at DESC;
