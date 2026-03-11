# Supabase Setup Guide for Real Estate Management System

This guide will help you set up the Real Estate Management System with Supabase instead of MySQL.

## What is Supabase?

Supabase is an open-source Firebase alternative that provides:
- PostgreSQL database
- Authentication system
- Real-time subscriptions
- Storage for files
- Edge functions

## Benefits for Your Project:

✅ **No local database setup needed**  
✅ **Built-in authentication**  
✅ **Real-time updates**  
✅ **File storage included**  
✅ **Easy deployment**  
✅ **Free tier available**

## Setup Steps:

### Step 1: Create Supabase Project

1. Go to: https://supabase.com
2. Click "Start your project" 
3. Sign up/login with GitHub/Google
4. Click "New Project"
5. Choose organization → Create new organization
6. Set project name: `real-estate-management`
7. Set database password (save it!)
8. Choose region closest to you
9. Click "Create new project"

### Step 2: Get Project Credentials

1. Wait for project to be created (2-3 minutes)
2. Go to: Settings → API
3. Copy these values:
   - **Project URL** (https://xxx.supabase.co)
   - **anon public key** (starts with eyJhbGciOiJIUzI1NiIs...)
   - **service_role key** (starts with eyJhbGciOiJIUzI1NiIs...)

### Step 3: Update Database Schema

1. Go to: Supabase Dashboard → SQL Editor
2. Click "New query"
3. Copy the entire contents of `database/schema.sql`
4. Paste into the SQL editor
5. Click "Run" to create all tables

### Step 4: Configure Environment

1. Copy `.env.example` to `.env`
2. Update with Supabase settings:
   ```env
   # Supabase Configuration
   SUPABASE_URL=https://your-project-id.supabase.co
   SUPABASE_ANON_KEY=your-anon-key-here
   SUPABASE_SERVICE_KEY=your-service-role-key-here
   
   # JWT Configuration (use Supabase JWT secret)
   JWT_SECRET=your-supabase-jwt-secret
   
   # Application Configuration
   APP_URL=http://localhost:8000
   APP_ENV=development
   APP_DEBUG=true
   ```

### Step 5: Install Supabase PHP Library

1. Update `composer.json`:
   ```json
   {
     "require": {
       "php": ">=8.0",
       "supabase/supabase-php": "^0.10.0",
       "firebase/php-jwt": "^6.0",
       "vlucas/phpdotenv": "^5.0"
     }
   }
   ```

2. Run: `composer install`

### Step 6: Update Database Configuration

Replace the contents of `config/database.php` with Supabase-compatible version.

### Step 7: Test the Application

1. Start PHP development server: `php -S localhost:8000`
2. Visit: http://localhost:8000
3. Register a new user
4. Test all features

## Migration Benefits:

### Authentication:
- ✅ Use Supabase Auth instead of custom JWT
- ✅ Social login options (Google, GitHub, etc.)
- ✅ Email verification built-in
- ✅ Password reset functionality

### Database:
- ✅ PostgreSQL (more powerful than MySQL)
- ✅ Real-time subscriptions
- ✅ Row Level Security
- ✅ Automatic backups

### Storage:
- ✅ File uploads directly to Supabase Storage
- ✅ CDN included
- ✅ Image transformations
- ✅ Automatic optimization

### API:
- ✅ Auto-generated REST API
- ✅ Real-time subscriptions
- ✅ GraphQL support
- ✅ Built-in rate limiting

## Next Steps:

1. **Create Supabase account and project**
2. **Run the database schema**
3. **Update environment variables**
4. **Install dependencies**
5. **Test the application**

## Troubleshooting:

**"Connection refused"**
- Check Supabase project URL
- Verify API keys are correct
- Ensure project is active

**"SQL errors"**
- Run schema in Supabase SQL Editor
- Check for PostgreSQL syntax differences
- Verify table creation

**"Authentication issues"**
- Check JWT secret matches Supabase
- Verify API key permissions
- Test with Supabase Auth UI

The Real Estate Management System will be even more powerful with Supabase!
