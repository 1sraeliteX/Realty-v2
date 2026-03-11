<?php

/**
 * Component Isolation Tests
 * Prevents regressions by testing component independence
 */

require_once __DIR__ . '/../config/init_framework.php';

class ComponentIsolationTest {
    private $results = [];
    
    public function runAllTests() {
        echo "🧪 Running Component Isolation Tests...\n\n";
        
        $this->testComponentRegistry();
        $this->testDataProvider();
        $this->testViewManager();
        $this->testDataIsolation();
        $this->testComponentIndependence();
        
        $this->printResults();
    }
    
    /**
     * Test Component Registry functionality
     */
    private function testComponentRegistry() {
        $this->results['component_registry'] = [
            'test_name' => 'Component Registry',
            'tests' => []
        ];
        
        try {
            // Test component loading
            ComponentRegistry::load('ui-components');
            $this->results['component_registry']['tests'][] = [
                'name' => 'Load UI Components',
                'status' => 'PASS',
                'message' => 'UI Components loaded successfully'
            ];
            
            // Test component info
            $info = ComponentRegistry::getInfo('ui-components');
            if ($info && isset($info['path'])) {
                $this->results['component_registry']['tests'][] = [
                    'name' => 'Get Component Info',
                    'status' => 'PASS',
                    'message' => 'Component info retrieved'
                ];
            } else {
                throw new Exception('Component info not found');
            }
            
            // Test dependency loading
            ComponentRegistry::load('searchable-dropdown');
            $this->results['component_registry']['tests'][] = [
                'name' => 'Load Component with Dependencies',
                'status' => 'PASS',
                'message' => 'Component with dependencies loaded'
            ];
            
        } catch (Exception $e) {
            $this->results['component_registry']['tests'][] = [
                'name' => 'Component Registry Error',
                'status' => 'FAIL',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test Data Provider functionality
     */
    private function testDataProvider() {
        $this->results['data_provider'] = [
            'test_name' => 'Data Provider',
            'tests' => []
        ];
        
        try {
            // Test data retrieval
            $properties = DataProvider::get('properties');
            if (is_array($properties) && count($properties) > 0) {
                $this->results['data_provider']['tests'][] = [
                    'name' => 'Get Properties Data',
                    'status' => 'PASS',
                    'message' => 'Retrieved ' . count($properties) . ' properties'
                ];
            } else {
                throw new Exception('No properties data found');
            }
            
            // Test data setting
            DataProvider::set('test_key', 'test_value');
            $value = DataProvider::get('test_key');
            if ($value === 'test_value') {
                $this->results['data_provider']['tests'][] = [
                    'name' => 'Set and Get Data',
                    'status' => 'PASS',
                    'message' => 'Data set and retrieved successfully'
                ];
            } else {
                throw new Exception('Data set/get failed');
            }
            
            // Test default values
            $nonExistent = DataProvider::get('non_existent', 'default');
            if ($nonExistent === 'default') {
                $this->results['data_provider']['tests'][] = [
                    'name' => 'Default Value Handling',
                    'status' => 'PASS',
                    'message' => 'Default values work correctly'
                ];
            } else {
                throw new Exception('Default value handling failed');
            }
            
        } catch (Exception $e) {
            $this->results['data_provider']['tests'][] = [
                'name' => 'Data Provider Error',
                'status' => 'FAIL',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test View Manager functionality
     */
    private function testViewManager() {
        $this->results['view_manager'] = [
            'test_name' => 'View Manager',
            'tests' => []
        ];
        
        try {
            // Test data setting
            ViewManager::set('test_var', 'test_value');
            $value = ViewManager::get('test_var');
            if ($value === 'test_value') {
                $this->results['view_manager']['tests'][] = [
                    'name' => 'View Manager Data',
                    'status' => 'PASS',
                    'message' => 'View manager data set/get works'
                ];
            } else {
                throw new Exception('View manager data failed');
            }
            
            // Test layout setting
            ViewManager::setLayout('admin.dashboard_layout');
            $this->results['view_manager']['tests'][] = [
                'name' => 'Layout Setting',
                'status' => 'PASS',
                'message' => 'Layout set successfully'
            ];
            
            // Test component rendering
            $component = ViewManager::component('ui-components.button', [
                'text' => 'Test Button',
                'type' => 'primary'
            ]);
            if (strpos($component, 'Test Button') !== false) {
                $this->results['view_manager']['tests'][] = [
                    'name' => 'Component Rendering',
                    'status' => 'PASS',
                    'message' => 'Component rendered successfully'
                ];
            } else {
                throw new Exception('Component rendering failed');
            }
            
        } catch (Exception $e) {
            $this->results['view_manager']['tests'][] = [
                'name' => 'View Manager Error',
                'status' => 'FAIL',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test data isolation
     */
    private function testDataIsolation() {
        $this->results['data_isolation'] = [
            'test_name' => 'Data Isolation',
            'tests' => []
        ];
        
        try {
            // Store original data
            $originalProperties = DataProvider::get('properties');
            $originalCount = count($originalProperties);
            
            // Modify data
            $newProperty = ['id' => 999, 'name' => 'Test Property'];
            DataProvider::set('properties', array_merge($originalProperties, [$newProperty]));
            
            // Check modification
            $modifiedProperties = DataProvider::get('properties');
            if (count($modifiedProperties) === $originalCount + 1) {
                $this->results['data_isolation']['tests'][] = [
                    'name' => 'Data Modification',
                    'status' => 'PASS',
                    'message' => 'Data can be modified safely'
                ];
            } else {
                throw new Exception('Data modification failed');
            }
            
            // Restore original data
            DataProvider::set('properties', $originalProperties);
            $restoredProperties = DataProvider::get('properties');
            
            if (count($restoredProperties) === $originalCount) {
                $this->results['data_isolation']['tests'][] = [
                    'name' => 'Data Restoration',
                    'status' => 'PASS',
                    'message' => 'Data can be restored safely'
                ];
            } else {
                throw new Exception('Data restoration failed');
            }
            
        } catch (Exception $e) {
            $this->results['data_isolation']['tests'][] = [
                'name' => 'Data Isolation Error',
                'status' => 'FAIL',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test component independence
     */
    private function testComponentIndependence() {
        $this->results['component_independence'] = [
            'test_name' => 'Component Independence',
            'tests' => []
        ];
        
        try {
            // Test UI Components alone
            ComponentRegistry::load('ui-components');
            $button = UIComponents::button('Test', 'primary');
            if (strpos($button, 'Test') !== false) {
                $this->results['component_independence']['tests'][] = [
                    'name' => 'UI Components Independence',
                    'status' => 'PASS',
                    'message' => 'UI Components work independently'
                ];
            } else {
                throw new Exception('UI Components independence failed');
            }
            
            // Test multiple components don't interfere
            $card = UIComponents::card('Test Content');
            $badge = UIComponents::badge('Test Badge');
            
            if (strpos($card, 'Test Content') !== false && strpos($badge, 'Test Badge') !== false) {
                $this->results['component_independence']['tests'][] = [
                    'name' => 'Multiple Components',
                    'status' => 'PASS',
                    'message' => 'Multiple components work without interference'
                ];
            } else {
                throw new Exception('Multiple components failed');
            }
            
        } catch (Exception $e) {
            $this->results['component_independence']['tests'][] = [
                'name' => 'Component Independence Error',
                'status' => 'FAIL',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Print test results
     */
    private function printResults() {
        echo "\n📊 Test Results:\n";
        echo str_repeat("=", 50) . "\n";
        
        $totalTests = 0;
        $passedTests = 0;
        
        foreach ($this->results as $result) {
            echo "\n🔍 {$result['test_name']}:\n";
            
            foreach ($result['tests'] as $test) {
                $totalTests++;
                $status = $test['status'] === 'PASS' ? '✅' : '❌';
                echo "  $status {$test['name']}: {$test['message']}\n";
                
                if ($test['status'] === 'PASS') {
                    $passedTests++;
                }
            }
        }
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📈 Summary: $passedTests/$totalTests tests passed\n";
        
        if ($passedTests === $totalTests) {
            echo "🎉 All tests passed! Your components are properly isolated.\n";
        } else {
            echo "⚠️  Some tests failed. Check the results above.\n";
        }
    }
}

// Run tests if called directly
if (php_sapi_name() === 'cli') {
    $test = new ComponentIsolationTest();
    $test->runAllTests();
}
