-- Migration: add admin_id to tenants and units
-- Safe to re-run (SQLite ignores duplicate ALTER TABLE if wrapped in application logic)

ALTER TABLE tenants ADD COLUMN admin_id INTEGER;
UPDATE tenants SET admin_id = (
    SELECT admin_id FROM properties WHERE properties.id = tenants.property_id
) WHERE admin_id IS NULL;

ALTER TABLE units ADD COLUMN admin_id INTEGER;
UPDATE units SET admin_id = (
    SELECT admin_id FROM properties WHERE properties.id = units.property_id
) WHERE admin_id IS NULL;
