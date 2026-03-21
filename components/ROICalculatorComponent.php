<?php

class ROICalculatorComponent {
    /**
     * Render the ROI calculator interface
     */
    public static function render() {
        ob_start();
        ?>
        <div id="roiCalculatorModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <!-- ROI Calculator Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-chart-line mr-2 text-purple-600"></i>
                        ROI Calculator
                    </h3>
                    <button onclick="window.closeROICalculator()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- ROI Calculator Form -->
                <div class="p-6">
                    <form id="roi-form" class="space-y-4">
                        <!-- Property Details -->
                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                            <h4 class="font-medium text-purple-800 dark:text-purple-200 mb-3">Property Details</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Property Purchase Price (₦)
                                    </label>
                                    <input type="number" id="purchase-price" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="5000000" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Initial Investment (₦)
                                    </label>
                                    <input type="number" id="initial-investment" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="1500000" required>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Income -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <h4 class="font-medium text-green-800 dark:text-green-200 mb-3">Monthly Income</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Monthly Rent (₦)
                                    </label>
                                    <input type="number" id="monthly-rent" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="50000" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Other Monthly Income (₦)
                                    </label>
                                    <input type="number" id="other-income" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="0">
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Expenses -->
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <h4 class="font-medium text-red-800 dark:text-red-200 mb-3">Monthly Expenses</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Property Tax (₦/month)
                                    </label>
                                    <input type="number" id="property-tax" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="5000">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Insurance (₦/month)
                                    </label>
                                    <input type="number" id="insurance" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="3000">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Maintenance (₦/month)
                                    </label>
                                    <input type="number" id="maintenance" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="2000">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Other Expenses (₦/month)
                                    </label>
                                    <input type="number" id="other-expenses" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="0">
                                </div>
                            </div>
                        </div>

                        <!-- Calculate Button -->
                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-chart-line mr-2"></i>
                                Calculate ROI
                            </button>
                            <button type="button" onclick="window.resetROICalculator()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-redo mr-2"></i>
                                Reset
                            </button>
                        </div>
                    </form>

                    <!-- Results Section -->
                    <div id="roi-results" class="mt-6 hidden">
                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-purple-800 dark:text-purple-200 mb-4">Investment Analysis</h4>
                            
                            <!-- Key Metrics -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-purple-200 dark:border-purple-700 text-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Annual ROI</div>
                                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="annual-roi">0%</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-purple-200 dark:border-purple-700 text-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Monthly Cash Flow</div>
                                    <div class="text-xl font-semibold text-green-600 dark:text-green-400" id="monthly-cashflow">₦0</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-purple-200 dark:border-purple-700 text-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Cap Rate</div>
                                    <div class="text-xl font-semibold text-gray-900 dark:text-white" id="cap-rate">0%</div>
                                </div>
                            </div>

                            <!-- Detailed Breakdown -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-3">Income Breakdown</h5>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Monthly Rent:</span>
                                            <span class="text-sm font-medium" id="result-rent">₦0</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Other Income:</span>
                                            <span class="text-sm font-medium" id="result-other-income">₦0</span>
                                        </div>
                                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Total Monthly Income:</span>
                                            <span class="text-sm font-bold text-green-600 dark:text-green-400" id="total-income">₦0</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Annual Income:</span>
                                            <span class="text-sm font-medium" id="annual-income">₦0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-3">Expense Breakdown</h5>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Property Tax:</span>
                                            <span class="text-sm font-medium" id="result-tax">₦0</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Insurance:</span>
                                            <span class="text-sm font-medium" id="result-insurance">₦0</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Maintenance:</span>
                                            <span class="text-sm font-medium" id="result-maintenance">₦0</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Other Expenses:</span>
                                            <span class="text-sm font-medium" id="result-other-expenses">₦0</span>
                                        </div>
                                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Total Monthly Expenses:</span>
                                            <span class="text-sm font-bold text-red-600 dark:text-red-400" id="total-expenses">₦0</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Annual Expenses:</span>
                                            <span class="text-sm font-medium" id="annual-expenses">₦0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Investment Summary -->
                            <div class="mt-4 bg-white dark:bg-gray-800 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                                <h5 class="font-medium text-gray-900 dark:text-white mb-3">Investment Summary</h5>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Net Operating Income (Annual):</span>
                                        <div class="text-lg font-semibold text-gray-900 dark:text-white" id="noi">₦0</div>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Initial Investment:</span>
                                        <div class="text-lg font-semibold text-gray-900 dark:text-white" id="result-initial-investment">₦0</div>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Cash-on-Cash Return:</span>
                                        <div class="text-lg font-semibold text-purple-600 dark:text-purple-400" id="coc-return">0%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        // Make all functions globally accessible for debug checker
        window.openROICalculator = function() {
            const modal = document.getElementById('roiCalculatorModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        window.closeROICalculator = function() {
            const modal = document.getElementById('roiCalculatorModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                window.resetROICalculator();
            }
        }

        window.resetROICalculator = function() {
            document.getElementById('roi-form').reset();
            document.getElementById('roi-results').classList.add('hidden');
        }

        window.calculateROI = function() {
            const purchasePrice = parseFloat(document.getElementById('purchase-price').value);
            const initialInvestment = parseFloat(document.getElementById('initial-investment').value);
            const monthlyRent = parseFloat(document.getElementById('monthly-rent').value);
            const otherIncome = parseFloat(document.getElementById('other-income').value) || 0;
            const propertyTax = parseFloat(document.getElementById('property-tax').value) || 0;
            const insurance = parseFloat(document.getElementById('insurance').value) || 0;
            const maintenance = parseFloat(document.getElementById('maintenance').value) || 0;
            const otherExpenses = parseFloat(document.getElementById('other-expenses').value) || 0;

            if (!purchasePrice || !initialInvestment || !monthlyRent) {
                alert('Please fill in all required fields');
                return;
            }

            // Calculate monthly totals
            const totalMonthlyIncome = monthlyRent + otherIncome;
            const totalMonthlyExpenses = propertyTax + insurance + maintenance + otherExpenses;
            const monthlyCashFlow = totalMonthlyIncome - totalMonthlyExpenses;

            // Calculate annual totals
            const annualIncome = totalMonthlyIncome * 12;
            const annualExpenses = totalMonthlyExpenses * 12;
            const netOperatingIncome = annualIncome - annualExpenses;

            // Calculate ROI metrics
            const annualROI = (netOperatingIncome / initialInvestment) * 100;
            const capRate = (netOperatingIncome / purchasePrice) * 100;
            const cashOnCashReturn = (monthlyCashFlow * 12 / initialInvestment) * 100;

            // Display results
            document.getElementById('annual-roi').textContent = annualROI.toFixed(2) + '%';
            document.getElementById('monthly-cashflow').textContent = formatCurrency(monthlyCashFlow);
            document.getElementById('cap-rate').textContent = capRate.toFixed(2) + '%';

            // Income breakdown
            document.getElementById('result-rent').textContent = formatCurrency(monthlyRent);
            document.getElementById('result-other-income').textContent = formatCurrency(otherIncome);
            document.getElementById('total-income').textContent = formatCurrency(totalMonthlyIncome);
            document.getElementById('annual-income').textContent = formatCurrency(annualIncome);

            // Expense breakdown
            document.getElementById('result-tax').textContent = formatCurrency(propertyTax);
            document.getElementById('result-insurance').textContent = formatCurrency(insurance);
            document.getElementById('result-maintenance').textContent = formatCurrency(maintenance);
            document.getElementById('result-other-expenses').textContent = formatCurrency(otherExpenses);
            document.getElementById('total-expenses').textContent = formatCurrency(totalMonthlyExpenses);
            document.getElementById('annual-expenses').textContent = formatCurrency(annualExpenses);

            // Investment summary
            document.getElementById('noi').textContent = formatCurrency(netOperatingIncome);
            document.getElementById('result-initial-investment').textContent = formatCurrency(initialInvestment);
            document.getElementById('coc-return').textContent = cashOnCashReturn.toFixed(2) + '%';

            // Color code cash flow
            const cashFlowElement = document.getElementById('monthly-cashflow');
            if (monthlyCashFlow >= 0) {
                cashFlowElement.className = 'text-xl font-semibold text-green-600 dark:text-green-400';
            } else {
                cashFlowElement.className = 'text-xl font-semibold text-red-600 dark:text-red-400';
            }

            document.getElementById('roi-results').classList.remove('hidden');
        }

        window.formatCurrency = function(amount) {
            return '₦' + amount.toLocaleString('en-NG', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        // Form submission
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('roi-form').addEventListener('submit', function(e) {
                e.preventDefault();
                window.calculateROI();
            });
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('roiCalculatorModal');
            if (modal && e.target === modal) {
                window.closeROICalculator();
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
?>
