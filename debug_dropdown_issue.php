<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropdown Debug - Step 1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Dropdown Debug - Is JavaScript Working?</h1>
        
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-4">Step 1: Check JavaScript Console</h2>
            <p class="text-sm text-gray-600 mb-4">Open browser developer tools (F12) and check Console tab for errors</p>
            
            <div id="console-output" class="bg-yellow-50 border border-yellow-200 rounded p-4">
                <h3 class="font-semibold mb-2">Console Output:</h3>
                <div id="console-messages" class="text-sm font-mono"></div>
            </div>
        </div>
        
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-4">Step 2: Manual Dropdown Test</h2>
            <p class="text-sm text-gray-600 mb-4">Click the button below to manually test dropdown toggle:</p>
            
            <button onclick="testDropdown()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Test Dropdown Toggle
            </button>
            
            <div id="test-results" class="mt-4 bg-gray-50 rounded p-4"></div>
        </div>
        
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-4">Step 3: Check Elements</h2>
            <p class="text-sm text-gray-600 mb-4">Information about dropdown elements:</p>
            <div id="element-info" class="text-sm"></div>
        </div>
    </div>

<script>
// Log console messages
const originalLog = console.log;
console.log = function(...args) {
    const messages = document.getElementById('console-messages');
    if (messages) {
        messages.innerHTML += args.join(' ') + '<br>';
    }
    originalLog.apply(console, args);
};

// Test dropdown functionality
function testDropdown() {
    console.log('Testing dropdown toggle...');
    
    // Check if dropdown elements exist
    const dropdown = document.querySelector('[id*="_dropdown"]');
    const searchInput = document.querySelector('[id*="_search"]');
    const toggleButton = document.querySelector('[id*="_dropdown_toggle"]');
    
    const results = document.getElementById('test-results');
    
    if (!dropdown || !searchInput || !toggleButton) {
        results.innerHTML = '<p class="text-red-600">❌ Dropdown elements not found!</p>';
        return;
    }
    
    results.innerHTML = `
        <div class="space-y-2">
            <p><strong>✅ Dropdown Found:</strong> ${dropdown.id}</p>
            <p><strong>✅ Search Input Found:</strong> ${searchInput.id}</p>
            <p><strong>✅ Toggle Button Found:</strong> ${toggleButton.id}</p>
            <p><strong>✅ Dropdown Classes:</strong> ${dropdown.className}</p>
            <p><strong>✅ Is Hidden:</strong> ${dropdown.classList.contains('hidden') ? 'Yes' : 'No'}</p>
        </div>
    `;
    
    // Test toggle
    console.log('Toggling dropdown...');
    toggleButton.click();
    
    setTimeout(() => {
        const isVisible = !dropdown.classList.contains('hidden');
        results.innerHTML += `
            <p><strong>🔄 After Toggle:</strong> Dropdown is ${isVisible ? 'VISIBLE' : 'HIDDEN'}</p>
        `;
        
        if (isVisible) {
            // Check options
            const options = dropdown.querySelectorAll('.dropdown-option');
            results.innerHTML += `<p><strong>📋 Options Found:</strong> ${options.length} options</p>`;
            
            if (options.length > 0) {
                results.innerHTML += `<p><strong>✅ First Option:</strong> ${options[0].textContent}</p>`;
            }
        }
    }, 100);
}

// Auto-check on page load
window.addEventListener('load', () => {
    console.log('Page loaded, checking dropdown elements...');
    
    setTimeout(() => {
        const dropdowns = document.querySelectorAll('[id*="_dropdown"]');
        console.log(`Found ${dropdowns.length} dropdown elements`);
        
        dropdowns.forEach((dropdown, index) => {
            console.log(`Dropdown ${index + 1}:`, {
                id: dropdown.id,
                classes: dropdown.className,
                hidden: dropdown.classList.contains('hidden'),
                hasOptions: dropdown.querySelectorAll('.dropdown-option').length > 0
            });
        });
    }, 500);
});
</script>

<style>
body { font-family: Arial, sans-serif; }
</style>
</body>
</html>
