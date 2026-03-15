<?php

class CalculatorComponent {
    /**
     * Render the calculator interface
     */
    public static function render() {
        ob_start();
        ?>
        <div id="calculator-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                <!-- Calculator Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-calculator mr-2 text-primary-600"></i>
                        Calculator
                    </h3>
                    <button onclick="closeCalculator()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Calculator Display -->
                <div class="p-4">
                    <div class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 mb-4">
                        <div id="calc-display" class="text-right text-2xl font-mono text-gray-900 dark:text-white min-h-[40px]">0</div>
                        <div id="calc-expression" class="text-right text-sm text-gray-500 dark:text-gray-400 mt-1 min-h-[20px]"></div>
                    </div>

                    <!-- Calculator Buttons -->
                    <div class="grid grid-cols-4 gap-2">
                        <!-- Row 1 -->
                        <button onclick="clearCalc()" class="calc-btn col-span-2 bg-red-500 hover:bg-red-600 text-white">C</button>
                        <button onclick="deleteCalc()" class="calc-btn bg-gray-500 hover:bg-gray-600 text-white">←</button>
                        <button onclick="appendOperator('/')" class="calc-btn bg-blue-500 hover:bg-blue-600 text-white">÷</button>
                        
                        <!-- Row 2 -->
                        <button onclick="appendNumber('7')" class="calc-btn">7</button>
                        <button onclick="appendNumber('8')" class="calc-btn">8</button>
                        <button onclick="appendNumber('9')" class="calc-btn">9</button>
                        <button onclick="appendOperator('*')" class="calc-btn bg-blue-500 hover:bg-blue-600 text-white">×</button>
                        
                        <!-- Row 3 -->
                        <button onclick="appendNumber('4')" class="calc-btn">4</button>
                        <button onclick="appendNumber('5')" class="calc-btn">5</button>
                        <button onclick="appendNumber('6')" class="calc-btn">6</button>
                        <button onclick="appendOperator('-')" class="calc-btn bg-blue-500 hover:bg-blue-600 text-white">−</button>
                        
                        <!-- Row 4 -->
                        <button onclick="appendNumber('1')" class="calc-btn">1</button>
                        <button onclick="appendNumber('2')" class="calc-btn">2</button>
                        <button onclick="appendNumber('3')" class="calc-btn">3</button>
                        <button onclick="appendOperator('+')" class="calc-btn bg-blue-500 hover:bg-blue-600 text-white">+</button>
                        
                        <!-- Row 5 -->
                        <button onclick="appendNumber('0')" class="calc-btn col-span-2">0</button>
                        <button onclick="appendDecimal()" class="calc-btn">.</button>
                        <button onclick="calculate()" class="calc-btn bg-green-500 hover:bg-green-600 text-white">=</button>
                    </div>

                    <!-- Additional Functions -->
                    <div class="grid grid-cols-4 gap-2 mt-2">
                        <button onclick="calculatePercentage()" class="calc-btn bg-purple-500 hover:bg-purple-600 text-white">%</button>
                        <button onclick="calculateSquareRoot()" class="calc-btn bg-purple-500 hover:bg-purple-600 text-white">√</button>
                        <button onclick="calculateSquare()" class="calc-btn bg-purple-500 hover:bg-purple-600 text-white">x²</button>
                        <button onclick="toggleSign()" class="calc-btn bg-purple-500 hover:bg-purple-600 text-white">±</button>
                    </div>
                </div>

                <!-- Memory Functions -->
                <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex space-x-2">
                            <button onclick="memoryClear()" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">MC</button>
                            <button onclick="memoryRecall()" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">MR</button>
                            <button onclick="memoryAdd()" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">M+</button>
                            <button onclick="memorySubtract()" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">M-</button>
                        </div>
                        <div id="memory-indicator" class="text-gray-500 dark:text-gray-400">
                            Memory: <span id="memory-value">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
        .calc-btn {
            @apply py-3 px-4 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500;
            @apply bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white;
        }
        </style>

        <script>
        // Calculator state
        let currentInput = '0';
        let expression = '';
        let previousValue = null;
        let currentOperator = null;
        let waitingForOperand = false;
        let memory = 0;

        function updateDisplay() {
            const display = document.getElementById('calc-display');
            const expressionDisplay = document.getElementById('calc-expression');
            const memoryValue = document.getElementById('memory-value');
            
            if (display) display.textContent = currentInput;
            if (expressionDisplay) expressionDisplay.textContent = expression;
            if (memoryValue) memoryValue.textContent = memory;
        }

        function clearCalc() {
            currentInput = '0';
            expression = '';
            previousValue = null;
            currentOperator = null;
            waitingForOperand = false;
            updateDisplay();
        }

        function deleteCalc() {
            if (currentInput.length > 1) {
                currentInput = currentInput.slice(0, -1);
            } else {
                currentInput = '0';
            }
            updateDisplay();
        }

        function appendNumber(num) {
            if (waitingForOperand) {
                currentInput = num;
                waitingForOperand = false;
            } else {
                currentInput = currentInput === '0' ? num : currentInput + num;
            }
            updateDisplay();
        }

        function appendDecimal() {
            if (waitingForOperand) {
                currentInput = '0.';
                waitingForOperand = false;
            } else if (currentInput.indexOf('.') === -1) {
                currentInput += '.';
            }
            updateDisplay();
        }

        function appendOperator(op) {
            const inputValue = parseFloat(currentInput);

            if (previousValue === null) {
                previousValue = inputValue;
            } else if (currentOperator) {
                const result = performCalculation();
                currentInput = String(result);
                previousValue = result;
            }

            waitingForOperand = true;
            currentOperator = op;
            expression = `${previousValue} ${getOperatorSymbol(op)}`;
            updateDisplay();
        }

        function getOperatorSymbol(op) {
            switch(op) {
                case '+': return '+';
                case '-': return '−';
                case '*': return '×';
                case '/': return '÷';
                default: return op;
            }
        }

        function performCalculation() {
            const inputValue = parseFloat(currentInput);
            let result = previousValue;

            switch(currentOperator) {
                case '+':
                    result += inputValue;
                    break;
                case '-':
                    result -= inputValue;
                    break;
                case '*':
                    result *= inputValue;
                    break;
                case '/':
                    if (inputValue !== 0) {
                        result /= inputValue;
                    } else {
                        return 'Error';
                    }
                    break;
            }

            return Math.round(result * 100000000) / 100000000; // Prevent floating point errors
        }

        function calculate() {
            if (currentOperator && previousValue !== null) {
                const result = performCalculation();
                expression += ` ${getOperatorSymbol(currentOperator)} ${currentInput} =`;
                currentInput = String(result);
                previousValue = null;
                currentOperator = null;
                waitingForOperand = true;
                updateDisplay();
            }
        }

        function calculatePercentage() {
            const value = parseFloat(currentInput);
            currentInput = String(value / 100);
            updateDisplay();
        }

        function calculateSquareRoot() {
            const value = parseFloat(currentInput);
            if (value >= 0) {
                currentInput = String(Math.sqrt(value));
            } else {
                currentInput = 'Error';
            }
            updateDisplay();
        }

        function calculateSquare() {
            const value = parseFloat(currentInput);
            currentInput = String(value * value);
            updateDisplay();
        }

        function toggleSign() {
            const value = parseFloat(currentInput);
            currentInput = String(-value);
            updateDisplay();
        }

        // Memory functions
        function memoryClear() {
            memory = 0;
            updateDisplay();
        }

        function memoryRecall() {
            currentInput = String(memory);
            waitingForOperand = true;
            updateDisplay();
        }

        function memoryAdd() {
            memory += parseFloat(currentInput);
            waitingForOperand = true;
            updateDisplay();
        }

        function memorySubtract() {
            memory -= parseFloat(currentInput);
            waitingForOperand = true;
            updateDisplay();
        }

        function openCalculator() {
            const modal = document.getElementById('calculator-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                updateDisplay();
            } else {
                console.error('Calculator modal not found');
            }
        }

        function closeCalculator() {
            const modal = document.getElementById('calculator-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Keyboard support
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('calculator-modal');
            if (modal && !modal.classList.contains('hidden')) {
                e.preventDefault();
                
                if (e.key >= '0' && e.key <= '9') {
                    appendNumber(e.key);
                } else if (e.key === '.') {
                    appendDecimal();
                } else if (e.key === '+' || e.key === '-' || e.key === '*' || e.key === '/') {
                    appendOperator(e.key);
                } else if (e.key === 'Enter' || e.key === '=') {
                    calculate();
                } else if (e.key === 'Escape') {
                    closeCalculator();
                } else if (e.key === 'Backspace') {
                    deleteCalc();
                } else if (e.key === 'Delete' || e.key.toLowerCase() === 'c') {
                    clearCalc();
                }
            }
        });

        // Close on outside click (but not when clicking inside calculator)
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('calculator-modal');
            if (modal && e.target === modal) {
                closeCalculator();
            }
        });

        // Prevent closing when clicking inside the calculator
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('calculator-modal');
            const calculatorContent = modal?.querySelector('.bg-white, .dark\\:bg-gray-800');
            if (modal && !modal.classList.contains('hidden') && calculatorContent && calculatorContent.contains(e.target)) {
                e.stopPropagation();
            }
        });

        // Initialize display when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            updateDisplay();
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
