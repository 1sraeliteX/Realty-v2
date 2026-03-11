<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minimal Dropdown Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Minimal Dropdown Test</h1>
        
        <!-- Test the exact same dropdown as in the form -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
            <div class="relative">
                <input type="text" id="test_search" placeholder="Search or select..." class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900">
                <input type="hidden" id="test_hidden" name="property_type">
                <button type="button" id="test_toggle" class="absolute right-2 top-2.5 text-gray-400">
                    <i class="fas fa-chevron-down"></i>
                </button>
                
                <!-- Dropdown with exact same structure -->
                <div id="test_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto hidden">
                    <div class="p-2">
                        <div class="dropdown-option px-3 py-2 cursor-pointer hover:bg-gray-100 rounded" data-value="apartment">
                            <div class="font-medium text-gray-900">Apartment</div>
                            <div class="text-sm text-gray-500">Self-contained housing unit</div>
                        </div>
                        <div class="dropdown-option px-3 py-2 cursor-pointer hover:bg-gray-100 rounded" data-value="flat">
                            <div class="font-medium text-gray-900">Flat</div>
                            <div class="text-sm text-gray-500">British term for apartment</div>
                        </div>
                        <div class="dropdown-option px-3 py-2 cursor-pointer hover:bg-gray-100 rounded" data-value="house">
                            <div class="font-medium text-gray-900">House</div>
                            <div class="text-sm text-gray-500">Single-family residential building</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <button onclick="testMinimalDropdown()" class="bg-blue-500 text-white px-4 py-2 rounded">Test Dropdown</button>
            <div id="test-output" class="mt-4 p-4 bg-gray-100 rounded"></div>
        </div>
    </div>

<script>
// Simplified test without the SearchableDropdown component
let isDropdownOpen = false;
const dropdown = document.getElementById('test_dropdown');
const searchInput = document.getElementById('test_search');
const hiddenInput = document.getElementById('test_hidden');
const toggleButton = document.getElementById('test_toggle');

function toggleDropdown() {
    isDropdownOpen = !isDropdownOpen;
    if (isDropdownOpen) {
        dropdown.classList.remove('hidden');
        console.log('Dropdown opened');
    } else {
        dropdown.classList.add('hidden');
        console.log('Dropdown closed');
    }
}

// Add event listeners
toggleButton.addEventListener('click', toggleDropdown);
searchInput.addEventListener('focus', () => {
    if (!isDropdownOpen) {
        toggleDropdown();
    }
});

// Close when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('#test_dropdown, #test_search, #test_toggle')) {
        isDropdownOpen = false;
        dropdown.classList.add('hidden');
    }
});

// Option selection
dropdown.querySelectorAll('.dropdown-option').forEach(option => {
    option.addEventListener('click', function() {
        const value = this.dataset.value;
        const label = this.querySelector('.font-medium').textContent;
        
        hiddenInput.value = value;
        searchInput.value = label;
        
        isDropdownOpen = false;
        dropdown.classList.add('hidden');
        
        document.getElementById('test-output').innerHTML = `
            <p class="text-green-600">✅ Selected: <strong>${label}</strong> (value: ${value})</p>
        `;
    });
});

document.addEventListener('DOMContentLoaded', () => {
    console.log('Minimal dropdown test loaded');
    console.log('Dropdown element:', dropdown);
    console.log('Search input:', searchInput);
    console.log('Toggle button:', toggleButton);
});
</script>

<style>
body { font-family: Arial, sans-serif; }
</style>
</body>
</html>
