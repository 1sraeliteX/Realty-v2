/**
 * Dashboard JavaScript functionality
 * Handles charts, real-time updates, and dashboard interactions
 */

class DashboardManager {
    constructor() {
        this.api = window.api;
        this.charts = {};
        this.refreshInterval = null;
        this.init();
    }

    /**
     * Initialize dashboard
     */
    async init() {
        if (window.location.pathname !== '/dashboard') return;

        await this.loadDashboardData();
        this.initEventListeners();
        this.startAutoRefresh();
    }

    /**
     * Load dashboard data
     */
    async loadDashboardData() {
        try {
            // Show loading state
            this.showLoadingState();

            // Load all dashboard data in parallel
            const [stats, revenueData, activities, properties] = await Promise.all([
                this.api.get('/api/dashboard/stats'),
                this.api.get('/api/dashboard/revenue?months=12'),
                this.api.get('/api/dashboard/recent-activities?limit=10'),
                this.api.get('/api/dashboard/recent-properties?limit=5')
            ]);

            // Update UI with loaded data
            this.updateStatsCards(stats);
            this.updateRevenueChart(revenueData);
            this.updateOccupancyChart(stats);
            this.updateRecentActivities(activities);
            this.updateRecentProperties(properties);

            // Hide loading state
            this.hideLoadingState();

        } catch (error) {
            console.error('Failed to load dashboard data:', error);
            showToast('Failed to load dashboard data', 'error');
            this.hideLoadingState();
        }
    }

    /**
     * Update statistics cards
     */
    updateStatsCards(stats) {
        const cards = {
            'total-properties': stats.total_properties || 0,
            'total-units': stats.total_units || 0,
            'active-tenants': stats.active_tenants || 0,
            'occupancy-rate': stats.occupancy_rate || 0,
            'monthly-revenue': stats.monthly_revenue || 0,
            'occupied-units': stats.occupied_units || 0,
            'pending-payments': stats.pending_payments || 0
        };

        Object.entries(cards).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                if (id.includes('revenue')) {
                    element.textContent = `$${Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                } else if (id.includes('rate')) {
                    element.textContent = `${value}%`;
                } else {
                    element.textContent = Number(value).toLocaleString();
                }
            }
        });
    }

    /**
     * Update revenue chart
     */
    updateRevenueChart(revenueData) {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        // Destroy existing chart if it exists
        if (this.charts.revenue) {
            this.charts.revenue.destroy();
        }

        const labels = revenueData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });

        const data = revenueData.map(item => item.revenue);

        this.charts.revenue = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: data,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Revenue: $${Number(context.parsed.y).toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Update occupancy chart
     */
    updateOccupancyChart(stats) {
        const ctx = document.getElementById('occupancyChart');
        if (!ctx) return;

        // Destroy existing chart if it exists
        if (this.charts.occupancy) {
            this.charts.occupancy.destroy();
        }

        const occupiedUnits = stats.occupied_units || 0;
        const totalUnits = stats.total_units || 0;
        const vacantUnits = totalUnits - occupiedUnits;

        this.charts.occupancy = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Occupied', 'Vacant'],
                datasets: [{
                    data: [occupiedUnits, vacantUnits],
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Update recent activities
     */
    updateRecentActivities(activities) {
        const container = document.getElementById('recent-activities');
        if (!container) return;

        if (activities.length === 0) {
            container.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent activities</p>';
            return;
        }

        const html = activities.map(activity => `
            <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-${this.getActivityIcon(activity.action)} text-primary-600 dark:text-primary-400 text-xs"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900 dark:text-white">
                        ${activity.description}
                    </p>
                    ${activity.property_name ? `
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Property: ${activity.property_name}
                        </p>
                    ` : ''}
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        ${this.formatDate(activity.created_at)}
                    </p>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    /**
     * Update recent properties
     */
    updateRecentProperties(properties) {
        const container = document.getElementById('recent-properties');
        if (!container) return;

        if (properties.length === 0) {
            container.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No properties found</p>';
            return;
        }

        const html = properties.map(property => `
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">${property.name}</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${property.address}</p>
                    <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                        <span class="mr-3">${property.unit_count} units</span>
                        <span>${property.occupied_units} occupied</span>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        ${property.status}
                    </span>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    /**
     * Get activity icon based on action type
     */
    getActivityIcon(action) {
        const icons = {
            'login': 'sign-in-alt',
            'logout': 'sign-out-alt',
            'create': 'plus',
            'update': 'edit',
            'delete': 'trash',
            'register': 'user-plus',
            'payment': 'dollar-sign',
            'invoice': 'file-invoice'
        };
        
        return icons[action] || 'circle';
    }

    /**
     * Format date for display
     */
    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 1) {
            return 'Yesterday at ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        } else if (diffDays < 7) {
            return diffDays + ' days ago';
        } else {
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined 
            });
        }
    }

    /**
     * Initialize event listeners
     */
    initEventListeners() {
        // Revenue period selector
        const periodSelector = document.getElementById('revenue-period');
        if (periodSelector) {
            periodSelector.addEventListener('change', async (e) => {
                const months = parseInt(e.target.value);
                try {
                    const revenueData = await this.api.get(`/api/dashboard/revenue?months=${months}`);
                    this.updateRevenueChart(revenueData);
                } catch (error) {
                    console.error('Failed to update revenue chart:', error);
                    showToast('Failed to update revenue data', 'error');
                }
            });
        }

        // Refresh button
        const refreshBtn = document.getElementById('refresh-dashboard');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.loadDashboardData();
            });
        }
    }

    /**
     * Start auto-refresh
     */
    startAutoRefresh() {
        // Refresh dashboard every 5 minutes
        this.refreshInterval = setInterval(() => {
            this.loadDashboardData();
        }, 5 * 60 * 1000);
    }

    /**
     * Stop auto-refresh
     */
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    /**
     * Show loading state
     */
    showLoadingState() {
        const loadingElements = document.querySelectorAll('[data-loading]');
        loadingElements.forEach(element => {
            element.classList.add('opacity-50', 'pointer-events-none');
        });
    }

    /**
     * Hide loading state
     */
    hideLoadingState() {
        const loadingElements = document.querySelectorAll('[data-loading]');
        loadingElements.forEach(element => {
            element.classList.remove('opacity-50', 'pointer-events-none');
        });
    }

    /**
     * Cleanup when navigating away
     */
    destroy() {
        this.stopAutoRefresh();
        
        // Destroy charts
        Object.values(this.charts).forEach(chart => {
            if (chart) chart.destroy();
        });
        this.charts = {};
    }
}

// Initialize dashboard manager
let dashboard;

document.addEventListener('DOMContentLoaded', () => {
    dashboard = new DashboardManager();
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (dashboard) {
        dashboard.destroy();
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardManager;
} else {
    window.DashboardManager = DashboardManager;
}
