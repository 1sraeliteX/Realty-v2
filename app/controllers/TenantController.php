<?php

namespace App\Controllers;

class TenantController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Mock data for tenants - in production this would come from database
        $tenants = [
            [
                'id' => 1,
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@email.com',
                'phone' => '(555) 123-4567',
                'property_name' => 'Sunset Apartments',
                'unit_number' => '1A',
                'lease_status' => 'active',
                'payment_status' => 'current',
                'rent_amount' => 1200,
                'lease_start' => '2023-01-01',
                'lease_end' => '2024-01-01',
                'move_in_date' => '2023-01-01',
                'emergency_contact' => 'Jane Smith - (555) 987-6543',
                'created_at' => '2022-12-15'
            ],
            [
                'id' => 2,
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@email.com',
                'phone' => '(555) 234-5678',
                'property_name' => 'Sunset Apartments',
                'unit_number' => '1B',
                'lease_status' => 'active',
                'payment_status' => 'current',
                'rent_amount' => 1200,
                'lease_start' => '2023-02-01',
                'lease_end' => '2024-02-01',
                'move_in_date' => '2023-02-01',
                'emergency_contact' => 'Mike Johnson - (555) 876-5432',
                'created_at' => '2023-01-20'
            ],
            [
                'id' => 3,
                'first_name' => 'Mike',
                'last_name' => 'Chen',
                'email' => 'mike.chen@email.com',
                'phone' => '(555) 345-6789',
                'property_name' => 'Downtown Plaza',
                'unit_number' => '301',
                'lease_status' => 'active',
                'payment_status' => 'overdue',
                'rent_amount' => 2000,
                'lease_start' => '2023-01-15',
                'lease_end' => '2024-01-15',
                'move_in_date' => '2023-01-15',
                'emergency_contact' => 'Emily Chen - (555) 765-4321',
                'created_at' => '2023-01-10'
            ],
            [
                'id' => 4,
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@email.com',
                'phone' => '(555) 456-7890',
                'property_name' => 'Riverside Complex',
                'unit_number' => '401',
                'lease_status' => 'active',
                'payment_status' => 'current',
                'rent_amount' => 1500,
                'lease_start' => '2023-03-01',
                'lease_end' => '2024-03-01',
                'move_in_date' => '2023-03-01',
                'emergency_contact' => 'Robert Davis - (555) 654-3210',
                'created_at' => '2023-02-15'
            ],
            [
                'id' => 5,
                'first_name' => 'Robert',
                'last_name' => 'Wilson',
                'email' => 'robert.wilson@email.com',
                'phone' => '(555) 567-8901',
                'property_name' => 'Sunset Apartments',
                'unit_number' => '3B',
                'lease_status' => 'expiring',
                'payment_status' => 'current',
                'rent_amount' => 900,
                'lease_start' => '2023-05-01',
                'lease_end' => '2024-02-01',
                'move_in_date' => '2023-05-01',
                'emergency_contact' => 'Mary Wilson - (555) 543-2109',
                'created_at' => '2023-04-20'
            ],
            [
                'id' => 6,
                'first_name' => 'Lisa',
                'last_name' => 'Anderson',
                'email' => 'lisa.anderson@email.com',
                'phone' => '(555) 678-9012',
                'property_name' => 'Downtown Plaza',
                'unit_number' => '101',
                'lease_status' => 'terminated',
                'payment_status' => 'paid',
                'rent_amount' => 2000,
                'lease_start' => '2023-01-01',
                'lease_end' => '2023-12-31',
                'move_in_date' => '2023-01-01',
                'emergency_contact' => 'Tom Anderson - (555) 432-1098',
                'created_at' => '2022-12-01'
            ]
        ];
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set data through ViewManager (anti-scattering compliant)
        \ViewManager::set('title', 'Tenants Management');
        \ViewManager::set('user', $admin);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('stats', [
            'total_tenants' => count($tenants),
            'active_tenants' => count(array_filter($tenants, fn($t) => $t['lease_status'] === 'active')),
            'expiring_leases' => count(array_filter($tenants, fn($t) => $t['lease_status'] === 'expiring')),
            'overdue_payments' => count(array_filter($tenants, fn($t) => $t['payment_status'] === 'overdue'))
        ]);
        
        // Render using ViewManager with admin dashboard layout (anti-scattering compliant)
        echo \ViewManager::render('admin.tenants.list', [], 'admin.dashboard_layout');
    }
    
    public function create() {
        $admin = $this->requireAuth();
        $pdo   = $this->db->getConnection();

        // Fetch real properties belonging to this admin
        $stmt = $pdo->prepare("
            SELECT id, name
            FROM properties
            WHERE admin_id = ? AND deleted_at IS NULL
            ORDER BY name ASC
        ");
        $stmt->execute([$admin['id']]);
        $properties = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch all available units for this admin's properties
        $stmt = $pdo->prepare("
            SELECT u.id, u.unit_number, u.type, u.rent_price,
                   u.property_id, p.name AS property_name
            FROM units u
            JOIN properties p ON p.id = u.property_id
            WHERE p.admin_id = ?
              AND u.deleted_at IS NULL
              AND u.status = 'available'
            ORDER BY p.name ASC, u.unit_number ASC
        ");
        $stmt->execute([$admin['id']]);
        $units = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch existing tenants for Associated Tenant dropdown
        $stmt = $pdo->prepare("
            SELECT id, name, email
            FROM tenants
            WHERE admin_id = ?
              AND deleted_at IS NULL
            ORDER BY name ASC
        ");
        $stmt->execute([$admin['id']]);
        $tenants = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('admin/tenants/create', [
            'admin'      => $admin,
            'properties' => $properties,
            'units'      => $units,
            'tenants'    => $tenants,
            'title'      => 'Create New Tenant'
        ]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Validate required fields
        $required_fields = ['first_name', 'last_name', 'email', 'phone', 'monthly_rent', 'lease_start', 'lease_end'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Field '{$field}' is required.";
                $this->redirect('/admin/tenants/create');
                return;
            }
        }
        
        // Validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Please enter a valid email address.";
            $this->redirect('/admin/tenants/create');
            return;
        }
        
        // Validate dates
        if (strtotime($_POST['lease_end']) <= strtotime($_POST['lease_start'])) {
            $_SESSION['error'] = "Lease end date must be after lease start date.";
            $this->redirect('/admin/tenants/create');
            return;
        }
        
        // In production, save to database here
        // For now, just show success message
        
        $tenantName = "{$_POST['first_name']} {$_POST['last_name']}";
        
        // Create notification
        \App\Helpers\NotificationHelper::createTenantNotification($admin['id'], $tenantName, 'registered');
        
        $_SESSION['success'] = "Tenant '{$tenantName}' has been created successfully!";
        $this->redirect('/admin/tenants');
    }
    
    public function show($id) {
        $admin = $this->requireAuth();
        
        // Mock tenant data - in production this would come from database
        $tenant = [
            'id' => $id,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@email.com',
            'phone' => '(555) 123-4567',
            'address' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
            'property_name' => 'Sunset Apartments',
            'unit_number' => '1A',
            'lease_status' => 'active',
            'payment_status' => 'current',
            'rent_amount' => 1200,
            'lease_start' => '2023-01-01',
            'lease_end' => '2024-01-01',
            'move_in_date' => '2023-01-01',
            'emergency_contact_name' => 'Jane Smith',
            'emergency_contact_phone' => '(555) 987-6543',
            'created_at' => '2022-12-15'
        ];
        
        // Mock payment history
        $payment_history = [
            ['date' => '2023-12-01', 'amount' => 1200, 'status' => 'paid', 'method' => 'Bank Transfer'],
            ['date' => '2023-11-01', 'amount' => 1200, 'status' => 'paid', 'method' => 'Credit Card'],
            ['date' => '2023-10-01', 'amount' => 1200, 'status' => 'paid', 'method' => 'Bank Transfer'],
            ['date' => '2023-09-01', 'amount' => 1200, 'status' => 'paid', 'method' => 'Bank Transfer']
        ];
        
        // Mock maintenance requests
        $maintenance_requests = [
            ['id' => 1, 'type' => 'Plumbing', 'description' => 'Leaky faucet', 'status' => 'completed', 'date' => '2023-11-15'],
            ['id' => 2, 'type' => 'Electrical', 'description' => 'Broken light switch', 'status' => 'pending', 'date' => '2023-12-01']
        ];
        
        $this->view('admin/tenants/details', [
            'admin' => $admin,
            'tenant' => $tenant,
            'payment_history' => $payment_history,
            'maintenance_requests' => $maintenance_requests,
            'title' => 'Tenant Details'
        ]);
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        
        // Mock tenant data - in production this would come from database
        $tenant = [
            'id' => $id,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@email.com',
            'phone' => '(555) 123-4567',
            'address' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
            'property_id' => 1,
            'property_name' => 'Sunset Apartments',
            'unit_number' => '1A',
            'lease_status' => 'active',
            'payment_status' => 'current',
            'rent_amount' => 1200,
            'lease_start' => '2023-01-01',
            'lease_end' => '2024-01-01',
            'move_in_date' => '2023-01-01',
            'emergency_contact_name' => 'Jane Smith',
            'emergency_contact_phone' => '(555) 987-6543',
            'created_at' => '2022-12-15'
        ];
        
        // Mock properties for dropdown
        $properties = [
            ['id' => 1, 'name' => 'Sunset Apartments', 'available_units' => ['101', '102', '201', '202']],
            ['id' => 2, 'name' => 'Downtown Plaza', 'available_units' => ['301', '302', '303']],
            ['id' => 3, 'name' => 'Riverside Complex', 'available_units' => ['401', '402']]
        ];
        
        $this->view('admin/tenants/edit', [
            'admin' => $admin,
            'tenant' => $tenant,
            'properties' => $properties,
            'title' => 'Edit Tenant'
        ]);
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Validate required fields
        $required_fields = ['first_name', 'last_name', 'email', 'phone', 'monthly_rent', 'lease_start', 'lease_end'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Field '{$field}' is required.";
                $this->redirect("/admin/tenants/{$id}/edit");
                return;
            }
        }
        
        // Validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Please enter a valid email address.";
            $this->redirect("/admin/tenants/{$id}/edit");
            return;
        }
        
        // Validate dates
        if (strtotime($_POST['lease_end']) <= strtotime($_POST['lease_start'])) {
            $_SESSION['error'] = "Lease end date must be after lease start date.";
            $this->redirect("/admin/tenants/{$id}/edit");
            return;
        }
        
        // In production, update database here
        // For now, just show success message
        
        $_SESSION['success'] = "Tenant '{$_POST['first_name']} {$_POST['last_name']}' has been updated successfully!";
        $this->redirect('/admin/tenants');
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // In production, check if tenant exists and has no active leases
        // For now, just show success message
        
        $_SESSION['success'] = "Tenant has been deleted successfully!";
        $this->redirect('/admin/tenants');
    }
}
