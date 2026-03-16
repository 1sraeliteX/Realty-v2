<?php
// Debug checker for Units Button & Property-Scoped Units functionality
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Units Button & Property-Scoped Units Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        h2 { color: #007bff; margin-top: 30px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .check { margin: 10px 0; padding: 8px; background: #f8f9fa; border-radius: 4px; }
        .pass { color: #28a745; font-weight: bold; }
        .fail { color: #dc3545; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        .summary { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Units Button & Property-Scoped Units Debug</h1>
        
        <div class="section">
            <h2>1. Route Checks</h2>
            <?php
            $routesFile = file_get_contents(__DIR__ . '/../routes/web.php');
            
            // Check main unit routes
            $hasUnitsIndex = strpos($routesFile, "'GET /admin/units' => 'UnitController@index'") !== false;
            echo '<div class="check">';
            echo $hasUnitsIndex ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' GET /admin/units → UnitController@index</div>';
            
            $hasUnitsCreate = strpos($routesFile, "'GET /admin/units/create' => 'UnitController@create'") !== false;
            echo '<div class="check">';
            echo $hasUnitsCreate ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' GET /admin/units/create → UnitController@create</div>';
            
            // Check property-scoped routes
            $hasPropertyUnitsIndex = strpos($routesFile, "'GET /admin/properties/{id}/units' => 'UnitController@indexByProperty'") !== false;
            echo '<div class="check">';
            echo $hasPropertyUnitsIndex ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' GET /admin/properties/{id}/units → UnitController@indexByProperty</div>';
            
            $hasPropertyUnitsCreate = strpos($routesFile, "'GET /admin/properties/{id}/units/create' => 'UnitController@createForProperty'") !== false;
            echo '<div class="check">';
            echo $hasPropertyUnitsCreate ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' GET /admin/properties/{id}/units/create → UnitController@createForProperty</div>';
            ?>
        </div>

        <div class="section">
            <h2>2. Controller Method Checks</h2>
            <?php
            $controllerFile = file_get_contents(__DIR__ . '/../app/controllers/UnitController.php');
            
            $hasIndex = strpos($controllerFile, 'public function index()') !== false;
            echo '<div class="check">';
            echo $hasIndex ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' index() method exists</div>';
            
            $hasIndexByProperty = strpos($controllerFile, 'public function indexByProperty(') !== false;
            echo '<div class="check">';
            echo $hasIndexByProperty ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' indexByProperty() method exists</div>';
            
            $hasCreateForProperty = strpos($controllerFile, 'public function createForProperty(') !== false;
            echo '<div class="check">';
            echo $hasCreateForProperty ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' createForProperty() method exists</div>';
            
            $hasStore = strpos($controllerFile, 'public function store()') !== false;
            echo '<div class="check">';
            echo $hasStore ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' store() method exists</div>';
            ?>
        </div>

        <div class="section">
            <h2>3. View File Checks</h2>
            <?php
            $unitsListFile = __DIR__ . '/../views/admin/units/list.php';
            $unitsCreateFile = __DIR__ . '/../views/admin/units/create.php';
            
            $hasUnitsList = file_exists($unitsListFile);
            echo '<div class="check">';
            echo $hasUnitsList ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' views/admin/units/list.php exists</div>';
            
            $hasUnitsCreate = file_exists($unitsCreateFile);
            echo '<div class="check">';
            echo $hasUnitsCreate ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' views/admin/units/create.php exists</div>';
            ?>
        </div>

        <div class="section">
            <h2>4. viewUnits() JavaScript Check</h2>
            <?php
            $propertiesListFile = file_get_contents(__DIR__ . '/../views/admin/properties/list.php');
            
            $hasCorrectUrl = strpos($propertiesListFile, '/admin/units?property_id=') !== false;
            echo '<div class="check">';
            echo $hasCorrectUrl ? '<span class="pass">✅ CORRECT URL</span>' : '<span class="fail">❌ MISSING</span>';
            echo ' viewUnits() uses /admin/units?property_id=</div>';
            
            $hasOldUrl = strpos($propertiesListFile, '/admin/properties/${id}/units') !== false;
            echo '<div class="check">';
            echo !$hasOldUrl ? '<span class="pass">✅ OLD URL REMOVED</span>' : '<span class="fail">❌ OLD URL STILL EXISTS</span>';
            echo ' Old /admin/properties/${id}/units URL removed</div>';
            ?>
        </div>

        <div class="section">
            <h2>5. Property Filter Banner Check</h2>
            <?php
            $unitsListContent = file_get_contents($unitsListFile);
            
            $hasBanner = strpos($unitsListContent, 'Showing units for') !== false;
            echo '<div class="check">';
            echo $hasBanner ? '<span class="pass">✅ BANNER EXISTS</span>' : '<span class="fail">❌ BANNER MISSING</span>';
            echo ' Property context banner exists</div>';
            
            $hasViewAllLink = strpos($unitsListContent, 'View all units') !== false;
            echo '<div class="check">';
            echo $hasViewAllLink ? '<span class="pass">✅ VIEW ALL LINK</span>' : '<span class="fail">❌ VIEW ALL MISSING</span>';
            echo ' "View all units" link exists</div>';
            ?>
        </div>

        <div class="section">
            <h2>6. Live Units Count</h2>
            <?php
            // Use app's own database config — never hardcode DB name
            $dbConfigFile = __DIR__ . '/../config/database.php';
            if (!class_exists('Config\Database') && file_exists($dbConfigFile)) {
                require_once $dbConfigFile;
            }

            $db = null;
            try {
                if (class_exists('Config\Database')) {
                    $db = \Config\Database::getInstance();
                }
            } catch (\Throwable $e) {
                echo '<p style="color:red">❌ DB connection failed: '
                     . htmlspecialchars($e->getMessage()) . '</p>';
            }

            if ($db) {
                try {
                    $pdo = $db->getConnection();
                    
                    // Show which database we're connected to
                    $stmt = $pdo->query("SELECT DATABASE()");
                    $dbName = $stmt->fetchColumn();
                    echo '<p>ℹ️ Connected to database: <strong>'
                         . htmlspecialchars($dbName) . '</strong></p>';
                    
                    $stmt = $pdo->query("
                        SELECT p.name, COUNT(u.id) as unit_count
                        FROM properties p
                        LEFT JOIN units u ON u.property_id = p.id AND u.deleted_at IS NULL
                        WHERE p.deleted_at IS NULL
                        GROUP BY p.id, p.name
                        ORDER BY p.created_at DESC
                        LIMIT 5
                    ");
                    
                    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($properties) > 0) {
                        echo '<table>';
                        echo '<tr><th>Property Name</th><th>Unit Count</th></tr>';
                        foreach ($properties as $property) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($property['name']) . '</td>';
                            echo '<td>' . $property['unit_count'] . '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    } else {
                        echo '<p>No properties found in database.</p>';
                    }
                    
                    echo '<div class="check"><span class="pass">✅</span> Database connection successful</div>';
                    
                } catch (\Throwable $e) {
                    echo '<div class="check"><span class="fail">❌</span> Database query failed: '
                         . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
            ?>
        </div>

        <div class="section">
            <h2>7. Units List Page Debug</h2>
            <?php
            $listFile = file_get_contents(__DIR__ . '/../views/admin/units/list.php');
            $createFile = file_get_contents(__DIR__ . '/../views/admin/units/create.php');
            
            // Variable order check
            $hasCorrectOrder = strpos($listFile, '$filteredPropertyId = ViewManager::get') !== false && 
                              strpos($listFile, '// MUST be assigned here') !== false;
            echo '<div class="check">';
            echo $hasCorrectOrder ? '<span class="pass">✅ CORRECT ORDER</span>' : '<span class="fail">❌ WRONG ORDER</span>';
            echo ' $filteredPropertyId assigned before HTML output</div>';
            
            $noDuplicate = strpos($listFile, '$filteredPropertyId = ViewManager::get', strpos($listFile, '<!-- Units Management Content -->')) === false;
            echo '<div class="check">';
            echo $noDuplicate ? '<span class="pass">✅ NO DUPLICATE</span>' : '<span class="fail">❌ DUPLICATE EXISTS</span>';
            echo ' No duplicate $filteredPropertyId assignment after HTML</div>';
            
            // Header layout check
            $hasFlexLayout = strpos($listFile, 'flex-col sm:flex-row sm:items-center sm:justify-between') !== false;
            echo '<div class="check">';
            echo $hasFlexLayout ? '<span class="pass">✅ FLEX LAYOUT</span>' : '<span class="fail">❌ MISSING</span>';
            echo ' Header has responsive flex layout</div>';
            
            $hasExportFunction = strpos($listFile, 'onclick="exportUnits()"') !== false;
            echo '<div class="check">';
            echo $hasExportFunction ? '<span class="pass">✅ EXPORT FUNCTION</span>' : '<span class="fail">❌ MISSING</span>';
            echo ' Export button calls exportUnits()</div>';
            
            // Currency check
            $hasNairaList = strpos($listFile, '₦') !== false;
            echo '<div class="check">';
            echo $hasNairaList ? '<span class="pass">✅ NAIRA</span>' : '<span class="fail">❌ STILL DOLLAR</span>';
            echo ' ₦ symbol in list.php table</div>';
            
            $hasNairaCreate = strpos($createFile, '₦') !== false;
            echo '<div class="check">';
            echo $hasNairaCreate ? '<span class="pass">✅ NAIRA</span>' : '<span class="fail">❌ STILL DOLLAR</span>';
            echo ' ₦ symbol in create.php</div>';
            
            // Live unit count
            $db = null;
            try {
                if (class_exists('Config\Database')) {
                    $db = \Config\Database::getInstance();
                }
            } catch (\Throwable $e) {
                // Database already initialized above, just continue
            }
            
            if ($db) {
                try {
                    $pdo = $db->getConnection();
                    $stmt = $pdo->query("SELECT COUNT(*) FROM units WHERE deleted_at IS NULL");
                    $unitCount = $stmt->fetchColumn();
                    echo '<div class="check"><span class="pass">✅</span> ' . $unitCount . ' total units in database</div>';
                } catch (\Throwable $e) {
                    echo '<div class="check"><span class="fail">❌</span> Database query failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } else {
                echo '<div class="check"><span class="fail">❌</span> Database not available</div>';
            }
            ?>
        </div>

        <div class="section">
            <h2>9. Occupant Create Page Debug</h2>
            <?php
            // ── FORCE MIGRATION: add missing next_of_kin columns ──────────────
            echo '<h4><strong>0. Force Migration — next_of_kin columns</strong></h4>';
            if ($db) {
                try {
                    $pdo = $db->getConnection();
                    $migrations = [
                        'next_of_kin'         => "VARCHAR(255) NULL",
                        'next_of_kin_phone'   => "VARCHAR(50) NULL",
                        'next_of_kin_address' => "TEXT NULL",
                    ];
                    foreach ($migrations as $col => $def) {
                        $chk = $pdo->prepare("
                            SELECT COUNT(*) FROM information_schema.COLUMNS
                            WHERE TABLE_SCHEMA = DATABASE()
                              AND TABLE_NAME   = 'tenants'
                              AND COLUMN_NAME  = ?
                        ");
                        $chk->execute([$col]);
                        if ((int)$chk->fetchColumn() === 0) {
                            $pdo->exec(
                                "ALTER TABLE tenants ADD COLUMN `{$col}` {$def}"
                            );
                            echo '<p style="color:green">✅ CREATED column: '
                                 . $col . '</p>';
                        } else {
                            echo '<p>✅ Already exists: ' . $col . '</p>';
                        }
                    }
                } catch (\Throwable $e) {
                    echo '<p style="color:red">❌ Migration error: '
                         . htmlspecialchars($e->getMessage()) . '</p>';
                }
            } else {
                echo '<p style="color:red">❌ No DB connection — migration skipped</p>';
            }

            // 1. Column existence check
            echo '<h3>1. Tenants table next_of_kin columns check</h3>';
            if ($db) {
                try {
                    $pdo = $db->getConnection();
                    $stmt = $pdo->query("SHOW COLUMNS FROM tenants LIKE 'next_of_kin%'");
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo '<table>';
                    echo '<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Status</th></tr>';
                    
                    $nextOfKinExists = false;
                    $nextOfKinPhoneExists = false;
                    $nextOfKinAddressExists = false;
                    
                    foreach ($columns as $col) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($col['Field']) . '</td>';
                        echo '<td>' . htmlspecialchars($col['Type']) . '</td>';
                        echo '<td>' . htmlspecialchars($col['Null']) . '</td>';
                        
                        if ($col['Field'] === 'next_of_kin') {
                            $nextOfKinExists = true;
                            echo '<td><span class="pass">✅ next_of_kin</span></td>';
                        } elseif ($col['Field'] === 'next_of_kin_phone') {
                            $nextOfKinPhoneExists = true;
                            echo '<td><span class="pass">✅ next_of_kin_phone</span></td>';
                        } elseif ($col['Field'] === 'next_of_kin_address') {
                            $nextOfKinAddressExists = true;
                            echo '<td><span class="pass">✅ next_of_kin_address</span></td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        echo '</tr>';
                    }
                    echo '</table>';
                    
                    if (!$nextOfKinExists) {
                        echo '<div class="check"><span class="fail">❌ next_of_kin column missing</span></div>';
                    }
                    if (!$nextOfKinPhoneExists) {
                        echo '<div class="check"><span class="fail">❌ next_of_kin_phone column missing</span></div>';
                    }
                    if (!$nextOfKinAddressExists) {
                        echo '<div class="check"><span class="fail">❌ next_of_kin_address column missing</span></div>';
                    }
                    
                } catch (\Throwable $e) {
                    echo '<div class="check"><span class="fail">❌ Could not check tenants table: ' . htmlspecialchars($e->getMessage()) . '</div></div>';
                }
            } else {
                echo '<p style="color:red">Database not available for column check</p>';
            }

            // 2. Upload directory check
            echo '<h3>2. Upload directory check</h3>';
            $uploadDir = __DIR__ . '/../public/uploads/documents';
            if (is_dir($uploadDir)) {
                if (is_writable($uploadDir)) {
                    echo '<div class="check"><span class="pass">✅</span> public/uploads/documents exists and writable</div>';
                } else {
                    echo '<div class="check"><span class="fail">❌</span> public/uploads/documents exists but not writable</div>';
                }
            } else {
                echo '<div class="check"><span class="fail">❌</span> public/uploads/documents does not exist</div>';
            }

            // 3. storeOccupant() implementation check
            echo '<h3>3. storeOccupant() implementation check</h3>';
            $controllerFile = file_get_contents(__DIR__ . '/../app/controllers/TenantOccupantController.php');
            
            $hasDbInsert = strpos($controllerFile, 'db->insert') !== false && strpos($controllerFile, 'storeOccupant') !== false;
            echo '<div class="check">';
            echo $hasDbInsert ? '<span class="pass">✅ IMPLEMENTED</span>' : '<span class="fail">❌ STUB</span>';
            echo ' db->insert exists in storeOccupant</div>';
            
            $hasNextOfKinAddress = strpos($controllerFile, 'next_of_kin_address') !== false;
            echo '<div class="check">';
            echo $hasNextOfKinAddress ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' next_of_kin_address in storeOccupant</div>';

            // 4. Form fields check
            echo '<h3>4. Form fields check</h3>';
            $createFile = file_get_contents(__DIR__ . '/../views/admin/occupants/create.php');
            
            $hasNextOfKinField = strpos($createFile, 'name="next_of_kin"') !== false;
            echo '<div class="check">';
            echo $hasNextOfKinField ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' next_of_kin input exists</div>';
            
            $hasNextOfKinPhoneField = strpos($createFile, 'name="next_of_kin_phone"') !== false;
            echo '<div class="check">';
            echo $hasNextOfKinPhoneField ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' next_of_kin_phone input exists</div>';
            
            $hasNextOfKinAddressField = strpos($createFile, 'name="next_of_kin_address"') !== false;
            echo '<div class="check">';
            echo $hasNextOfKinAddressField ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' next_of_kin_address textarea exists</div>';
            
            $hasCameraCaptureLogic = strpos($createFile, 'camera_capture_data') !== false;
            echo '<div class="check">';
            echo $hasCameraCaptureLogic ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' camera_capture_data hidden input logic exists</div>';
            
            $hasStartCamera = strpos($createFile, 'function startCamera') !== false;
            echo '<div class="check">';
            echo $hasStartCamera ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' startCamera function exists</div>';
            
            $hasEnctype = strpos($createFile, 'enctype="multipart/form-data"') !== false;
            echo '<div class="check">';
            echo $hasEnctype ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' enctype="multipart/form-data" on form</div>';

            // 5. Route check
            echo '<h3>5. Route check</h3>';
            $routesFile = file_get_contents(__DIR__ . '/../routes/web.php');
            
            $hasOccupantsRoute = strpos($routesFile, "'POST /admin/occupants' => 'TenantOccupantController@storeOccupant'") !== false;
            echo '<div class="check">';
            echo $hasOccupantsRoute ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' POST /admin/occupants → TenantOccupantController@storeOccupant</div>';
            ?>
        </div>
            <?php
            // 1. Tenants table structure check
            echo '<h3>1. Tenants table structure check</h3>';
            if ($db) {
                try {
                    $pdo = $db->getConnection();
                    $stmt = $pdo->query("SHOW COLUMNS FROM tenants");
                    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    
                    echo '<p><strong>Columns found:</strong> ' . implode(', ', $columns) . '</p>';
                    
                    if (in_array('unit_id', $columns)) {
                        echo '<div class="check"><span class="pass">✅</span> unit_id column exists</div>';
                        $joinColumn = 'unit_id';
                    } elseif (in_array('property_id', $columns)) {
                        echo '<div class="check"><span class="pass">✅</span> property_id column exists</div>';
                        $joinColumn = 'property_id';
                    } else {
                        echo '<div class="check"><span class="fail">❌</span> Neither unit_id nor property_id found — JOIN will fail</div>';
                        $joinColumn = null;
                    }
                } catch (\Throwable $e) {
                    echo '<div class="check"><span class="fail">❌</span> Could not check tenants table: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } else {
                echo '<p style="color:red">Database not available for table structure check</p>';
            }

            // 2. tenant_name in query check
            echo '<h3>2. tenant_name in query check</h3>';
            $controllerFile = file_get_contents(__DIR__ . '/../app/controllers/UnitController.php');
            
            $hasTenantNameInQuery = strpos($controllerFile, 'tenant_name') !== false;
            echo '<div class="check">';
            echo $hasTenantNameInQuery ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo " 'tenant_name' exists in getFilteredUnits() query</div>";
            
            $hasLeftJoinTenants = strpos($controllerFile, 'LEFT JOIN tenants') !== false;
            echo '<div class="check">';
            echo $hasLeftJoinTenants ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' LEFT JOIN tenants exists in getFilteredUnits()</div>';

            // 3. Defensive access check
            echo '<h3>3. Defensive access check</h3>';
            $listFile = file_get_contents(__DIR__ . '/../views/admin/units/list.php');
            
            $hasDefensiveAccess = strpos($listFile, '!empty($unit[\'tenant_name\'])') !== false || strpos($listFile, '$unit[\'tenant_name\'] ?? null') !== false;
            echo '<div class="check">';
            echo $hasDefensiveAccess ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' Defensive tenant_name access (!empty or ?? null)</div>';
            
            $hasUnguardedAccess = strpos($listFile, '$unit[\'tenant_name\'] ?:') !== false && strpos($listFile, '!empty($unit[\'tenant_name\'])') === false;
            echo '<div class="check">';
            echo !$hasUnguardedAccess ? '<span class="pass">✅</span>' : '<span class="fail">⚠️ UNGUARDED</span>';
            echo ' No unguarded $unit[\'tenant_name\'] still present</div>';

            // 4. Live test
            echo '<h3>4. Live test — query first 5 units with tenant join</h3>';
            if ($db && isset($joinColumn)) {
                try {
                    $pdo = $db->getConnection();
                    
                    // Updated query without status filter
                    $testQuery = "SELECT u.unit_number, u.status, t.name as tenant_name
                                  FROM units u
                                  LEFT JOIN tenants t
                                         ON t.unit_id = u.id
                                        AND t.deleted_at IS NULL
                                  WHERE u.deleted_at IS NULL
                                  LIMIT 5";
                    
                    $stmt = $pdo->query($testQuery);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($results) > 0) {
                        echo '<table>';
                        echo '<tr><th>Unit Number</th><th>Status</th><th>Tenant Name</th></tr>';
                        foreach ($results as $row) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['unit_number']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                            echo '<td>' . ($row['tenant_name'] ? htmlspecialchars($row['tenant_name']) : '<em>NULL</em>') . '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                        echo '<div class="check"><span class="pass">✅</span> Live query test successful</div>';
                    } else {
                        echo '<p>No units found for live test</p>';
                    }

                    // Show first 3 tenants raw to verify data exists
                    echo '<h3>5. Raw tenants data check</h3>';
                    $stmt = $pdo->query(
                        "SELECT id, name, unit_id, status FROM tenants
                         WHERE deleted_at IS NULL LIMIT 3"
                    );
                    $rawTenants = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    echo '<p><strong>Raw tenants sample:</strong></p>';
                    if ($rawTenants) {
                        echo '<table border="1" cellpadding="4">';
                        echo '<tr><th>ID</th><th>Name</th><th>unit_id</th><th>status</th></tr>';
                        foreach ($rawTenants as $t) {
                            echo '<tr>'
                                 . '<td>' . $t['id'] . '</td>'
                                 . '<td>' . htmlspecialchars($t['name'] ?? 'NULL') . '</td>'
                                 . '<td>' . ($t['unit_id'] ?? 'NULL') . '</td>'
                                 . '<td>' . htmlspecialchars($t['status'] ?? 'NULL') . '</td>'
                                 . '</tr>';
                        }
                        echo '</table>';
                    } else {
                        echo '<p>⚠️ No tenants found in database</p>';
                    }
                    
                } catch (\Throwable $e) {
                    echo '<div class="check"><span class="fail">❌</span> Live test failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } else {
                echo '<p style="color:red">Cannot perform live test — database unavailable or no join column found</p>';
            }
            ?>
        </div>

        <div class="section">
            <h2>10. Properties Filter Bar Alignment Debug</h2>
            <?php
            $propertiesListFile = file_get_contents(__DIR__ . '/../views/admin/properties/list.php');
            
            // Check search container has flex-1 min-w-0
            $hasSearchFlex = strpos($propertiesListFile, 'flex-1 min-w-0') !== false;
            echo '<div class="check">';
            echo $hasSearchFlex ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' flex-1 min-w-0 on search container</div>';
            
            // Check type_filter has flex-shrink-0
            $hasTypeFlexShrink = strpos($propertiesListFile, 'id="type_filter"') !== false && 
                                 strpos($propertiesListFile, 'flex-shrink-0', strpos($propertiesListFile, 'id="type_filter"')) !== false;
            echo '<div class="check">';
            echo $hasTypeFlexShrink ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' flex-shrink-0 on type_filter container</div>';
            
            // Check status_filter has flex-shrink-0
            $hasStatusFlexShrink = strpos($propertiesListFile, 'id="status_filter"') !== false && 
                                  strpos($propertiesListFile, 'flex-shrink-0', strpos($propertiesListFile, 'id="status_filter"')) !== false;
            echo '<div class="check">';
            echo $hasStatusFlexShrink ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' flex-shrink-0 on status_filter container</div>';
            
            // Check no labels before selects
            $typeLabelExists = strpos($propertiesListFile, '<label') !== false && 
                              strpos($propertiesListFile, 'type_filter', strpos($propertiesListFile, '<label')) !== false;
            $statusLabelExists = strpos($propertiesListFile, '<label') !== false && 
                                strpos($propertiesListFile, 'status_filter', strpos($propertiesListFile, '<label')) !== false;
            
            echo '<div class="check">';
            echo (!$typeLabelExists && !$statusLabelExists) ? '<span class="pass">✅ CLEAN</span>' : '<span class="fail">⚠️ LABELS STILL PRESENT</span>';
            echo ' No <label> elements immediately before type_filter or status_filter selects</div>';
            
            // Check type_filter ID exists
            $hasTypeId = strpos($propertiesListFile, 'id="type_filter"') !== false;
            echo '<div class="check">';
            echo $hasTypeId ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' id="type_filter" exists</div>';
            
            // Check status_filter ID exists
            $hasStatusId = strpos($propertiesListFile, 'id="status_filter"') !== false;
            echo '<div class="check">';
            echo $hasStatusId ? '<span class="pass">✅</span>' : '<span class="fail">❌</span>';
            echo ' id="status_filter" exists</div>';
            ?>
        </div>

        <div class="summary">
            <h2>Summary</h2>
            <?php
            $totalChecks = 25;
            $passedChecks = ($hasUnitsIndex ? 1 : 0) + ($hasUnitsCreate ? 1 : 0) + 
                           ($hasPropertyUnitsIndex ? 1 : 0) + ($hasPropertyUnitsCreate ? 1 : 0) +
                           ($hasIndex ? 1 : 0) + ($hasIndexByProperty ? 1 : 0) + 
                           ($hasCreateForProperty ? 1 : 0) + ($hasStore ? 1 : 0) +
                           ($hasUnitsList ? 1 : 0) + ($hasUnitsCreate ? 1 : 0) +
                           ($hasCorrectUrl ? 1 : 0) + (!$hasOldUrl ? 1 : 0) +
                           ($hasCorrectOrder ? 1 : 0) + ($noDuplicate ? 1 : 0) +
                           ($hasFlexLayout ? 1 : 0) + ($hasExportFunction ? 1 : 0) +
                           ($hasNairaList ? 1 : 0) + ($hasNairaCreate ? 1 : 0) +
                           ($hasSearchFlex ? 1 : 0) + ($hasTypeFlexShrink ? 1 : 0) + 
                           ($hasStatusFlexShrink ? 1 : 0) + ((!$typeLabelExists && !$statusLabelExists) ? 1 : 0) +
                           ($hasTypeId ? 1 : 0) + ($hasStatusId ? 1 : 0);
            
            $successRate = round(($passedChecks / $totalChecks) * 100, 1);
            
            echo '<p><strong>Total Checks:</strong> ' . $totalChecks . '</p>';
            echo '<p><strong>Passed:</strong> <span class="pass">' . $passedChecks . '</span></p>';
            echo '<p><strong>Failed:</strong> <span class="fail">' . ($totalChecks - $passedChecks) . '</span></p>';
            echo '<p><strong>Success Rate:</strong> <strong>' . $successRate . '%</strong></p>';
            
            if ($successRate >= 90) {
                echo '<p><span class="pass">🎉 EXCELLENT! Units functionality is fully implemented.</span></p>';
            } elseif ($successRate >= 70) {
                echo '<p><span class="pass">✅ GOOD! Most units functionality is working.</span></p>';
            } else {
                echo '<p><span class="fail">⚠️ NEEDS WORK! Several issues need to be fixed.</span></p>';
            }
            ?>
        </div>

        <div class="section">
            <h2>11. Tenants Create Page Debug</h2>
            <?php
            // Check for enctype attribute
            $createFile = file_get_contents(__DIR__ . '/../views/admin/tenants/create.php');
            
            $hasEnctype = strpos($createFile, 'enctype="multipart/form-data"') !== false;
            echo '<div class="check">';
            echo $hasEnctype ? '<span class="pass">✅ PASS</span>' : '<span class="fail">❌ FAIL</span>';
            echo ' enctype="multipart/form-data" found</div>';
            
            // Check for mobile layout in Form Actions
            $hasMobileLayout = strpos($createFile, 'flex-col sm:flex-row sm:justify-between') !== false;
            echo '<div class="check">';
            echo $hasMobileLayout ? '<span class="pass">✅ MOBILE FIXED</span>' : '<span class="fail">❌ FAIL</span>';
            echo ' Mobile layout in Form Actions</div>';
            
            // Check if AttachmentComponent is still present
            $hasAttachmentComponent = strpos($createFile, 'AttachmentComponent::renderUploadArea') !== false;
            echo '<div class="check">';
            echo !$hasAttachmentComponent ? '<span class="pass">✅ REMOVED</span>' : '<span class="fail">⚠️ STILL THERE</span>';
            echo ' AttachmentComponent::renderUploadArea removed</div>';
            
            $hasAttachmentJS = strpos($createFile, 'AttachmentComponentJS::renderJS') !== false;
            echo '<div class="check">';
            echo !$hasAttachmentJS ? '<span class="pass">✅ REMOVED</span>' : '<span class="fail">⚠️ STILL THERE</span>';
            echo ' AttachmentComponentJS::renderJS removed</div>';
            
            // Check for camera functions
            $hasCameraFunction = strpos($createFile, 'function tenantStartCamera') !== false;
            echo '<div class="check">';
            echo $hasCameraFunction ? '<span class="pass">✅ PASS</span>' : '<span class="fail">❌ FAIL</span>';
            echo ' tenantStartCamera function exists</div>';
            
            // Check for Next of Kin fields
            $hasNextOfKin = strpos($createFile, 'name="next_of_kin"') !== false;
            echo '<div class="check">';
            echo $hasNextOfKin ? '<span class="pass">✅ PASS</span>' : '<span class="fail">❌ FAIL</span>';
            echo ' next_of_kin input exists</div>';
            
            $hasNextOfKinPhone = strpos($createFile, 'name="next_of_kin_phone"') !== false;
            echo '<div class="check">';
            echo $hasNextOfKinPhone ? '<span class="pass">✅ PASS</span>' : '<span class="fail">❌ FAIL</span>';
            echo ' next_of_kin_phone input exists</div>';
            
            $hasNextOfKinAddress = strpos($createFile, 'name="next_of_kin_address"') !== false;
            echo '<div class="check">';
            echo $hasNextOfKinAddress ? '<span class="pass">✅ PASS</span>' : '<span class="fail">❌ FAIL</span>';
            echo ' next_of_kin_address textarea exists</div>';
            ?>
        </div>

        <div class="summary">
            <h2>Summary</h2>
            <?php
            $totalChecks = 26;
            $passedChecks = ($hasUnitsIndex ? 1 : 0) + ($hasUnitsCreate ? 1 : 0) + 
                           ($hasPropertyUnitsIndex ? 1 : 0) + ($hasPropertyUnitsCreate ? 1 : 0) +
                           ($hasIndex ? 1 : 0) + ($hasIndexByProperty ? 1 : 0) + 
                           ($hasCreateForProperty ? 1 : 0) + ($hasStore ? 1 : 0) +
                           ($hasUnitsList ? 1 : 0) + ($hasUnitsCreate ? 1 : 0) +
                           ($hasCorrectUrl ? 1 : 0) + (!$hasOldUrl ? 1 : 0) +
                           ($hasCorrectOrder ? 1 : 0) + ($noDuplicate ? 1 : 0) +
                           ($hasFlexLayout ? 1 : 0) + ($hasExportFunction ? 1 : 0) +
                           ($hasNairaList ? 1 : 0) + ($hasNairaCreate ? 1 : 0) +
                           ($hasSearchFlex ? 1 : 0) + ($hasTypeFlexShrink ? 1 : 0) + 
                           ($hasStatusFlexShrink ? 1 : 0) + ((!$typeLabelExists && !$statusLabelExists) ? 1 : 0) +
                           ($hasTypeId ? 1 : 0) + ($hasStatusId ? 1 : 0) +
                           ($hasEnctype ? 1 : 0) + ($hasMobileLayout ? 1 : 0) +
                           (!$hasAttachmentComponent ? 1 : 0) + (!$hasAttachmentJS ? 1 : 0) +
                           ($hasCameraFunction ? 1 : 0) + ($hasNextOfKin ? 1 : 0) +
                           ($hasNextOfKinPhone ? 1 : 0) + ($hasNextOfKinAddress ? 1 : 0);
            
            $successRate = round(($passedChecks / $totalChecks) * 100, 1);
            
            echo '<p><strong>Total Checks:</strong> ' . $totalChecks . '</p>';
            echo '<p><strong>Passed:</strong> <span class="pass">' . $passedChecks . '</span></p>';
            echo '<p><strong>Failed:</strong> <span class="fail">' . ($totalChecks - $passedChecks) . '</span></p>';
            echo '<p><strong>Success Rate:</strong> <strong>' . $successRate . '%</strong></p>';
            
            if ($successRate >= 90) {
                echo '<p><span class="pass">🎉 EXCELLENT! All functionality is fully implemented.</span></p>';
            } elseif ($successRate >= 70) {
                echo '<p><span class="pass">✅ GOOD! Most functionality is working.</span></p>';
            } else {
                echo '<p><span class="fail">⚠️ NEEDS WORK! Several issues need to be fixed.</span></p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
