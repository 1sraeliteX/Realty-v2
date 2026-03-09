<?php
/**
 * Reusable Searchable Dropdown Component
 * 
 * This component provides a searchable dropdown with the following features:
 * - Search input with debouncing
 * - Keyboard navigation
 * - Highlighted search results
 * - Empty state handling
 * - Form validation integration
 * - Disabled state support
 * 
 * @param array $options Array of dropdown options with 'value', 'label', and optional 'description'
 * @param string $name Form field name
 * @param string $id HTML element ID
 * @param string $label Field label
 * @param string $placeholder Search placeholder text
 * @param mixed $value Currently selected value
 * @param bool $required Whether field is required
 * @param bool $disabled Whether field is disabled
 * @param string $error Error message to display
 */

function renderSearchableDropdown($options, $name, $id, $label, $placeholder = 'Search or select...', $value = '', $required = false, $disabled = false, $error = '') {
    $requiredAttr = $required ? 'required' : '';
    $disabledAttr = $disabled ? 'disabled' : '';
    $errorClass = $error ? 'border-red-500' : '';
    
    // Find current selection
    $selectedOption = null;
    if ($value) {
        foreach ($options as $option) {
            if ($option['value'] === $value) {
                $selectedOption = $option;
                break;
            }
        }
    }
    
    $selectedLabel = $selectedOption ? $selectedOption['label'] : '';
    $errorId = $id . '_error';
    $searchId = $id . '_search';
    $dropdownId = $id . '_dropdown';
    $toggleId = $id . '_dropdown_toggle';
    $emptyStateId = $id . '_empty_state';
    
    ob_start();
    ?>
    <div class="searchable-dropdown-container relative">
        <label for="<?php echo $searchId; ?>" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            <?php echo $label; ?><?php if ($required): ?> <span class="text-red-500">*</span><?php endif; ?>
        </label>
        <div class="relative z-0">
            <input 
                type="text" 
                id="<?php echo $searchId; ?>" 
                name="<?php echo $searchId; ?>" 
                <?php echo $requiredAttr; ?>
                <?php echo $disabledAttr; ?>
                autocomplete="off"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent <?php echo $errorClass; ?>" 
                placeholder="<?php echo $placeholder; ?>"
                value="<?php echo htmlspecialchars($selectedLabel); ?>"
            >
            <input type="hidden" id="<?php echo $id; ?>" name="<?php echo $name; ?>" <?php echo $requiredAttr; ?> value="<?php echo htmlspecialchars($value); ?>">
            <button type="button" id="<?php echo $toggleId; ?>" class="absolute right-2 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" <?php echo $disabledAttr; ?>>
                <i class="fas fa-chevron-down"></i>
            </button>
            
            <!-- Dropdown Options -->
            <div id="<?php echo $dropdownId; ?>" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl max-h-60 overflow-y-auto hidden">
                <div class="p-2">
                    <!-- Options will be dynamically populated by JavaScript -->
                </div>
                
                <!-- Empty State -->
                <div id="<?php echo $emptyStateId; ?>" class="hidden p-4 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-search mb-2"></i>
                    <p>No option found</p>
                </div>
            </div>
        </div>
        <?php if ($error): ?>
            <span class="text-red-500 text-sm mt-1" id="<?php echo $errorId; ?>"><?php echo htmlspecialchars($error); ?></span>
        <?php else: ?>
            <span class="text-red-500 text-sm mt-1 hidden" id="<?php echo $errorId; ?>"><?php echo $label; ?> is required</span>
        <?php endif; ?>
    </div>
    
    <script>
    // Initialize searchable dropdown for <?php echo $id; ?>
    (function() {
        const dropdownData = <?php echo json_encode($options); ?>;
        const searchInput = document.getElementById('<?php echo $searchId; ?>');
        const hiddenInput = document.getElementById('<?php echo $id; ?>');
        const dropdown = document.getElementById('<?php echo $dropdownId; ?>');
        const dropdownToggle = document.getElementById('<?php echo $toggleId; ?>');
        const emptyState = document.getElementById('<?php echo $emptyStateId; ?>');
        const errorElement = document.getElementById('<?php echo $errorId; ?>');
        
        if (!searchInput || !hiddenInput || !dropdown || !dropdownToggle || !emptyState) {
            console.error('Searchable dropdown elements not found for <?php echo $id; ?>');
            return;
        }
        
        let selectedOption = <?php echo $selectedOption ? json_encode($selectedOption) : 'null'; ?>;
        let isDropdownOpen = false;
        let searchTimeout;
        
        // Initialize with existing value
        renderOptions(dropdownData, '');
        
        // Toggle dropdown
        function toggleDropdown() {
            isDropdownOpen = !isDropdownOpen;
            if (isDropdownOpen) {
                dropdown.classList.remove('hidden');
                filterOptions(searchInput.value);
            } else {
                dropdown.classList.add('hidden');
            }
        }
        
        // Filter options with debouncing
        function filterOptions(searchTerm) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const filtered = dropdownData.filter(option => 
                    option.label.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    (option.description && option.description.toLowerCase().includes(searchTerm.toLowerCase()))
                );
                
                renderOptions(filtered, searchTerm);
            }, 150);
        }
        
        // Render options
        function renderOptions(options, searchTerm) {
            const optionsContainer = dropdown.querySelector('.p-2');
            
            if (options.length === 0) {
                optionsContainer.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }
            
            emptyState.classList.add('hidden');
            
            optionsContainer.innerHTML = options.map(option => {
                const highlightedLabel = highlightMatch(option.label, searchTerm);
                const highlightedDescription = option.description ? highlightMatch(option.description, searchTerm) : '';
                const isSelected = selectedOption && selectedOption.value === option.value;
                
                return `
                    <div class="dropdown-option px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 rounded ${isSelected ? 'bg-primary-50 dark:bg-primary-900 border border-primary-300 dark:border-primary-600' : ''}" 
                         data-value="${option.value}">
                        <div class="font-medium text-gray-900 dark:text-white">${highlightedLabel}</div>
                        ${option.description ? `<div class="text-sm text-gray-500 dark:text-gray-400">${highlightedDescription}</div>` : ''}
                    </div>
                `;
            }).join('');
            
            // Add click handlers to new options
            optionsContainer.querySelectorAll('.dropdown-option').forEach(option => {
                option.addEventListener('click', function() {
                    selectOption(this.dataset.value);
                });
            });
        }
        
        // Highlight matching text
        function highlightMatch(text, searchTerm) {
            if (!searchTerm) return text;
            
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            return text.replace(regex, '<span class="font-semibold bg-yellow-200 dark:bg-yellow-800">$1</span>');
        }
        
        // Select option
        function selectOption(value) {
            const option = dropdownData.find(opt => opt.value === value);
            if (option) {
                selectedOption = option;
                searchInput.value = option.label;
                hiddenInput.value = option.value;
                isDropdownOpen = false;
                dropdown.classList.add('hidden');
                
                // Clear validation error if present
                if (errorElement) {
                    errorElement.classList.add('hidden');
                    searchInput.classList.remove('border-red-500');
                }
                
                // Update dropdown toggle icon
                dropdownToggle.innerHTML = '<i class="fas fa-chevron-down"></i>';
            }
        }
        
        // Event listeners
        dropdownToggle.addEventListener('click', toggleDropdown);
        
        searchInput.addEventListener('focus', () => {
            if (!isDropdownOpen && !searchInput.disabled) {
                toggleDropdown();
            }
        });
        
        searchInput.addEventListener('input', (e) => {
            filterOptions(e.target.value);
            if (selectedOption && selectedOption.label !== e.target.value) {
                selectedOption = null;
                hiddenInput.value = '';
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.searchable-dropdown-container')) {
                isDropdownOpen = false;
                dropdown.classList.add('hidden');
                dropdownToggle.innerHTML = '<i class="fas fa-chevron-down"></i>';
            }
        });
        
        // Keyboard navigation
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                isDropdownOpen = false;
                dropdown.classList.add('hidden');
                searchInput.blur();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const firstVisible = dropdown.querySelector('.dropdown-option:not(.hidden)');
                if (firstVisible) {
                    selectOption(firstVisible.dataset.value);
                }
            } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                const options = Array.from(dropdown.querySelectorAll('.dropdown-option:not(.hidden)'));
                const currentIndex = options.findIndex(opt => opt.classList.contains('bg-primary-50'));
                
                let nextIndex;
                if (e.key === 'ArrowDown') {
                    nextIndex = currentIndex < options.length - 1 ? currentIndex + 1 : 0;
                } else {
                    nextIndex = currentIndex > 0 ? currentIndex - 1 : options.length - 1;
                }
                
                options.forEach(opt => opt.classList.remove('bg-primary-50', 'dark:bg-primary-900'));
                options[nextIndex].classList.add('bg-primary-50', 'dark:bg-primary-900');
                options[nextIndex].scrollIntoView({ block: 'nearest' });
            }
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}
?>
