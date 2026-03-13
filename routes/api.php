<?php

// API Routes - REST endpoints
return [
    // Admin Authentication endpoints
    'POST /api/admin/auth/register' => 'AdminAuthController@register',
    'POST /api/admin/auth/login' => 'AdminAuthController@login',
    'POST /api/admin/auth/logout' => 'AdminAuthController@logout',
    'GET /api/admin/auth/me' => 'AdminAuthController@me',

    // Super Admin Authentication endpoints
    'POST /api/superadmin/auth/login' => 'SuperAdminAuthController@login',
    'POST /api/superadmin/auth/logout' => 'SuperAdminAuthController@logout',
    'GET /api/superadmin/auth/me' => 'SuperAdminAuthController@me',

    // Admin Property endpoints
    'GET /api/admin/properties' => 'ApiPropertyController@index',
    'GET /api/admin/properties/{id}' => 'ApiPropertyController@show',
    'POST /api/admin/properties' => 'ApiPropertyController@store',
    'PUT /api/admin/properties/{id}' => 'ApiPropertyController@update',
    'DELETE /api/admin/properties/{id}' => 'ApiPropertyController@delete',

    // Admin Unit endpoints
    'GET /api/admin/units' => 'ApiUnitController@index',
    'POST /api/admin/units' => 'ApiUnitController@store',
    'PUT /api/admin/units/{id}' => 'ApiUnitController@update',
    'DELETE /api/admin/units/{id}' => 'ApiUnitController@delete',

    // Admin Tenant endpoints
    'GET /api/admin/tenants' => 'ApiTenantController@index',
    'POST /api/admin/tenants' => 'ApiTenantController@store',
    'PUT /api/admin/tenants/{id}' => 'ApiTenantController@update',
    'DELETE /api/admin/tenants/{id}' => 'ApiTenantController@delete',

    // Admin Payment endpoints
    'GET /api/admin/payments' => 'ApiPaymentController@index',
    'POST /api/admin/payments' => 'ApiPaymentController@store',
    'PUT /api/admin/payments/{id}' => 'ApiPaymentController@update',

    // Admin Invoice endpoints
    'GET /api/admin/invoices' => 'ApiInvoiceController@index',
    'POST /api/admin/invoices' => 'ApiInvoiceController@store',
    'PUT /api/admin/invoices/{id}' => 'ApiInvoiceController@update',

    // Admin Maintenance endpoints
    'GET /api/admin/maintenance' => 'ApiMaintenanceController@index',
    'POST /api/admin/maintenance' => 'ApiMaintenanceController@store',
    'PUT /api/admin/maintenance/{id}' => 'ApiMaintenanceController@update',

    // Admin Communication endpoints
    'GET /api/admin/communications' => 'ApiCommunicationController@index',
    'POST /api/admin/communications' => 'ApiCommunicationController@store',
    'PUT /api/admin/communications/{id}' => 'ApiCommunicationController@update',

    // Admin Document endpoints
    'GET /api/admin/documents' => 'ApiDocumentController@index',
    'POST /api/admin/documents' => 'ApiDocumentController@store',
    'PUT /api/admin/documents/{id}' => 'ApiDocumentController@update',

    // Admin Dashboard endpoints
    'GET /api/admin/dashboard/stats' => 'ApiDashboardController@stats',
    'GET /api/admin/dashboard/revenue' => 'ApiDashboardController@revenue',
    'GET /api/admin/dashboard/recent-activities' => 'ApiDashboardController@recentActivities',
    'GET /api/admin/dashboard/recent-properties' => 'ApiDashboardController@recentProperties',

    // Super Admin endpoints
    'GET /api/superadmin/admins' => 'SuperAdminController@admins',
    'GET /api/superadmin/export' => 'SuperAdminController@exportData',
    'GET /api/superadmin/stats' => 'SuperAdminController@getPlatformStats',

    // Legacy API endpoints - redirect to admin endpoints
    'POST /api/auth/register' => 'AdminAuthController@register',
    'POST /api/auth/login' => 'AdminAuthController@login',
    'POST /api/auth/logout' => 'AdminAuthController@logout',
    'GET /api/auth/me' => 'AdminAuthController@me',
    'GET /api/properties' => 'ApiPropertyController@index',
    'GET /api/properties/{id}' => 'ApiPropertyController@show',
    'POST /api/properties' => 'ApiPropertyController@store',
    'PUT /api/properties/{id}' => 'ApiPropertyController@update',
    'DELETE /api/properties/{id}' => 'ApiPropertyController@delete',
    'GET /api/units' => 'ApiUnitController@index',
    'POST /api/units' => 'ApiUnitController@store',
    'PUT /api/units/{id}' => 'ApiUnitController@update',
    'DELETE /api/units/{id}' => 'ApiUnitController@delete',
    'GET /api/tenants' => 'ApiTenantController@index',
    'POST /api/tenants' => 'ApiTenantController@store',
    'PUT /api/tenants/{id}' => 'ApiTenantController@update',
    'DELETE /api/tenants/{id}' => 'ApiTenantController@delete',
    'GET /api/payments' => 'ApiPaymentController@index',
    'POST /api/payments' => 'ApiPaymentController@store',
    'PUT /api/payments/{id}' => 'ApiPaymentController@update',
    'GET /api/invoices' => 'ApiInvoiceController@index',
    'POST /api/invoices' => 'ApiInvoiceController@store',
    'PUT /api/invoices/{id}' => 'ApiInvoiceController@update',
    'GET /api/dashboard/stats' => 'ApiDashboardController@stats',
    'GET /api/dashboard/revenue' => 'ApiDashboardController@revenue',
    'GET /api/dashboard/recent-activities' => 'ApiDashboardController@recentActivities',
    'GET /api/dashboard/recent-properties' => 'ApiDashboardController@recentProperties',
];
