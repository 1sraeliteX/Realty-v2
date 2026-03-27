<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';
?>

<div class="min-h-screen flex flex-col">
    <!-- Top Navigation -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Title -->
                <div class="flex items-center">
                    <button onclick="history.back()" class="mr-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-calculator mr-2 text-primary-600"></i>
                        Calculator
                    </h1>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-4">
                    <button onclick="openCalculator()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-calculator mr-2"></i>
                        Basic Calculator
                    </button>
                    <a href="/admin/dashboard" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-home"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Property Management Calculator Suite
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Comprehensive calculators for property-related calculations, mortgage planning, and investment analysis.
                </p>
                
                </div>

            <!-- Calculator Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Basic Calculator Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <i class="fas fa-calculator text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900 dark:text-white">Basic Calculator</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Standard arithmetic operations with memory functions and percentage calculations.
                    </p>
                    <button onclick="openCalculator()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Open Calculator
                    </button>
                </div>

                <!-- Mortgage Calculator Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <i class="fas fa-home text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900 dark:text-white">Mortgage Calculator</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Calculate monthly mortgage payments, interest, and amortization schedules.
                    </p>
                    <button onclick="openMortgageCalculator()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Open Calculator
                    </button>
                </div>

                <!-- ROI Calculator Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900 dark:text-white">ROI Calculator</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Calculate return on investment for rental properties and portfolio analysis.
                    </p>
                    <button onclick="openROICalculator()" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Open Calculator
                    </button>
                </div>
            </div>

            <!-- Quick Tips Section -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Calculator Features
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <h4 class="font-medium text-blue-800 dark:text-blue-200">Basic Calculator</h4>
                        <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                            <li><i class="fas fa-check mr-2"></i>Standard arithmetic operations</li>
                            <li><i class="fas fa-check mr-2"></i>Memory functions (M+, M-, MR, MC)</li>
                            <li><i class="fas fa-check mr-2"></i>Percentage calculations</li>
                            <li><i class="fas fa-check mr-2"></i>Keyboard support (0-9, +, -, *, /)</li>
                        </ul>
                    </div>
                    <div class="space-y-2">
                        <h4 class="font-medium text-blue-800 dark:text-blue-200">Mortgage Calculator</h4>
                        <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                            <li><i class="fas fa-check mr-2"></i>Monthly payment calculations</li>
                            <li><i class="fas fa-check mr-2"></i>Interest and total cost analysis</li>
                            <li><i class="fas fa-check mr-2"></i>Amortization schedule</li>
                            <li><i class="fas fa-check mr-2"></i>Down payment percentage calculator</li>
                        </ul>
                    </div>
                    <div class="space-y-2">
                        <h4 class="font-medium text-blue-800 dark:text-blue-200">ROI Calculator</h4>
                        <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                            <li><i class="fas fa-check mr-2"></i>Return on investment analysis</li>
                            <li><i class="fas fa-check mr-2"></i>Cash flow calculations</li>
                            <li><i class="fas fa-check mr-2"></i>Cap rate and cash-on-cash return</li>
                            <li><i class="fas fa-check mr-2"></i>Income and expense breakdown</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Instructions Section -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-keyboard mr-2"></i>
                    How to Use
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="bg-blue-100 dark:bg-blue-900 rounded-lg p-4 mb-3">
                            <i class="fas fa-calculator text-3xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Basic Calculator</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Click "Open Calculator" or use keyboard for quick calculations. Perfect for everyday math operations.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="bg-green-100 dark:bg-green-900 rounded-lg p-4 mb-3">
                            <i class="fas fa-home text-3xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Mortgage Calculator</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Enter property details, loan terms, and interest rates to calculate monthly payments and total costs.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-100 dark:bg-purple-900 rounded-lg p-4 mb-3">
                            <i class="fas fa-chart-line text-3xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">ROI Calculator</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Input property costs, rental income, and expenses to analyze investment returns and profitability.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Don't auto-open calculator - let users click buttons to test
document.addEventListener('DOMContentLoaded', function() {
    console.log('Calculator suite loaded successfully');
    console.log('Available calculators: Basic, Mortgage, ROI');
    console.log('Click any "Open Calculator" button to test');
    
    // Debug: Check if calculator functions exist
    console.log('openCalculator function:', typeof window.openCalculator);
    console.log('openMortgageCalculator function:', typeof window.openMortgageCalculator);
    console.log('openROICalculator function:', typeof window.openROICalculator);
    
    // Debug: Check if modals exist
    const basicModal = document.getElementById('calculatorModal');
    const mortgageModal = document.getElementById('mortgageCalculatorModal');
    const roiModal = document.getElementById('roiCalculatorModal');
    
    console.log('Basic calculator modal found:', !!basicModal);
    console.log('Mortgage calculator modal found:', !!mortgageModal);
    console.log('ROI calculator modal found:', !!roiModal);
    
    if (basicModal) {
        console.log('Basic modal classes:', basicModal.className);
        console.log('Basic modal hidden?:', basicModal.classList.contains('hidden'));
    }
});

// Add helpful console messages
console.log('Calculator suite loaded successfully');
console.log('Available calculators: Basic, Mortgage, ROI');
</script>

<!-- ===== CALCULATOR MODALS + JS — SELF CONTAINED ===== -->

<!-- Basic Calculator Modal -->
<div id="calculatorModal" class="fixed inset-0 bg-black bg-opacity-60 z-[9999] hidden items-center justify-center" onclick="if(event.target===this)closeCalculator()">
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 w-80 relative">
    <button onclick="closeCalculator()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-xl font-bold">✕</button>
    <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Basic Calculator</h2>
    <div id="calcDisplay" class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3 text-right text-2xl font-mono text-gray-800 dark:text-white mb-3 min-h-[48px] break-all">0</div>
    <div class="grid grid-cols-4 gap-2">
      <button onclick="clearCalc()" class="col-span-2 bg-red-100 hover:bg-red-200 text-red-700 font-bold py-3 rounded-lg">C</button>
      <button onclick="appendOperator('%')" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white font-bold py-3 rounded-lg">%</button>
      <button onclick="appendOperator('/')" class="bg-amber-400 hover:bg-amber-500 text-white font-bold py-3 rounded-lg">÷</button>
      <button onclick="appendNumber('7')" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">7</button>
      <button onclick="appendNumber('8')" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">8</button>
      <button onclick="appendNumber('9')" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">9</button>
      <button onclick="appendOperator('*')" class="bg-amber-400 hover:bg-amber-500 text-white font-bold py-3 rounded-lg">×</button>
      <button onclick="appendNumber('4')" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">4</button>
      <button onclick="appendNumber('5')" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">5</button>
      <button onclick="appendNumber('6')" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">6</button>
      <button onclick="appendOperator('-')" class="bg-amber-400 hover:bg-amber-500 text-white font-bold py-3 rounded-lg">−</button>
      <button onclick="appendNumber('1')" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">1</button>
      <button onclick="appendNumber('2')" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">2</button>
      <button onclick="appendNumber('3')" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">3</button>
      <button onclick="appendOperator('+')" class="bg-amber-400 hover:bg-amber-500 text-white font-bold py-3 rounded-lg">+</button>
      <button onclick="appendNumber('0')" class="col-span-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">0</button>
      <button onclick="appendNumber('.')" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-white font-semibold py-3 rounded-lg">.</button>
      <button onclick="calculate()" class="bg-amber-400 hover:bg-amber-500 text-white font-bold py-3 rounded-lg">=</button>
    </div>
  </div>
</div>

<!-- Mortgage Calculator Modal -->
<div id="mortgageCalculatorModal" class="fixed inset-0 bg-black bg-opacity-60 z-[9999] hidden items-center justify-center" onclick="if(event.target===this)closeMortgageCalculator()">
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 w-full max-w-md relative">
    <button onclick="closeMortgageCalculator()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-xl font-bold">✕</button>
    <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Mortgage Calculator</h2>
    <div class="space-y-3">
      <div>
        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">Loan Amount (₦)</label>
        <input id="mortgageLoan" type="number" placeholder="e.g. 5000000" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
      </div>
      <div>
        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">Annual Interest Rate (%)</label>
        <input id="mortgageRate" type="number" step="0.01" placeholder="e.g. 12.5" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
      </div>
      <div>
        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">Loan Term (Years)</label>
        <input id="mortgageTerm" type="number" placeholder="e.g. 20" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
      </div>
      <button onclick="calculateMortgage()" class="w-full bg-amber-400 hover:bg-amber-500 text-white font-bold py-3 rounded-lg mt-2">Calculate</button>
      <div id="mortgageResult" class="hidden bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-2 text-sm"></div>
    </div>
  </div>
</div>

<!-- ROI Calculator Modal -->
<div id="roiCalculatorModal" class="fixed inset-0 bg-black bg-opacity-60 z-[9999] hidden items-center justify-center" onclick="if(event.target===this)closeROICalculator()">
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 w-full max-w-md relative">
    <button onclick="closeROICalculator()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-xl font-bold">✕</button>
    <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">ROI Calculator</h2>
    <div class="space-y-3">
      <div>
        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">Initial Investment (₦)</label>
        <input id="roiInitial" type="number" placeholder="e.g. 1000000" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>
      <div>
        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">Final Value (₦)</label>
        <input id="roiFinal" type="number" placeholder="e.g. 1500000" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>
      <div>
        <label class="text-sm text-gray-600 dark:text-gray-300 block mb-1">Duration (Months) — Optional</label>
        <input id="roiDuration" type="number" placeholder="e.g. 12" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>
      <button onclick="calculateROI()" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-lg mt-2">Calculate</button>
      <div id="roiResult" class="hidden bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-2 text-sm"></div>
    </div>
  </div>
</div>

<!-- ===== CALCULATOR JAVASCRIPT ===== -->
<script>
(function() {
  // ---- Basic Calculator ----
  var calcExpression = '';
  var calcDisplay = null;

  function getDisplay() {
    return document.getElementById('calcDisplay');
  }

  window.openCalculator = function() {
    calcExpression = '';
    var d = document.getElementById('calculatorModal');
    d.classList.remove('hidden');
    d.classList.add('flex');
    getDisplay().textContent = '0';
  };
  window.closeCalculator = function() {
    var d = document.getElementById('calculatorModal');
    d.classList.add('hidden');
    d.classList.remove('flex');
  };
  window.appendNumber = function(num) {
    if (calcExpression === '0' && num !== '.') calcExpression = '';
    calcExpression += num;
    getDisplay().textContent = calcExpression;
  };
  window.appendOperator = function(op) {
    if (op === '%') {
      try { calcExpression = String(parseFloat(calcExpression) / 100); }
      catch(e) { calcExpression = '0'; }
    } else {
      var last = calcExpression.slice(-1);
      if (['+','-','*','/'].includes(last)) calcExpression = calcExpression.slice(0,-1);
      calcExpression += op;
    }
    getDisplay().textContent = calcExpression;
  };
  window.calculate = function() {
    try {
      if (calcExpression === '' || calcExpression === undefined) return;
      var result = Function('"use strict"; return (' + calcExpression + ')')();
      if (!isFinite(result)) { getDisplay().textContent = 'Error'; calcExpression = ''; return; }
      calcExpression = String(parseFloat(result.toFixed(10)));
      getDisplay().textContent = calcExpression;
    } catch(e) {
      getDisplay().textContent = 'Error';
      calcExpression = '';
    }
  };
  window.clearCalc = function() {
    calcExpression = '';
    getDisplay().textContent = '0';
  };

  // ---- Mortgage Calculator ----
  window.openMortgageCalculator = function() {
    var d = document.getElementById('mortgageCalculatorModal');
    d.classList.remove('hidden');
    d.classList.add('flex');
    document.getElementById('mortgageResult').classList.add('hidden');
    document.getElementById('mortgageLoan').value = '';
    document.getElementById('mortgageRate').value = '';
    document.getElementById('mortgageTerm').value = '';
  };
  window.closeMortgageCalculator = function() {
    var d = document.getElementById('mortgageCalculatorModal');
    d.classList.add('hidden');
    d.classList.remove('flex');
  };
  window.calculateMortgage = function() {
    var P = parseFloat(document.getElementById('mortgageLoan').value);
    var annualRate = parseFloat(document.getElementById('mortgageRate').value);
    var years = parseFloat(document.getElementById('mortgageTerm').value);
    var res = document.getElementById('mortgageResult');
    if (!P || !annualRate || !years || P <= 0 || annualRate <= 0 || years <= 0) {
      res.innerHTML = '<p class="text-red-500">Please fill in all fields with valid values.</p>';
      res.classList.remove('hidden'); return;
    }
    var r = annualRate / 100 / 12;
    var n = years * 12;
    var M = P * (r * Math.pow(1+r, n)) / (Math.pow(1+r, n) - 1);
    var total = M * n;
    var interest = total - P;
    res.innerHTML =
      '<div class="flex justify-between"><span class="text-gray-600 dark:text-gray-300">Monthly Payment</span><span class="font-bold text-gray-800 dark:text-white">₦' + fmt(M) + '</span></div>' +
      '<div class="flex justify-between"><span class="text-gray-600 dark:text-gray-300">Total Payment</span><span class="font-bold text-gray-800 dark:text-white">₦' + fmt(total) + '</span></div>' +
      '<div class="flex justify-between"><span class="text-gray-600 dark:text-gray-300">Total Interest</span><span class="font-bold text-red-500">₦' + fmt(interest) + '</span></div>';
    res.classList.remove('hidden');
  };

  // ---- ROI Calculator ----
  window.openROICalculator = function() {
    var d = document.getElementById('roiCalculatorModal');
    d.classList.remove('hidden');
    d.classList.add('flex');
    document.getElementById('roiResult').classList.add('hidden');
    document.getElementById('roiInitial').value = '';
    document.getElementById('roiFinal').value = '';
    document.getElementById('roiDuration').value = '';
  };
  window.closeROICalculator = function() {
    var d = document.getElementById('roiCalculatorModal');
    d.classList.add('hidden');
    d.classList.remove('flex');
  };
  window.calculateROI = function() {
    var initial = parseFloat(document.getElementById('roiInitial').value);
    var final = parseFloat(document.getElementById('roiFinal').value);
    var duration = parseFloat(document.getElementById('roiDuration').value);
    var res = document.getElementById('roiResult');
    if (!initial || !final || initial <= 0) {
      res.innerHTML = '<p class="text-red-500">Please enter valid investment values.</p>';
      res.classList.remove('hidden'); return;
    }
    var roi = ((final - initial) / initial) * 100;
    var profit = final - initial;
    var html =
      '<div class="flex justify-between"><span class="text-gray-600 dark:text-gray-300">ROI</span><span class="font-bold ' + (roi >= 0 ? 'text-green-600' : 'text-red-500') + '">' + roi.toFixed(2) + '%</span></div>' +
      '<div class="flex justify-between"><span class="text-gray-600 dark:text-gray-300">Net Profit</span><span class="font-bold ' + (profit >= 0 ? 'text-green-600' : 'text-red-500') + '">₦' + fmt(profit) + '</span></div>';
    if (duration && duration > 0) {
      var annualized = (Math.pow(final / initial, 12 / duration) - 1) * 100;
      html += '<div class="flex justify-between"><span class="text-gray-600 dark:text-gray-300">Annualized ROI</span><span class="font-bold text-blue-500">' + annualized.toFixed(2) + '%</span></div>';
    }
    res.innerHTML = html;
    res.classList.remove('hidden');
  };

  // ---- Helpers ----
  function fmt(n) {
    return Math.abs(n).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // ---- Escape key handler ----
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeCalculator();
      closeMortgageCalculator();
      closeROICalculator();
    }
  });
})();
</script>
<!-- ===== END CALCULATORS ===== -->
