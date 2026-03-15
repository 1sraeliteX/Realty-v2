<?php

namespace Components;

class AutoFillComponent {
    
    /**
     * Generate auto-fill button and JavaScript for a form
     * @param string $formId The ID of the form to auto-fill
     * @param array $fillData The data to fill the form with
     * @param string $buttonText Text for the auto-fill button
     * @param string $buttonStyle CSS classes for the button
     */
    public static function generateAutoFillButton($formId, $fillData, $buttonText = 'Auto-Fill Form', $buttonStyle = 'bg-purple-600 hover:bg-purple-700 text-white') {
        ?>
        <!-- Auto-Fill Button -->
        <div class="mb-4 flex justify-end">
            <button type="button" 
                    onclick="autoFillForm('<?php echo $formId; ?>', <?php echo json_encode($fillData); ?>)" 
                    class="<?php echo $buttonStyle; ?> px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-magic"></i>
                <?php echo $buttonText; ?>
            </button>
        </div>
        
        <!-- Auto-Fill JavaScript -->
        <script>
        function autoFillForm(formId, data) {
            const form = document.getElementById(formId);
            if (!form) {
                console.error('Form not found:', formId);
                return;
            }
            
            // Fill all input fields
            Object.keys(data).forEach(key => {
                if (key === 'amenities') {
                    // Handle amenities checkboxes
                    const amenities = data[key];
                    const amenityCheckboxes = form.querySelectorAll('input[name="amenities[]"]');
                    amenityCheckboxes.forEach(checkbox => {
                        checkbox.checked = amenities.includes(checkbox.value);
                    });
                } else {
                    const field = form.querySelector(`[name="${key}"], [id="${key}"]`);
                    if (field) {
                        if (field.type === 'checkbox' || field.type === 'radio') {
                            field.checked = data[key];
                        } else if (field.tagName === 'SELECT') {
                            field.value = data[key];
                            // Trigger change event for select elements
                            field.dispatchEvent(new Event('change'));
                        } else if (field.tagName === 'TEXTAREA') {
                            field.value = data[key];
                        } else {
                            field.value = data[key];
                        }
                        
                        // Trigger input event for fields that have listeners
                        field.dispatchEvent(new Event('input'));
                    }
                }
            });
            
            // Show success feedback
            showAutoFillSuccess();
        }
        
        function showAutoFillSuccess() {
            // Create success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
            notification.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>Form auto-filled successfully!</span>
            `;
            
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        </script>
        <?php
    }
    
    /**
     * Get predefined auto-fill data for property form
     */
    public static function getPropertyFillData() {
        return [
            'name' => 'Sunset Apartments - Unit ' . rand(100, 999),
            'address' => rand(100, 999) . ' Sunset Boulevard, Los Angeles, CA ' . rand(90001, 99999),
            'type' => array_rand(['apartment' => 'apartment', 'house' => 'house', 'commercial' => 'commercial']),
            'status' => 'active',
            'year_built' => rand(2015, 2023),
            'water_availability' => 'yes',
            'description' => 'Beautiful modern apartment with stunning city views. This spacious unit features hardwood floors, granite countertops, and stainless steel appliances. Located in the heart of downtown with easy access to shopping, dining, and entertainment.',
            'bedrooms' => rand(1, 4),
            'bathrooms' => rand(1, 3),
            'kitchens' => rand(1, 2),
            'parking' => 'yes',
            'category' => 'residential',
            'amenities' => [
                'Swimming Pool',
                'Air Conditioning',
                'Secured Parking',
                'Pet Friendly'
            ],
            // Monthly Revenue and Expenses (renamed pricing section)
            'monthly_revenue' => rand(18000, 42000) . '.00', // Updated to yearly amount
            'annual_expenses' => rand(20000, 50000) . '.00',
            'property_tax' => rand(200, 500) . '.00',
            'insurance' => rand(100, 300) . '.00',
            'maintenance_fee' => rand(50, 150) . '.00',
            // Rent Record Information
            'monthly_rent' => rand(1200, 2800) . '.00',
            'rent_frequency' => 'monthly',
            'security_deposit' => rand(1000, 2000) . '.00',
            'late_fee' => '50.00'
        ];
    }
    
    /**
     * Get predefined auto-fill data for tenant form
     */
    public static function getTenantFillData() {
        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Mary'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];
        
        return [
            'first_name' => $firstNames[array_rand($firstNames)],
            'last_name' => $lastNames[array_rand($lastNames)],
            'email' => 'tenant' . rand(100, 999) . '@example.com',
            'phone' => '(' . rand(200, 999) . ') ' . rand(200, 999) . '-' . rand(1000, 9999),
            'alternate_phone' => '(' . rand(200, 999) . ') ' . rand(200, 999) . '-' . rand(1000, 9999),
            'date_of_birth' => rand(1970, 1995) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
            'nationality' => 'United States',
            'occupation' => 'Software Engineer',
            'id_type' => 'driver_license',
            'id_number' => 'DL' . rand(1000000, 9999999),
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_phone' => '(' . rand(200, 999) . ') ' . rand(200, 999) . '-' . rand(1000, 9999),
            'emergency_contact_relationship' => 'spouse',
            'emergency_contact_email' => 'emergency@example.com',
            'status' => 'active'
        ];
    }
    
    /**
     * Get predefined auto-fill data for maintenance form
     */
    public static function getMaintenanceFillData() {
        return [
            'title' => 'Maintenance Request - ' . rand(100, 999),
            'description' => 'Please fix the issue in the apartment. The problem has been ongoing for a few days and needs immediate attention.',
            'priority' => 'medium',
            'category' => 'plumbing',
            'status' => 'pending'
        ];
    }
    
    /**
     * Get predefined auto-fill data for invoice form
     */
    public static function getInvoiceFillData() {
        return [
            'tenant_id' => rand(1, 3),
            'property_id' => rand(1, 3),
            'invoice_number' => 'INV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'issue_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'pending',
            'notes' => 'Monthly rent payment for the current month. Please ensure payment is made on or before the due date to avoid late fees.'
        ];
    }
}
