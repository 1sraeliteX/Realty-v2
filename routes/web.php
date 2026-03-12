<?php

// Web Routes - Frontend pages
return [
    // Root route
    'GET /' => 'LandingController@index',

    // Admin Authentication routes
    'GET /admin/login' => 'AdminAuthController@showLogin',
    'POST /admin/login' => 'AdminAuthController@login',
    'GET /admin/register' => 'AdminAuthController@showRegister',
    'POST /admin/register' => 'AdminAuthController@register',
    'POST /admin/logout' => 'AdminAuthController@logout',

    // Super Admin Authentication routes
    'GET /superadmin' => 'SuperAdminAuthController@showLogin',
    'GET /superadmin/login' => 'SuperAdminAuthController@showLogin',
    'POST /superadmin/login' => 'SuperAdminAuthController@login',
    'POST /superadmin/logout' => 'SuperAdminAuthController@logout',

    // Admin Dashboard routes
    'GET /admin/dashboard' => 'AdminDashboardController@index',

    // Super Admin Dashboard routes
    'GET /superadmin/dashboard' => 'SuperAdminController@index',
    'GET /superadmin/admins' => 'SuperAdminController@admins',
    'GET /superadmin/export' => 'SuperAdminController@exportData',

    // Public Property routes
    'GET /properties' => 'PropertyController@index',
    'GET /properties/create' => 'PropertyController@create',
    'POST /properties' => 'PropertyController@store',

    // Admin Property routes
    'GET /admin/properties' => 'PropertyController@index',
    'GET /admin/properties/create' => 'PropertyController@create',
    'POST /admin/properties' => 'PropertyController@store',
    'GET /admin/properties/{id}' => 'PropertyController@show',
    'GET /admin/properties/{id}/edit' => 'PropertyController@edit',
    'POST /admin/properties/{id}' => 'PropertyController@update',
    'POST /admin/properties/{id}/delete' => 'PropertyController@delete',

    // Admin Unit routes
    'GET /admin/units' => 'UnitController@index',
    'GET /admin/units/create' => 'UnitController@create',
    'POST /admin/units' => 'UnitController@store',
    'GET /admin/units/{id}/edit' => 'UnitController@edit',
    'POST /admin/units/{id}' => 'UnitController@update',
    'POST /admin/units/{id}/delete' => 'UnitController@delete',

    // Admin Tenant routes
    'GET /admin/tenants' => 'TenantController@index',
    'GET /admin/tenants/create' => 'TenantController@create',
    'POST /admin/tenants' => 'TenantController@store',
    'GET /admin/tenants/{id}' => 'TenantController@show',
    'GET /admin/tenants/{id}/edit' => 'TenantController@edit',
    'POST /admin/tenants/{id}' => 'TenantController@update',
    'POST /admin/tenants/{id}/delete' => 'TenantController@delete',

    // Admin Tenants & Occupants routes
    'GET /admin/tenants-occupants' => 'TenantOccupantController@index',
    'GET /admin/occupants/create' => 'TenantOccupantController@createOccupant',
    'POST /admin/occupants' => 'TenantOccupantController@storeOccupant',

    // Admin Payment routes
    'GET /admin/payments' => 'PaymentController@index',
    'GET /admin/payments/create' => 'PaymentController@create',
    'POST /admin/payments' => 'PaymentController@store',
    'GET /admin/payments/{id}' => 'PaymentController@show',
    'GET /admin/payments/{id}/edit' => 'PaymentController@edit',
    'POST /admin/payments/{id}' => 'PaymentController@update',
    'POST /admin/payments/{id}/delete' => 'PaymentController@delete',
    'GET /admin/payments/receipt/{id}/download' => 'PaymentController@downloadReceipt',

    // Admin Invoice routes
    'GET /admin/invoices' => 'InvoiceController@index',
    'GET /admin/invoices/create' => 'InvoiceController@create',
    'POST /admin/invoices' => 'InvoiceController@store',
    'GET /admin/invoices/{id}' => 'InvoiceController@show',
    'GET /admin/invoices/{id}/edit' => 'InvoiceController@edit',
    'POST /admin/invoices/{id}' => 'InvoiceController@update',
    'POST /admin/invoices/{id}/delete' => 'InvoiceController@delete',

    // Admin Financial routes
    'GET /admin/finances' => 'FinanceController@index',
    
    // Admin Maintenance routes
    'GET /admin/maintenance' => 'MaintenanceController@index',
    'GET /admin/maintenance/create' => 'MaintenanceController@create',
    'POST /admin/maintenance' => 'MaintenanceController@store',
    'GET /admin/maintenance/{id}/edit' => 'MaintenanceController@edit',
    'POST /admin/maintenance/{id}' => 'MaintenanceController@update',
    'POST /admin/maintenance/{id}/delete' => 'MaintenanceController@delete',

    // Admin Communications routes
    'GET /admin/communications' => 'CommunicationController@index',
    'GET /admin/communications/create' => 'CommunicationController@create',
    'POST /admin/communications' => 'CommunicationController@store',
    'GET /admin/communications/{id}/edit' => 'CommunicationController@edit',
    'POST /admin/communications/{id}' => 'CommunicationController@update',
    'POST /admin/communications/{id}/delete' => 'CommunicationController@delete',

    // Admin Documents routes
    'GET /admin/documents' => 'DocumentController@index',
    'GET /admin/documents/create' => 'DocumentController@create',
    'POST /admin/documents' => 'DocumentController@store',
    'GET /admin/documents/{id}/edit' => 'DocumentController@edit',
    'POST /admin/documents/{id}' => 'DocumentController@update',
    'POST /admin/documents/{id}/delete' => 'DocumentController@delete',

    // Admin Dashboard Reports routes
    'GET /admin/dashboard/reports' => 'ReportController@dashboardReports',
    
    // Admin Reports routes
    'GET /admin/reports' => 'ReportController@index',
    'GET /admin/reports/create' => 'ReportController@create',
    'POST /admin/reports' => 'ReportController@store',
    'GET /admin/reports/{id}/edit' => 'ReportController@edit',
    'POST /admin/reports/{id}' => 'ReportController@update',
    'POST /admin/reports/{id}/delete' => 'ReportController@delete',

    // Admin Settings routes
    'GET /admin/settings' => 'SettingsController@index',
    'POST /admin/settings' => 'SettingsController@update',
    'GET /admin/profile' => 'ProfileController@index',
    'POST /admin/profile' => 'ProfileController@update',

    // Direct access routes (for development/testing)
    'GET /admin-direct' => 'AdminDashboardController@index',
    'GET /superadmin-direct' => 'SuperAdminController@index',

    // Legacy routes - COMMENTED OUT FOR DEVELOPMENT
    // 'GET /' => 'LegacyController@redirectToAdminLogin',
    // 'GET /login' => 'LegacyController@redirectToAdminLogin',
    // 'POST /login' => 'AdminAuthController@login',
    // 'GET /register' => 'LegacyController@redirectToAdminRegister',
    // 'POST /register' => 'AdminAuthController@register',
    // 'POST /logout' => 'AdminAuthController@logout',
    // 'GET /dashboard' => 'LegacyController@redirectToAdminDashboard',
    
    // Direct access routes for development (bypass login) - COMMENTED OUT
    // 'GET /' => 'AdminDashboardController@index',
    // 'GET /admin' => 'AdminDashboardController@index',
    // 'GET /login' => 'AdminDashboardController@index',
    // 'GET /dashboard' => 'AdminDashboardController@index',
];
