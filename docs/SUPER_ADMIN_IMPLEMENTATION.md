# Super Admin Dashboard Implementation

This document outlines the implementation of the Super Admin functionality for the Cornerstone Realty platform.

## Overview

The system now supports two distinct user roles:
- **Admin**: Regular property managers with access to their own properties and data
- **Super Admin**: Platform administrators with access to all platform data and administrative functions

## Features Implemented

### Super Admin Dashboard (`/superadmin`)
- **Platform Overview**: Shows total admins, properties, active subscriptions, and platform revenue
- **Admin Management**: Full CRUD operations for managing admin accounts
- **Export Data**: Export platform data in JSON or CSV format
- **DotBot Assistant**: Toggle for floating assistant functionality
- **Recent Admins**: Display of newly registered administrators
- **Recent Activity**: Platform-wide activity tracking

### Role-Based Access Control
- Automatic redirection based on user role
- Super admins are redirected to `/superadmin` when accessing `/dashboard`
- Regular admins cannot access super admin routes
- Protected API endpoints with role validation

### Enhanced Navigation
- Different sidebar for super admin vs regular admin
- Role indicators in user profiles
- Consistent branding with "Cornerstone Realty Platform Admin"

## File Structure

### Controllers
- `app/controllers/SuperAdminController.php` - Super admin specific functionality
- `app/controllers/DashboardController.php` - Updated with role-based redirection
- `app/controllers/BaseController.php` - Added `requireSuperAdmin()` method

### Views
- `views/superadmin/dashboard.php` - Main super admin dashboard
- `views/superadmin/admins.php` - Admin management interface
- `views/superadmin/superadmin_layout.php` - Super admin specific layout
- `views/dashboard/layout.php` - Updated regular admin layout

### Routes
- Added super admin routes in `routes/web.php`:
  - `GET /superadmin` - Super admin dashboard
  - `GET /superadmin/admins` - Admin management
  - `GET /superadmin/export` - Data export

### Database
- Uses existing `admins` table with role column
- Supports both 'admin' and 'super_admin' roles
- Test accounts available in `database/create_test_superadmin.sql`

## Setup Instructions

### 1. Database Setup
Run the SQL script to create test accounts:
```sql
-- Run in Supabase SQL Editor
\i database/create_test_superadmin.sql
```

### 2. Test Accounts
- **Super Admin**: 
  - Email: `superadmin@cornerstone.com`
  - Password: `admin123`
- **Regular Admin**:
  - Email: `admin@cornerstone.com` 
  - Password: `admin123`

### 3. Access URLs
- Super Admin Dashboard: `/superadmin`
- Admin Management: `/superadmin/admins`
- Export Data: `/superadmin/export?format=json` or `?format=csv`
- Regular Admin Dashboard: `/dashboard` (redirects based on role)

## Security Features

### Authentication
- Role-based session management
- Automatic redirection based on user role
- Protected routes with middleware

### Authorization
- `requireSuperAdmin()` method for super admin only routes
- Regular admins cannot access super admin functionality
- API endpoints protected with role validation

### Data Protection
- Admins can only see their own data
- Super admins have platform-wide access
- Secure password hashing

## UI/UX Features

### Dark Mode Support
- Consistent dark mode across all interfaces
- Theme toggle in both admin and super admin dashboards

### Responsive Design
- Mobile-friendly navigation
- Collapsible sidebar for small screens
- Responsive tables and cards

### Interactive Elements
- Toast notifications for user feedback
- Modal dialogs for admin management
- Hover states and transitions

## Future Enhancements

### Planned Features
- Admin activity logging
- Platform analytics and reporting
- Bulk admin operations
- Admin permission levels
- Email notifications for admin actions

### API Endpoints
- RESTful API for admin management
- Webhook support for platform events
- Export scheduling

## Troubleshooting

### Common Issues
1. **Access Denied**: Ensure user has correct role in database
2. **404 Errors**: Verify routes are properly configured
3. **Database Errors**: Check Supabase connection and schema

### Debug Mode
Enable error reporting in development:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Support

For questions or issues with the super admin implementation, refer to:
- Database schema: `database/supabase_schema.sql`
- Route configuration: `routes/web.php`
- Controller logic: `app/controllers/SuperAdminController.php`
