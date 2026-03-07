/**
 * Authentication utilities for Real Estate Management System
 * Handles login, registration, and session management
 */

class AuthManager {
    constructor() {
        this.api = window.api;
        this.init();
    }

    /**
     * Initialize authentication system
     */
    init() {
        // Check if user is on login page and has valid token
        if (this.api.isAuthenticated() && window.location.pathname === '/login') {
            window.location.href = '/dashboard';
        }

        // Check if user is trying to access protected routes without authentication
        this.checkAuthStatus();
    }

    /**
     * Check authentication status for protected routes
     */
    async checkAuthStatus() {
        const protectedRoutes = ['/dashboard', '/properties', '/tenants', '/payments', '/invoices'];
        const currentPath = window.location.pathname;

        if (protectedRoutes.some(route => currentPath.startsWith(route))) {
            if (!this.api.isAuthenticated()) {
                window.location.href = '/login';
                return;
            }

            try {
                // Verify token is still valid
                await this.api.get('/api/auth/me');
            } catch (error) {
                console.error('Auth check failed:', error);
                this.logout();
            }
        }
    }

    /**
     * Login user
     */
    async login(credentials) {
        try {
            const response = await this.api.post('/api/auth/login', credentials);
            
            if (response.token) {
                this.api.setToken(response.token);
                this.saveUserData(response.admin);
                
                // Show success message
                showToast('Login successful!', 'success');
                
                // Redirect to dashboard
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1000);
                
                return response;
            }
        } catch (error) {
            showToast(error.message || 'Login failed', 'error');
            throw error;
        }
    }

    /**
     * Register new user
     */
    async register(userData) {
        try {
            const response = await this.api.post('/api/auth/register', userData);
            
            showToast('Registration successful! Please login.', 'success');
            
            // Redirect to login page
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
            
            return response;
        } catch (error) {
            if (error.errors) {
                // Show validation errors
                Object.values(error.errors).forEach(errorMessage => {
                    showToast(errorMessage, 'error');
                });
            } else {
                showToast(error.message || 'Registration failed', 'error');
            }
            throw error;
        }
    }

    /**
     * Logout user
     */
    async logout() {
        try {
            if (this.api.isAuthenticated()) {
                await this.api.post('/api/auth/logout');
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.api.removeToken();
            this.clearUserData();
            window.location.href = '/login';
        }
    }

    /**
     * Get current user data
     */
    getCurrentUser() {
        const userData = localStorage.getItem('user_data');
        return userData ? JSON.parse(userData) : null;
    }

    /**
     * Save user data to localStorage
     */
    saveUserData(userData) {
        localStorage.setItem('user_data', JSON.stringify(userData));
    }

    /**
     * Clear user data from localStorage
     */
    clearUserData() {
        localStorage.removeItem('user_data');
    }

    /**
     * Update user data
     */
    updateUserData(newData) {
        const currentData = this.getCurrentUser() || {};
        const updatedData = { ...currentData, ...newData };
        this.saveUserData(updatedData);
    }

    /**
     * Check if user has specific role
     */
    hasRole(role) {
        const user = this.getCurrentUser();
        return user && user.role === role;
    }

    /**
     * Get user initials for avatar
     */
    getUserInitials() {
        const user = this.getCurrentUser();
        if (!user || !user.name) return 'U';
        
        return user.name
            .split(' ')
            .map(word => word.charAt(0).toUpperCase())
            .join('')
            .substring(0, 2);
    }
}

// Form validation utilities
class FormValidator {
    constructor(form) {
        this.form = form;
        this.errors = {};
    }

    /**
     * Validate required fields
     */
    validateRequired(fields) {
        fields.forEach(field => {
            const input = this.form.querySelector(`[name="${field}"]`);
            if (input && !input.value.trim()) {
                this.errors[field] = `${this.getFieldLabel(field)} is required`;
                this.showFieldError(input, this.errors[field]);
            } else {
                this.clearFieldError(input);
            }
        });

        return Object.keys(this.errors).length === 0;
    }

    /**
     * Validate email format
     */
    validateEmail(field) {
        const input = this.form.querySelector(`[name="${field}"]`);
        if (input && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                this.errors[field] = 'Please enter a valid email address';
                this.showFieldError(input, this.errors[field]);
            } else {
                this.clearFieldError(input);
            }
        }

        return !this.errors[field];
    }

    /**
     * Validate password confirmation
     */
    validatePasswordMatch(passwordField, confirmField) {
        const password = this.form.querySelector(`[name="${passwordField}"]`);
        const confirm = this.form.querySelector(`[name="${confirmField}"]`);
        
        if (password && confirm && password.value !== confirm.value) {
            this.errors[confirmField] = 'Passwords do not match';
            this.showFieldError(confirm, this.errors[confirmField]);
        } else {
            this.clearFieldError(confirm);
        }

        return !this.errors[confirmField];
    }

    /**
     * Validate minimum password length
     */
    validatePasswordLength(field, minLength = 8) {
        const input = this.form.querySelector(`[name="${field}"]`);
        if (input && input.value && input.value.length < minLength) {
            this.errors[field] = `Password must be at least ${minLength} characters long`;
            this.showFieldError(input, this.errors[field]);
        } else {
            this.clearFieldError(input);
        }

        return !this.errors[field];
    }

    /**
     * Show field error
     */
    showFieldError(input, message) {
        this.clearFieldError(input);
        
        input.classList.add('border-red-500');
        
        const errorElement = document.createElement('p');
        errorElement.className = 'mt-1 text-sm text-red-600 dark:text-red-400 field-error';
        errorElement.textContent = message;
        
        input.parentNode.appendChild(errorElement);
    }

    /**
     * Clear field error
     */
    clearFieldError(input) {
        input.classList.remove('border-red-500');
        
        const errorElement = input.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    /**
     * Get field label for error messages
     */
    getFieldLabel(field) {
        const label = this.form.querySelector(`label[for="${field}"]`);
        return label ? label.textContent.replace('*', '').trim() : field;
    }

    /**
     * Clear all errors
     */
    clearAllErrors() {
        this.form.querySelectorAll('.border-red-500').forEach(input => {
            input.classList.remove('border-red-500');
        });
        
        this.form.querySelectorAll('.field-error').forEach(error => {
            error.remove();
        });
        
        this.errors = {};
    }

    /**
     * Get all errors
     */
    getErrors() {
        return this.errors;
    }
}

// Initialize auth manager
const auth = new AuthManager();

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { AuthManager, FormValidator, auth };
} else {
    window.AuthManager = AuthManager;
    window.FormValidator = FormValidator;
    window.auth = auth;
}
