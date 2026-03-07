# How to Find Your Supabase Service Role Key

## 📍 Where to Find Service Role Key:

### Method 1: Supabase Dashboard (Recommended)

1. **Go to**: https://supabase.com
2. **Select your project**: `real-estate-management`
3. **Navigate to**: Settings → API
4. **Look for**: "service_role (secret)" key

### Method 2: Project Settings

1. **Click**: ⚙️ Settings icon (left sidebar)
2. **Select**: "API" from the menu
3. **Scroll down**: to "Project API keys" section
4. **Find**: "service_role" key (not the "anon" key!)

## 🔑 Key Differences:

### **anon public key** (starts with `eyJhbGciOiJIUzI1NiIs...`)
- Used for **public access**
- Limited permissions
- Safe to expose in frontend

### **service_role key** (starts with `eyJhbGciOiJIUzI1NiIs...`)
- Used for **admin/backend operations**
- Full database access
- **KEEP SECRET** - never expose in frontend

## 📋 What You'll See:

```
Project URL
https://your-project-id.supabase.co

Project API keys
anon public    eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9... (public)
service_role   eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9... (secret)
```

## ⚠️ Important Notes:

1. **Copy the service_role key** (not the anon key)
2. **Save it securely** - it gives full database access
3. **Use it in your .env** as `SUPABASE_SERVICE_KEY`
4. **Never commit it to Git** or expose in frontend code

## 🔐 Security Best Practices:

- ✅ Use service_role key only in backend
- ✅ Use anon key for frontend operations
- ✅ Store service_role key in environment variables
- ✅ Never hardcode keys in your application

## 🚀 Once You Have the Key:

1. **Update your `.env`** file with the service_role key
2. **Re-run the database schema** (the fixed version)
3. **Test your application**

The service_role key is essential for your backend to have full database access!
