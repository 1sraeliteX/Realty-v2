<?php

namespace App\Controllers;

class MaintenanceController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get pagination and filter parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $priority = $_GET['priority'] ?? '';
        $propertyId = $_GET['property_id'] ?? '';
        
        // Mock maintenance requests data
        $mockRequests = [
            [
                'id' => 1,
                'title' => 'Leaking Kitchen Faucet',
                'description' => 'The kitchen faucet is leaking continuously and needs to be repaired or replaced.',
                'priority' => 'high',
                'status' => 'pending',
                'property_id' => 1,
                'property_name' => 'Sunset Apartments',
                'tenant_id' => 1,
                'tenant_name' => 'John Doe',
                'tenant_email' => 'john.doe@email.com',
                'tenant_phone' => '+1-555-0101',
                'unit_number' => '101',
                'category' => 'plumbing',
                'assigned_to' => null,
                'assigned_to_name' => null,
                'cost_estimate' => 150.00,
                'actual_cost' => null,
                'scheduled_date' => '2024-01-15',
                'completion_date' => null,
                'created_at' => '2024-01-10 09:30:00',
                'updated_at' => '2024-01-10 09:30:00',
                'notes' => 'Tenant reports leak is getting worse',
                'updates' => []
            ],
            [
                'id' => 2,
                'title' => 'HVAC System Not Cooling',
                'description' => 'Air conditioning unit is not blowing cold air, needs immediate attention.',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'property_id' => 2,
                'property_name' => 'Downtown Plaza',
                'tenant_id' => 2,
                'tenant_name' => 'Jane Smith',
                'tenant_email' => 'jane.smith@email.com',
                'tenant_phone' => '+1-555-0102',
                'unit_number' => '201',
                'category' => 'hvac',
                'assigned_to' => 1,
                'assigned_to_name' => 'ABC HVAC Services',
                'cost_estimate' => 500.00,
                'actual_cost' => null,
                'scheduled_date' => '2024-01-12',
                'completion_date' => null,
                'created_at' => '2024-01-09 14:15:00',
                'updated_at' => '2024-01-11 10:30:00',
                'notes' => 'Technician scheduled for tomorrow',
                'updates' => [
                    [
                        'id' => 1,
                        'status' => 'assigned',
                        'notes' => 'Assigned to ABC HVAC Services',
                        'created_at' => '2024-01-11 10:30:00'
                    ]
                ]
            ],
            [
                'id' => 3,
                'title' => 'Broken Window in Living Room',
                'description' => 'Living room window has a crack and needs to be replaced for safety.',
                'priority' => 'medium',
                'status' => 'completed',
                'property_id' => 1,
                'property_name' => 'Sunset Apartments',
                'tenant_id' => 3,
                'tenant_name' => 'Bob Johnson',
                'tenant_email' => 'bob.johnson@email.com',
                'tenant_phone' => '+1-555-0103',
                'unit_number' => '205',
                'category' => 'structural',
                'assigned_to' => 2,
                'assigned_to_name' => 'Glass Repair Pro',
                'cost_estimate' => 300.00,
                'actual_cost' => 280.00,
                'scheduled_date' => '2024-01-08',
                'completion_date' => '2024-01-09',
                'created_at' => '2024-01-07 11:45:00',
                'updated_at' => '2024-01-09 16:20:00',
                'notes' => 'Window replaced successfully',
                'updates' => [
                    [
                        'id' => 2,
                        'status' => 'completed',
                        'notes' => 'Window replaced, tenant satisfied',
                        'created_at' => '2024-01-09 16:20:00'
                    ]
                ]
            ],
            [
                'id' => 4,
                'title' => 'Electrical Outlet Not Working',
                'description' => 'Bedroom electrical outlet is not providing power, needs inspection.',
                'priority' => 'low',
                'status' => 'pending',
                'property_id' => 3,
                'property_name' => 'Garden View Homes',
                'tenant_id' => 4,
                'tenant_name' => 'Alice Brown',
                'tenant_email' => 'alice.brown@email.com',
                'tenant_phone' => '+1-555-0104',
                'unit_number' => '305',
                'category' => 'electrical',
                'assigned_to' => null,
                'assigned_to_name' => null,
                'cost_estimate' => 75.00,
                'actual_cost' => null,
                'scheduled_date' => null,
                'completion_date' => null,
                'created_at' => '2024-01-11 16:00:00',
                'updated_at' => '2024-01-11 16:00:00',
                'notes' => null,
                'updates' => []
            ],
            [
                'id' => 5,
                'title' => 'Garbage Disposal Jammed',
                'description' => 'Kitchen garbage disposal is jammed and making grinding noises.',
                'priority' => 'medium',
                'status' => 'in_progress',
                'property_id' => 2,
                'property_name' => 'Downtown Plaza',
                'tenant_id' => 5,
                'tenant_name' => 'Charlie Wilson',
                'tenant_email' => 'charlie.wilson@email.com',
                'tenant_phone' => '+1-555-0105',
                'unit_number' => '102',
                'category' => 'appliance',
                'assigned_to' => 3,
                'assigned_to_name' => 'Quick Fix Appliances',
                'cost_estimate' => 120.00,
                'actual_cost' => null,
                'scheduled_date' => '2024-01-13',
                'completion_date' => null,
                'created_at' => '2024-01-10 13:20:00',
                'updated_at' => '2024-01-11 09:15:00',
                'notes' => 'Technician will inspect tomorrow',
                'updates' => [
                    [
                        'id' => 3,
                        'status' => 'assigned',
                        'notes' => 'Assigned to Quick Fix Appliances',
                        'created_at' => '2024-01-11 09:15:00'
                    ]
                ]
            ]
        ];
        
        // Apply filters (mock implementation)
        $filteredRequests = $mockRequests;
        if (!empty($search)) {
            $filteredRequests = array_filter($filteredRequests, function($req) use ($search) {
                return stripos($req['title'], $search) !== false || 
                       stripos($req['description'], $search) !== false;
            });
        }
        if (!empty($status)) {
            $filteredRequests = array_filter($filteredRequests, function($req) use ($status) {
                return $req['status'] === $status;
            });
        }
        if (!empty($priority)) {
            $filteredRequests = array_filter($filteredRequests, function($req) use ($priority) {
                return $req['priority'] === $priority;
            });
        }
        if (!empty($propertyId)) {
            $filteredRequests = array_filter($filteredRequests, function($req) use ($propertyId) {
                return $req['property_id'] == $propertyId;
            });
        }
        
        // Pagination
        $total = count($filteredRequests);
        $offset = ($page - 1) * $limit;
        $requests = array_slice($filteredRequests, $offset, $limit);
        
        // Calculate statistics
        $stats = [
            'total_requests' => count($mockRequests),
            'urgent_count' => count(array_filter($mockRequests, fn($r) => $r['priority'] === 'urgent')),
            'high_count' => count(array_filter($mockRequests, fn($r) => $r['priority'] === 'high')),
            'medium_count' => count(array_filter($mockRequests, fn($r) => $r['priority'] === 'medium')),
            'low_count' => count(array_filter($mockRequests, fn($r) => $r['priority'] === 'low')),
            'pending_count' => count(array_filter($mockRequests, fn($r) => $r['status'] === 'pending')),
            'in_progress_count' => count(array_filter($mockRequests, fn($r) => $r['status'] === 'in_progress')),
            'completed_count' => count(array_filter($mockRequests, fn($r) => $r['status'] === 'completed')),
            'avg_estimated_cost' => array_sum(array_column($mockRequests, 'cost_estimate')) / count($mockRequests),
            'total_actual_cost' => array_sum(array_column($mockRequests, 'actual_cost'))
        ];
        
        // Mock properties for filters
        $properties = [
            ['id' => 1, 'name' => 'Sunset Apartments'],
            ['id' => 2, 'name' => 'Downtown Plaza'],
            ['id' => 3, 'name' => 'Garden View Homes']
        ];
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('requests', $requests);
        \ViewManager::set('pagination', [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'last_page' => ceil($total / $limit)
        ]);
        \ViewManager::set('stats', $stats);
        \ViewManager::set('properties', $properties);
        \ViewManager::set('filters', [
            'search' => $search,
            'status' => $status,
            'priority' => $priority,
            'property_id' => $propertyId
        ]);
        
        // Set content for view (anti-scattering compliant)
        ob_start();
        include __DIR__ . '/../../views/admin/maintenance/index.php';
        $content = ob_get_clean();
        
        // Verify content was generated
        if (empty($content)) {
            $content = '<div class="text-center py-8"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Maintenance Requests</h1><p class="text-gray-600 dark:text-gray-400 mt-2">No content generated</p></div>';
        }
        
        // Set data for layout (anti-scattering compliant)
        \ViewManager::set('content', $content);
        \ViewManager::set('title', 'Maintenance Requests');
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        
        // Include the admin dashboard layout
        include __DIR__ . '/../../views/admin/dashboard_layout.php';
    }
    
    public function create() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Mock properties data
        $properties = [
            ['id' => 1, 'name' => 'Sunset Apartments'],
            ['id' => 2, 'name' => 'Downtown Plaza'],
            ['id' => 3, 'name' => 'Garden View Homes']
        ];
        
        // Mock tenants data
        $tenants = [
            ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'property_id' => 1, 'unit_id' => 1],
            ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Smith', 'property_id' => 2, 'unit_id' => 2],
            ['id' => 3, 'first_name' => 'Bob', 'last_name' => 'Johnson', 'property_id' => 1, 'unit_id' => 3],
            ['id' => 4, 'first_name' => 'Alice', 'last_name' => 'Brown', 'property_id' => 3, 'unit_id' => 4],
            ['id' => 5, 'first_name' => 'Charlie', 'last_name' => 'Wilson', 'property_id' => 2, 'unit_id' => 5]
        ];
        
        // Mock contractors/vendors data
        $contractors = [
            ['id' => 1, 'name' => 'ABC HVAC Services', 'specialty' => 'HVAC'],
            ['id' => 2, 'name' => 'Glass Repair Pro', 'specialty' => 'Glass/Windows'],
            ['id' => 3, 'name' => 'Quick Fix Appliances', 'specialty' => 'Appliances'],
            ['id' => 4, 'name' => 'Pro Plumbing Solutions', 'specialty' => 'Plumbing'],
            ['id' => 5, 'name' => 'Electric Masters', 'specialty' => 'Electrical']
        ];
        
        // Define categories and priorities
        $categories = [
            ['value' => 'plumbing', 'label' => 'Plumbing'],
            ['value' => 'electrical', 'label' => 'Electrical'],
            ['value' => 'hvac', 'label' => 'HVAC'],
            ['value' => 'appliance', 'label' => 'Appliance'],
            ['value' => 'structural', 'label' => 'Structural'],
            ['value' => 'pest_control', 'label' => 'Pest Control'],
            ['value' => 'landscaping', 'label' => 'Landscaping'],
            ['value' => 'other', 'label' => 'Other']
        ];
        
        $priorities = [
            ['value' => 'low', 'label' => 'Low'],
            ['value' => 'medium', 'label' => 'Medium'],
            ['value' => 'high', 'label' => 'High'],
            ['value' => 'urgent', 'label' => 'Urgent']
        ];
        
        $statuses = [
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'in_progress', 'label' => 'In Progress'],
            ['value' => 'completed', 'label' => 'Completed'],
            ['value' => 'cancelled', 'label' => 'Cancelled']
        ];
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('properties', $properties);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('contractors', $contractors);
        \ViewManager::set('categories', $categories);
        \ViewManager::set('priorities', $priorities);
        \ViewManager::set('statuses', $statuses);
        
        // Set content for view (anti-scattering compliant)
        ob_start();
        include __DIR__ . '/../../views/admin/maintenance/create.php';
        $content = ob_get_clean();
        
        // Set data for layout (anti-scattering compliant)
        \ViewManager::set('content', $content);
        \ViewManager::set('title', 'Create Maintenance Request');
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        
        // Include the admin dashboard layout
        include __DIR__ . '/../../views/admin/dashboard_layout.php';
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Validate required fields
        $required = ['title', 'description', 'priority'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Field '$field' is required";
                $this->redirect('/admin/maintenance/create');
                return;
            }
        }
        
        // Validate priority
        $validPriorities = ['low', 'medium', 'high', 'urgent'];
        if (!in_array($_POST['priority'], $validPriorities)) {
            $_SESSION['error'] = 'Invalid priority';
            $this->redirect('/admin/maintenance/create');
            return;
        }
        
        // Validate property/unit if provided
        if (!empty($_POST['property_id'])) {
            $propertyCheck = $this->db->query("SELECT id FROM properties WHERE id = ? AND admin_id = ?", 
                                             [$_POST['property_id'], $admin['id']])->fetch();
            if (!$propertyCheck) {
                $_SESSION['error'] = 'Property not found';
                $this->redirect('/admin/maintenance/create');
                return;
            }
        }
        
        if (!empty($_POST['unit_id'])) {
            $unitCheck = $this->db->query("SELECT id FROM units WHERE id = ? AND admin_id = ?", 
                                         [$_POST['unit_id'], $admin['id']])->fetch();
            if (!$unitCheck) {
                $_SESSION['error'] = 'Unit not found';
                $this->redirect('/admin/maintenance/create');
                return;
            }
        }
        
        try {
            $sql = "INSERT INTO maintenance_requests (admin_id, tenant_id, property_id, unit_id, title, 
                      description, priority, status, requested_date, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $admin['id'],
                $_POST['tenant_id'] ?? null,
                $_POST['property_id'] ?? null,
                $_POST['unit_id'] ?? null,
                $_POST['title'],
                $_POST['description'],
                $_POST['priority'],
                $_POST['status'] ?? 'pending'
            ];
            
            $this->db->query($sql, $params);
            $requestId = $this->db->lastInsertId();
            
            $_SESSION['success'] = 'Maintenance request created successfully';
            $this->redirect('/admin/maintenance');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to create maintenance request: ' . $e->getMessage();
            $this->redirect('/admin/maintenance/create');
        }
    }
    
    public function show($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Mock maintenance request data
        $mockRequests = [
            1 => [
                'id' => 1,
                'title' => 'Leaking Kitchen Faucet',
                'description' => 'The kitchen faucet is leaking continuously and needs to be repaired or replaced. The leak has been ongoing for 3 days and is causing water damage to the cabinet below.',
                'priority' => 'high',
                'status' => 'pending',
                'property_id' => 1,
                'property_name' => 'Sunset Apartments',
                'tenant_id' => 1,
                'tenant_name' => 'John Doe',
                'tenant_email' => 'john.doe@email.com',
                'tenant_phone' => '+1-555-0101',
                'unit_number' => '101',
                'category' => 'plumbing',
                'assigned_to' => null,
                'assigned_to_name' => null,
                'cost_estimate' => 150.00,
                'actual_cost' => null,
                'scheduled_date' => '2024-01-15',
                'completion_date' => null,
                'created_at' => '2024-01-10 09:30:00',
                'updated_at' => '2024-01-10 09:30:00',
                'notes' => 'Tenant reports leak is getting worse and has caused minor water damage to the cabinet. Immediate attention required.',
                'updates' => []
            ],
            2 => [
                'id' => 2,
                'title' => 'HVAC System Not Cooling',
                'description' => 'Air conditioning unit is not blowing cold air, needs immediate attention. The unit turns on but only blows warm air.',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'property_id' => 2,
                'property_name' => 'Downtown Plaza',
                'tenant_id' => 2,
                'tenant_name' => 'Jane Smith',
                'tenant_email' => 'jane.smith@email.com',
                'tenant_phone' => '+1-555-0102',
                'unit_number' => '201',
                'category' => 'hvac',
                'assigned_to' => 1,
                'assigned_to_name' => 'ABC HVAC Services',
                'cost_estimate' => 500.00,
                'actual_cost' => null,
                'scheduled_date' => '2024-01-12',
                'completion_date' => null,
                'created_at' => '2024-01-09 14:15:00',
                'updated_at' => '2024-01-11 10:30:00',
                'notes' => 'Technician scheduled for tomorrow. Tenant is elderly and needs AC working.',
                'updates' => [
                    [
                        'id' => 1,
                        'status' => 'assigned',
                        'notes' => 'Assigned to ABC HVAC Services',
                        'created_at' => '2024-01-11 10:30:00'
                    ]
                ]
            ]
        ];
        
        $request = $mockRequests[$id] ?? null;
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        // Mock contractors for assignment
        $contractors = [
            ['id' => 1, 'name' => 'ABC HVAC Services', 'specialty' => 'HVAC'],
            ['id' => 2, 'name' => 'Glass Repair Pro', 'specialty' => 'Glass/Windows'],
            ['id' => 3, 'name' => 'Quick Fix Appliances', 'specialty' => 'Appliances'],
            ['id' => 4, 'name' => 'Pro Plumbing Solutions', 'specialty' => 'Plumbing'],
            ['id' => 5, 'name' => 'Electric Masters', 'specialty' => 'Electrical']
        ];
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('request', $request);
        \ViewManager::set('contractors', $contractors);
        
        // Set content for view (anti-scattering compliant)
        ob_start();
        include __DIR__ . '/../../views/admin/maintenance/show.php';
        $content = ob_get_clean();
        
        // Set data for layout (anti-scattering compliant)
        \ViewManager::set('content', $content);
        \ViewManager::set('title', 'Maintenance Request Details');
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        
        // Include the admin dashboard layout
        include __DIR__ . '/../../views/admin/dashboard_layout.php';
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Mock maintenance request data
        $mockRequests = [
            1 => [
                'id' => 1,
                'title' => 'Leaking Kitchen Faucet',
                'description' => 'The kitchen faucet is leaking continuously and needs to be repaired or replaced. The leak has been ongoing for 3 days and is causing water damage to the cabinet below.',
                'priority' => 'high',
                'status' => 'pending',
                'property_id' => 1,
                'property_name' => 'Sunset Apartments',
                'tenant_id' => 1,
                'tenant_name' => 'John Doe',
                'tenant_email' => 'john.doe@email.com',
                'tenant_phone' => '+1-555-0101',
                'unit_number' => '101',
                'category' => 'plumbing',
                'assigned_to' => null,
                'assigned_to_name' => null,
                'cost_estimate' => 150.00,
                'actual_cost' => null,
                'scheduled_date' => '2024-01-15',
                'completion_date' => null,
                'created_at' => '2024-01-10 09:30:00',
                'updated_at' => '2024-01-10 09:30:00',
                'notes' => 'Tenant reports leak is getting worse and has caused minor water damage to the cabinet. Immediate attention required.'
            ]
        ];
        
        $request = $mockRequests[$id] ?? null;
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        // Mock properties and tenants for assignment
        $properties = [
            ['id' => 1, 'name' => 'Sunset Apartments'],
            ['id' => 2, 'name' => 'Downtown Plaza'],
            ['id' => 3, 'name' => 'Garden View Homes']
        ];
        
        $tenants = [
            ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'property_id' => 1, 'unit_id' => 1],
            ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Smith', 'property_id' => 2, 'unit_id' => 2],
            ['id' => 3, 'first_name' => 'Bob', 'last_name' => 'Johnson', 'property_id' => 1, 'unit_id' => 3],
            ['id' => 4, 'first_name' => 'Alice', 'last_name' => 'Brown', 'property_id' => 3, 'unit_id' => 4],
            ['id' => 5, 'first_name' => 'Charlie', 'last_name' => 'Wilson', 'property_id' => 2, 'unit_id' => 5]
        ];
        
        // Mock contractors for assignment
        $contractors = [
            ['id' => 1, 'name' => 'ABC HVAC Services', 'specialty' => 'HVAC'],
            ['id' => 2, 'name' => 'Glass Repair Pro', 'specialty' => 'Glass/Windows'],
            ['id' => 3, 'name' => 'Quick Fix Appliances', 'specialty' => 'Appliances'],
            ['id' => 4, 'name' => 'Pro Plumbing Solutions', 'specialty' => 'Plumbing'],
            ['id' => 5, 'name' => 'Electric Masters', 'specialty' => 'Electrical']
        ];
        
        // Define categories and priorities
        $categories = ['plumbing', 'electrical', 'hvac', 'appliance', 'structural', 'pest_control', 'landscaping', 'other'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('request', $request);
        \ViewManager::set('properties', $properties);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('contractors', $contractors);
        \ViewManager::set('categories', $categories);
        \ViewManager::set('priorities', $priorities);
        
        // Set content for view (anti-scattering compliant)
        ob_start();
        include __DIR__ . '/../../views/admin/maintenance/edit.php';
        $content = ob_get_clean();
        
        // Set data for layout (anti-scattering compliant)
        \ViewManager::set('content', $content);
        \ViewManager::set('title', 'Edit Maintenance Request');
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        
        // Include the admin dashboard layout
        include __DIR__ . '/../../views/admin/dashboard_layout.php';
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests m WHERE id = ? AND admin_id = ? AND m.deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        try {
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['title', 'description', 'priority', 'status', 'assigned_to', 'cost_estimate', 
                              'actual_cost', 'completion_date', 'notes'];
            
            foreach ($allowedFields as $field) {
                if (isset($_POST[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $_POST[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE maintenance_requests SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
                
                // Add update to history if status changed
                if (isset($_POST['status'])) {
                    $historySql = "INSERT INTO maintenance_updates (request_id, status, notes, created_at) VALUES (?, ?, ?, NOW())";
                    $this->db->query($historySql, [$id, $_POST['status'], $_POST['update_notes'] ?? 'Status updated']);
                }
            }
            
            $_SESSION['success'] = 'Maintenance request updated successfully';
            $this->redirect('/admin/maintenance');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to update maintenance request: ' . $e->getMessage();
            $this->redirect("/admin/maintenance/$id/edit");
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests m WHERE id = ? AND admin_id = ? AND m.deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        try {
            // Soft delete request
            $this->db->query("UPDATE maintenance_requests SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            $_SESSION['success'] = 'Maintenance request deleted successfully';
            $this->redirect('/admin/maintenance');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to delete maintenance request: ' . $e->getMessage();
            $this->redirect('/admin/maintenance');
        }
    }
    
    public function assignVendor($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        if (empty($_POST['vendor_id'])) {
            $_SESSION['error'] = 'Vendor ID is required';
            $this->redirect("/admin/maintenance/$id");
            return;
        }
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests m WHERE id = ? AND admin_id = ? AND m.deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Update request with vendor assignment
            $this->db->query("UPDATE maintenance_requests SET assigned_to = ?, status = 'assigned', updated_at = NOW() WHERE id = ?", 
                              [$_POST['vendor_id'], $id]);
            
            // Add update to history
            $historySql = "INSERT INTO maintenance_updates (request_id, status, notes, created_at) VALUES (?, ?, ?, NOW())";
            $this->db->query($historySql, [$id, 'assigned', 'Assigned to vendor: ' . $_POST['vendor_id']]);
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Vendor assigned successfully';
            $this->redirect("/admin/maintenance/$id");
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to assign vendor: ' . $e->getMessage();
            $this->redirect("/admin/maintenance/$id");
        }
    }
    
    public function complete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests m WHERE id = ? AND admin_id = ? AND m.deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Update request as completed
            $sql = "UPDATE maintenance_requests SET status = 'completed', completion_date = NOW(), updated_at = NOW()";
            $params = [];
            
            if (!empty($_POST['actual_cost'])) {
                $sql .= ", actual_cost = ?";
                $params[] = $_POST['actual_cost'];
            }
            
            if (!empty($_POST['completion_notes'])) {
                $sql .= ", notes = ?";
                $params[] = $_POST['completion_notes'];
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $this->db->query($sql, $params);
            
            // Add update to history
            $historySql = "INSERT INTO maintenance_updates (request_id, status, notes, created_at) VALUES (?, ?, ?, NOW())";
            $this->db->query($historySql, [$id, 'completed', $_POST['completion_notes'] ?? 'Request completed']);
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Maintenance request completed successfully';
            $this->redirect("/admin/maintenance/$id");
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to complete maintenance request: ' . $e->getMessage();
            $this->redirect("/admin/maintenance/$id");
        }
    }
}
