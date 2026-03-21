-- Add deleted_at column to invoices table for soft deletes
-- This follows the same pattern as payments, tenants, and properties tables

ALTER TABLE invoices ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
