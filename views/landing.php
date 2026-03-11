<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Cornerstone Realty - Complete Property Management Solution');
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-600 to-primary-800 text-white">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Complete Property Management
                <span class="block text-primary-200">Simplified</span>
            </h1>
            <p class="text-xl md:text-2xl text-primary-100 mb-8 max-w-3xl mx-auto">
                Streamline your real estate operations with our comprehensive property management platform. 
                Manage properties, tenants, payments, and maintenance all in one place.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/admin/login" class="inline-flex items-center px-8 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-primary-50 transition-colors">
                    <i class="fas fa-right-to-bracket mr-2"></i>
                    Admin Login
                </a>
                <a href="/admin/properties/create" class="inline-flex items-center px-8 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-rocket mr-2"></i>
                    Get Started
                </a>
            </div>
        </div>
    </div>
    
    <!-- Decorative elements -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-24 text-gray-50 dark:text-gray-900" preserveAspectRatio="none" viewBox="0 0 1440 54" fill="currentColor">
            <path d="M0 22L120 16.7C240 11 480 1.7 720 0.8C960 0 1200 7 1320 10.7L1440 14V54H1320C1200 54 960 54 720 54C480 54 240 54 120 54H0V22Z"></path>
        </svg>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Everything You Need to Manage Properties
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Powerful features designed to make property management efficient and hassle-free
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Property Management -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-building text-primary-600 dark:text-primary-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Property Management</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Manage unlimited properties with detailed information, photos, and documentation. 
                    Track property status, occupancy, and performance metrics.
                </p>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Unlimited properties</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Photo galleries</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Document storage</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Performance analytics</li>
                </ul>
            </div>

            <!-- Tenant Management -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Tenant Management</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Complete tenant database with lease management, communication tools, 
                    and rental payment tracking.
                </p>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Tenant profiles</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Lease agreements</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Communication logs</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Background checks</li>
                </ul>
            </div>

            <!-- Financial Management -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-dollar-sign text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Financial Management</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Automated rent collection, expense tracking, and comprehensive financial 
                    reporting with real-time insights.
                </p>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Online payments</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Invoice generation</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Expense tracking</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Financial reports</li>
                </ul>
            </div>

            <!-- Maintenance Management -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-tools text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Maintenance Tracking</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Streamlined maintenance request system with automated workflows, 
                    vendor management, and cost tracking.
                </p>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Request tracking</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Vendor management</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Cost tracking</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Automated alerts</li>
                </ul>
            </div>

            <!-- Reporting & Analytics -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-chart-line text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Analytics & Reports</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Comprehensive dashboards and custom reports to track performance, 
                    occupancy rates, and financial metrics.
                </p>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Real-time dashboards</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Custom reports</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Data export</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Trend analysis</li>
                </ul>
            </div>

            <!-- Communication Tools -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-envelope text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Communication Hub</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Built-in messaging system, email templates, and notification center 
                    for seamless tenant communication.
                </p>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Messaging system</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Email templates</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Notifications</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Broadcast messages</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-20 bg-primary-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Trusted by Property Managers</h2>
            <p class="text-xl text-primary-100">Join thousands of professionals managing their properties efficiently</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">10K+</div>
                <div class="text-primary-100">Properties Managed</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">50K+</div>
                <div class="text-primary-100">Happy Tenants</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">$2M+</div>
                <div class="text-primary-100">Monthly Rent Processed</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold mb-2">99.9%</div>
                <div class="text-primary-100">Uptime</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Ready to Transform Your Property Management?
        </h2>
        <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">
            Get started today and experience the difference of a truly comprehensive property management solution.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/admin/login" class="inline-flex items-center px-8 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-rocket mr-2"></i>
                Start Free Trial
            </a>
            <a href="#demo" class="inline-flex items-center px-8 py-3 bg-white text-primary-600 font-semibold rounded-lg border border-primary-600 hover:bg-primary-50 transition-colors">
                <i class="fas fa-play mr-2"></i>
                Request Demo
            </a>
        </div>
    </div>
</section>

<!-- Product Section -->
<section id="product" class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Powerful Property Management Platform
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Everything you need to manage your real estate business efficiently
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-building text-primary-600 dark:text-primary-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Property Management</h3>
                <p class="text-gray-600 dark:text-gray-400">Manage unlimited properties with detailed tracking and analytics</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-primary-600 dark:text-primary-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Tenant Management</h3>
                <p class="text-gray-600 dark:text-gray-400">Streamlined tenant onboarding and communication tools</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-dollar-sign text-primary-600 dark:text-primary-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Financial Tracking</h3>
                <p class="text-gray-600 dark:text-gray-400">Automated rent collection and comprehensive financial reporting</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Advanced Features for Modern Property Management
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Cutting-edge tools designed to make property management effortless
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <i class="fas fa-mobile-alt text-primary-600 text-2xl mb-4"></i>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Mobile App</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Manage properties on the go with our mobile application</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <i class="fas fa-chart-line text-primary-600 text-2xl mb-4"></i>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Analytics Dashboard</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Real-time insights and performance metrics</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <i class="fas fa-bell text-primary-600 text-2xl mb-4"></i>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Smart Notifications</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Automated alerts for important events and deadlines</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <i class="fas fa-shield-alt text-primary-600 text-2xl mb-4"></i>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Secure Data</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Bank-level security for all your sensitive information</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Simple, Transparent Pricing
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Choose the plan that fits your business needs
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-50 dark:bg-gray-900 p-8 rounded-lg">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">1 Room Apartment</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-4">₦3,000<span class="text-lg text-gray-600">/year</span></p>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400 mb-6">
                    <li>Single room management</li>
                    <li>Basic tenant tracking</li>
                    <li>Rent collection reminders</li>
                    <li>Email support</li>
                </ul>
                <button class="w-full py-2 px-4 border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50">Get Started</button>
            </div>
            <div class="bg-primary-600 text-white p-8 rounded-lg transform scale-105">
                <h3 class="text-xl font-semibold mb-2">Self Contained</h3>
                <p class="text-3xl font-bold mb-4">₦5,000<span class="text-lg text-primary-200">/unit/year</span></p>
                <ul class="space-y-2 text-primary-100 mb-6">
                    <li>Self-contained apartment</li>
                    <li>Advanced tenant management</li>
                    <li>Automated rent collection</li>
                    <li>Maintenance tracking</li>
                    <li>Priority support</li>
                    <li>Mobile app access</li>
                </ul>
                <button class="w-full py-2 px-4 bg-white text-primary-600 rounded-lg hover:bg-primary-50">Get Started</button>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 p-8 rounded-lg">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Multi-Unit</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-4">₦8,000<span class="text-lg text-gray-600">/unit/year</span></p>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400 mb-6">
                    <li>Multiple apartment units</li>
                    <li>Advanced analytics</li>
                    <li>Financial reporting</li>
                    <li>API access</li>
                    <li>Dedicated support</li>
                    <li>Custom integrations</li>
                </ul>
                <button class="w-full py-2 px-4 border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50">Contact Sales</button>
            </div>
        </div>
    </div>
</section>

<!-- Demo Section -->
<section id="demo" class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                See It in Action
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Take a guided tour of our platform and see how it can transform your business
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8">
            <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-gray-700 rounded-lg mb-6 flex items-center justify-center">
                <i class="fas fa-play-circle text-6xl text-primary-600"></i>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Interactive Product Demo</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Experience the full power of our platform with a personalized demo</p>
                <button class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700">
                    <i class="fas fa-calendar mr-2"></i>
                    Schedule Demo
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Integrations Section -->
<section id="integrations" class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Seamless Integrations
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Connect with your favorite tools and services
            </p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-calculator text-2xl text-gray-600 dark:text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">QuickBooks</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-envelope text-2xl text-gray-600 dark:text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Gmail</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-credit-card text-2xl text-gray-600 dark:text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Stripe</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-file-invoice text-2xl text-gray-600 dark:text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Xero</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-map-marked-alt text-2xl text-gray-600 dark:text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Google Maps</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-cloud text-2xl text-gray-600 dark:text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Dropbox</p>
            </div>
        </div>
    </div>
</section>

<!-- Support Section -->
<section id="support" class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                We're Here to Help
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Get the support you need, when you need it
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-primary-600 dark:text-primary-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">24/7 Support</h3>
                <p class="text-gray-600 dark:text-gray-400">Round-the-clock assistance for all your needs</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book text-primary-600 dark:text-primary-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Extensive Documentation</h3>
                <p class="text-gray-600 dark:text-gray-400">Comprehensive guides and tutorials</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-graduation-cap text-primary-600 dark:text-primary-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Training Resources</h3>
                <p class="text-gray-600 dark:text-gray-400">Video tutorials and best practices</p>
            </div>
        </div>
    </div>
</section>

<!-- Help Center Section -->
<section id="help" class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Help Center
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Find answers to common questions and learn how to make the most of our platform
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Popular Articles</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-primary-600 hover:text-primary-700">Getting Started Guide</a></li>
                    <li><a href="#" class="text-primary-600 hover:text-primary-700">Setting Up Your First Property</a></li>
                    <li><a href="#" class="text-primary-600 hover:text-primary-700">Managing Tenants Effectively</a></li>
                    <li><a href="#" class="text-primary-600 hover:text-primary-700">Understanding Financial Reports</a></li>
                </ul>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Help</h3>
                <div class="space-y-4">
                    <input type="text" placeholder="Search for help..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    <button class="w-full py-2 px-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        <i class="fas fa-search mr-2"></i>
                        Search Help Center
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Get in Touch
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                We'd love to hear from you. Reach out with any questions or feedback.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Contact Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-primary-600 mr-4"></i>
                        <span class="text-gray-600 dark:text-gray-400">support@cornerstonerealty.com</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-primary-600 mr-4"></i>
                        <span class="text-gray-600 dark:text-gray-400">1-800-CORNERSTONE</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-primary-600 mr-4"></i>
                        <span class="text-gray-600 dark:text-gray-400">123 Business Ave, Suite 100, New York, NY 10001</span>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Send us a Message</h3>
                <form class="space-y-4">
                    <input type="text" placeholder="Your Name" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    <input type="email" placeholder="Your Email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    <textarea placeholder="Your Message" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"></textarea>
                    <button type="submit" class="w-full py-2 px-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- API Docs Section -->
<section id="api" class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Developer API
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Integrate Cornerstone Realty with your existing systems using our powerful REST API
            </p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">API Features</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>• RESTful architecture</li>
                        <li>• JSON responses</li>
                        <li>• OAuth 2.0 authentication</li>
                        <li>• Rate limiting</li>
                        <li>• Webhook support</li>
                        <li>• Comprehensive documentation</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Available Endpoints</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>• Properties CRUD</li>
                        <li>• Tenants management</li>
                        <li>• Payment processing</li>
                        <li>• Maintenance requests</li>
                        <li>• Financial reports</li>
                        <li>• User authentication</li>
                    </ul>
                </div>
            </div>
            <div class="text-center">
                <button class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700">
                    <i class="fas fa-code mr-2"></i>
                    View API Documentation
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Status Section -->
<section id="status" class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                System Status
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Real-time status of all Cornerstone Realty services
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-900 dark:text-white">API Services</span>
                    </div>
                    <span class="text-green-600 dark:text-green-400">Operational</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-900 dark:text-white">Web Application</span>
                    </div>
                    <span class="text-green-600 dark:text-green-400">Operational</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-900 dark:text-white">Database</span>
                    </div>
                    <span class="text-green-600 dark:text-green-400">Operational</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-900 dark:text-white">Email Services</span>
                    </div>
                    <span class="text-yellow-600 dark:text-yellow-400">Degraded Performance</span>
                </div>
            </div>
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">Last updated: <span id="status-time">Just now</span></p>
            </div>
        </div>
    </div>
</section>

<!-- Company Section -->
<section id="company" class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                About Cornerstone Realty
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Transforming property management since 2020
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
                    Cornerstone Realty was founded with a simple mission: to make property management effortless for landlords and property managers. Our platform combines cutting-edge technology with industry expertise to deliver solutions that real businesses actually need.
                </p>
                <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
                    Today, we help thousands of property managers across the country streamline their operations, increase their revenue, and provide better service to their tenants.
                </p>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-3xl font-bold text-primary-600">10K+</p>
                        <p class="text-gray-600 dark:text-gray-400">Properties Managed</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-primary-600">50K+</p>
                        <p class="text-gray-600 dark:text-gray-400">Happy Tenants</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-primary-600">99.9%</p>
                        <p class="text-gray-600 dark:text-gray-400">Uptime</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-8 text-center">
                <i class="fas fa-building text-6xl text-primary-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Built for Property Managers</h3>
                <p class="text-gray-600 dark:text-gray-400">By property management experts</p>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section id="blog" class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Latest from Our Blog
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Tips, insights, and industry news for property managers
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <article class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="h-48 bg-gray-200 dark:bg-gray-700"></div>
                <div class="p-6">
                    <div class="text-sm text-primary-600 mb-2">March 15, 2024</div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">5 Ways to Increase Property Value</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Discover proven strategies to maximize your property's value and attract quality tenants.</p>
                    <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Read More →</a>
                </div>
            </article>
            <article class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="h-48 bg-gray-200 dark:bg-gray-700"></div>
                <div class="p-6">
                    <div class="text-sm text-primary-600 mb-2">March 10, 2024</div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Tenant Retention Strategies</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Learn how to keep your best tenants longer and reduce turnover costs.</p>
                    <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Read More →</a>
                </div>
            </article>
            <article class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="h-48 bg-gray-200 dark:bg-gray-700"></div>
                <div class="p-6">
                    <div class="text-sm text-primary-600 mb-2">March 5, 2024</div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Digital Marketing for Rentals</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Modern marketing techniques to fill vacancies faster and attract better tenants.</p>
                    <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Read More →</a>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Careers Section -->
<section id="careers" class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Join Our Team
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Help us build the future of property management
            </p>
        </div>
        <div class="bg-primary-600 text-white rounded-lg p-8 text-center">
            <h3 class="text-2xl font-semibold mb-4">We're Hiring!</h3>
            <p class="text-primary-100 mb-6">We're always looking for talented people to join our growing team.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white/10 rounded-lg p-4">
                    <h4 class="font-semibold mb-2">Engineering</h4>
                    <p class="text-primary-200 text-sm">Frontend, Backend, DevOps</p>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <h4 class="font-semibold mb-2">Customer Success</h4>
                    <p class="text-primary-200 text-sm">Support, Onboarding, Training</p>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <h4 class="font-semibold mb-2">Business</h4>
                    <p class="text-primary-200 text-sm">Sales, Marketing, Operations</p>
                </div>
            </div>
            <button class="inline-flex items-center px-6 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-primary-50">
                <i class="fas fa-briefcase mr-2"></i>
                View Open Positions
            </button>
        </div>
    </div>
</section>

<!-- Privacy Section -->
<section id="privacy" class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Privacy & Security
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Your data security and privacy are our top priorities
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg">
                <div class="flex items-center mb-4">
                    <i class="fas fa-shield-alt text-primary-600 text-2xl mr-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Data Protection</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400">We use industry-standard encryption and security measures to protect your data. All information is stored in secure, SOC 2 compliant data centers.</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user-shield text-primary-600 text-2xl mr-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Privacy First</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400">We never sell your data to third parties. You maintain complete control over your information and can export or delete it at any time.</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg">
                <div class="flex items-center mb-4">
                    <i class="fas fa-lock text-primary-600 text-2xl mr-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Compliance</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400">We comply with GDPR, CCPA, and other major privacy regulations. Our practices are regularly audited to ensure compliance.</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg">
                <div class="flex items-center mb-4">
                    <i class="fas fa-eye-slash text-primary-600 text-2xl mr-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Transparency</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Our privacy policy is clear and easy to understand. We're always transparent about how we collect, use, and protect your data.</p>
            </div>
        </div>
        <div class="text-center mt-12">
            <button class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700">
                <i class="fas fa-file-alt mr-2"></i>
                Read Full Privacy Policy
            </button>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">Cornerstone Realty</h3>
                <p class="text-gray-400">
                    Your complete property management solution for efficient operations and maximum ROI.
                </p>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Product</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#features" class="hover:text-white">Features</a></li>
                    <li><a href="#pricing" class="hover:text-white">Pricing</a></li>
                    <li><a href="#demo" class="hover:text-white">Demo</a></li>
                    <li><a href="#integrations" class="hover:text-white">Integrations</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Support</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#help" class="hover:text-white">Help Center</a></li>
                    <li><a href="#contact" class="hover:text-white">Contact</a></li>
                    <li><a href="#api" class="hover:text-white">API Docs</a></li>
                    <li><a href="#status" class="hover:text-white">Status</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Company</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#about" class="hover:text-white">About</a></li>
                    <li><a href="#blog" class="hover:text-white">Blog</a></li>
                    <li><a href="#careers" class="hover:text-white">Careers</a></li>
                    <li><a href="#privacy" class="hover:text-white">Privacy</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 Cornerstone Realty. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Dark mode toggle
function toggleDarkMode() {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
}

// Initialize dark mode
if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
}
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
