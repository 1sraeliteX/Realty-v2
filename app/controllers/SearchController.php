<?php

namespace App\Controllers;

class SearchController extends BaseController {
    
    public function search() {
        // Require admin authentication
        $admin = $this->requireAuth();
        
        // Get search query
        $query = $_GET['q'] ?? '';
        
        // Minimum 2 characters required
        if (strlen($query) < 2) {
            $this->json([
                'properties' => [],
                'tenants' => [],
                'units' => [],
                'payments' => []
            ]);
            return;
        }
        
        // Initialize models
        $propertyModel = new \App\Models\PropertyModel();
        $tenantModel = new \App\Models\TenantModel();
        $unitModel = new \App\Models\UnitModel();
        $paymentModel = new \App\Models\PaymentModel();
        
        // Get admin filter
        $adminId = $this->getAdminIdForQuery();
        
        try {
            // Search across all models
            $properties = $propertyModel->searchByKeyword($query, $adminId);
            $tenants = $tenantModel->searchByKeyword($query, $adminId);
            $units = $unitModel->searchByKeyword($query, $adminId);
            $payments = $paymentModel->searchByKeyword($query, $adminId);
            
            $this->json([
                'properties' => $properties,
                'tenants' => $tenants,
                'units' => $units,
                'payments' => $payments
            ]);
            
        } catch (\Exception $e) {
            error_log('SearchController::search error: ' . $e->getMessage());
            $this->json([
                'properties' => [],
                'tenants' => [],
                'units' => [],
                'payments' => []
            ], 500);
        }
    }
}
