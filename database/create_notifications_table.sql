-- Create notifications table for Realty-v2
-- This table stores all system notifications for admin users

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    activity_type VARCHAR(50) NOT NULL COMMENT 'Type of activity that triggered notification',
    related_id INT NULL COMMENT 'ID of related record (property, tenant, etc.)',
    related_type VARCHAR(50) NULL COMMENT 'Type of related record',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    
    INDEX idx_admin_unread (admin_id, is_read),
    INDEX idx_admin_created (admin_id, created_at),
    INDEX idx_activity_type (activity_type),
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

-- Insert sample notifications for testing
INSERT INTO notifications (admin_id, title, message, type, activity_type, related_id, related_type) VALUES
(1, 'New Tenant Added', 'John Doe has been added as a new tenant', 'success', 'tenant_created', 1, 'tenant'),
(1, 'Rent Payment Received', 'Monthly rent payment of ₦150,000 received from Jane Smith', 'success', 'payment_received', 1, 'payment'),
(1, 'Maintenance Request', 'New maintenance request reported for Property #123', 'warning', 'maintenance_request', 123, 'property'),
(1, 'Lease Expiring Soon', 'Lease for tenant Mike Johnson expires in 7 days', 'warning', 'lease_expiring', 2, 'lease'),
(1, 'Unit Status Changed', 'Unit #A101 status changed from vacant to occupied', 'info', 'unit_status_change', 101, 'unit');
