<?php
$title = 'Dashboard';
$pageTitle = 'Dashboard Overview';

// Initialize variables with default values to prevent undefined variable errors
$stats = $stats ?? [
    'total_properties' => 0,
    'total_units' => 0,
    'active_tenants' => 0,
    'occupancy_rate' => 0,
    'monthly_revenue' => 0,
    'occupied_units' => 0,
    'pending_payments' => 0
];

$recentProperties = $recentProperties ?? [];
$recentActivities = $recentActivities ?? [];
$revenueData = $revenueData ?? [];

$content = ob_start();
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-primary-100 dark:bg-primary-900 rounded-lg p-3">
                <i class="fas fa-home text-primary-600 dark:text-primary-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Properties</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['total_properties']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                <i class="fas fa-door-open text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Units</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['total_units']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Tenants</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['active_tenants']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                <i class="fas fa-percentage text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupancy Rate</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $stats['occupancy_rate']; ?>%</p>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Monthly Revenue</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">$<?php echo number_format($stats['monthly_revenue'], 2); ?></p>
            </div>
            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                <i class="fas fa-dollar-sign text-green-600 dark:text-green-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupied Units</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?php echo number_format($stats['occupied_units']); ?></p>
            </div>
            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                <i class="fas fa-check-circle text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Payments</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400"><?php echo number_format($stats['pending_payments']); ?></p>
            </div>
            <div class="flex-shrink-0 bg-red-100 dark:bg-red-900 rounded-lg p-3">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Revenue Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Revenue Overview</h3>
            <select id="revenue-period" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="12">Last 12 months</option>
                <option value="6">Last 6 months</option>
                <option value="3">Last 3 months</option>
            </select>
        </div>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Occupancy Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Occupancy Status</h3>
        <div class="h-64">
            <canvas id="occupancyChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
                <button onclick="window.location.href='/properties/create'" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors cursor-pointer">
                    <i class="fas fa-plus-circle text-blue-600 dark:text-blue-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Add Property</span>
                </button>
                <button onclick="window.location.href='/tenants/create'" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors cursor-pointer">
                    <i class="fas fa-user-plus text-green-600 dark:text-green-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Add Tenant</span>
                </button>
                <button onclick="window.location.href='/payments/create'" class="flex flex-col items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors cursor-pointer">
                    <i class="fas fa-file-invoice-dollar text-yellow-600 dark:text-yellow-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Create Invoice</span>
                </button>
                <button onclick="window.location.href='/properties'" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors cursor-pointer">
                    <i class="fas fa-tools text-purple-600 dark:text-purple-400 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Maintenance</span>
                </button>
            </div>
        </div>
    </div>

    <!-- My Notes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">My Notes</h3>
        </div>
        <div class="p-6">
            <!-- Note Creation Form -->
            <div id="note-form" class="space-y-4 mb-6">
                <div>
                    <input 
                        type="text" 
                        id="note-title" 
                        placeholder="Note Title"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                </div>
                <div>
                    <textarea 
                        id="note-content" 
                        placeholder="Write your note content here..."
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                    ></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Color:</span>
                        <div class="flex space-x-2">
                            <button type="button" onclick="selectNoteColor('bg-blue-500')" data-color="bg-blue-500" class="note-color-btn w-6 h-6 rounded-full bg-blue-500 hover:ring-2 hover:ring-blue-300 transition-all"></button>
                            <button type="button" onclick="selectNoteColor('bg-green-500')" data-color="bg-green-500" class="note-color-btn w-6 h-6 rounded-full bg-green-500 hover:ring-2 hover:ring-green-300 transition-all"></button>
                            <button type="button" onclick="selectNoteColor('bg-yellow-500')" data-color="bg-yellow-500" class="note-color-btn w-6 h-6 rounded-full bg-yellow-500 hover:ring-2 hover:ring-yellow-300 transition-all"></button>
                            <button type="button" onclick="selectNoteColor('bg-red-500')" data-color="bg-red-500" class="note-color-btn w-6 h-6 rounded-full bg-red-500 hover:ring-2 hover:ring-red-300 transition-all"></button>
                            <button type="button" onclick="selectNoteColor('bg-purple-500')" data-color="bg-purple-500" class="note-color-btn w-6 h-6 rounded-full bg-purple-500 hover:ring-2 hover:ring-purple-300 transition-all"></button>
                            <button type="button" onclick="selectNoteColor('bg-pink-500')" data-color="bg-pink-500" class="note-color-btn w-6 h-6 rounded-full bg-pink-500 hover:ring-2 hover:ring-pink-300 transition-all"></button>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        onclick="addNewNote()" 
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        <i class="fas fa-plus mr-2"></i>Add Note
                    </button>
                </div>
            </div>

            <!-- Notes Display Area -->
            <div id="notes-container" class="space-y-3 max-h-64 overflow-y-auto">
                <!-- Notes will be dynamically loaded here -->
            </div>
            
            <!-- Empty State -->
            <div id="empty-notes" class="text-center py-8">
                <i class="fas fa-file-alt text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">No notes yet.</p>
                <p class="text-gray-400 dark:text-gray-500 text-xs">Create your first note above.</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Export Data Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Export Data</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Download platform data in various formats</p>
        <div class="flex space-x-3">
            <a href="/admin/export?format=json" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-download mr-2"></i>Export JSON
            </a>
            <a href="/admin/export?format=csv" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-file-csv mr-2"></i>Export CSV
            </a>
        </div>
    </div>

    </div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Properties -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Properties</h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentProperties)): ?>
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No properties found</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentProperties as $property): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($property['name']); ?></h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($property['address']); ?></p>
                                <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="mr-3"><?php echo $property['unit_count']; ?> units</span>
                                    <span><?php echo $property['occupied_units']; ?> occupied</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <?php echo ucfirst($property['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="/properties" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">View all properties →</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activities</h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentActivities)): ?>
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent activities</p>
            <?php else: ?>
                <?php 
                $getActivityIcon = function($action) {
                    $icons = [
                        'create' => 'plus',
                        'update' => 'edit',
                        'delete' => 'trash',
                        'login' => 'sign-in-alt',
                        'logout' => 'sign-out-alt',
                        'view' => 'eye',
                        'export' => 'download',
                        'upload' => 'upload',
                        'payment' => 'credit-card',
                        'invoice' => 'file-invoice',
                        'tenant' => 'user',
                        'property' => 'home',
                        'unit' => 'building',
                        'maintenance' => 'tools'
                    ];
                    return $icons[$action] ?? 'circle';
                };
                
                $formatActivityTime = function($timestamp) {
                    $time = strtotime($timestamp);
                    $now = time();
                    $diff = $now - $time;
                    
                    if ($diff < 60) {
                        return 'Just now';
                    } elseif ($diff < 3600) {
                        return floor($diff / 60) . ' minutes ago';
                    } elseif ($diff < 86400) {
                        return floor($diff / 3600) . ' hours ago';
                    } else {
                        return date('M j, Y', $time);
                    }
                };
                ?>
                <div class="space-y-4">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-<?php echo $getActivityIcon($activity['action']); ?> text-primary-600 dark:text-primary-400 text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($activity['description']); ?>
                                </p>
                                <?php if (isset($activity['property_name']) && $activity['property_name']): ?>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Property: <?php echo htmlspecialchars($activity['property_name']); ?>
                                    </p>
                                <?php endif; ?>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo $formatActivityTime($activity['created_at']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?php echo json_encode($revenueData); ?>;
    const revenueLabels = Object.keys(revenueData);
    const revenueValues = Object.values(revenueData);

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueLabels.map(date => {
                const d = new Date(date + '-01');
                return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Revenue',
                data: revenueValues,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
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

    // Occupancy Chart
    const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
    const occupiedUnits = <?php echo $stats['occupied_units']; ?>;
    const totalUnits = <?php echo $stats['total_units']; ?>;
    const vacantUnits = totalUnits - occupiedUnits;

    new Chart(occupancyCtx, {
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
                }
            }
        }
    });

    // Revenue period change
    document.getElementById('revenue-period').addEventListener('change', function() {
        const months = this.value;
        // Reload chart data for selected period
        apiRequest(`/api/dashboard/revenue?months=${months}`)
            .then(data => {
                // Update chart with new data
                const chart = Chart.getChart('revenueChart');
                const labels = data.map(item => {
                    const d = new Date(item.month + '-01');
                    return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                });
                const values = data.map(item => item.revenue);
                
                chart.data.labels = labels;
                chart.data.datasets[0].data = values;
                chart.update();
            });
    });

    // Notes Management
    let notes = JSON.parse(localStorage.getItem('dashboardNotes')) || [];
    let editingNoteId = null;
    let selectedColor = 'bg-blue-500';

    function renderNotes() {
        const container = document.getElementById('notes-container');
        const emptyState = document.getElementById('empty-notes');
        
        if (notes.length === 0) {
            container.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }
        
        emptyState.classList.add('hidden');
        container.innerHTML = notes.map(note => `
            <div class="note-item p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow" data-note-id="${note.id}">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full ${note.color || 'bg-blue-500'}"></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">${formatDate(note.createdAt)}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button onclick="editNote(${note.id})" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 text-xs">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteNote(${note.id})" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 text-xs">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                ${note.title ? `<h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">${note.title}</h4>` : ''}
                <p class="text-sm text-gray-700 dark:text-gray-300 ${note.completed ? 'line-through opacity-60' : ''}">${note.content || note.text}</p>
                ${note.completed ? '<div class="mt-2"><span class="text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-2 py-1 rounded">Completed</span></div>' : ''}
            </div>
        `).join('');
    }

    function selectNoteColor(color) {
        selectedColor = color;
        
        // Update UI to show selected color
        document.querySelectorAll('.note-color-btn').forEach(btn => {
            btn.classList.remove('ring-2', 'ring-offset-2', 'ring-gray-400', 'dark:ring-gray-600');
        });
        
        const selectedBtn = document.querySelector(`[data-color="${color}"]`);
        if (selectedBtn) {
            selectedBtn.classList.add('ring-2', 'ring-offset-2', 'ring-gray-400', 'dark:ring-gray-600');
        }
    }

    function addNewNote() {
        const titleInput = document.getElementById('note-title');
        const contentInput = document.getElementById('note-content');
        
        const title = titleInput.value.trim();
        const content = contentInput.value.trim();
        
        if (!title && !content) {
            showToast('Please enter a title or content for your note', 'warning');
            return;
        }
        
        const note = {
            id: Date.now(),
            title: title,
            content: content,
            createdAt: new Date().toISOString(),
            color: selectedColor,
            completed: false
        };
        
        notes.unshift(note);
        saveNotes();
        renderNotes();
        
        // Clear form
        titleInput.value = '';
        contentInput.value = '';
        selectNoteColor('bg-blue-500'); // Reset to default color
        
        showToast('Note added successfully', 'success');
    }

    function editNote(id) {
        const note = notes.find(n => n.id === id);
        if (note) {
            // Populate form with note data
            document.getElementById('note-title').value = note.title || '';
            document.getElementById('note-content').value = note.content || note.text || '';
            selectNoteColor(note.color || 'bg-blue-500');
            
            // Remove the old note temporarily
            notes = notes.filter(n => n.id !== id);
            saveNotes();
            renderNotes();
            
            // Focus on title field
            document.getElementById('note-title').focus();
            
            showToast('Edit your note and click "Add Note" to save changes', 'info');
        }
    }

    function deleteNote(id) {
        if (confirm('Are you sure you want to delete this note?')) {
            notes = notes.filter(n => n.id !== id);
            saveNotes();
            renderNotes();
            showToast('Note deleted successfully', 'success');
        }
    }

    function toggleNoteComplete(id) {
        const note = notes.find(n => n.id === id);
        if (note) {
            note.completed = !note.completed;
            saveNotes();
            renderNotes();
        }
    }

    function saveNotes() {
        localStorage.setItem('dashboardNotes', JSON.stringify(notes));
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 0) {
            return 'Today';
        } else if (diffDays === 1) {
            return 'Yesterday';
        } else if (diffDays < 7) {
            return `${diffDays} days ago`;
        } else {
            return date.toLocaleDateString();
        }
    }

    // Initialize color selection and keyboard shortcuts
    document.addEventListener('DOMContentLoaded', function() {
        selectNoteColor('bg-blue-500');
        
        // Add keyboard support for note creation
        const titleInput = document.getElementById('note-title');
        const contentInput = document.getElementById('note-content');
        
        // Ctrl+Enter to add note when in content textarea
        contentInput.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                addNewNote();
            }
        });
        
        // Enter to move to content when in title field (unless Shift+Enter)
        titleInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                contentInput.focus();
            }
        });
    });

    // Initialize notes on page load
    renderNotes();

    // Quick Actions handlers
    document.querySelector('.fa-plus-circle').parentElement.addEventListener('click', function() {
        window.location.href = '/properties/create';
    });

    document.querySelector('.fa-user-plus').parentElement.addEventListener('click', function() {
        window.location.href = '/tenants/create';
    });

    document.querySelector('.fa-file-invoice-dollar').parentElement.addEventListener('click', function() {
        window.location.href = '/invoices/create';
    });

    document.querySelector('.fa-tools').parentElement.addEventListener('click', function() {
        window.location.href = '/maintenance';
    });
});

<?php
// Helper function to get activity icon (moved from controller)
function getActivityIcon($action) {
    $icons = [
        'login' => 'sign-in-alt',
        'logout' => 'sign-out-alt',
        'create' => 'plus',
        'update' => 'edit',
        'delete' => 'trash',
        'register' => 'user-plus',
        'payment' => 'dollar-sign',
        'invoice' => 'file-invoice'
    ];
    
    return $icons[$action] ?? 'circle';
}
?>
