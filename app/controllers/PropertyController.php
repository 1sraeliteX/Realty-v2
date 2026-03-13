<?php

namespace App\Controllers;

class PropertyController extends BaseController {
    public function index() {
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/init_framework.php';
        
        $admin = $this->requireAuth();
        
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';
        
        // Set view data in ViewManager (anti-scattering compliant)
        \ViewManager::set('search', $search);
        \ViewManager::set('type', $type);
        \ViewManager::set('category', $category);
        \ViewManager::set('status', $status);
        
        // Build query with admin filtering (reverted to direct admin_id for reliability)
        $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
        $params = [$admin['id']];
        
        if (!empty($search)) {
            $where[] = "(p.name LIKE ? OR p.address LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($type)) {
            $where[] = "p.type = ?";
            $params[] = $type;
        }
        
        if (!empty($category)) {
            // Load property type helper through component registry (anti-scattering compliant)
            ComponentRegistry::load('property-type-helper');
            $categoryTypes = getPropertiesByCategory($category);
            if (!empty($categoryTypes)) {
                $placeholders = str_repeat('?,', count($categoryTypes));
                $where[] = "p.type IN ($placeholders)";
                $params = array_merge($params, array_column($categoryTypes, 'value'));
            }
        }
        
        if (!empty($status)) {
            $where[] = "p.status = ?";
            $params[] = $status;
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Get properties with unit counts
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                FROM properties p 
                WHERE {$whereClause}
                ORDER BY p.created_at DESC";
        
        $result = $this->paginate($sql, $page, 10, $params);
        
        // Debug: Log the SQL query and results
        error_log("Property Query SQL: " . $sql);
        error_log("Property Query Params: " . json_encode($params));
        error_log("Property Query Results: " . json_encode($result));
        
        // Set data in ViewManager for dashboard layout compatibility (anti-scattering compliant)
        \ViewManager::set('properties', $result['data']);
        \ViewManager::set('pagination', $result['pagination']);
        \ViewManager::set('search', $search);
        \ViewManager::set('type', $type);
        \ViewManager::set('category', $category);
        \ViewManager::set('status', $status);
        
        // Always use dashboard layout for properties
        $this->view('admin.dashboard_layout', [
            'admin' => $admin,
            'title' => 'Properties',
            'pageTitle' => 'Properties Management',
            'content' => $this->renderView('properties.index', [
                'properties' => $result['data'],
                'pagination' => $result['pagination'],
                'search' => $search,
                'type' => $type,
                'category' => $category,
                'status' => $status
            ])
        ]);
    }

    public function create() {
        $admin = $this->requireAuth();
        
        // Always use dashboard layout for properties
        $this->view('admin.dashboard_layout', [
            'admin' => $admin,
            'title' => 'Add Property',
            'pageTitle' => 'Add New Property',
            'content' => $this->renderView('properties.create')
        ]);
    }

    public function store() {
        $admin = $this->requireAuth();
        $data = $this->getPostData();
        
        // Handle both regular form submission and AJAX mapped field names
        $mappedData = [
            'property_name' => $data['name'] ?? $data['property_name'] ?? '',
            'address' => $data['address'] ?? '',
            'property_type' => $data['type'] ?? $data['property_type'] ?? '',
            'yearly_rent' => $data['rent_price'] ?? $data['yearly_rent'] ?? '',
            'year_built' => $data['year_built'] ?? '',
            'rooms' => $data['bedrooms'] ?? $data['rooms'] ?? '',
            'bathrooms' => $data['bathrooms'] ?? '',
            'kitchens' => $data['kitchens'] ?? '',
            'parking' => $data['parking'] ?? '',
            'water_availability' => $data['water_availability'] ?? '',
            'description' => $data['description'] ?? '',
            'category' => $data['category'] ?? '',
            'status' => $data['status'] ?? 'active'
        ];
        
        // Validate required fields
        $required = ['property_name', 'address', 'property_type', 'water_availability'];
        $errors = $this->validateRequired($mappedData, $required);
        
        if (!empty($errors)) {
            if ($this->isApiRequest()) {
                $this->json(['errors' => $errors], 422);
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $mappedData;
                $this->redirect('/properties/create');
            }
        }
        
        // Handle file uploads
        $images = [];
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadDir = __DIR__ . '/../../storage/uploads/properties';
            
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $name,
                        'type' => $_FILES['images']['type'][$key],
                        'tmp_name' => $_FILES['images']['tmp_name'][$key],
                        'error' => $_FILES['images']['error'][$key],
                        'size' => $_FILES['images']['size'][$key]
                    ];
                    
                    try {
                        $filename = $this->uploadFile($file, $uploadDir);
                        $images[] = $filename;
                    } catch (\Exception $e) {
                        $errors['images'] = $e->getMessage();
                    }
                }
            }
        }

        if (!empty($errors)) {
            if ($this->isApiRequest()) {
                $this->json(['errors' => $errors], 422);
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $mappedData;
                $this->redirect('/properties/create');
            }
        }

        // Prepare property data
        $propertyData = [
            'admin_id' => $admin['id'],
            'name' => $mappedData['property_name'],
            'address' => $mappedData['address'],
            'type' => $mappedData['property_type'],
            'category' => $mappedData['category'] ?? null,
            'description' => $mappedData['description'] ?? null,
            'year_built' => $mappedData['year_built'] ?? null,
            'bedrooms' => $mappedData['rooms'] ?? null,
            'bathrooms' => $mappedData['bathrooms'] ?? null,
            'kitchens' => $mappedData['kitchens'] ?? 1,
            'parking' => $mappedData['parking'] ?? 0,
            'rent_price' => $mappedData['yearly_rent'] ?? null,
            'status' => $mappedData['status'] ?? 'active',
            'amenities' => !empty($data['amenities']) ? json_decode($data['amenities'], true) : null,
            'images' => !empty($images) ? json_encode($images) : null
        ];

        $propertyId = $this->db->insert('properties', $propertyData);
        
        // Debug: Log property creation
        error_log("Property Creation Data: " . json_encode($propertyData));
        error_log("Property Creation ID: " . $propertyId);

        // Log activity
        $this->logActivity($admin['id'], 'create', "Created property: {$mappedData['property_name']}", 'property', $propertyId);

        if ($this->isApiRequest() || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->json([
                'message' => 'Property created successfully',
                'property_id' => $propertyId
            ], 201);
        } else {
            $_SESSION['success'] = 'Property created successfully!';
            $this->redirect('/admin/properties');
        }
    }

    public function show($id) {
        $admin = $this->requireAuth();
        
        // Get property with units
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                FROM properties p 
                WHERE p.id = ? AND p.admin_id = ? AND p.deleted_at IS NULL";
        
        $property = $this->db->fetch($sql, [$id, $admin['id']]);
        
        if (!$property) {
            if ($this->isApiRequest()) {
                $this->json(['error' => 'Property not found'], 404);
            } else {
                $_SESSION['error'] = 'Property not found';
                $this->redirect('/admin/properties');
            }
        }

        // Get units for this property
        $unitsSql = "SELECT * FROM units WHERE property_id = ? AND deleted_at IS NULL ORDER BY unit_number";
        $units = $this->db->fetchAll($unitsSql, [$id]);

        // Get tenants for this property
        $tenantsSql = "SELECT t.*, u.unit_number FROM tenants t 
                       JOIN units u ON t.unit_id = u.id 
                       WHERE t.property_id = ? AND t.deleted_at IS NULL 
                       ORDER BY t.name";
        $tenants = $this->db->fetchAll($tenantsSql, [$id]);

        if ($this->isApiRequest()) {
            $this->json([
                'property' => $property,
                'units' => $units,
                'tenants' => $tenants
            ]);
        } else {
            $this->view('admin.dashboard_layout', [
                'admin' => $admin,
                'pageTitle' => 'Property Details',
                'content' => $this->renderPropertyDetails($property, $units, $tenants)
            ]);
        }
    }

    public function edit($id) {
        $admin = $this->requireAuth();
        
        $sql = "SELECT * FROM properties WHERE id = ? AND admin_id = ? AND deleted_at IS NULL";
        $property = $this->db->fetch($sql, [$id, $admin['id']]);
        
        if (!$property) {
            $_SESSION['error'] = 'Property not found';
            $this->redirect('/admin/properties');
        }

        $this->view('admin.dashboard_layout', [
            'admin' => $admin,
            'pageTitle' => 'Edit Property',
            'content' => $this->renderView('properties.create', ['property' => $property])
        ]);
    }

    public function update($id) {
        $admin = $this->requireAuth();
        $data = $this->getPostData();
        
        // Check if property exists and belongs to admin
        $sql = "SELECT id FROM properties WHERE id = ? AND admin_id = ? AND deleted_at IS NULL";
        $property = $this->db->fetch($sql, [$id, $admin['id']]);
        
        if (!$property) {
            if ($this->isApiRequest()) {
                $this->json(['error' => 'Property not found'], 404);
            } else {
                $_SESSION['error'] = 'Property not found';
                $this->redirect('/admin/properties');
            }
        }

        // Validate required fields
        $required = ['property_name', 'address', 'property_type'];
        $errors = $this->validateRequired($data, $required);
        
        if (!empty($errors)) {
            if ($this->isApiRequest()) {
                $this->json(['errors' => $errors], 422);
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                $this->redirect("/properties/{$id}/edit");
            }
        }

        // Handle file uploads
        $images = [];
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadDir = __DIR__ . '/../../storage/uploads/properties';
            
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $name,
                        'type' => $_FILES['images']['type'][$key],
                        'tmp_name' => $_FILES['images']['tmp_name'][$key],
                        'error' => $_FILES['images']['error'][$key],
                        'size' => $_FILES['images']['size'][$key]
                    ];
                    
                    try {
                        $filename = $this->uploadFile($file, $uploadDir);
                        $images[] = $filename;
                    } catch (\Exception $e) {
                        $errors['images'] = $e->getMessage();
                    }
                }
            }
        }

        if (!empty($errors)) {
            if ($this->isApiRequest()) {
                $this->json(['errors' => $errors], 422);
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                $this->redirect("/properties/{$id}/edit");
            }
        }

        // Prepare update data
        $updateData = [
            'name' => $data['property_name'],
            'address' => $data['address'],
            'type' => $data['property_type'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'year_built' => $data['year_built'] ?? null,
            'bedrooms' => $data['rooms'] ?? null,
            'bathrooms' => $data['bathrooms'] ?? null,
            'kitchens' => $data['kitchens'] ?? 1,
            'parking' => $data['parking'] ?? 0,
            'rent_price' => $data['yearly_rent'] ?? null,
            'status' => $data['status'] ?? 'active',
            'amenities' => !empty($data['amenities']) ? json_decode($data['amenities'], true) : null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Add new images if uploaded
        if (!empty($images)) {
            $existingImages = json_decode($this->db->fetch("SELECT images FROM properties WHERE id = ?", [$id])['images'] ?? '[]', true);
            $allImages = array_merge($existingImages, $images);
            $updateData['images'] = json_encode($allImages);
        }

        $this->db->update('properties', $updateData, 'id = ?', [$id]);

        // Log activity
        $this->logActivity($admin['id'], 'update', "Updated property: {$data['property_name']}", 'property', $id);

        if ($this->isApiRequest() || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->json(['message' => 'Property updated successfully']);
        } else {
            $_SESSION['success'] = 'Property updated successfully!';
            $this->redirect('/admin/properties');
        }
    }

    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Check if property exists and belongs to admin
        $sql = "SELECT name FROM properties WHERE id = ? AND admin_id = ? AND deleted_at IS NULL";
        $property = $this->db->fetch($sql, [$id, $admin['id']]);
        
        if (!$property) {
            if ($this->isApiRequest()) {
                $this->json(['error' => 'Property not found'], 404);
            } else {
                $_SESSION['error'] = 'Property not found';
                $this->redirect('/admin/properties');
            }
        }

        // Soft delete
        $this->db->update('properties', ['deleted_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);

        // Log activity
        $this->logActivity($admin['id'], 'delete', "Deleted property: {$property['name']}", 'property', $id);

        if ($this->isApiRequest()) {
            $this->json(['message' => 'Property deleted successfully']);
        } else {
            $_SESSION['success'] = 'Property deleted successfully!';
            $this->redirect('/admin/properties');
        }
    }

    private function renderPropertyList($result, $search, $type, $status) {
        ob_start();
        ?>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-0">Properties</h2>
            <a href="/properties/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-plus mr-2"></i>
                Add Property
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" name="search" placeholder="Search properties..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Types</option>
                        <option value="residential" <?php echo $type === 'residential' ? 'selected' : ''; ?>>Residential</option>
                        <option value="commercial" <?php echo $type === 'commercial' ? 'selected' : ''; ?>>Commercial</option>
                        <option value="mixed" <?php echo $type === 'mixed' ? 'selected' : ''; ?>>Mixed</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Status</option>
                        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="maintenance" <?php echo $status === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Properties Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Units</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Occupancy</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php if (empty($result['data'])): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No properties found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($result['data'] as $property): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($property['name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                <?php echo htmlspecialchars(substr($property['address'], 0, 50)) . '...'; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            <?php echo ucfirst($property['type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <?php echo $property['unit_count']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                <?php echo $property['occupied_units']; ?> / <?php echo $property['unit_count']; ?>
                                            </div>
                                            <?php if ($property['unit_count'] > 0): ?>
                                                <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo round(($property['occupied_units'] / $property['unit_count']) * 100); ?>%"></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $property['status'] === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                   ($property['status'] === 'inactive' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'); ?>">
                                            <?php echo ucfirst($property['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="/properties/<?php echo $property['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 mr-3">View</a>
                                        <a href="/properties/<?php echo $property['id']; ?>/edit" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mr-3">Edit</a>
                                        <form action="/properties/<?php echo $property['id']; ?>/delete" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this property?')">
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($result['pagination']['total_pages'] > 1): ?>
                <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <?php if ($result['pagination']['has_prev']): ?>
                            <a href="?page=<?php echo $result['pagination']['current_page'] - 1; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">Previous</a>
                        <?php endif; ?>
                        <?php if ($result['pagination']['has_next']): ?>
                            <a href="?page=<?php echo $result['pagination']['current_page'] + 1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">Next</a>
                        <?php endif; ?>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span class="font-medium"><?php echo ($result['pagination']['current_page'] - 1) * $result['pagination']['per_page'] + 1; ?></span> to 
                                <span class="font-medium"><?php echo min($result['pagination']['current_page'] * $result['pagination']['per_page'], $result['pagination']['total']); ?></span> of 
                                <span class="font-medium"><?php echo $result['pagination']['total']; ?></span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <?php if ($result['pagination']['has_prev']): ?>
                                    <a href="?page=<?php echo $result['pagination']['current_page'] - 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">Previous</a>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $result['pagination']['current_page'] - 2); $i <= min($result['pagination']['total_pages'], $result['pagination']['current_page'] + 2); $i++): ?>
                                    <a href="?page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium 
                                        <?php echo $i === $result['pagination']['current_page'] ? 
                                            'z-10 bg-primary-50 border-primary-500 text-primary-600 dark:bg-primary-900 dark:border-primary-400 dark:text-primary-300' : 
                                            'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($result['pagination']['has_next']): ?>
                                    <a href="?page=<?php echo $result['pagination']['current_page'] + 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">Next</a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderPropertyForm($property = null) {
        ob_start();
        ?>
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    <?php echo $property ? 'Edit Property' : 'Add New Property'; ?>
                </h2>
                
                <form method="POST" <?php echo $property ? "action=\"/properties/{$property['id']}\"" : 'action="/properties"'; ?> enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Property Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required
                                   value="<?php echo htmlspecialchars($_SESSION['old']['name'] ?? $property['name'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Property Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Select Type</option>
                                <option value="residential" <?php echo (($property['type'] ?? '') === 'residential') ? 'selected' : ''; ?>>Residential</option>
                                <option value="commercial" <?php echo (($property['type'] ?? '') === 'commercial') ? 'selected' : ''; ?>>Commercial</option>
                                <option value="mixed" <?php echo (($property['type'] ?? '') === 'mixed') ? 'selected' : ''; ?>>Mixed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" required rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><?php echo htmlspecialchars($_SESSION['old']['address'] ?? $property['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <input type="text" name="category"
                                   value="<?php echo htmlspecialchars($_SESSION['old']['category'] ?? $property['category'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year Built</label>
                            <input type="number" name="year_built" min="1800" max="<?php echo date('Y'); ?>"
                                   value="<?php echo htmlspecialchars($_SESSION['old']['year_built'] ?? $property['year_built'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select name="status"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="active" <?php echo (($property['status'] ?? 'active') === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (($property['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                <option value="maintenance" <?php echo (($property['status'] ?? '') === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bedrooms</label>
                            <input type="number" name="bedrooms" min="0"
                                   value="<?php echo htmlspecialchars($_SESSION['old']['bedrooms'] ?? $property['bedrooms'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bathrooms</label>
                            <input type="number" name="bathrooms" min="0"
                                   value="<?php echo htmlspecialchars($_SESSION['old']['bathrooms'] ?? $property['bathrooms'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kitchens</label>
                            <input type="number" name="kitchens" min="0"
                                   value="<?php echo htmlspecialchars($_SESSION['old']['kitchens'] ?? $property['kitchens'] ?? '1'); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parking</label>
                            <input type="number" name="parking" min="0"
                                   value="<?php echo htmlspecialchars($_SESSION['old']['parking'] ?? $property['parking'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rent Price ($)</label>
                            <input type="number" name="rent_price" min="0" step="0.01"
                                   value="<?php echo htmlspecialchars($_SESSION['old']['rent_price'] ?? $property['rent_price'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Images</label>
                            <input type="file" name="images[]" multiple accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><?php echo htmlspecialchars($_SESSION['old']['description'] ?? $property['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="/properties" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                            <?php echo $property ? 'Update Property' : 'Create Property'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderPropertyDetails($property, $units, $tenants) {
        // Decode JSON fields
        $images = json_decode($property['images'] ?? '[]', true);
        $amenities = json_decode($property['amenities'] ?? '[]', true);
        
        ob_start();
        ?>
        <div class="max-w-6xl mx-auto">
            <!-- Property Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($property['name']); ?></h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1"><?php echo htmlspecialchars($property['address']); ?></p>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <?php echo ucfirst($property['type']); ?>
                            </span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $property['status'] === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($property['status'] === 'inactive' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'); ?>">
                                <?php echo ucfirst($property['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-3">
                        <a href="/properties/<?php echo $property['id']; ?>/edit" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <a href="/units/create?property_id=<?php echo $property['id']; ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                            <i class="fas fa-plus mr-2"></i>Add Unit
                        </a>
                    </div>
                </div>
            </div>

            <!-- Property Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                            <i class="fas fa-door-open text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Units</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo count($units); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupied</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $property['occupied_units']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                            <i class="fas fa-users text-yellow-600 dark:text-yellow-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tenants</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo count($tenants); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                            <i class="fas fa-dollar-sign text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rent Price</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">$<?php echo number_format($property['rent_price'] ?? 0, 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property Details and Images -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Property Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Category</p>
                                <p class="font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($property['category'] ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Year Built</p>
                                <p class="font-medium text-gray-900 dark:text-white"><?php echo $property['year_built'] ?? 'N/A'; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Bedrooms</p>
                                <p class="font-medium text-gray-900 dark:text-white"><?php echo $property['bedrooms'] ?? '0'; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Bathrooms</p>
                                <p class="font-medium text-gray-900 dark:text-white"><?php echo $property['bathrooms'] ?? '0'; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Kitchens</p>
                                <p class="font-medium text-gray-900 dark:text-white"><?php echo $property['kitchens'] ?? '0'; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Parking</p>
                                <p class="font-medium text-gray-900 dark:text-white"><?php echo $property['parking'] ?? '0'; ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($property['description'])): ?>
                            <div class="mt-6">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Description</p>
                                <p class="text-gray-900 dark:text-white"><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($amenities)): ?>
                            <div class="mt-6">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Amenities</p>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($amenities as $amenity): ?>
                                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm">
                                            <?php echo htmlspecialchars($amenity); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($images)): ?>
                    <div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Property Images</h3>
                            <div class="space-y-4">
                                <?php foreach ($images as $image): ?>
                                    <img src="/storage/uploads/properties/<?php echo htmlspecialchars($image); ?>" 
                                         alt="Property Image" 
                                         class="w-full h-48 object-cover rounded-lg">
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Units Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Units</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tenant</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php if (empty($units)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No units found. <a href="/units/create?property_id=<?php echo $property['id']; ?>" class="text-primary-600 hover:text-primary-500 dark:text-primary-400">Add your first unit</a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($units as $unit): ?>
                                    <?php 
                                    $tenant = null;
                                    foreach ($tenants as $t) {
                                        if ($t['unit_id'] == $unit['id']) {
                                            $tenant = $t;
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($unit['unit_number']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo ucfirst($unit['unit_type']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            $<?php echo number_format($unit['rent_price'] ?? 0, 2); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $unit['status'] === 'occupied' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                       ($unit['status'] === 'available' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                                       ($unit['status'] === 'maintenance' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')); ?>">
                                                <?php echo ucfirst($unit['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo $tenant ? htmlspecialchars($tenant['name']) : '-'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
