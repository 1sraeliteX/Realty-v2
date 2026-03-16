<?php

namespace App\Controllers;

require_once __DIR__ . '/BaseController.php';

class TenantOccupantController extends BaseController {
    
    public function index() {
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set data through DataProvider (anti-scattering compliant)
        \DataProvider::set('tenants', []);
        \DataProvider::set('occupants', []);
        
        // Set page metadata
        \ViewManager::set('title', 'Tenants & Occupants');
        \ViewManager::set('user', ['name' => 'Admin User', 'email' => 'admin@example.com']);
        
        // Render using ViewManager with dashboard layout
        echo \ViewManager::render('admin.tenants_occupants.index', [], 'admin.dashboard_layout');
    }
    
    public function createOccupant() {
        $admin = $this->requireAuth();
        $pdo   = $this->db->getConnection();

        // Real properties
        $stmt = $pdo->prepare("
            SELECT id, name FROM properties
            WHERE admin_id = ? AND deleted_at IS NULL
            ORDER BY name ASC
        ");
        $stmt->execute([$admin['id']]);
        $properties = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Real available units
        $stmt = $pdo->prepare("
            SELECT u.id, u.unit_number, u.type,
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

        // Real tenants
        $stmt = $pdo->prepare("
            SELECT id, name, email FROM tenants
            WHERE admin_id = ?
              AND deleted_at IS NULL
            ORDER BY name ASC
        ");
        $stmt->execute([$admin['id']]);
        $tenants = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        \ViewManager::set('title', 'Add New Occupant');
        \ViewManager::set('user', $admin);
        \ViewManager::set('notifications', []);
        \DataProvider::set('properties', $properties);
        \DataProvider::set('units', $units);
        \DataProvider::set('tenants', $tenants);

        include __DIR__ . '/../../views/admin/occupants/create.php';
    }
    
    public function storeOccupant() {
        $admin = $this->requireAuth();
        $this->ensureNextOfKinAddressColumn();

        $data = $this->getPostData();

        // Validate required fields
        $required = ['first_name', 'last_name', 'email', 'phone',
                   'property_id', 'unit_id', 'move_in_date', 'status'];
        $errors = $this->validateRequired($data, $required);

        if (!empty($errors)) {
            if ($this->isApiRequest()) {
                $this->json(['success' => false, 'errors' => $errors], 422);
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old']    = $data;
                $this->redirect('/admin/occupants/create');
            }
            return;
        }

        // Handle ID document upload
        $idDocumentFilename = null;
        if (isset($_FILES['id_documents']) &&
          !empty($_FILES['id_documents']['name'][0])) {

          $uploadDir = __DIR__ . '/../../public/uploads/documents';
          if (!is_dir($uploadDir)) {
              mkdir($uploadDir, 0755, true);
          }

          $file = [
              'name'     => $_FILES['id_documents']['name'][0],
              'type'     => $_FILES['id_documents']['type'][0],
              'tmp_name' => $_FILES['id_documents']['tmp_name'][0],
              'error'    => $_FILES['id_documents']['error'][0],
              'size'     => $_FILES['id_documents']['size'][0],
          ];

          try {
              $idDocumentFilename = $this->uploadFile($file, $uploadDir);
          } catch (\Exception $e) {
              error_log('ID document upload error: ' . $e->getMessage());
          }
      }

      // Also handle camera-captured photo (base64 via hidden input)
      if (empty($idDocumentFilename) &&
          !empty($data['camera_capture_data'])) {
          $idDocumentFilename = $this->saveBase64Image(
              $data['camera_capture_data'],
              __DIR__ . '/../../public/uploads/documents'
          );
      }

      // Build tenant record
      $tenantData = [
          'admin_id'             => $admin['id'],
          'first_name'           => $data['first_name'],
          'last_name'            => $data['last_name'],
          'email'                => $data['email'],
          'phone'                => $data['phone'],
          'date_of_birth'        => $data['date_of_birth']   ?? null,
          'gender'               => $data['gender']          ?? null,
          'unit_id'              => $data['unit_id'],
          'property_id'          => $data['property_id'],
          'relationship'         => $data['relationship']    ?? null,
          'move_in_date'         => $data['move_in_date'],
          'status'               => $data['status'],
          'notes'                => $data['notes']           ?? null,
          'next_of_kin'          => $data['next_of_kin']     ?? null,
          'next_of_kin_phone'    => $data['next_of_kin_phone'] ?? null,
          'next_of_kin_address'  => $data['next_of_kin_address'] ?? null,
          'id_document'          => $idDocumentFilename,
          'created_at'           => date('Y-m-d H:i:s'),
          'updated_at'           => date('Y-m-d H:i:s'),
      ];

      // Remove null values for columns that may not exist yet
      $tenantData = array_filter($tenantData,
          fn($v) => $v !== null || in_array(
              array_search($v, $tenantData),
              ['date_of_birth','gender','notes','next_of_kin',
               'next_of_kin_phone','next_of_kin_address','id_document']
          )
      );

      try {
          $tenantId = $this->db->insert('tenants', $tenantData);

          if (!$tenantId) {
              throw new \Exception('Insert returned no ID');
          }

          $this->logActivity(
              $admin['id'], 'create',
              "Added occupant: {$data['first_name']} {$data['last_name']}",
              'tenant', $tenantId
          );

          if ($this->isApiRequest()) {
              $this->json([
                  'success' => true,
                  'message' => 'Occupant created successfully',
                  'tenant_id' => $tenantId
              ], 201);
          } else {
              $_SESSION['success'] = 'Occupant created successfully!';
              $this->redirect('/admin/tenants-occupants');
          }

      } catch (\Throwable $e) {
          error_log('storeOccupant error: ' . $e->getMessage());
          if ($this->isApiRequest()) {
              $this->json([
                  'success' => false,
                  'error'   => 'Failed to create occupant: ' . $e->getMessage()
              ], 500);
          } else {
              $_SESSION['error'] = 'Failed to create occupant. Please try again.';
              $this->redirect('/admin/occupants/create');
          }
      }
    }

    private function ensureNextOfKinAddressColumn(): void {
        try {
            $pdo = $this->db->getConnection();
            // Check if column exists
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'tenants'
                AND COLUMN_NAME = 'next_of_kin_address'
            ");
            $stmt->execute();
            if ((int)$stmt->fetchColumn() === 0) {
                $pdo->exec("ALTER TABLE tenants
                            ADD COLUMN next_of_kin_address TEXT NULL
                            AFTER next_of_kin_phone");
            }
        } catch (\Throwable $e) {
            error_log('Migration error: ' . $e->getMessage());
        }
    }

    // Helper: save base64 image from camera capture
    private function saveBase64Image(string $base64, string $dir): ?string {
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $data = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $bytes = base64_decode($data);
        if (!$bytes) return null;
        $filename = uniqid('cam_', true) . '.jpg';
        file_put_contents($dir . '/' . $filename, $bytes);
        return $filename;
    }
    
    public function create() {
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set page metadata
        \ViewManager::set('title', 'Add New Tenant/Occupant');
        
        // Render using ViewManager
        echo \ViewManager::render('admin.tenants_occupants.create');
    }
    
    public function show($id) {
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set mock data through DataProvider
        \DataProvider::set('tenant', []);
        
        // Set page metadata
        \ViewManager::set('title', 'Tenant/Occupant Details');
        
        // Render using ViewManager
        echo \ViewManager::render('admin.tenants_occupants.show');
    }
    
    public function edit($id) {
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set mock data through DataProvider
        \DataProvider::set('tenant', []);
        
        // Set page metadata
        \ViewManager::set('title', 'Edit Tenant/Occupant');
        
        // Render using ViewManager
        echo \ViewManager::render('admin.tenants_occupants.edit');
    }
    
    public function store() {
        // Store new tenant/occupant
        // Implementation would go here
        header('Location: /admin/tenants-occupants');
        exit;
    }
    
    public function update($id) {
        // Update tenant/occupant
        // Implementation would go here
        header('Location: /admin/tenants-occupants');
        exit;
    }
    
    public function delete($id) {
        // Delete tenant/occupant
        // Implementation would go here
        header('Location: /admin/tenants-occupants');
        exit;
    }
}
