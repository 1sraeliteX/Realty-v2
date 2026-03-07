/**
 * API Client for Real Estate Management System
 * Handles all API requests with JWT authentication and error handling
 */

class ApiClient {
    constructor() {
        this.baseURL = window.location.origin;
        this.token = localStorage.getItem('jwt_token');
    }

    /**
     * Set JWT token for authentication
     */
    setToken(token) {
        this.token = token;
        localStorage.setItem('jwt_token', token);
    }

    /**
     * Remove JWT token (logout)
     */
    removeToken() {
        this.token = null;
        localStorage.removeItem('jwt_token');
    }

    /**
     * Get current token
     */
    getToken() {
        return this.token;
    }

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return !!this.token;
    }

    /**
     * Make API request
     */
    async request(endpoint, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (this.token) {
            defaultOptions.headers.Authorization = `Bearer ${this.token}`;
        }

        const finalOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers
            }
        };

        try {
            const response = await fetch(this.baseURL + endpoint, finalOptions);
            const data = await response.json();

            if (!response.ok) {
                if (response.status === 401) {
                    // Token expired or invalid
                    this.removeToken();
                    window.location.href = '/login';
                    throw new Error('Session expired. Please login again.');
                }
                throw new Error(data.error || 'Request failed');
            }

            return data;
        } catch (error) {
            console.error('API Request Error:', error);
            throw error;
        }
    }

    /**
     * GET request
     */
    async get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        
        return this.request(url, { method: 'GET' });
    }

    /**
     * POST request
     */
    async post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    /**
     * PUT request
     */
    async put(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    /**
     * DELETE request
     */
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }

    /**
     * Upload file
     */
    async upload(endpoint, formData) {
        const options = {
            method: 'POST',
            body: formData
        };

        // Don't set Content-Type header for file uploads
        // Browser will set it with boundary
        if (this.token) {
            options.headers = {
                'Authorization': `Bearer ${this.token}`,
                'X-Requested-With': 'XMLHttpRequest'
            };
        }

        try {
            const response = await fetch(this.baseURL + endpoint, options);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Upload failed');
            }

            return data;
        } catch (error) {
            console.error('Upload Error:', error);
            throw error;
        }
    }
}

// Create global API client instance
const api = new ApiClient();

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ApiClient, api };
} else {
    window.ApiClient = ApiClient;
    window.api = api;
}
