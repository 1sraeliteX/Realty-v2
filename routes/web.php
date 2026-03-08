<?php

// Web Routes - Frontend pages
return [
    // Admin Authentication routes
    'GET /admin' => 'AdminAuthController@showLogin',
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

    // Admin Payment routes
    'GET /admin/payments' => 'PaymentController@index',
    'GET /admin/payments/create' => 'PaymentController@create',
    'POST /admin/payments' => 'PaymentController@store',
    'GET /admin/payments/{id}/edit' => 'PaymentController@edit',
    'POST /admin/payments/{id}' => 'PaymentController@update',
    'POST /admin/payments/{id}/delete' => 'PaymentController@delete',

    // Admin Invoice routes
    'GET /admin/invoices' => 'InvoiceController@index',
    'GET /admin/invoices/create' => 'InvoiceController@create',
    'POST /admin/invoices' => 'InvoiceController@store',
    'GET /admin/invoices/{id}' => 'InvoiceController@show',
    'GET /admin/invoices/{id}/edit' => 'InvoiceController@edit',
    'POST /admin/invoices/{id}' => 'InvoiceController@update',
    'POST /admin/invoices/{id}/delete' => 'InvoiceController@delete',

    // Admin Profile routes
    'GET /admin/profile' => 'ProfileController@index',
    'POST /admin/profile' => 'ProfileController@update',

    // Direct access routes (for development/testing)
    'GET /admin-direct' => 'AdminDashboardController@index',
    'GET /superadmin-direct' => 'SuperAdminController@index',

    // Legacy routes - redirect to new admin routes
    'GET /' => 'LegacyController@redirectToAdminLogin',
    'GET /login' => 'LegacyController@redirectToAdminLogin',
    'POST /login' => 'AdminAuthController@login',
    'GET /register' => 'LegacyController@redirectToAdminRegister',
    'POST /register' => 'AdminAuthController@register',
    'POST /logout' => 'AdminAuthController@logout',
    'GET /dashboard' => 'LegacyController@redirectToAdminDashboard',
];
