# Access Guide - Admin & Super Admin Dashboard

## 🚀 Quick Access Instructions

### 1. **Access the Super Admin Dashboard**
- **URL**: `/superadmin`
- **Login**: Use the quick login button on the login page
- **Credentials**: `superadmin@cornerstone.com` / `admin123`

### 2. **Access Regular Admin Dashboard**
- **URL**: `/dashboard`
- **Login**: Regular login form
- **Credentials**: `admin@cornerstone.com` / `admin123`

### 3. **Register New Admins**
- **URL**: `/register`
- **Options**: Choose between "Property Admin" or "Super Admin" roles

---

## 🔐 Authentication Features

### **Enhanced Login Page**
- ✅ **Regular Login**: Standard email/password form
- ✅ **Quick Super Admin Login**: One-click credential fill
- ✅ **API Token Access**: Generate JWT tokens for API usage

### **Enhanced Registration Page**
- ✅ **Role Selection**: Choose account type during registration
- ✅ **Account Types**:
  - **Property Admin**: Manages their own properties only
  - **Super Admin**: Manages entire platform and all admins

---

## 📊 Dashboard Access by Role

### **Super Admin Dashboard** (`/superadmin`)
**Features Available:**
- 📈 Platform Overview (Total Admins, Properties, Revenue)
- 👥 Admin Management (Create, Edit, Delete admins)
- 📤 Data Export (JSON/CSV format)
- 🤖 DotBot Assistant toggle
- 📋 Recent Admins list
- 🔄 Platform Activity tracking

**Navigation Menu:**
- Dashboard
- Admin Management
- Properties (view all)
- Tenants & Occupants (view all)
- Units (view all)
- Finances (platform-wide)
- Invoices (all invoices)
- Maintenance
- Reports
- Communications
- Documents

### **Regular Admin Dashboard** (`/dashboard`)
**Features Available:**
- 🏠 Property Overview (Your properties only)
- 📊 Property Statistics (Your data only)
- 💰 Revenue Tracking (Your revenue only)
- 👥 Tenant Management (Your tenants only)
- 🏢 Unit Management (Your units only)

**Navigation Menu:**
- Dashboard
- Properties (your properties)
- Units (your units)
- Tenants (your tenants)
- Payments (your payments)
- Invoices (your invoices)
- Profile

---

## 🎯 How to Use the System

### **Step 1: Database Setup**
```sql
-- Run this in Supabase SQL Editor
-- Copy contents from: database/create_test_superadmin.sql
```

### **Step 2: Access Super Admin**
1. Go to `/login`
2. Click "Login as Super Admin" button
3. Credentials auto-fill: `superadmin@cornerstone.com` / `admin123`
4. Click "Sign in"
5. You'll be redirected to `/superadmin`

### **Step 3: Create Admins**
1. In Super Admin Dashboard, go to "Admin Management"
2. Click "Add New Admin"
3. Fill in admin details
4. Choose role (Admin or Super Admin)
5. Save

### **Step 4: Register New Admins**
1. Go to `/register`
2. Fill in registration form
3. Select account type:
   - "Property Admin - Manage my properties"
   - "Super Admin - Manage entire platform"
4. Submit form
5. New admin can log in immediately

### **Step 5: Test Role Separation**
- **Super Admin**: Logs into `/superadmin` with platform view
- **Regular Admin**: Logs into `/dashboard` with property view
- **Security**: Regular admins cannot access `/superadmin` routes

---

## 🔗 Direct URLs

| Page | URL | Who Can Access |
|------|-----|----------------|
| Login | `/login` | Everyone |
| Register | `/register` | Everyone |
| Super Admin Dashboard | `/superadmin` | Super Admins only |
| Admin Dashboard | `/dashboard` | All admins (redirects based on role) |
| Admin Management | `/superadmin/admins` | Super Admins only |
| Export Data | `/superadmin/export` | Super Admins only |
| Properties | `/properties` | All admins (filtered by role) |
| Tenants | `/tenants` | All admins (filtered by role) |

---

## 🛡️ Security Features

### **Role-Based Access Control**
- ✅ Super Admins can access everything
- ✅ Regular admins see only their data
- ✅ Automatic redirection based on role
- ✅ Protected routes with middleware

### **Authentication**
- ✅ Password hashing
- ✅ Session management
- ✅ JWT token support
- ✅ Activity logging

---

## 📱 Quick Login Options

### **Super Admin Quick Login**
1. Visit `/login`
2. Look for purple "Super Admin Access" box
3. Click "Login as Super Admin"
4. Credentials auto-filled
5. Click "Sign in"

### **Test Accounts**
| Role | Email | Password | Dashboard |
|------|-------|----------|-----------|
| Super Admin | superadmin@cornerstone.com | admin123 | `/superadmin` |
| Regular Admin | admin@cornerstone.com | admin123 | `/dashboard` |

---

## 🎨 UI Features

### **Super Admin Interface**
- Purple accent color theme
- Platform-wide statistics
- Admin management tools
- Export functionality
- Enhanced navigation

### **Regular Admin Interface**
- Blue accent color theme
- Property-focused statistics
- Personal dashboard
- Simplified navigation

---

## 📞 Troubleshooting

### **Common Issues**
1. **Access Denied**: Check user role in database
2. **Wrong Dashboard**: Clear session and re-login
3. **Registration Fails**: Ensure role is selected
4. **Quick Login Not Working**: Check JavaScript is enabled

### **Reset Test Accounts**
```sql
-- Delete and recreate test accounts
DELETE FROM admins WHERE email IN ('superadmin@cornerstone.com', 'admin@cornerstone.com');
-- Then run the create_test_superadmin.sql script again
```

---

## 🎯 Success Checklist

- [ ] Database setup complete
- [ ] Can access login page
- [ ] Quick super admin login works
- [ ] Registration with role selection works
- [ ] Super admin dashboard accessible
- [ ] Regular admin dashboard accessible
- [ ] Admin management works
- [ ] Role separation working
- [ ] Export functionality works

The system is now fully functional with role-based access control! 🎉
