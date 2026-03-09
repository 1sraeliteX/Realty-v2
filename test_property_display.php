<?php
require_once 'config/config_simple.php';
require_once 'config/database.php';
require_once 'app/controllers/BaseController.php';

use App\Controllers\BaseController;
use Config\Database;

// Simulate the PropertyController index method
class TestPropertyController extends BaseController {
    public function testIndex() {
        // Simulate admin user
        $admin = ['id' => 1];
        
        $page = 1;
        $search = '';
        $type = '';
        $category = '';
        $status = '';
        
        // Build query (same as PropertyController)
        $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
        $params = [$admin['id']];
        
        $whereClause = implode(' AND ', $where);
        
        // Get properties with unit counts
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                FROM properties p 
                WHERE {$whereClause}
                ORDER BY p.created_at DESC";
        
        $result = $this->paginate($sql, $page, 10, $params);
        
        echo "Properties for admin ID " . $admin['id'] . ": " . count($result['data']) . PHP_EOL;
        foreach($result['data'] as $property) {
            echo "- " . $property['name'] . " (Units: " . $property['unit_count'] . ", Occupied: " . $property['occupied_units'] . ")" . PHP_EOL;
        }
        
        return $result;
    }
    
    public function paginate($query, $page = 1, $limit = 10, $params = []) {
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM ({$query}) as count_table";
        $total = $this->db->fetch($countSql, $params)['total'];
        
        // Get paginated results
        $paginatedSql = $query . " LIMIT {$limit} OFFSET {$offset}";
        $data = $this->db->fetchAll($paginatedSql, $params);
        
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
                'has_prev' => $page > 1,
                'has_next' => $page < ceil($total / $limit)
            ]
        ];
    }
}

try {
    $controller = new TestPropertyController();
    $result = $controller->testIndex();
    echo PHP_EOL . "Pagination info:" . PHP_EOL;
    echo "- Total: " . $result['pagination']['total'] . PHP_EOL;
    echo "- Current page: " . $result['pagination']['current_page'] . PHP_EOL;
    echo "- Total pages: " . $result['pagination']['total_pages'] . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}
?>
