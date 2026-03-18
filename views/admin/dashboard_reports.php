<?php
// Anti-scattering compliant framework initialization
require_once __DIR__ . '/../../config/bootstrap.php';

// Get data from ViewManager (anti-scattering compliant)
$reports = ViewManager::get('reports', []);
$user = ViewManager::get('user', []);

// Helper functions for reports (anti-scattering compliant - isolated in view)
function formatAmount($amount) {
    if ($amount >= 1000000000) {
        return 'N' . number_format($amount / 1000000000, 2) . 'B';
    } elseif ($amount >= 1000000) {
        return 'N' . number_format($amount / 1000000, 2) . 'M';
    } elseif ($amount >= 1000) {
        return 'N' . number_format($amount / 1000, 1) . 'K';
    } else {
        return 'N' . number_format($amount);
    }
}

function formatPercentage($value) {
    return number_format($value, 1) . '%';
}
?>

<?php
// Anti-scattering compliant framework initialization
require_once __DIR__ . '/../../config/bootstrap.php';

// Get data from ViewManager (anti-scattering compliant)
$reports = ViewManager::get('reports', []);
$user = ViewManager::get('user', []);

// Include the dashboard layout which will render the content properly
include __DIR__ . '/dashboard_layout.php';
