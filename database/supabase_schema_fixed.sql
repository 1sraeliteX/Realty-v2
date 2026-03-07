-- Real Estate Management System Database Schema for Supabase (PostgreSQL)
-- Run this in Supabase SQL Editor

-- Enable necessary extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ----------------------------
-- Table structure for admins
-- ----------------------------
CREATE TABLE IF NOT EXISTS admins (
  id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  business_name VARCHAR(255),
  phone VARCHAR(50),
  role TEXT DEFAULT 'admin' CHECK (role IN ('admin', 'super_admin')),
  email_verified_at TIMESTAMP WITH TIME ZONE,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  deleted_at TIMESTAMP WITH TIME ZONE
);

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
CREATE TABLE IF NOT EXISTS sessions (
  id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
  admin_id UUID NOT NULL REFERENCES admins(id) ON DELETE CASCADE,
  token VARCHAR(255) NOT NULL UNIQUE,
  expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
  ip_address INET,
  user_agent TEXT,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ----------------------------
-- Table structure for properties
-- ----------------------------
CREATE TABLE IF NOT EXISTS properties (
  id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
  admin_id UUID NOT NULL REFERENCES admins(id) ON DELETE CASCADE,
  name VARCHAR(255) NOT NULL,
  address TEXT NOT NULL,
  type TEXT DEFAULT 'residential' CHECK (type IN ('residential', 'commercial', 'mixed')),
  category VARCHAR(100),
  description TEXT,
  year_built INTEGER,
  bedrooms INTEGER,
  bathrooms INTEGER,
  kitchens INTEGER DEFAULT 1,
  parking INTEGER DEFAULT 0,
  rent_price DECIMAL(10,2),
  status TEXT DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'maintenance')),
  amenities JSONB,
  images JSONB,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  deleted_at TIMESTAMP WITH TIME ZONE
);

-- ----------------------------
-- Table structure for units
-- ----------------------------
CREATE TABLE IF NOT EXISTS units (
  id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
  property_id UUID NOT NULL REFERENCES properties(id) ON DELETE CASCADE,
  unit_number VARCHAR(50) NOT NULL,
  unit_type TEXT DEFAULT 'studio' CHECK (unit_type IN ('studio', '1br', '2br', '3br', '4br', 'penthouse', 'office', 'retail', 'warehouse')),
  floor INTEGER,
  size DECIMAL(8,2),
  rent_price DECIMAL(10,2),
  status TEXT DEFAULT 'available' CHECK (status IN ('available', 'occupied', 'maintenance', 'reserved')),
  amenities JSONB,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  deleted_at TIMESTAMP WITH TIME ZONE,
  UNIQUE(property_id, unit_number)
);

-- ----------------------------
-- Table structure for tenants
-- ----------------------------
CREATE TABLE IF NOT EXISTS tenants (
  id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
  admin_id UUID NOT NULL REFERENCES admins(id) ON DELETE CASCADE,
  unit_id UUID NOT NULL REFERENCES units(id) ON DELETE CASCADE,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255),
  phone VARCHAR(50) NOT NULL,
  next_of_kin VARCHAR(255),
  next_of_kin_phone VARCHAR(50),
  number_of_occupants INTEGER DEFAULT 1,
  rent_start_date DATE NOT NULL,
  rent_expiry_date DATE NOT NULL,
  payment_status TEXT DEFAULT 'pending' CHECK (payment_status IN ('paid', 'pending', 'overdue')),
  id_document VARCHAR(255),
  notes TEXT,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  deleted_at TIMESTAMP WITH TIME ZONE,
  UNIQUE(unit_id)
);

-- ----------------------------
-- Table structure for payments
-- ----------------------------
CREATE TABLE IF NOT EXISTS payments (
  id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
  admin_id UUID NOT NULL REFERENCES admins(id) ON DELETE CASCADE,
  tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
  property_id UUID NOT NULL REFERENCES properties(id) ON DELETE CASCADE,
  amount DECIMAL(10,2) NOT NULL,
  payment_type TEXT DEFAULT 'rent' CHECK (payment_type IN ('rent', 'deposit', 'utility', 'maintenance', 'other')),
  payment_method TEXT DEFAULT 'cash' CHECK (payment_method IN ('cash', 'bank_transfer', 'check', 'online', 'mobile')),
  due_date DATE NOT NULL,
  payment_date DATE,
  status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'paid', 'overdue', 'cancelled')),
  receipt_reference VARCHAR(255),
  notes TEXT,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ----------------------------
-- Table structure for invoices
-- ----------------------------
CREATE TABLE IF NOT EXISTS invoices (
  id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
  admin_id UUID NOT NULL REFERENCES admins(id) ON DELETE CASCADE,
  tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
  property_id UUID NOT NULL REFERENCES properties(id) ON DELETE CASCADE,
  invoice_number VARCHAR(50) NOT NULL UNIQUE,
  amount DECIMAL(10,2) NOT NULL,
  due_date DATE NOT NULL,
  status TEXT DEFAULT 'draft' CHECK (status IN ('draft', 'sent', 'paid', 'overdue', 'cancelled')),
  items JSONB,
  notes TEXT,
  reminder_sent BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ----------------------------
-- Table structure for activities
-- ----------------------------
CREATE TABLE IF NOT EXISTS activities (
  id UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
  admin_id UUID NOT NULL REFERENCES admins(id) ON DELETE CASCADE,
  action VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  entity_type VARCHAR(50),
  entity_id UUID,
  metadata JSONB,
  ip_address INET,
  user_agent TEXT,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ----------------------------
-- Create indexes for better performance
-- ----------------------------
CREATE INDEX IF NOT EXISTS idx_admins_email ON admins(email);
CREATE INDEX IF NOT EXISTS idx_admins_role ON admins(role);
CREATE INDEX IF NOT EXISTS idx_admins_deleted_at ON admins(deleted_at);

CREATE INDEX IF NOT EXISTS idx_sessions_admin_id ON sessions(admin_id);
CREATE INDEX IF NOT EXISTS idx_sessions_token ON sessions(token);
CREATE INDEX IF NOT EXISTS idx_sessions_expires_at ON sessions(expires_at);

CREATE INDEX IF NOT EXISTS idx_properties_admin_id ON properties(admin_id);
CREATE INDEX IF NOT EXISTS idx_properties_type ON properties(type);
CREATE INDEX IF NOT EXISTS idx_properties_status ON properties(status);
CREATE INDEX IF NOT EXISTS idx_properties_deleted_at ON properties(deleted_at);

CREATE INDEX IF NOT EXISTS idx_units_property_id ON units(property_id);
CREATE INDEX IF NOT EXISTS idx_units_type ON units(unit_type);
CREATE INDEX IF NOT EXISTS idx_units_status ON units(status);
CREATE INDEX IF NOT EXISTS idx_units_deleted_at ON units(deleted_at);

CREATE INDEX IF NOT EXISTS idx_tenants_admin_id ON tenants(admin_id);
CREATE INDEX IF NOT EXISTS idx_tenants_unit_id ON tenants(unit_id);
CREATE INDEX IF NOT EXISTS idx_tenants_payment_status ON tenants(payment_status);
CREATE INDEX IF NOT EXISTS idx_tenants_deleted_at ON tenants(deleted_at);

CREATE INDEX IF NOT EXISTS idx_payments_admin_id ON payments(admin_id);
CREATE INDEX IF NOT EXISTS idx_payments_tenant_id ON payments(tenant_id);
CREATE INDEX IF NOT EXISTS idx_payments_property_id ON payments(property_id);
CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status);
CREATE INDEX IF NOT EXISTS idx_payments_due_date ON payments(due_date);

CREATE INDEX IF NOT EXISTS idx_invoices_admin_id ON invoices(admin_id);
CREATE INDEX IF NOT EXISTS idx_invoices_tenant_id ON invoices(tenant_id);
CREATE INDEX IF NOT EXISTS idx_invoices_property_id ON invoices(property_id);
CREATE INDEX IF NOT EXISTS idx_invoices_status ON invoices(status);
CREATE INDEX IF NOT EXISTS idx_invoices_due_date ON invoices(due_date);

CREATE INDEX IF NOT EXISTS idx_activities_admin_id ON activities(admin_id);
CREATE INDEX IF NOT EXISTS idx_activities_action ON activities(action);
CREATE INDEX IF NOT EXISTS idx_activities_entity_type ON activities(entity_type);
CREATE INDEX IF NOT EXISTS idx_activities_created_at ON activities(created_at);

-- ----------------------------
-- Create storage bucket for property images
-- ----------------------------
INSERT INTO storage.buckets (id, name, public) 
VALUES ('property-images', 'property-images', true) 
ON CONFLICT (id) DO NOTHING;

-- ----------------------------
-- Enable Row Level Security (RLS)
-- ----------------------------
ALTER TABLE admins ENABLE ROW LEVEL SECURITY;
ALTER TABLE properties ENABLE ROW LEVEL SECURITY;
ALTER TABLE units ENABLE ROW LEVEL SECURITY;
ALTER TABLE tenants ENABLE ROW LEVEL SECURITY;
ALTER TABLE payments ENABLE ROW LEVEL SECURITY;
ALTER TABLE invoices ENABLE ROW LEVEL SECURITY;
ALTER TABLE activities ENABLE ROW LEVEL SECURITY;

-- ----------------------------
-- Create RLS policies
-- ----------------------------
-- Admins can only see their own data
CREATE POLICY "Admins can view own data" ON admins
FOR SELECT USING (auth.uid()::text = id::text);

CREATE POLICY "Admins can update own data" ON admins
FOR UPDATE USING (auth.uid()::text = id::text);

CREATE POLICY "Admins can insert own data" ON admins
FOR INSERT WITH CHECK (auth.uid()::text = id::text);

-- Properties - admins can only manage their own properties
CREATE POLICY "Admins can view own properties" ON properties
FOR SELECT USING (auth.uid()::text = admin_id::text);

CREATE POLICY "Admins can insert own properties" ON properties
FOR INSERT WITH CHECK (auth.uid()::text = admin_id::text);

CREATE POLICY "Admins can update own properties" ON properties
FOR UPDATE USING (auth.uid()::text = admin_id::text);

CREATE POLICY "Admins can delete own properties" ON properties
FOR DELETE USING (auth.uid()::text = admin_id::text);

-- Similar policies for other tables
CREATE POLICY "Admins can view own units" ON units
FOR SELECT USING (
  EXISTS (
    SELECT 1 FROM properties 
    WHERE properties.id = units.property_id 
    AND properties.admin_id::text = auth.uid()::text
  )
);

CREATE POLICY "Admins can manage own units" ON units
FOR ALL USING (
  EXISTS (
    SELECT 1 FROM properties 
    WHERE properties.id = units.property_id 
    AND properties.admin_id::text = auth.uid()::text
  )
);

-- Enable real-time subscriptions
ALTER PUBLICATION supabase_realtime ADD TABLE properties;
ALTER PUBLICATION supabase_realtime ADD TABLE units;
ALTER PUBLICATION supabase_realtime ADD TABLE tenants;
ALTER PUBLICATION supabase_realtime ADD TABLE payments;
ALTER PUBLICATION supabase_realtime ADD TABLE invoices;
