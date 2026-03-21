<!DOCTYPE html>
<html>
<head>
    <title>Navbar JavaScript Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <h1 class="text-2xl font-bold mb-4">Navbar JavaScript Test</h1>
    
    <div class="bg-white p-4 rounded shadow mb-4">
        <h2 class="font-bold mb-2">Test Navigation Links:</h2>
        <div class="space-y-2">
            <a href="/admin/dashboard" class="block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Dashboard</a>
            <a href="/admin/properties" class="block px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Properties</a>
            <a href="/admin/tenants" class="block px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">Tenants</a>
            <a href="/admin/maintenance" class="block px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">Maintenance</a>
        </div>
    </div>
    
    <div class="bg-white p-4 rounded shadow mb-4">
        <h2 class="font-bold mb-2">JavaScript Console Test:</h2>
        <button onclick="testLinks()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Test Link Clicks</button>
        <div id="test-result" class="mt-2"></div>
    </div>
    
    <script>
        function testLinks() {
            const result = document.getElementById('test-result');
            result.innerHTML = '<p>Testing links...</p>';
            
            // Test if links exist
            const links = document.querySelectorAll('a[href*="/admin/"]');
            result.innerHTML = `<p>Found ${links.length} admin links</p>`;
            
            // Add click event listeners
            links.forEach((link, index) => {
                link.addEventListener('click', function(e) {
                    console.log('Link clicked:', this.href);
                    result.innerHTML += `<p>Link ${index + 1} clicked: ${this.href}</p>`;
                });
            });
            
            result.innerHTML += '<p>Event listeners added. Click the links above to test.</p>';
        }
        
        // Test page load
        console.log('Navbar test page loaded');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded');
            const result = document.getElementById('test-result');
            result.innerHTML = '<p>✅ JavaScript is working</p>';
        });
    </script>
    
    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-bold mb-2">Quick Actions:</h2>
        <p><a href="/admin/login" class="text-blue-500 hover:underline">Go to Login</a></p>
        <p><a href="/debug_navbar.php" class="text-blue-500 hover:underline">Check Authentication</a></p>
    </div>
</body>
</html>
