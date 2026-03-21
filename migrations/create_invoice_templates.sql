-- Create invoice_templates table
-- This table stores reusable invoice templates for admins

CREATE TABLE IF NOT EXISTS invoice_templates (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    admin_id        INT NOT NULL,
    name            VARCHAR(150) NOT NULL,
    description     TEXT NULL,
    default_notes   TEXT NULL,
    default_terms   TEXT NULL,
    is_default      TINYINT(1) NOT NULL DEFAULT 0,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_invoice_templates_admin
        FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert a default template for each admin
INSERT INTO invoice_templates 
    (admin_id, name, description, default_notes, default_terms, is_default)
SELECT 
    id, 
    'Default Template', 
    'Standard invoice template for recurring charges', 
    'Thank you for your business. Payment is due within 30 days.', 
    'All payments are non-refundable unless specified in writing.',
    1
FROM admins 
WHERE deleted_at IS NULL;
