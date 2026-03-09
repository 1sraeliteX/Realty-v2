<?php

/**
 * UI Components Library for Cornerstone Realty
 * Reusable UI components with consistent styling and functionality
 */

class UIComponents {
    
    /**
     * Button Component
     */
    public static function button($text, $type = 'primary', $size = 'medium', $icon = null, $onclick = null, $class = '') {
        $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';
        
        // Type classes
        $typeClasses = [
            'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
            'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600',
            'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
            'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
            'warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
            'ghost' => 'bg-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-500 dark:text-gray-300 dark:hover:bg-gray-800',
            'link' => 'text-primary-600 hover:text-primary-700 focus:ring-primary-500 dark:text-primary-400'
        ];
        
        // Size classes
        $sizeClasses = [
            'small' => 'px-3 py-1.5 text-sm',
            'medium' => 'px-4 py-2 text-sm',
            'large' => 'px-6 py-3 text-base'
        ];
        
        $classes = $baseClasses . ' ' . ($typeClasses[$type] ?? $typeClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['medium']) . ' ' . $class;
        $onclickAttr = $onclick ? "onclick=\"$onclick\"" : '';
        $iconHtml = $icon ? "<i class=\"fas fa-$icon mr-2\"></i>" : '';
        
        return "<button class=\"$classes\" $onclickAttr>$iconHtml$text</button>";
    }
    
    /**
     * Card Component
     */
    public static function card($content, $header = null, $footer = null, $class = '') {
        $baseClasses = 'bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700';
        $classes = $baseClasses . ' ' . $class;
        
        $headerHtml = $header ? "<div class=\"px-6 py-4 border-b border-gray-200 dark:border-gray-700\">$header</div>" : '';
        $footerHtml = $footer ? "<div class=\"px-6 py-4 border-t border-gray-200 dark:border-gray-700\">$footer</div>" : '';
        
        return "<div class=\"$classes\">$headerHtml<div class=\"p-6\">$content</div>$footerHtml</div>";
    }
    
    /**
     * Stats Card Component
     */
    public static function statsCard($title, $value, $icon, $trend = null, $color = 'primary') {
        $iconColors = [
            'primary' => 'bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400',
            'green' => 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400',
            'blue' => 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400',
            'yellow' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400',
            'red' => 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400',
            'purple' => 'bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400'
        ];
        
        $iconClass = $iconColors[$color] ?? $iconColors['primary'];
        $trendHtml = '';
        
        if ($trend !== null) {
            $trendIcon = $trend > 0 ? 'arrow-up' : 'arrow-down';
            $trendColor = $trend > 0 ? 'text-green-600' : 'text-red-600';
            $trendHtml = "<span class=\"text-sm $trendColor\"><i class=\"fas fa-$trend-icon mr-1\"></i>" . abs($trend) . "%</span>";
        }
        
        return "
            <div class=\"bg-white dark:bg-gray-800 rounded-lg shadow p-6\">
                <div class=\"flex items-center\">
                    <div class=\"flex-shrink-0 $iconClass rounded-lg p-3\">
                        <i class=\"fas fa-$icon text-xl\"></i>
                    </div>
                    <div class=\"ml-4\">
                        <p class=\"text-sm font-medium text-gray-600 dark:text-gray-400\">$title</p>
                        <p class=\"text-2xl font-bold text-gray-900 dark:text-white\">$value</p>
                        $trendHtml
                    </div>
                </div>
            </div>
        ";
    }
    
    /**
     * Badge Component
     */
    public static function badge($text, $type = 'primary', $size = 'medium') {
        $typeClasses = [
            'primary' => 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200',
            'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
        ];
        
        $sizeClasses = [
            'small' => 'px-2 py-0.5 text-xs',
            'medium' => 'px-2.5 py-0.5 text-xs',
            'large' => 'px-3 py-1 text-sm'
        ];
        
        $classes = 'inline-flex items-center rounded-full font-medium ' . ($typeClasses[$type] ?? $typeClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['medium']);
        
        return "<span class=\"$classes\">$text</span>";
    }
    
    /**
     * Avatar Component
     */
    public static function avatar($name, $image = null, $size = 'medium', $status = null) {
        $sizeClasses = [
            'xs' => 'w-6 h-6 text-xs',
            'small' => 'w-8 h-8 text-sm',
            'medium' => 'w-10 h-10 text-base',
            'large' => 'w-12 h-12 text-lg',
            'xl' => 'w-16 h-16 text-xl'
        ];
        
        $class = $sizeClasses[$size] ?? $sizeClasses['medium'];
        $initials = self::getInitials($name);
        
        if ($image) {
            $content = "<img src=\"$image\" alt=\"$name\" class=\"w-full h-full rounded-full object-cover\">";
        } else {
            $content = "<span class=\"font-medium text-gray-700 dark:text-gray-300\">$initials</span>";
        }
        
        $statusIndicator = '';
        if ($status) {
            $statusColors = [
                'online' => 'bg-green-400',
                'offline' => 'bg-gray-400',
                'away' => 'bg-yellow-400',
                'busy' => 'bg-red-400'
            ];
            $statusColor = $statusColors[$status] ?? $statusColors['offline'];
            $statusIndicator = "<span class=\"absolute bottom-0 right-0 w-3 h-3 $statusColor rounded-full border-2 border-white dark:border-gray-800\"></span>";
        }
        
        return "<div class=\"relative inline-flex items-center justify-center $class rounded-full bg-gray-100 dark:bg-gray-700\">$content$statusIndicator</div>";
    }
    
    /**
     * Modal Component
     */
    public static function modal($id, $title, $content, $footer = null, $size = 'medium') {
        $sizeClasses = [
            'small' => 'max-w-md',
            'medium' => 'max-w-lg',
            'large' => 'max-w-2xl',
            'xlarge' => 'max-w-4xl'
        ];
        
        $sizeClass = $sizeClasses[$size] ?? $sizeClasses['medium'];
        $footerHtml = $footer ? "<div class=\"px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3\">$footer</div>" : '';
        
        return "
            <div id=\"$id\" class=\"fixed inset-0 z-50 hidden overflow-y-auto\" aria-labelledby=\"modal-title\" role=\"dialog\" aria-modal=\"true\">
                <div class=\"flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0\">
                    <div class=\"fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity\" aria-hidden=\"true\"></div>
                    <span class=\"hidden sm:inline-block sm:align-middle sm:h-screen\" aria-hidden=\"true\">&#8203;</span>
                    <div class=\"inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full $sizeClass\">
                        <div class=\"px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between\">
                            <h3 class=\"text-lg font-medium text-gray-900 dark:text-white\" id=\"modal-title\">$title</h3>
                            <button onclick=\"closeModal('$id')\" class=\"text-gray-400 hover:text-gray-600 dark:hover:text-gray-300\">
                                <i class=\"fas fa-times\"></i>
                            </button>
                        </div>
                        <div class=\"px-6 py-4\">$content</div>
                        $footerHtml
                    </div>
                </div>
            </div>
        ";
    }
    
    /**
     * Form Input Component
     */
    public static function input($name, $label, $type = 'text', $value = '', $placeholder = '', $required = false, $error = null, $class = '') {
        $requiredAttr = $required ? 'required' : '';
        $errorHtml = $error ? "<p class=\"mt-1 text-sm text-red-600 dark:text-red-400\">$error</p>" : '';
        $requiredLabel = $required ? '<span class="text-red-500">*</span>' : '';
        
        return "
            <div class=\"$class\">
                <label for=\"$name\" class=\"block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1\">$label$requiredLabel</label>
                <input type=\"$type\" id=\"$name\" name=\"$name\" value=\"$value\" placeholder=\"$placeholder\" $requiredAttr
                    class=\"w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent\">
                $errorHtml
            </div>
        ";
    }
    
    /**
     * Form Select Component
     */
    public static function select($name, $label, $options, $value = '', $required = false, $class = '') {
        $requiredAttr = $required ? 'required' : '';
        $requiredLabel = $required ? '<span class="text-red-500">*</span>' : '';
        
        $optionsHtml = '';
        foreach ($options as $optionValue => $optionLabel) {
            $selected = $optionValue == $value ? 'selected' : '';
            $optionsHtml .= "<option value=\"$optionValue\" $selected>$optionLabel</option>";
        }
        
        return "
            <div class=\"$class\">
                <label for=\"$name\" class=\"block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1\">$label$requiredLabel</label>
                <select id=\"$name\" name=\"$name\" $requiredAttr
                    class=\"w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent\">
                    $optionsHtml
                </select>
            </div>
        ";
    }
    
    /**
     * Table Component
     */
    public static function table($headers, $rows, $class = '') {
        $headerHtml = '';
        foreach ($headers as $header) {
            $headerHtml .= "<th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider\">$header</th>";
        }
        
        $rowHtml = '';
        foreach ($rows as $row) {
            $rowHtml .= "<tr class=\"bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700\">";
            foreach ($row as $cell) {
                $rowHtml .= "<td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100\">$cell</td>";
            }
            $rowHtml .= "</tr>";
        }
        
        return "
            <div class=\"overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg $class\">
                <table class=\"min-w-full divide-y divide-gray-300 dark:divide-gray-700\">
                    <thead class=\"bg-gray-50 dark:bg-gray-900\">
                        <tr>$headerHtml</tr>
                    </thead>
                    <tbody class=\"divide-y divide-gray-200 dark:divide-gray-700\">
                        $rowHtml
                    </tbody>
                </table>
            </div>
        ";
    }
    
    /**
     * Alert Component
     */
    public static function alert($message, $type = 'info', $dismissible = false) {
        $typeClasses = [
            'success' => 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200',
            'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-200',
            'danger' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-200',
            'info' => 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-200'
        ];
        
        $iconClasses = [
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-triangle',
            'danger' => 'fa-exclamation-circle',
            'info' => 'fa-info-circle'
        ];
        
        $class = $typeClasses[$type] ?? $typeClasses['info'];
        $icon = $iconClasses[$type] ?? $iconClasses['info'];
        $dismissBtn = $dismissible ? '<button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 inline-flex h-8 w-8" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>' : '';
        
        return "
            <div class=\"flex p-4 border rounded-lg $class\">
                <i class=\"fas $icon mr-3 mt-0.5\"></i>
                <div class=\"flex-1\">$message</div>
                $dismissBtn
            </div>
        ";
    }
    
    /**
     * Loading Spinner Component
     */
    public static function spinner($size = 'medium') {
        $sizeClasses = [
            'small' => 'w-4 h-4',
            'medium' => 'w-6 h-6',
            'large' => 'w-8 h-8'
        ];
        
        $class = $sizeClasses[$size] ?? $sizeClasses['medium'];
        
        return "<div class=\"animate-spin rounded-full border-2 border-gray-300 border-t-primary-600 $class\"></div>";
    }
    
    /**
     * Breadcrumb Component
     */
    public static function breadcrumb($items) {
        $breadcrumbHtml = '<nav class="flex" aria-label="Breadcrumb">';
        
        foreach ($items as $index => $item) {
            $isLast = $index === count($items) - 1;
            
            if ($isLast) {
                $breadcrumbHtml .= "<span class=\"text-gray-500 dark:text-gray-400\">{$item['label']}</span>";
            } else {
                $href = $item['href'] ?? '#';
                $breadcrumbHtml .= "<a href=\"$href\" class=\"text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400\">{$item['label']}</a>";
                $breadcrumbHtml .= '<svg class="flex-shrink-0 mx-2 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>';
            }
        }
        
        $breadcrumbHtml .= '</nav>';
        return $breadcrumbHtml;
    }
    
    /**
     * Helper function to get initials from name
     */
    private static function getInitials($name) {
        $words = explode(' ', $name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return substr($initials, 0, 2);
    }
    
    /**
     * Search Bar Component
     */
    public static function searchBar($placeholder = 'Search...', $value = '', $onSearch = null) {
        $onSearchAttr = $onSearch ? "onkeyup=\"$onSearch\"" : '';
        
        return "
            <div class=\"relative\">
                <div class=\"absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none\">
                    <i class=\"fas fa-search text-gray-400\"></i>
                </div>
                <input type=\"text\" value=\"$value\" placeholder=\"$placeholder\" $onSearchAttr
                    class=\"block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent\">
            </div>
        ";
    }
    
    /**
     * Pagination Component
     */
    public static function pagination($currentPage, $totalPages, $onPageChange = null) {
        if ($totalPages <= 1) return '';
        
        $onPageAttr = $onPageChange ? "onclick=\"$onPageChange\"" : '';
        $pagination = '<nav class="flex items-center justify-between">';
        $pagination .= '<div class="flex-1 flex justify-between sm:hidden">';
        
        // Mobile pagination
        $prevDisabled = $currentPage <= 1 ? 'disabled' : '';
        $nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
        
        $pagination .= self::button('Previous', 'secondary', 'medium', 'arrow-left', $prevDisabled ? '' : "goToPage($currentPage - 1)", $prevDisabled);
        $pagination .= self::button('Next', 'secondary', 'medium', 'arrow-right', $nextDisabled ? '' : "goToPage($currentPage + 1)", $nextDisabled);
        
        $pagination .= '</div>';
        $pagination .= '<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">';
        $pagination .= "<div><p class=\"text-sm text-gray-700 dark:text-gray-300\">Page <span class=\"font-medium\">$currentPage</span> of <span class=\"font-medium\">$totalPages</span></p></div>";
        
        // Desktop pagination
        $pagination .= '<div><nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">';
        
        // Previous button
        $prevClass = $currentPage <= 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50';
        $pagination .= "<button class=\"relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 $prevClass\" $prevDisabled>";
        $pagination .= '<i class="fas fa-chevron-left"></i></button>';
        
        // Page numbers
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            $activeClass = $i == $currentPage ? 'bg-primary-50 border-primary-500 text-primary-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50';
            $pagination .= "<button class=\"relative inline-flex items-center px-4 py-2 border text-sm font-medium $activeClass\" onclick=\"goToPage($i)\">$i</button>";
        }
        
        // Next button
        $nextClass = $currentPage >= $totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50';
        $pagination .= "<button class=\"relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 $nextClass\" $nextDisabled>";
        $pagination .= '<i class="fas fa-chevron-right"></i></button>';
        
        $pagination .= '</nav></div></div></nav>';
        
        return $pagination;
    }
}
