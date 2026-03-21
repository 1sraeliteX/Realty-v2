<?php

class MortgageCalculatorComponent {
    /**
     * Render the mortgage calculator interface
     */
    public static function render() {
        ob_start();
        ?>
        <div id="mortgageCalculatorModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Mortgage Calculator Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-home mr-2 text-green-600"></i>
                        Mortgage Calculator
                    </h3>
                    <button onclick="window.closeMortgageCalculator()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Mortgage Calculator Form -->
                <div class="p-6">
                    <form id="mortgage-form" class="space-y-4">
                        <!-- Loan Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Property Price (₦)
                            </label>
                            <input type="number" id="property-price" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="5000000" required>
                        </div>

                        <!-- Down Payment -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Down Payment (₦)
                            </label>
                            <div class="flex space-x-2">
                                <input type="number" id="down-payment" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="1000000" required>
                                <input type="number" id="down-payment-percent" class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="20" min="0" max="100">
                                <span class="flex items-center text-gray-600 dark:text-gray-400">%</span>
                            </div>
                        </div>

                        <!-- Interest Rate -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Annual Interest Rate (%)
                            </label>
                            <input type="number" id="interest-rate" step="0.1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="15.5" required>
                        </div>

                        <!-- Loan Term -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Loan Term
                            </label>
                            <select id="loan-term" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="60">5 years</option>
                                <option value="120">10 years</option>
                                <option value="180">15 years</option>
                                <option value="240">20 years</option>
                                <option value="300" selected>25 years</option>
                                <option value="360">30 years</option>
                            </select>
                        </div>

                        <!-- Calculate Button -->
                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-calculator mr-2"></i>
                                Calculate Payment
                            </button>
                            <button type="button" onclick="window.resetMortgageCalculator()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-redo mr-2"></i>
                                Reset
                            </button>
                        </div>
                    </form>

                    <!-- Results Section -->
                    <div id="mortgage-results" class="mt-6 hidden">
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">Monthly Payment Breakdown</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Monthly Payment</div>
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="monthly-payment">₦0</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Interest</div>
                                    <div class="text-xl font-semibold text-gray-900 dark:text-white" id="total-interest">₦0</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Amount</div>
                                    <div class="text-xl font-semibold text-gray-900 dark:text-white" id="total-amount">₦0</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Loan Amount</div>
                                    <div class="text-xl font-semibold text-gray-900 dark:text-white" id="loan-amount">₦0</div>
                                </div>
                            </div>

                            <!-- Amortization Schedule Preview -->
                            <div class="mt-4">
                                <h5 class="font-medium text-green-800 dark:text-green-200 mb-2">Payment Schedule (First 6 months)</h5>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-green-200 dark:border-green-700">
                                                <th class="text-left py-2 text-gray-700 dark:text-gray-300">Month</th>
                                                <th class="text-right py-2 text-gray-700 dark:text-gray-300">Payment</th>
                                                <th class="text-right py-2 text-gray-700 dark:text-gray-300">Principal</th>
                                                <th class="text-right py-2 text-gray-700 dark:text-gray-300">Interest</th>
                                                <th class="text-right py-2 text-gray-700 dark:text-gray-300">Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody id="amortization-schedule">
                                            <!-- Schedule will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        // Make all functions globally accessible for debug checker
        window.openMortgageCalculator = function() {
            const modal = document.getElementById('mortgageCalculatorModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        window.closeMortgageCalculator = function() {
            const modal = document.getElementById('mortgageCalculatorModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                window.resetMortgageCalculator();
            }
        }

        window.resetMortgageCalculator = function() {
            document.getElementById('mortgage-form').reset();
            document.getElementById('mortgage-results').classList.add('hidden');
            document.getElementById('down-payment-percent').value = '';
        }

        window.calculateMortgage = function() {
            const propertyPrice = parseFloat(document.getElementById('property-price').value);
            const downPayment = parseFloat(document.getElementById('down-payment').value);
            const annualRate = parseFloat(document.getElementById('interest-rate').value);
            const loanTermMonths = parseInt(document.getElementById('loan-term').value);

            if (!propertyPrice || !downPayment || !annualRate || !loanTermMonths) {
                alert('Please fill in all fields');
                return;
            }

            const loanAmount = propertyPrice - downPayment;
            const monthlyRate = annualRate / 100 / 12;

            // Calculate monthly payment using mortgage formula
            const monthlyPayment = loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, loanTermMonths)) / 
                                  (Math.pow(1 + monthlyRate, loanTermMonths) - 1);

            const totalAmount = monthlyPayment * loanTermMonths;
            const totalInterest = totalAmount - loanAmount;

            // Display results
            document.getElementById('monthly-payment').textContent = formatCurrency(monthlyPayment);
            document.getElementById('total-interest').textContent = formatCurrency(totalInterest);
            document.getElementById('total-amount').textContent = formatCurrency(totalAmount);
            document.getElementById('loan-amount').textContent = formatCurrency(loanAmount);

            // Generate amortization schedule (first 6 months)
            generateAmortizationSchedule(loanAmount, monthlyRate, monthlyPayment, loanTermMonths);

            document.getElementById('mortgage-results').classList.remove('hidden');
        }

        window.generateAmortizationSchedule = function(loanAmount, monthlyRate, monthlyPayment, totalMonths) {
            const schedule = document.getElementById('amortization-schedule');
            schedule.innerHTML = '';

            let balance = loanAmount;
            const monthsToShow = Math.min(6, totalMonths);

            for (let month = 1; month <= monthsToShow; month++) {
                const interestPayment = balance * monthlyRate;
                const principalPayment = monthlyPayment - interestPayment;
                balance -= principalPayment;

                const row = document.createElement('tr');
                row.className = 'border-b border-green-100 dark:border-green-800';
                row.innerHTML = `
                    <td class="py-2">${month}</td>
                    <td class="text-right py-2">${formatCurrency(monthlyPayment)}</td>
                    <td class="text-right py-2">${formatCurrency(principalPayment)}</td>
                    <td class="text-right py-2">${formatCurrency(interestPayment)}</td>
                    <td class="text-right py-2">${formatCurrency(Math.max(0, balance))}</td>
                `;
                schedule.appendChild(row);
            }
        }

        window.formatCurrency = function(amount) {
            return '₦' + amount.toLocaleString('en-NG', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        // Auto-calculate down payment percentage
        document.addEventListener('DOMContentLoaded', function() {
            const propertyPrice = document.getElementById('property-price');
            const downPayment = document.getElementById('down-payment');
            const downPaymentPercent = document.getElementById('down-payment-percent');

            function updatePercentage() {
                if (propertyPrice.value && downPayment.value) {
                    const percent = (downPayment.value / propertyPrice.value * 100).toFixed(1);
                    downPaymentPercent.value = percent;
                }
            }

            function updateDownPayment() {
                if (propertyPrice.value && downPaymentPercent.value) {
                    const amount = propertyPrice.value * downPaymentPercent.value / 100;
                    downPayment.value = amount.toFixed(0);
                }
            }

            propertyPrice.addEventListener('input', updatePercentage);
            downPayment.addEventListener('input', updatePercentage);
            downPaymentPercent.addEventListener('input', updateDownPayment);

            // Form submission
            document.getElementById('mortgage-form').addEventListener('submit', function(e) {
                e.preventDefault();
                window.calculateMortgage();
            });
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('mortgageCalculatorModal');
            if (modal && e.target === modal) {
                window.closeMortgageCalculator();
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
?>
