<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Create Invoice');
ViewManager::set('user', [
    'name' => 'Admin User',
    'email' => 'admin@cornerstone.com',
    'avatar' => null
]);
ViewManager::set('notifications', []);

// Mock data for form (would come from DataProvider in production)
$tenants = DataProvider::get('tenants', [
    ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'property_name' => 'Sunset Apartments', 'unit_number' => 'A-101'],
    ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Smith', 'property_name' => 'Ocean View Condos', 'unit_number' => 'B-201'],
    ['id' => 3, 'first_name' => 'Mike', 'last_name' => 'Johnson', 'property_name' => 'Mountain Heights', 'unit_number' => 'C-301']
]);

$properties = DataProvider::get('properties', [
    ['id' => 1, 'name' => 'Sunset Apartments'],
    ['id' => 2, 'name' => 'Ocean View Condos'],
    ['id' => 3, 'name' => 'Mountain Heights']
]);

ob_start();
?>

<!-- Breadcrumb -->
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/admin/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="/admin/invoices" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 md:ml-2">
                        Invoices
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">
                        Create Invoice
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Form Container -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
    <form id="invoiceForm" onsubmit="submitInvoiceForm(event)">
        <div class="p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Invoice Information</h2>
                <p class="text-gray-600 dark:text-gray-400">Enter the invoice details and line items</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tenant *</label>
                    <select name="tenant_id" required onchange="updateTenantInfo()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="">Select Tenant</option>
                        <?php foreach ($tenants as $tenant): ?>
                            <option value="<?php echo $tenant['id']; ?>" data-property="<?php echo htmlspecialchars($tenant['property_name']); ?>" data-unit="<?php echo htmlspecialchars($tenant['unit_number']); ?>">
                                <?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?> - <?php echo htmlspecialchars($tenant['property_name']); ?> <?php echo htmlspecialchars($tenant['unit_number']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invoice Number</label>
                    <input type="text" name="invoice_number" value="INV-<?php echo date('Y-m-'); ?>001" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invoice Date *</label>
                    <input type="date" name="invoice_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Due Date *</label>
                    <input type="date" name="due_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="draft" selected>Draft</option>
                        <option value="sent">Sent</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Terms</label>
                    <select name="payment_terms" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="net15">Net 15</option>
                        <option value="net30" selected>Net 30</option>
                        <option value="net45">Net 45</option>
                        <option value="net60">Net 60</option>
                        <option value="due_on_receipt">Due on Receipt</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Line Items Section -->
        <div class="border-t border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Line Items</h3>
                    <p class="text-gray-600 dark:text-gray-400">Add invoice line items</p>
                </div>

                <div id="lineItems" class="space-y-4">
                    <!-- Default Line Item -->
                    <div class="line-item grid grid-cols-1 md:grid-cols-5 gap-4 items-start">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description *</label>
                            <input type="text" name="items[0][description]" required value="Monthly Rent" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity *</label>
                            <input type="number" name="items[0][quantity]" required min="1" value="1" onchange="calculateLineTotal(this)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit Price *</label>
                            <input type="number" name="items[0][unit_price]" required min="0" step="0.01" value="1200.00" onchange="calculateLineTotal(this)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total</label>
                            <div class="flex items-center space-x-2">
                                <input type="number" name="items[0][total]" readonly value="1200.00" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white">
                                <button type="button" onclick="removeLineItem(this)" class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="addLineItem()" class="mt-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i> Add Line Item
                </button>

                <!-- Invoice Summary -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <p>Subtotal: <span id="subtotal" class="font-medium">$1,200.00</span></p>
                            <p>Tax (10%): <span id="tax" class="font-medium">$120.00</span></p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">Total: <span id="total">$1,320.00</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="border-t border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Additional Information</h3>
                    <p class="text-gray-600 dark:text-gray-400">Notes and payment instructions</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                        <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-none" placeholder="Additional notes about the invoice..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Instructions</label>
                        <textarea name="payment_instructions" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-none" placeholder="Payment instructions for the tenant..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-between">
            <a href="/admin/invoices" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Cancel
            </a>
            <div class="space-x-3">
                <button type="button" onclick="saveAsDraft()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    Save as Draft
                </button>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-check mr-2"></i> Create Invoice
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as default for invoice date
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="invoice_date"]').value = today;
    
    // Set due date to 30 days from today
    const thirtyDaysLater = new Date();
    thirtyDaysLater.setDate(thirtyDaysLater.getDate() + 30);
    document.querySelector('input[name="due_date"]').value = thirtyDaysLater.toISOString().split('T')[0];
    
    // Calculate initial totals
    calculateTotals();
});

let lineItemCount = 1;

function addLineItem() {
    const lineItemsContainer = document.getElementById('lineItems');
    const newItem = document.createElement('div');
    newItem.className = 'line-item grid grid-cols-1 md:grid-cols-5 gap-4 items-start';
    newItem.innerHTML = `
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description *</label>
            <input type="text" name="items[${lineItemCount}][description]" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors" placeholder="Enter description">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity *</label>
            <input type="number" name="items[${lineItemCount}][quantity]" required min="1" value="1" onchange="calculateLineTotal(this)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit Price *</label>
            <input type="number" name="items[${lineItemCount}][unit_price]" required min="0" step="0.01" value="0.00" onchange="calculateLineTotal(this)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total</label>
            <div class="flex items-center space-x-2">
                <input type="number" name="items[${lineItemCount}][total]" readonly value="0.00" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white">
                <button type="button" onclick="removeLineItem(this)" class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    lineItemsContainer.appendChild(newItem);
    lineItemCount++;
}

function removeLineItem(button) {
    const lineItem = button.closest('.line-item');
    if (document.querySelectorAll('.line-item').length > 1) {
        lineItem.remove();
        calculateTotals();
    } else {
        showToast('Invoice must have at least one line item.', 'error');
    }
}

function calculateLineTotal(input) {
    const lineItem = input.closest('.line-item');
    const quantity = parseFloat(lineItem.querySelector('input[name*="[quantity]"]').value) || 0;
    const unitPrice = parseFloat(lineItem.querySelector('input[name*="[unit_price]"]').value) || 0;
    const total = quantity * unitPrice;
    
    lineItem.querySelector('input[name*="[total]"]').value = total.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    const totals = Array.from(document.querySelectorAll('input[name*="[total]"]')).map(input => parseFloat(input.value) || 0);
    const subtotal = totals.reduce((sum, total) => sum + total, 0);
    const tax = subtotal * 0.1; // 10% tax
    const total = subtotal + tax;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    document.getElementById('tax').textContent = '$' + tax.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    document.getElementById('total').textContent = '$' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function updateTenantInfo() {
    const tenantSelect = document.querySelector('select[name="tenant_id"]');
    const selectedOption = tenantSelect.options[tenantSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        // You could auto-populate rent amount based on tenant here
        const propertyName = selectedOption.dataset.property;
        const unitNumber = selectedOption.dataset.unit;
        console.log('Selected tenant:', selectedOption.text, 'Property:', propertyName, 'Unit:', unitNumber);
    }
}

function submitInvoiceForm(event) {
    event.preventDefault();
    
    // Basic validation
    const form = event.target;
    const formData = new FormData(form);
    
    // Check required fields
    const requiredFields = ['tenant_id', 'invoice_date', 'due_date', 'status'];
    for (const field of requiredFields) {
        if (!formData.get(field)) {
            showToast('Please fill in all required fields.', 'error');
            return;
        }
    }
    
    // Check if at least one line item is filled
    const lineItems = Array.from(document.querySelectorAll('.line-item'));
    let hasValidLineItem = false;
    
    for (const item of lineItems) {
        const description = item.querySelector('input[name*="[description]"]').value;
        const quantity = item.querySelector('input[name*="[quantity]"]').value;
        const unitPrice = item.querySelector('input[name*="[unit_price]"]').value;
        
        if (description && quantity && unitPrice) {
            hasValidLineItem = true;
            break;
        }
    }
    
    if (!hasValidLineItem) {
        showToast('Please add at least one complete line item.', 'error');
        return;
    }
    
    // Date validation
    const invoiceDate = new Date(formData.get('invoice_date'));
    const dueDate = new Date(formData.get('due_date'));
    
    if (dueDate <= invoiceDate) {
        showToast('Due date must be after invoice date.', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Show success message
        showToast('Invoice created successfully!', 'success');
        
        // Redirect after a short delay
        setTimeout(() => {
            window.location.href = '/admin/invoices';
        }, 1500);
    }, 1500);
}

function saveAsDraft() {
    showToast('Draft saved successfully!', 'info');
}
</script>

<?php
// Capture content
$content = ob_get_clean();

// Set content for layout
ViewManager::set('content', $content);

// Render using the dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
