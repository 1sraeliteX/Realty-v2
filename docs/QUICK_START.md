# Quick Start Guide - Real Estate Management System

## 🚀 Project Access Links

Once PHP is installed and the server is running, you can access:

### **🔐 Login Pages**
- **Main Login**: `http://localhost:8000/login`
- **Registration**: `http://localhost:8000/register`

### **📊 Dashboard Pages**
- **Super Admin Dashboard**: `http://localhost:8000/superadmin`
- **Regular Admin Dashboard**: `http://localhost:8000/dashboard`

### **🎯 Test Accounts**
| Role | Email | Password | Dashboard URL |
|------|-------|----------|-----------|
| Super Admin | superadmin@cornerstone.com | admin123 | `/superadmin` |
| Regular Admin | admin@cornerstone.com | admin123 | `/dashboard` |

---

## ⚡ Quick Setup Instructions

### **Step 1: Install PHP (5 minutes)**

#### **Option A: XAMPP (Recommended - Easiest)**
1. Download XAMPP from: https://www.apachefriends.org/download.html
2. Install XAMPP (choose PHP 8.0+ version)
3. Start Apache and MySQL from XAMPP Control Panel
4. Your server is ready at `http://localhost`

#### **Option B: PHP Only**
1. Download PHP from: https://www.php.net/downloads.php
2. Extract to `C:\php`
3. Add `C:\php` to your system PATH

### **Step 2: Start the Development Server**

#### **Method 1: Using XAMPP**
1. Copy your project files to `C:\xampp\htdocs\realty-v2\`
2. Access at: `http://localhost/realty-v2/public`

#### **Method 2: Built-in PHP Server**
```bash
# Open Command Prompt/PowerShell
cd "C:\Users\HP\Downloads\Realty-v2\public"
php -S localhost:8000
```

### **Step 3: Database Setup**
1. Open Supabase SQL Editor
2. Run the SQL script: `database/create_test_superadmin.sql`
3. Test accounts will be created automatically

---

## 🎯 What You'll See

### **Enhanced Login Page Features**
- ✅ **Eye Icon**: Click to show/hide password
- ✅ **Quick Super Admin Login**: One-click credential fill
- ✅ **Loading Effect**: "Signing in..." animation
- ✅ **Clean Design**: No key icon, modern look

### **Registration Page Features**
- ✅ **Role Selection**: Choose Property Admin or Super Admin
- ✅ **Account Types**: Clear descriptions for each role
- ✅ **Password Toggle**: Show/hide password functionality

### **Dashboard Features**
- ✅ **Role-Based Access**: Different dashboards for different roles
- ✅ **Super Admin**: Platform-wide management
- ✅ **Regular Admin**: Property-focused view
- ✅ **Admin Management**: Super admins can manage all admins

---

## 🔗 Direct Access URLs

| Page | URL | Purpose |
|------|-----|---------|
| Login | `/login` | Sign in to your account |
| Register | `/register` | Create new admin account |
| Super Admin | `/superadmin` | Platform management |
| Admin Dashboard | `/dashboard` | Property management |
| Admin Management | `/superadmin/admins` | Manage admins |
| Export Data | `/superadmin/export` | Download data |

---

## 🎨 New Features Added

### **Authentication Enhancements**
- **Password Eye Icon**: Toggle password visibility
- **Quick Super Admin Login**: One-click access
- **Enhanced Loading**: Smooth "Signing in..." animation
- **Role Selection**: Choose account type during registration

### **Dashboard System**
- **Separate Dashboards**: Different interfaces for different roles
- **Admin Management**: Super admins can create/manage admins
- **Data Export**: JSON/CSV export functionality
- **Platform Statistics**: Overview for super admins

---

## 📱 Mobile Responsive
- All pages work perfectly on mobile devices
- Touch-friendly buttons and forms
- Responsive navigation menus

---

## 🛠️ Troubleshooting

### **"PHP not recognized" Error**
- Install PHP using XAMPP (recommended)
- Or add PHP to your system PATH

### **"Page not found" Error**
- Make sure the server is pointing to the `public` folder
- Check that `.htaccess` exists in the `public` folder

### **Database Connection Error**
- Run the SQL setup script in Supabase
- Check your `.env` configuration

---

## 🎉 Ready to Use!

Once PHP is installed and the server is running:

1. **Visit**: `http://localhost:8000/login`
2. **Click**: "Login as Super Admin" button
3. **Experience**: The enhanced authentication system
4. **Explore**: Both super admin and regular admin dashboards

All the new features (eye icon, loading effects, role selection) are ready to use! 🚀
