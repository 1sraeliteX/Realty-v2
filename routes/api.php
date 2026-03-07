<?php

// API Routes - REST endpoints
return [
    // Authentication endpoints
    'POST /api/auth/register' => 'ApiAuthController@register',
    'POST /api/auth/login' => 'ApiAuthController@login',
    'POST /api/auth/logout' => 'ApiAuthController@logout',
    'GET /api/auth/me' => 'ApiAuthController@me',

    // Property endpoints
    'GET /api/properties' => 'ApiPropertyController@index',
    'GET /api/properties/{id}' => 'ApiPropertyController@show',
    'POST /api/properties' => 'ApiPropertyController@store',
    'PUT /api/properties/{id}' => 'ApiPropertyController@update',
    'DELETE /api/properties/{id}' => 'ApiPropertyController@delete',

    // Unit endpoints
    'GET /api/units' => 'ApiUnitController@index',
    'POST /api/units' => 'ApiUnitController@store',
    'PUT /api/units/{id}' => 'ApiUnitController@update',
    'DELETE /api/units/{id}' => 'ApiUnitController@delete',

    // Tenant endpoints
    'GET /api/tenants' => 'ApiTenantController@index',
    'POST /api/tenants' => 'ApiTenantController@store',
    'PUT /api/tenants/{id}' => 'ApiTenantController@update',
    'DELETE /api/tenants/{id}' => 'ApiTenantController@delete',

    // Payment endpoints
    'GET /api/payments' => 'ApiPaymentController@index',
    'POST /api/payments' => 'ApiPaymentController@store',
    'PUT /api/payments/{id}' => 'ApiPaymentController@update',

    // Invoice endpoints
    'GET /api/invoices' => 'ApiInvoiceController@index',
    'POST /api/invoices' => 'ApiInvoiceController@store',
    'PUT /api/invoices/{id}' => 'ApiInvoiceController@update',

    // Dashboard endpoints
    'GET /api/dashboard/stats' => 'ApiDashboardController@stats',
    'GET /api/dashboard/revenue' => 'ApiDashboardController@revenue',
    'GET /api/dashboard/recent-activities' => 'ApiDashboardController@recentActivities',
    'GET /api/dashboard/recent-properties' => 'ApiDashboardController@recentProperties',
];
