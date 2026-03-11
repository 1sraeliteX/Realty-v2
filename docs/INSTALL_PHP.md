# PHP Installation Guide for Windows

This guide will help you install PHP on Windows so you can run the Real Estate Management System.

## Option 1: XAMPP (Recommended - Easiest)

XAMPP includes PHP, Apache, MySQL, and other tools in one package.

### Step 1: Download XAMPP
1. Go to: https://www.apachefriends.org/download.html
2. Download the latest XAMPP version for Windows
3. Choose the version with PHP 8.0 or higher

### Step 2: Install XAMPP
1. Run the downloaded installer
2. Choose installation location (default: `C:\xampp`)
3. Install Apache and MySQL components
4. Complete the installation

### Step 3: Start Services
1. Open XAMPP Control Panel
2. Start Apache and MySQL services
3. Make sure both services show "Running" in green

### Step 4: Test Installation
1. Open your browser and go to: http://localhost
2. You should see the XAMPP welcome page
3. PHP is now installed and working!

## Option 2: Manual PHP Installation

### Step 1: Download PHP
1. Go to: https://www.php.net/downloads.php
2. Download the latest PHP 8.x ZIP package for Windows
3. Choose the "Thread Safe" version

### Step 2: Extract PHP
1. Create a folder: `C:\php`
2. Extract the downloaded ZIP to this folder

### Step 3: Configure PHP
1. Rename `php.ini-development` to `php.ini`
2. Open `php.ini` in a text editor
3. Uncomment and set these lines:
   ```ini
   extension_dir = "ext"
   extension=pdo_mysql
   extension=mysqli
   extension=fileinfo
   extension=openssl
   ```

### Step 4: Add to PATH
1. Press `Win + R`, type `sysdm.cpl` and press Enter
2. Go to "Advanced" tab → "Environment Variables"
3. Under "System variables", find "Path" and click "Edit"
4. Click "New" and add: `C:\php`
5. Click OK on all windows

### Step 5: Verify Installation
1. Open Command Prompt
2. Type: `php --version`
3. You should see PHP version information

## Setup Real Estate Management System

### After Installing PHP/XAMPP:

#### Step 1: Move Project Files
1. Copy the `Realty-v2` folder to:
   - **XAMPP**: `C:\xampp\htdocs\real-estate`
   - **Manual**: Any web server directory

#### Step 2: Setup Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create new database: `real_estate_db`
3. Import the schema: `database/schema.sql`

#### Step 3: Configure Environment
1. Copy `.env.example` to `.env`
2. Update database settings:
   ```env
   DB_HOST=localhost
   DB_NAME=real_estate_db
   DB_USER=root
   DB_PASSWORD=
   ```

#### Step 4: Install Dependencies
1. Open Command Prompt in project folder
2. Run: `composer install`

#### Step 5: Access Application
1. Open browser: http://localhost/real-estate/public
2. You should see the login page!

## Troubleshooting

### Common Issues:

**"php is not recognized"**
- Restart Command Prompt after adding to PATH
- Verify PHP installation path is correct

**"MySQL connection failed"**
- Make sure MySQL service is running
- Check database credentials in `.env`
- Verify database name exists

**"Composer not found"**
- Download and install Composer: https://getcomposer.org/
- Add Composer to system PATH

**Blank white page**
- Check PHP error logs
- Enable error reporting in `php.ini`
- Verify file permissions

### Next Steps:

Once PHP is installed and working:
1. The application will be fully functional
2. You can register new users
3. Add properties and manage them
4. View real-time dashboard analytics
5. Use all features we built

## Need Help?

If you encounter any issues during installation:
1. Check the error messages carefully
2. Verify each step was completed correctly
3. Make sure all services are running
4. Check file permissions

The Real Estate Management System is ready to use once PHP is properly installed!
