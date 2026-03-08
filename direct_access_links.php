<?php
echo "<h1>Direct Access Links - Admin Dashboard (No Login Required)</h1>";

echo "<h2>Admin Dashboard Access:</h2>";
echo "<ul>";
echo "<li><a href='/admin-direct' target='_blank'><strong>/admin-direct</strong> - Direct Admin Dashboard</a></li>";
echo "<li><a href='/admin/dashboard' target='_blank'><strong>/admin/dashboard</strong> - Admin Dashboard (with auth disabled)</a></li>";
echo "<li><a href='/dashboard' target='_blank'><strong>/dashboard</strong> - Legacy Admin Dashboard (redirects)</a></li>";
echo "</ul>";

echo "<h2>Super Admin Dashboard Access:</h2>";
echo "<ul>";
echo "<li><a href='/superadmin-direct' target='_blank'><strong>/superadmin-direct</strong> - Direct Super Admin Dashboard</a></li>";
echo "<li><a href='/superadmin/dashboard' target='_blank'><strong>/superadmin/dashboard</strong> - Super Admin Dashboard (with auth disabled)</a></li>";
echo "<li><a href='/superadmin' target='_blank'><strong>/superadmin</strong> - Super Admin Login (redirects to dashboard)</a></li>";
echo "</ul>";

echo "<h2>Admin Management Routes:</h2>";
echo "<ul>";
echo "<li><a href='/admin/properties' target='_blank'><strong>/admin/properties</strong> - Properties Management</a></li>";
echo "<li><a href='/admin/tenants' target='_blank'><strong>/admin/tenants</strong> - Tenants Management</a></li>";
echo "<li><a href='/admin/payments' target='_blank'><strong>/admin/payments</strong> - Payments Management</a></li>";
echo "<li><a href='/admin/invoices' target='_blank'><strong>/admin/invoices</strong> - Invoices Management</a></li>";
echo "<li><a href='/admin/profile' target='_blank'><strong>/admin/profile</strong> - Admin Profile</a></li>";
echo "</ul>";

echo "<h2>Super Admin Management Routes:</h2>";
echo "<ul>";
echo "<li><a href='/superadmin/admins' target='_blank'><strong>/superadmin/admins</strong> - Manage All Admins</a></li>";
echo "<li><a href='/superadmin/export' target='_blank'><strong>/superadmin/export</strong> - Export Platform Data</a></li>";
echo "</ul>";

echo "<h3><strong>Note:</strong> Authentication is currently disabled in BaseController for development.</h3>";
echo "<h3>Mock User Data:</h3>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin@cornerstone.com (ID: 3)</li>";
echo "<li><strong>Super Admin:</strong> superadmin@cornerstone.com (ID: 4)</li>";
echo "</ul>";
?>
