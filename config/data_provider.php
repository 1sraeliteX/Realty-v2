<?php

/**
 * Data Provider - Centralized data management
 * Prevents scattering by managing all mock data in one place
 */

class DataProvider {
    private static $data = [];
    private static $initialized = false;
    
    /**
     * Initialize all data providers
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }
        
        self::$data = [
            'user' => self::getUserData(),
            'notifications' => self::getNotificationData(),
            'properties' => self::getPropertyData(),
            'tenants' => self::getTenantData(),
            'payments' => self::getPaymentData(),
            'invoices' => self::getInvoiceData(),
            'maintenance' => self::getMaintenanceData(),
            'reports' => self::getReportData(),
            'documents' => self::getDocumentData(),
            'settings' => self::getSettingsData(),
            'finance' => self::getFinanceData(),
            'dashboard_stats' => self::getDashboardStatsData(),
            'recent_properties' => self::getRecentPropertiesData(),
            'activities' => self::getActivitiesData(),
            'maintenance_requests' => self::getMaintenanceRequestsData(),
            'new_applications' => self::getNewApplicationsData(),
            'unit' => self::getUnitData(),
            'tenant' => self::getUnitTenantData(),
            'tenantDetails' => self::getTenantDetailsData(),
            'tenantPaymentHistory' => self::getTenantPaymentHistoryData(),
            'tenantDocuments' => self::getTenantDocumentsData(),
            'tenantMaintenanceRequests' => self::getTenantMaintenanceRequestsData(),
            'amenities' => self::getAmenitiesData(),
            'maintenanceHistory' => self::getMaintenanceHistoryData()
        ];
        
        self::$initialized = true;
    }
    
    /**
     * Get data by key
     */
    public static function get($key, $default = null) {
        self::init();
        return self::$data[$key] ?? $default;
    }
    
    /**
     * Set data
     */
    public static function set($key, $value) {
        self::init();
        self::$data[$key] = $value;
    }
    
    /**
     * Get all data
     */
    public static function all() {
        self::init();
        return self::$data;
    }
    
    /**
     * User data
     */
    private static function getUserData() {
        return [
            'name' => 'John Doe',
            'email' => 'admin@example.com',
            'avatar' => null,
            'role' => 'Administrator',
            'permissions' => ['all']
        ];
    }
    
    /**
     * Notification data
     */
    private static function getNotificationData() {
        return [
            ['id' => 1, 'type' => 'info', 'message' => 'New tenant application received', 'time' => '5 min ago', 'read' => false],
            ['id' => 2, 'type' => 'warning', 'message' => 'Rent payment overdue for Unit 3A', 'time' => '1 hour ago', 'read' => false],
            ['id' => 3, 'type' => 'success', 'message' => 'Maintenance request completed', 'time' => '2 hours ago', 'read' => true],
            ['id' => 4, 'type' => 'info', 'message' => 'New property listed successfully', 'time' => '3 hours ago', 'read' => true],
            ['id' => 5, 'type' => 'warning', 'message' => 'Lease expiring in 7 days', 'time' => '1 day ago', 'read' => false]
        ];
    }
    
    /**
     * Property data
     */
    private static function getPropertyData() {
        return [
            [
                'id' => 1,
                'name' => 'Sunset Apartments - 1 Room',
                'address' => '123 Main St, City, State 12345',
                'type' => '1 Room Apartment',
                'status' => 'active',
                'units' => 8,
                'occupied_units' => 7,
                'rental_income' => 24000,
                'price_per_unit' => 3000,
                'image' => '/assets/images/property1.jpg'
            ],
            [
                'id' => 2,
                'name' => 'Oak Villa Complex - Self Contained',
                'address' => '456 Oak Ave, City, State 67890',
                'type' => 'Self Contained',
                'status' => 'active',
                'units' => 6,
                'occupied_units' => 5,
                'rental_income' => 30000,
                'price_per_unit' => 5000,
                'image' => '/assets/images/property2.jpg'
            ],
            [
                'id' => 3,
                'name' => 'Garden View - Multi-Unit',
                'address' => '789 Garden Rd, City, State 11111',
                'type' => 'Multi-Unit',
                'status' => 'active',
                'units' => 12,
                'occupied_units' => 10,
                'rental_income' => 96000,
                'price_per_unit' => 8000,
                'image' => '/assets/images/property3.jpg'
            ],
            [
                'id' => 4,
                'name' => 'City Heights - 1 Room',
                'address' => '321 City Blvd, City, State 22222',
                'type' => '1 Room Apartment',
                'status' => 'active',
                'units' => 4,
                'occupied_units' => 4,
                'rental_income' => 12000,
                'price_per_unit' => 3000,
                'image' => '/assets/images/property4.jpg'
            ],
            [
                'id' => 5,
                'name' => 'Park Side - Self Contained',
                'address' => '654 Park St, City, State 33333',
                'type' => 'Self Contained',
                'status' => 'maintenance',
                'units' => 3,
                'occupied_units' => 2,
                'rental_income' => 15000,
                'price_per_unit' => 5000,
                'image' => '/assets/images/property5.jpg'
            ]
        ];
    }
    
    /**
     * Tenant data
     */
    private static function getTenantData() {
        return [
            [
                'id' => 1,
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'phone' => '+1 234-567-8901',
                'property' => 'Sunset Apartments',
                'unit' => 'Unit 3A',
                'status' => 'active',
                'rent_amount' => 1200,
                'lease_end' => '2024-12-31',
                'payment_status' => 'current'
            ],
            [
                'id' => 2,
                'name' => 'Bob Smith',
                'email' => 'bob@example.com',
                'phone' => '+1 234-567-8902',
                'property' => 'Oak Villa Complex',
                'unit' => 'Unit 2B',
                'status' => 'active',
                'rent_amount' => 1500,
                'lease_end' => '2024-11-30',
                'payment_status' => 'overdue'
            ],
            [
                'id' => 3,
                'name' => 'Carol Williams',
                'email' => 'carol@example.com',
                'phone' => '+1 234-567-8903',
                'property' => 'Sunset Apartments',
                'unit' => 'Unit 5C',
                'status' => 'active',
                'rent_amount' => 1000,
                'lease_end' => '2024-10-31',
                'payment_status' => 'current'
            ]
        ];
    }
    
    /**
     * Payment data
     */
    private static function getPaymentData() {
        return [
            [
                'id' => 1,
                'tenant' => 'Alice Johnson',
                'property' => 'Sunset Apartments',
                'unit' => 'Unit 3A',
                'amount' => 1200,
                'date' => '2024-09-01',
                'status' => 'paid',
                'method' => 'bank_transfer',
                'type' => 'rent'
            ],
            [
                'id' => 2,
                'tenant' => 'Bob Smith',
                'property' => 'Oak Villa Complex',
                'unit' => 'Unit 2B',
                'amount' => 1500,
                'date' => '2024-09-01',
                'status' => 'pending',
                'method' => 'credit_card',
                'type' => 'rent'
            ],
            [
                'id' => 3,
                'tenant' => 'Carol Williams',
                'property' => 'Sunset Apartments',
                'unit' => 'Unit 5C',
                'amount' => 1000,
                'date' => '2024-09-01',
                'status' => 'paid',
                'method' => 'bank_transfer',
                'type' => 'rent'
            ]
        ];
    }
    
    /**
     * Invoice data
     */
    private static function getInvoiceData() {
        return [
            [
                'id' => 1,
                'tenant' => 'Alice Johnson',
                'property' => 'Sunset Apartments',
                'unit' => 'Unit 3A',
                'amount' => 1200,
                'due_date' => '2024-10-01',
                'status' => 'paid',
                'items' => ['Monthly Rent - September 2024']
            ],
            [
                'id' => 2,
                'tenant' => 'Bob Smith',
                'property' => 'Oak Villa Complex',
                'unit' => 'Unit 2B',
                'amount' => 1500,
                'due_date' => '2024-10-01',
                'status' => 'overdue',
                'items' => ['Monthly Rent - September 2024']
            ]
        ];
    }
    
    /**
     * Maintenance data
     */
    private static function getMaintenanceData() {
        return [
            [
                'id' => 1,
                'property' => 'Sunset Apartments',
                'unit' => 'Unit 3A',
                'issue' => 'Leaking faucet',
                'priority' => 'medium',
                'status' => 'in_progress',
                'reported_date' => '2024-09-15',
                'assigned_to' => 'Maintenance Team A'
            ],
            [
                'id' => 2,
                'property' => 'Oak Villa Complex',
                'unit' => 'Unit 2B',
                'issue' => 'Broken window',
                'priority' => 'high',
                'status' => 'pending',
                'reported_date' => '2024-09-16',
                'assigned_to' => null
            ]
        ];
    }
    
    /**
     * Report data
     */
    private static function getReportData() {
        return [
            'total_properties' => 3,
            'total_units' => 44,
            'occupied_units' => 37,
            'occupancy_rate' => 84.1,
            'total_revenue' => 74000,
            'monthly_revenue' => 7400,
            'overdue_payments' => 1,
            'pending_maintenance' => 1
        ];
    }
    
    /**
     * Settings data
     */
    private static function getSettingsData() {
        return [
            'site_name' => 'Cornerstone Realty',
            'site_description' => 'Professional Property Management',
            'admin_email' => 'admin@cornerstone.com',
            'currency' => 'USD',
            'timezone' => 'America/New_York',
            'date_format' => 'Y-m-d',
            'theme' => 'light',
            'notifications_enabled' => true
        ];
    }
    
    /**
     * Document data
     */
    private static function getDocumentData() {
        return [
            [
                'id' => 1,
                'file_name' => 'Lease_Agreement_Unit_101.pdf',
                'original_name' => 'Lease Agreement - Unit 101.pdf',
                'file_path' => '/storage/uploads/documents/Lease_Agreement_Unit_101.pdf',
                'file_size' => 2546576, // 2.4 MB
                'mime_type' => 'application/pdf',
                'category' => 'lease',
                'property_id' => 1,
                'property_name' => 'Sunset Apartments',
                'tenant_id' => 1,
                'tenant_name' => 'Alice Johnson',
                'unit' => 'Unit 3A',
                'description' => 'Standard lease agreement for residential property',
                'uploaded_by' => 'admin',
                'created_at' => '2024-03-01 10:30:00',
                'updated_at' => '2024-03-01 10:30:00'
            ],
            [
                'id' => 2,
                'file_name' => 'Property_Inspection_Report.pdf',
                'original_name' => 'Property Inspection Report.pdf',
                'file_path' => '/storage/uploads/documents/Property_Inspection_Report.pdf',
                'file_size' => 1536789, // 1.5 MB
                'mime_type' => 'application/pdf',
                'category' => 'maintenance',
                'property_id' => 1,
                'property_name' => 'Sunset Apartments',
                'tenant_id' => null,
                'tenant_name' => null,
                'unit' => 'Building',
                'description' => 'Quarterly property inspection report',
                'uploaded_by' => 'admin',
                'created_at' => '2024-03-05 14:20:00',
                'updated_at' => '2024-03-05 14:20:00'
            ],
            [
                'id' => 3,
                'file_name' => 'Insurance_Certificate_2024.pdf',
                'original_name' => 'Insurance Certificate 2024.pdf',
                'file_path' => '/storage/uploads/documents/Insurance_Certificate_2024.pdf',
                'file_size' => 892456, // 892 KB
                'mime_type' => 'application/pdf',
                'category' => 'insurance',
                'property_id' => 2,
                'property_name' => 'Oak Villa Complex',
                'tenant_id' => null,
                'tenant_name' => null,
                'unit' => 'Building',
                'description' => 'Property insurance certificate for 2024',
                'uploaded_by' => 'admin',
                'created_at' => '2024-02-15 09:15:00',
                'updated_at' => '2024-02-15 09:15:00'
            ],
            [
                'id' => 4,
                'file_name' => 'Tenant_Photo_ID.jpg',
                'original_name' => 'Tenant Photo ID.jpg',
                'file_path' => '/storage/uploads/documents/Tenant_Photo_ID.jpg',
                'file_size' => 456789, // 456 KB
                'mime_type' => 'image/jpeg',
                'category' => 'legal',
                'property_id' => 1,
                'property_name' => 'Sunset Apartments',
                'tenant_id' => 2,
                'tenant_name' => 'Bob Smith',
                'unit' => 'Unit 5C',
                'description' => 'Tenant identification document',
                'uploaded_by' => 'admin',
                'created_at' => '2024-02-20 16:45:00',
                'updated_at' => '2024-02-20 16:45:00'
            ],
            [
                'id' => 5,
                'file_name' => 'Maintenance_Request_Photo.jpg',
                'original_name' => 'Maintenance Request Photo.jpg',
                'file_path' => '/storage/uploads/documents/Maintenance_Request_Photo.jpg',
                'file_size' => 789123, // 789 KB
                'mime_type' => 'image/jpeg',
                'category' => 'maintenance',
                'property_id' => 2,
                'property_name' => 'Oak Villa Complex',
                'tenant_id' => 3,
                'tenant_name' => 'Carol Williams',
                'unit' => 'Unit 2B',
                'description' => 'Photo showing leak in bathroom ceiling',
                'uploaded_by' => 'tenant',
                'created_at' => '2024-03-10 11:30:00',
                'updated_at' => '2024-03-10 11:30:00'
            ],
            [
                'id' => 6,
                'file_name' => 'Building_Permits.pdf',
                'original_name' => 'Building Permits.pdf',
                'file_path' => '/storage/uploads/documents/Building_Permits.pdf',
                'file_size' => 3456789, // 3.3 MB
                'mime_type' => 'application/pdf',
                'category' => 'legal',
                'property_id' => 3,
                'property_name' => 'Downtown Office Building',
                'tenant_id' => null,
                'tenant_name' => null,
                'unit' => 'Building',
                'description' => 'Original building permits and certificates',
                'uploaded_by' => 'admin',
                'created_at' => '2024-01-10 13:20:00',
                'updated_at' => '2024-01-10 13:20:00'
            ]
        ];
    }
    
    /**
     * Finance data
     */
    private static function getFinanceData() {
        return [
            'stats' => [
                'monthly_revenue' => 25000,
                'monthly_expenses' => 8500,
                'net_profit' => 16500,
                'pending_payments_count' => 8,
                'pending_payments_total' => 12000,
                'overdue_payments_count' => 3,
                'overdue_payments_total' => 4500,
                'yearly_revenue' => 275000
            ],
            'recent_transactions' => [
                [
                    'type' => 'payment',
                    'description' => 'Rent payment from Alice Johnson',
                    'amount' => 1200,
                    'date' => '2024-03-15',
                    'status' => 'paid',
                    'method' => 'bank_transfer'
                ],
                [
                    'type' => 'expense',
                    'description' => 'Maintenance - Plumbing repair',
                    'amount' => -350,
                    'date' => '2024-03-14',
                    'status' => 'paid',
                    'method' => 'maintenance'
                ],
                [
                    'type' => 'payment',
                    'description' => 'Rent payment from Bob Smith',
                    'amount' => 1500,
                    'date' => '2024-03-14',
                    'status' => 'paid',
                    'method' => 'credit_card'
                ],
                [
                    'type' => 'expense',
                    'description' => 'Insurance premium',
                    'amount' => -1200,
                    'date' => '2024-03-13',
                    'status' => 'paid',
                    'method' => 'insurance'
                ],
                [
                    'type' => 'payment',
                    'description' => 'Rent payment from Carol Williams',
                    'amount' => 1000,
                    'date' => '2024-03-13',
                    'status' => 'pending',
                    'method' => 'bank_transfer'
                ]
            ],
            'revenue_data' => [
                '2023-04' => 22000,
                '2023-05' => 23500,
                '2023-06' => 24100,
                '2023-07' => 23800,
                '2023-08' => 24500,
                '2023-09' => 25200,
                '2023-10' => 24900,
                '2023-11' => 25600,
                '2023-12' => 26300,
                '2024-01' => 24800,
                '2024-02' => 25400,
                '2024-03' => 25000
            ],
            'expense_data' => [
                '2023-04' => 7200,
                '2023-05' => 7800,
                '2023-06' => 8100,
                '2023-07' => 7900,
                '2023-08' => 8200,
                '2023-09' => 8500,
                '2023-10' => 8300,
                '2023-11' => 8600,
                '2023-12' => 8900,
                '2024-01' => 8100,
                '2024-02' => 8400,
                '2024-03' => 8500
            ],
            'upcoming_payments' => [
                [
                    'id' => 1,
                    'tenant_name' => 'David Brown',
                    'property' => 'Sunset Apartments',
                    'unit' => 'Unit 2A',
                    'amount' => 1200,
                    'due_date' => '2024-03-20',
                    'status' => 'pending'
                ],
                [
                    'id' => 2,
                    'tenant_name' => 'Emma Davis',
                    'property' => 'Oak Villa Complex',
                    'unit' => 'Unit 1B',
                    'amount' => 1500,
                    'due_date' => '2024-03-22',
                    'status' => 'pending'
                ],
                [
                    'id' => 3,
                    'tenant_name' => 'Frank Miller',
                    'property' => 'Garden View',
                    'unit' => 'Unit 4C',
                    'amount' => 1800,
                    'due_date' => '2024-03-25',
                    'status' => 'pending'
                ],
                [
                    'id' => 4,
                    'tenant_name' => 'Grace Wilson',
                    'property' => 'City Heights',
                    'unit' => 'Unit 3A',
                    'amount' => 1000,
                    'due_date' => '2024-03-28',
                    'status' => 'pending'
                ],
                [
                    'id' => 5,
                    'tenant_name' => 'Henry Taylor',
                    'property' => 'Park Side',
                    'unit' => 'Unit 1B',
                    'amount' => 1300,
                    'due_date' => '2024-03-30',
                    'status' => 'pending'
                ]
            ],
            'overdue_payments' => [
                [
                    'id' => 6,
                    'tenant_name' => 'Iris Anderson',
                    'property' => 'Sunset Apartments',
                    'unit' => 'Unit 5B',
                    'amount' => 1200,
                    'due_date' => '2024-03-05',
                    'status' => 'overdue',
                    'days_overdue' => 10
                ],
                [
                    'id' => 7,
                    'tenant_name' => 'Jack Thomas',
                    'property' => 'Oak Villa Complex',
                    'unit' => 'Unit 3A',
                    'amount' => 1500,
                    'due_date' => '2024-03-01',
                    'status' => 'overdue',
                    'days_overdue' => 14
                ],
                [
                    'id' => 8,
                    'tenant_name' => 'Kate Jackson',
                    'property' => 'Garden View',
                    'unit' => 'Unit 2B',
                    'amount' => 1800,
                    'due_date' => '2024-03-10',
                    'status' => 'overdue',
                    'days_overdue' => 5
                ]
            ]
        ];
    }
    
    /**
     * Dashboard stats data
     */
    private static function getDashboardStatsData() {
        return [
            'total_properties' => 15,
            'total_units' => 44,
            'active_tenants' => 37,
            'occupancy_rate' => 84.1,
            'monthly_revenue' => 25000,
            'occupied_units' => 37,
            'pending_payments' => 8,
            'maintenance_requests' => 3,
            'new_applications' => 2
        ];
    }
    
    /**
     * Recent properties data
     */
    private static function getRecentPropertiesData() {
        return [
            [
                'id' => 1,
                'name' => 'Sunset Apartments',
                'address' => '123 Main St, City, State 12345',
                'type' => 'Apartment Complex',
                'status' => 'occupied',
                'unit_count' => 8,
                'occupied_units' => 7,
                'image' => '/assets/images/property1.jpg'
            ],
            [
                'id' => 2,
                'name' => 'Oak Villa Complex',
                'address' => '456 Oak Ave, City, State 67890',
                'type' => 'Luxury Villa',
                'status' => 'available',
                'unit_count' => 6,
                'occupied_units' => 5,
                'image' => '/assets/images/property2.jpg'
            ],
            [
                'id' => 3,
                'name' => 'Garden View Properties',
                'address' => '789 Garden Rd, City, State 11111',
                'type' => 'Townhouse',
                'status' => 'occupied',
                'unit_count' => 12,
                'occupied_units' => 10,
                'image' => '/assets/images/property3.jpg'
            ]
        ];
    }
    
    /**
     * Activities data
     */
    private static function getActivitiesData() {
        return [
            [
                'id' => 1,
                'action' => 'payment',
                'description' => 'Rent payment received from Alice Johnson',
                'property_name' => 'Sunset Apartments',
                'created_at' => '2024-03-15 10:30:00'
            ],
            [
                'id' => 2,
                'action' => 'maintenance',
                'description' => 'Maintenance request completed for Unit 2B',
                'property_name' => 'Oak Villa Complex',
                'created_at' => '2024-03-15 09:15:00'
            ],
            [
                'id' => 3,
                'action' => 'tenant',
                'description' => 'New tenant application received',
                'property_name' => 'Garden View Properties',
                'created_at' => '2024-03-14 16:45:00'
            ],
            [
                'id' => 4,
                'action' => 'create',
                'description' => 'New property listed successfully',
                'property_name' => 'City Heights Complex',
                'created_at' => '2024-03-14 14:20:00'
            ]
        ];
    }
    
    /**
     * Maintenance requests data
     */
    private static function getMaintenanceRequestsData() {
        return [
            [
                'id' => 1,
                'property_name' => 'Sunset Apartments',
                'unit' => 'Unit 5A',
                'issue' => 'HVAC Repair',
                'priority' => 'urgent',
                'status' => 'pending',
                'created_at' => '2024-03-15 08:00:00'
            ],
            [
                'id' => 2,
                'property_name' => 'Downtown Plaza',
                'unit' => 'Unit 2B',
                'issue' => 'Plumbing Issue',
                'priority' => 'medium',
                'status' => 'pending',
                'created_at' => '2024-03-15 06:30:00'
            ]
        ];
    }
    
    /**
     * New applications data
     */
    private static function getNewApplicationsData() {
        return [
            [
                'id' => 1,
                'name' => 'Sarah Johnson',
                'property_name' => 'Sunset Apartments',
                'unit' => 'Unit 3C',
                'status' => 'pending',
                'created_at' => '2024-03-15 11:30:00'
            ],
            [
                'id' => 2,
                'name' => 'Mike Chen',
                'property_name' => 'Riverside Complex',
                'unit' => 'Unit 1A',
                'status' => 'pending',
                'created_at' => '2024-03-14 15:45:00'
            ]
        ];
    }
    
    /**
     * Unit data
     */
    private static function getUnitData() {
        return [
            'id' => 1,
            'unit_number' => '101',
            'type' => '1BR',
            'status' => 'occupied',
            'sqft' => 650,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'rent' => 1200,
            'security_deposit' => 2400,
            'property_id' => 1,
            'property_name' => 'Sunset Apartments',
            'floor' => 1,
            'section' => 'A',
            'parking_space' => 'P-101',
            'storage_unit' => 'S-101',
            'created_at' => '2023-01-10',
            'last_updated' => '2024-01-08'
        ];
    }
    
    /**
     * Unit tenant data
     */
    private static function getUnitTenantData() {
        return [
            'id' => 1,
            'name' => 'John Smith',
            'email' => 'john.smith@email.com',
            'phone' => '(555) 123-4567',
            'lease_start' => '2023-01-15',
            'lease_end' => '2024-01-14',
            'monthly_rent' => 1200
        ];
    }
    
    /**
     * Amenities data
     */
    private static function getAmenitiesData() {
        return [
            'Air Conditioning',
            'Heating',
            'Hardwood Floors',
            'Granite Countertops',
            'Stainless Steel Appliances',
            'In-Unit Laundry',
            'Balcony',
            'Walk-in Closet'
        ];
    }
    
    /**
     * Maintenance history data
     */
    private static function getMaintenanceHistoryData() {
        return [
            ['id' => 1, 'title' => 'AC Filter Replacement', 'date' => '2023-12-01', 'cost' => 50, 'status' => 'completed'],
            ['id' => 2, 'title' => 'Plumbing Repair', 'date' => '2023-10-15', 'cost' => 200, 'status' => 'completed'],
            ['id' => 3, 'title' => 'Window Repair', 'date' => '2023-08-20', 'cost' => 150, 'status' => 'completed'],
        ];
    }
    
    /**
     * Tenant details data
     */
    private static function getTenantDetailsData() {
        return [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@email.com',
            'phone' => '(555) 123-4567',
            'date_of_birth' => '1985-06-15',
            'ssn' => '***-**-6789',
            'emergency_contact' => 'Jane Smith',
            'emergency_phone' => '(555) 987-6543',
            'status' => 'active',
            'move_in_date' => '2023-01-15',
            'lease_start' => '2023-01-15',
            'lease_end' => '2024-01-14',
            'monthly_rent' => 1200,
            'security_deposit' => 2400,
            'property_name' => 'Sunset Apartments',
            'unit_number' => '101',
            'property_id' => 1,
            'unit_id' => 1,
            'created_at' => '2023-01-10',
            'last_updated' => '2024-01-08'
        ];
    }
    
    /**
     * Tenant payment history data
     */
    private static function getTenantPaymentHistoryData() {
        return [
            ['id' => 1, 'date' => '2024-01-01', 'amount' => 1200, 'type' => 'rent', 'status' => 'paid', 'method' => 'bank_transfer'],
            ['id' => 2, 'date' => '2023-12-01', 'amount' => 1200, 'type' => 'rent', 'status' => 'paid', 'method' => 'bank_transfer'],
            ['id' => 3, 'date' => '2023-11-01', 'amount' => 1200, 'type' => 'rent', 'status' => 'paid', 'method' => 'bank_transfer'],
            ['id' => 4, 'date' => '2023-10-01', 'amount' => 1200, 'type' => 'rent', 'status' => 'paid', 'method' => 'bank_transfer'],
        ];
    }
    
    /**
     * Tenant documents data
     */
    private static function getTenantDocumentsData() {
        return [
            ['id' => 1, 'name' => 'Lease Agreement', 'type' => 'lease', 'upload_date' => '2023-01-10', 'size' => '2.4 MB'],
            ['id' => 2, 'name' => 'ID Verification', 'type' => 'identification', 'upload_date' => '2023-01-10', 'size' => '1.2 MB'],
            ['id' => 3, 'name' => 'Background Check', 'type' => 'background', 'upload_date' => '2023-01-11', 'size' => '856 KB'],
        ];
    }
    
    /**
     * Tenant maintenance requests data
     */
    private static function getTenantMaintenanceRequestsData() {
        return [
            ['id' => 1, 'title' => 'Leaky Faucet', 'status' => 'resolved', 'priority' => 'medium', 'created_at' => '2023-12-15', 'resolved_at' => '2023-12-18'],
            ['id' => 2, 'title' => 'AC Not Working', 'status' => 'pending', 'priority' => 'high', 'created_at' => '2024-01-08', 'resolved_at' => null],
        ];
    }
}
