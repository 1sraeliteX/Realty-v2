<?php
// Test icons in the context of the main application
$title = 'Icon Debug Test';
$content = ob_start();
?>

<div class="p-8">
    <h1 class="text-3xl font-bold mb-6">Font Awesome Icon Debug Test</h1>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Icon Tests:</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 border rounded">
                <i class="fas fa-building text-3xl text-blue-600 mb-2"></i>
                <p class="text-sm">Building</p>
            </div>
            
            <div class="text-center p-4 border rounded">
                <i class="fas fa-home text-3xl text-green-600 mb-2"></i>
                <p class="text-sm">Home</p>
            </div>
            
            <div class="text-center p-4 border rounded">
                <i class="fas fa-users text-3xl text-purple-600 mb-2"></i>
                <p class="text-sm">Users</p>
            </div>
            
            <div class="text-center p-4 border rounded">
                <i class="fas fa-cog text-3xl text-gray-600 mb-2"></i>
                <p class="text-sm">Settings</p>
            </div>
            
            <div class="text-center p-4 border rounded">
                <i class="fas fa-search text-3xl text-red-600 mb-2"></i>
                <p class="text-sm">Search</p>
            </div>
            
            <div class="text-center p-4 border rounded">
                <i class="fas fa-plus text-3xl text-indigo-600 mb-2"></i>
                <p class="text-sm">Plus</p>
            </div>
            
            <div class="text-center p-4 border rounded">
                <i class="fas fa-edit text-3xl text-yellow-600 mb-2"></i>
                <p class="text-sm">Edit</p>
            </div>
            
            <div class="text-center p-4 border rounded">
                <i class="fas fa-trash text-3xl text-red-600 mb-2"></i>
                <p class="text-sm">Delete</p>
            </div>
        </div>
    </div>
    
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
        <h3 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Debug Information:</h3>
        <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
            <li>✅ All layout files include Font Awesome CDN</li>
            <li>✅ CDN URL: https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css</li>
            <li>✅ Using main layout.php which has Font Awesome</li>
            <li>✅ If you see icons above, Font Awesome is working correctly</li>
        </ul>
    </div>
    
    <div class="mt-6">
        <a href="/admin/login" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Login
        </a>
    </div>
</div>

<script>
// Test if Font Awesome loaded
setTimeout(() => {
    const icons = document.querySelectorAll('.fas');
    let loaded = 0;
    let failed = [];
    
    icons.forEach((icon, index) => {
        const styles = window.getComputedStyle(icon);
        if (styles.fontFamily.includes('Font Awesome') || styles.fontFamily.includes('FontAwesome')) {
            loaded++;
        } else {
            failed.push(icon.className);
        }
    });
    
    const resultDiv = document.createElement('div');
    resultDiv.className = 'mt-6 p-4 rounded-lg';
    if (loaded === icons.length) {
        resultDiv.className += ' bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800';
        resultDiv.innerHTML = `
            <h3 class="font-semibold text-green-800 dark:text-green-200">✅ Font Awesome Loaded Successfully!</h3>
            <p class="text-sm text-green-700 dark:text-green-300">All ${loaded} icons loaded correctly.</p>
        `;
    } else {
        resultDiv.className += ' bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800';
        resultDiv.innerHTML = `
            <h3 class="font-semibold text-red-800 dark:text-red-200">❌ Font Awesome Failed to Load</h3>
            <p class="text-sm text-red-700 dark:text-red-300">Only ${loaded}/${icons.length} icons loaded. Failed: ${failed.join(', ')}</p>
        `;
    }
    
    document.querySelector('.bg-white').appendChild(resultDiv);
}, 1000);
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
