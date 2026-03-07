# Real Estate Management System

A comprehensive real estate management platform built with PHP, MySQL, Tailwind CSS, and vanilla JavaScript. This system allows administrators to manage properties, units, tenants, payments, and invoices with full authentication and dashboard analytics.

## Features

- **Property Management**: Create, update, and manage properties with detailed information
- **Unit Management**: Manage individual units within properties
- **Tenant Management**: Track tenant information and assignments
- **Payment Tracking**: Record and monitor rent payments
- **Invoice Generation**: Create and manage invoices for tenants
- **Dashboard Analytics**: Real-time statistics and revenue charts
- **Authentication**: Secure JWT-based authentication system
- **Responsive Design**: Modern UI that works on all devices
- **API Support**: RESTful API for integration

## Technology Stack

### Backend
- **PHP 8+**: Server-side programming language
- **MySQL 8+**: Database management system
- **Composer**: Dependency management
- **JWT**: JSON Web Tokens for authentication

### Frontend
- **HTML5**: Markup language
- **Tailwind CSS**: Utility-first CSS framework
- **Vanilla JavaScript (ES6+)**: Client-side scripting
- **Chart.js**: Data visualization library

### Libraries
- **firebase/php-jwt**: JWT implementation
- **vlucas/phpdotenv**: Environment variable management
- **phpmailer/phpmailer**: Email functionality
- **respect/validation**: Input validation

## Installation

### Prerequisites

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache or Nginx web server
- Composer (PHP package manager)

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd real-estate-management
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Configure Environment

1. Copy the environment file:
```bash
cp .env.example .env
```

2. Edit the `.env` file with your database and application settings:
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=real_estate_db
DB_USER=root
DB_PASSWORD=your_password

# JWT Configuration
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
JWT_EXPIRE=86400

# Application Configuration
APP_URL=http://localhost/real-estate-management
APP_ENV=development
APP_DEBUG=true

# Email Configuration (Optional)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### Step 4: Set Up Database

1. Create a new database:
```sql
CREATE DATABASE real_estate_db;
```

2. Import the database schema:
```bash
mysql -u username -p real_estate_db < database/schema.sql
```

### Step 5: Configure Web Server

#### Apache Configuration

Create a virtual host configuration:

```apache
<VirtualHost *:80>
    DocumentRoot /path/to/real-estate-management/public
    ServerName real-estate.local
    
    <Directory /path/to/real-estate-management/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to your hosts file:
```
127.0.0.1 real-estate.local
```

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name real-estate.local;
    root /path/to/real-estate-management/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Step 6: Set File Permissions

```bash
chmod -R 755 storage/
chmod -R 755 public/
```

### Step 7: Access the Application

Open your web browser and navigate to:
```
http://real-estate.local
```

## API Documentation

### Authentication Endpoints

#### Register Admin
```
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "business_name": "ABC Properties",
    "phone": "+1234567890"
}
```

#### Login
```
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

#### Get Current User
```
GET /api/auth/me
Authorization: Bearer <jwt_token>
```

#### Logout
```
POST /api/auth/logout
Authorization: Bearer <jwt_token>
```

### Property Endpoints

#### Get Properties
```
GET /api/properties?page=1&search=&type=&status=
Authorization: Bearer <jwt_token>
```

#### Get Property
```
GET /api/properties/{id}
Authorization: Bearer <jwt_token>
```

#### Create Property
```
POST /api/properties
Authorization: Bearer <jwt_token>
Content-Type: application/json

{
    "name": "Sunset Apartments",
    "address": "123 Main St, City, State",
    "type": "residential",
    "category": "Apartment Building",
    "description": "Modern apartment building with amenities",
    "year_built": 2020,
    "bedrooms": 2,
    "bathrooms": 2,
    "kitchens": 1,
    "parking": 1,
    "rent_price": 1500.00,
    "status": "active",
    "amenities": ["Pool", "Gym", "Parking"],
    "images": ["image1.jpg", "image2.jpg"]
}
```

#### Update Property
```
PUT /api/properties/{id}
Authorization: Bearer <jwt_token>
Content-Type: application/json

{
    "name": "Updated Property Name",
    "status": "maintenance"
}
```

#### Delete Property
```
DELETE /api/properties/{id}
Authorization: Bearer <jwt_token>
```

### Dashboard Endpoints

#### Get Dashboard Stats
```
GET /api/dashboard/stats
Authorization: Bearer <jwt_token>
```

#### Get Revenue Data
```
GET /api/dashboard/revenue?months=12
Authorization: Bearer <jwt_token>
```

#### Get Recent Activities
```
GET /api/dashboard/recent-activities?limit=10
Authorization: Bearer <jwt_token>
```

## Project Structure

```
real-estate-management/
├── app/
│   ├── controllers/          # MVC Controllers
│   ├── middleware/          # Authentication middleware
│   └── models/              # Data models
├── config/                  # Configuration files
├── database/                # Database schema
├── public/                  # Web root directory
│   ├── assets/             # CSS, JS, images
│   └── index.php           # Application entry point
├── routes/                  # Route definitions
├── storage/                 # File uploads and logs
├── views/                   # View templates
├── vendor/                  # Composer dependencies
├── .env.example            # Environment template
├── composer.json           # PHP dependencies
└── README.md               # This file
```

## Security Features

- **Password Hashing**: Uses PHP's `password_hash()` function
- **JWT Authentication**: Secure token-based authentication
- **SQL Injection Prevention**: Uses prepared statements
- **CSRF Protection**: Cross-site request forgery protection
- **Input Validation**: Server-side input sanitization
- **File Upload Security**: Restricted file types and sizes

## Development

### Running in Development

1. Set up a local development environment (XAMPP, WAMP, MAMP, or Docker)
2. Configure your virtual hosts to point to the `public/` directory
3. Ensure `mod_rewrite` is enabled for Apache
4. Set PHP error reporting to `E_ALL` for debugging

### Adding New Features

1. Create controllers in `app/controllers/`
2. Add routes in `routes/web.php` or `routes/api.php`
3. Create views in `views/` directory
4. Follow the existing code patterns and naming conventions

## Deployment

### Production Deployment Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Change the JWT secret to a secure random string
- [ ] Configure HTTPS with SSL certificate
- [ ] Set up proper file permissions
- [ ] Configure database backups
- [ ] Set up monitoring and logging
- [ ] Test all functionality in production environment

## Support

For issues and questions:
1. Check the documentation above
2. Review the error logs in `storage/logs/`
3. Ensure all prerequisites are met
4. Verify database connection and permissions

## License

This project is licensed under the MIT License.
