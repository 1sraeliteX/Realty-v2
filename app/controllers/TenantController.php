<?php

namespace App\Controllers;

class TenantController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();

        require_once __DIR__ . '/../../config/bootstrap.php';

        $pdo = $this->db->getConnection();

        $scopeCondition = $admin['role'] === 'super_admin' ? '' : 'AND t.admin_id = ?';
        $params = $admin['role'] === 'super_admin' ? [] : [$admin['id']];

        $rows = $pdo->prepare("
            SELECT t.id, t.name, t.email, t.phone,
                   t.rent_amount, t.deposit_amount,
                   t.lease_start, t.lease_end,
                   t.status,
                   t.created_at,
                   p.name AS property_name,
                   u.unit_number,
                   (SELECT pay.status FROM payments pay
                    WHERE pay.tenant_id = t.id
                      AND pay.deleted_at IS NULL
                    ORDER BY pay.due_date DESC LIMIT 1) AS last_payment_status
            FROM tenants t
            LEFT JOIN properties p ON p.id = t.property_id
            LEFT JOIN units u ON u.id = t.unit_id
            WHERE t.deleted_at IS NULL $scopeCondition
            ORDER BY t.created_at DESC
        ");
        $rows->execute($params);
        $rawTenants = $rows->fetchAll(\PDO::FETCH_ASSOC);

        // Map DB columns to field names the view expects
        $today = date('Y-m-d');
        $thirtyDays = date('Y-m-d', strtotime('+30 days'));
        $tenants = array_map(function ($t) use ($today, $thirtyDays) {
            $nameParts = explode(' ', $t['name'], 2);
            $leaseEnd = $t['lease_end'] ?? '';
            if ($t['status'] === 'active' && $leaseEnd && $leaseEnd <= $thirtyDays) {
                $leaseStatus = 'expiring';
            } else {
                $leaseStatus = $t['status'] ?? 'active';
            }
            $payStatus = $t['last_payment_status'];
            if (!$payStatus) $payStatus = 'current';
            if ($payStatus === 'overdue') $payStatus = 'overdue';
            elseif ($payStatus === 'paid') $payStatus = 'current';
            return [
                'id'             => $t['id'],
                'first_name'     => $nameParts[0] ?? '',
                'last_name'      => $nameParts[1] ?? '',
                'email'          => $t['email'],
                'phone'          => $t['phone'],
                'property_name'  => $t['property_name'] ?? '—',
                'unit_number'    => $t['unit_number'] ?? '—',
                'lease_status'   => $leaseStatus,
                'payment_status' => $payStatus,
                'rent_amount'    => $t['rent_amount'],
                'lease_start'    => $t['lease_start'],
                'lease_end'      => $leaseEnd,
                'move_in_date'   => $t['lease_start'],
                'created_at'     => $t['created_at'],
            ];
        }, $rawTenants);

        \ViewManager::set('title', 'Tenants Management');
        \ViewManager::set('user', $admin);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('stats', [
            'total_tenants'    => count($tenants),
            'active_tenants'   => count(array_filter($tenants, fn($t) => $t['lease_status'] === 'active')),
            'expiring_leases'  => count(array_filter($tenants, fn($t) => $t['lease_status'] === 'expiring')),
            'overdue_payments' => count(array_filter($tenants, fn($t) => $t['payment_status'] === 'overdue')),
        ]);

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
        $isAjax = !empty($_POST['_ajax']) || ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

        // CSRF check
        $token = $_POST['_token'] ?? '';
        if (!empty($_SESSION['csrf_token']) && !hash_equals($_SESSION['csrf_token'], $token)) {
            if ($isAjax) { $this->json(['success' => false, 'error' => 'Invalid security token.'], 403); return; }
            $_SESSION['error'] = 'Security token expired. Please try again.';
            $this->redirect('/admin/tenants/create');
            return;
        }

        $firstName   = trim($_POST['first_name'] ?? '');
        $lastName    = trim($_POST['last_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $propertyId  = (int)($_POST['property_id'] ?? 0);
        $unitId      = (int)($_POST['unit_id'] ?? 0);
        $leaseStart  = $_POST['lease_start_date'] ?? $_POST['lease_start'] ?? '';
        $leaseEnd    = $_POST['lease_end_date'] ?? $_POST['lease_end'] ?? '';
        $rentAmount  = (float)($_POST['rent_amount'] ?? $_POST['monthly_rent'] ?? 0);
        $status      = $_POST['status'] ?? 'active';

        $errors = [];
        if (!$firstName) $errors[] = 'First name is required.';
        if (!$lastName) $errors[] = 'Last name is required.';
        if (!$email) $errors[] = 'Email is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
        if (!$phone) $errors[] = 'Phone is required.';
        if (!$leaseStart) $errors[] = 'Lease start date is required.';
        if ($leaseEnd && strtotime($leaseEnd) <= strtotime($leaseStart)) $errors[] = 'Lease end must be after lease start.';

        if ($errors) {
            if ($isAjax) { $this->json(['success' => false, 'error' => implode(' ', $errors)], 422); return; }
            $_SESSION['error'] = implode(' ', $errors);
            $this->redirect('/admin/tenants/create');
            return;
        }

        try {
            $pdo = $this->db->getConnection();
            $name = trim("$firstName $lastName");

            $stmt = $pdo->prepare("
                INSERT INTO tenants (admin_id, property_id, unit_id, name, email, phone,
                                     rent_amount, lease_start, lease_end, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([
                $admin['id'], $propertyId ?: null, $unitId ?: null,
                $name, $email, $phone,
                $rentAmount, $leaseStart, $leaseEnd ?: null, $status,
            ]);

            // Mark unit as occupied if unit was selected
            if ($unitId) {
                $pdo->prepare("UPDATE units SET status = 'occupied', tenant_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                    ->execute([$pdo->lastInsertId(), $unitId]);
            }

            if ($isAjax) { $this->json(['success' => true, 'message' => "Tenant '$name' created successfully."]); return; }
            $_SESSION['success'] = "Tenant '$name' has been created successfully!";
            $this->redirect('/admin/tenants-occupants');
        } catch (\Throwable $e) {
            error_log('TenantController::store error: ' . $e->getMessage());
            if ($isAjax) { $this->json(['success' => false, 'error' => 'Database error. Please try again.'], 500); return; }
            $_SESSION['error'] = 'An error occurred. Please try again.';
            $this->redirect('/admin/tenants/create');
        }
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
