<?php

// Web Routes - Frontend pages
return [
    // Authentication routes
    'GET /' => 'AuthController@showLogin',
    'GET /login' => 'AuthController@showLogin',
    'POST /login' => 'AuthController@login',
    'GET /register' => 'AuthController@showRegister',
    'POST /register' => 'AuthController@register',
    'POST /logout' => 'AuthController@logout',

    // Dashboard routes
    'GET /dashboard' => 'DashboardController@index',

    // Super Admin routes
    'GET /superadmin' => 'SuperAdminController@index',
    'GET /superadmin/admins' => 'SuperAdminController@admins',
    'GET /superadmin/export' => 'SuperAdminController@exportData',

    // Property routes
    'GET /properties' => 'PropertyController@index',
    'GET /properties/create' => 'PropertyController@create',
    'POST /properties' => 'PropertyController@store',
    'GET /properties/{id}' => 'PropertyController@show',
    'GET /properties/{id}/edit' => 'PropertyController@edit',
    'POST /properties/{id}' => 'PropertyController@update',
    'POST /properties/{id}/delete' => 'PropertyController@delete',

    // Unit routes
    'GET /units' => 'UnitController@index',
    'POST /units' => 'UnitController@store',
    'GET /units/{id}/edit' => 'UnitController@edit',
    'POST /units/{id}' => 'UnitController@update',
    'POST /units/{id}/delete' => 'UnitController@delete',

    // Tenant routes
    'GET /tenants' => 'TenantController@index',
    'GET /tenants/create' => 'TenantController@create',
    'POST /tenants' => 'TenantController@store',
    'GET /tenants/{id}' => 'TenantController@show',
    'GET /tenants/{id}/edit' => 'TenantController@edit',
    'POST /tenants/{id}' => 'TenantController@update',
    'POST /tenants/{id}/delete' => 'TenantController@delete',

    // Payment routes
    'GET /payments' => 'PaymentController@index',
    'GET /payments/create' => 'PaymentController@create',
    'POST /payments' => 'PaymentController@store',
    'GET /payments/{id}/edit' => 'PaymentController@edit',
    'POST /payments/{id}' => 'PaymentController@update',
    'POST /payments/{id}/delete' => 'PaymentController@delete',

    // Invoice routes
    'GET /invoices' => 'InvoiceController@index',
    'GET /invoices/create' => 'InvoiceController@create',
    'POST /invoices' => 'InvoiceController@store',
    'GET /invoices/{id}' => 'InvoiceController@show',
    'GET /invoices/{id}/edit' => 'InvoiceController@edit',
    'POST /invoices/{id}' => 'InvoiceController@update',
    'POST /invoices/{id}/delete' => 'InvoiceController@delete',

    // Profile routes
    'GET /profile' => 'ProfileController@index',
    'POST /profile' => 'ProfileController@update',
];
