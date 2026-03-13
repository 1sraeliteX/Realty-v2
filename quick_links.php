<?php
echo "<h2>Quick Access Links</h2>";

echo "<h3>🔐 Admin Login</h3>";
echo "<p><a href='/admin/login' target='_blank'>📋 Admin Login Page</a></p>";

echo "<h3>🏠 Dashboard</h3>";
echo "<p><a href='/admin/dashboard' target='_blank'>📊 Admin Dashboard</a></p>";

echo "<h3>🏢 Properties</h3>";
echo "<p><a href='/admin/properties' target='_blank'>🏘️ Properties List</a></p>";
echo "<p><a href='/admin/properties/create' target='_blank'>➕ Add New Property</a></p>";

echo "<h3>👥 Tenants</h3>";
echo "<p><a href='/admin/tenants' target='_blank'>👤 Tenants List</a></p>";
echo "<p><a href='/admin/tenants/create' target='_blank'>👤 Add New Tenant</a></p>";

echo "<h3>💰 Finances</h3>";
echo "<p><a href='/admin/payments' target='_blank'>💳 Payments</a></p>";
echo "<p><a href='/admin/invoices' target='_blank'>🧾 Invoices</a></p>";

echo "<h3>🔧 Other Pages</h3>";
echo "<p><a href='/admin/maintenance' target='_blank'>🛠️ Maintenance</a></p>";
echo "<p><a href='/admin/reports' target='_blank'>📈 Reports</a></p>";
echo "<p><a href='/admin/settings' target='_blank'>⚙️ Settings</a></p>";

echo "<h3>🏠 Landing Page</h3>";
echo "<p><a href='/' target='_blank'>🌐 Main Landing Page</a></p>";

echo "<h3>🧪 Test Pages</h3>";
echo "<p><a href='/test_admin_session.php' target='_blank'>🔍 Test Admin Session</a></p>";
echo "<p><a href='/check_current_properties.php' target='_blank'>🏘️ Check Properties</a></p>";

echo "<hr>";
echo "<h3>📱 Login Credentials</h3>";
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Email</th><th>Password</th><th>Role</th></tr>";
echo "<tr><td>test@admin.com</td><td>password123</td><td>Admin</td></tr>";
echo "<tr><td>admin@test.com</td><td>password123</td><td>Admin</td></tr>";
echo "<tr><td>admin@cornerstone.com</td><td>password123</td><td>Admin</td></tr>";
echo "<tr><td>superadmin@cornerstone.com</td><td>password123</td><td>Super Admin</td></tr>";
echo "</table>";

echo "<hr>";
echo "<p><strong>💡 Quick Start:</strong></p>";
echo "<ol>";
echo "<li>Go to <a href='/admin/login' target='_blank'>Admin Login</a></li>";
echo "<li>Login with <strong>test@admin.com / password123</strong></li>";
echo "<li>You'll be redirected to the Dashboard</li>";
echo "<li>Navigate to any section from the sidebar</li>";
echo "</ol>";
?>
