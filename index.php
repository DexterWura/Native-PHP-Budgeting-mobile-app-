<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#007AFF">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="description" content="Track your income, expenses, and budgets with beautiful analytics">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <title>Budget Tracker</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f2f2f7;
            color: #000;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        
        .app-container {
            max-width: 428px;
            margin: 0 auto;
            background: #fff;
            min-height: 100vh;
            position: relative;
        }
        
        .header {
            background: linear-gradient(135deg, #007AFF 0%, #5856D6 100%);
            color: white;
            padding: 60px 20px 20px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .content {
            padding: 20px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .btn {
            background: #007AFF;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn:active {
            transform: scale(0.98);
            opacity: 0.8;
        }
        
        .btn-secondary {
            background: #f2f2f7;
            color: #007AFF;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #3a3a3c;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #e5e5ea;
            border-radius: 10px;
            font-size: 16px;
            background: #f2f2f7;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007AFF;
            background: white;
        }
        
        .hidden {
            display: none;
        }
        
        .error {
            color: #FF3B30;
            font-size: 14px;
            margin-top: 8px;
        }
        
        .success {
            color: #34C759;
            font-size: 14px;
            margin-top: 8px;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #8e8e93;
        }
        
        .loading::after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
        }
        
        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60%, 100% { content: '...'; }
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div id="auth-screen" class="screen">
            <div class="header">
                <h1>Budget Tracker</h1>
                <p>Take control of your finances</p>
            </div>
            <div class="content">
                <div id="login-form">
                    <div class="card">
                        <h2 style="margin-bottom: 20px; font-size: 22px;">Login</h2>
                        <form id="loginForm">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" id="loginEmail" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" id="loginPassword" required>
                            </div>
                            <button type="submit" class="btn">Login</button>
                            <div id="loginError" class="error hidden"></div>
                        </form>
                        <p style="text-align: center; margin-top: 16px; font-size: 14px;">
                            Don't have an account? 
                            <a href="#" id="showRegister" style="color: #007AFF; text-decoration: none;">Sign up</a>
                        </p>
                    </div>
                </div>
                
                <div id="register-form" class="hidden">
                    <div class="card">
                        <h2 style="margin-bottom: 20px; font-size: 22px;">Create Account</h2>
                        <form id="registerForm">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" id="registerName" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" id="registerEmail" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" id="registerPassword" required minlength="8">
                            </div>
                            <button type="submit" class="btn">Create Account</button>
                            <div id="registerError" class="error hidden"></div>
                        </form>
                        <p style="text-align: center; margin-top: 16px; font-size: 14px;">
                            Already have an account? 
                            <a href="#" id="showLogin" style="color: #007AFF; text-decoration: none;">Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="app-screen" class="screen hidden">
            <!-- App content will be loaded here -->
        </div>
    </div>
    
    <script src="/config.js"></script>
    <script>
        // API base URL - uses config.js or defaults to relative path
        const API_URL = (window.API_CONFIG && window.API_CONFIG.API_URL) || '/api';
        
        // Auth token storage
        let authToken = localStorage.getItem('authToken');
        
        // Initialize app
        if (authToken) {
            showApp();
        } else {
            showAuth();
        }
        
        // Auth screen handlers
        document.getElementById('showRegister').addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('register-form').classList.remove('hidden');
        });
        
        document.getElementById('showLogin').addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('register-form').classList.add('hidden');
            document.getElementById('login-form').classList.remove('hidden');
        });
        
        // Login form
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const errorDiv = document.getElementById('loginError');
            const submitBtn = e.target.querySelector('button[type="submit"]');
            
            errorDiv.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Logging in...';
            
            try {
                const response = await fetch(`${API_URL}/auth/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    authToken = data.data.token;
                    localStorage.setItem('authToken', authToken);
                    showApp();
                } else {
                    errorDiv.textContent = data.message || 'Login failed';
                    errorDiv.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Login';
                }
            } catch (error) {
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Login';
            }
        });
        
        // Register form
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('registerName').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const errorDiv = document.getElementById('registerError');
            const submitBtn = e.target.querySelector('button[type="submit"]');
            
            errorDiv.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating account...';
            
            try {
                const response = await fetch(`${API_URL}/auth/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, email, password })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    authToken = data.data.token;
                    localStorage.setItem('authToken', authToken);
                    showApp();
                } else {
                    errorDiv.textContent = data.message || 'Registration failed';
                    errorDiv.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Account';
                }
            } catch (error) {
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Account';
            }
        });
        
        function showAuth() {
            document.getElementById('auth-screen').classList.remove('hidden');
            document.getElementById('app-screen').classList.add('hidden');
        }
        
        function showApp() {
            document.getElementById('auth-screen').classList.add('hidden');
            document.getElementById('app-screen').classList.remove('hidden');
            loadDashboard();
        }
        
        async function loadDashboard() {
            const appScreen = document.getElementById('app-screen');
            
            try {
                const summary = await apiCall('/analytics/summary');
                
                appScreen.innerHTML = `
                    <div class="header">
                        <h1>Dashboard</h1>
                        <p>${new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</p>
                    </div>
                    <div class="content">
                        <div class="card">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #8e8e93; font-size: 14px;">Income</span>
                                <span style="color: #34C759; font-size: 20px; font-weight: 700;">$${formatMoney(summary.current_month.income)}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #8e8e93; font-size: 14px;">Expenses</span>
                                <span style="color: #FF3B30; font-size: 20px; font-weight: 700;">$${formatMoney(summary.current_month.expenses)}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid #e5e5ea;">
                                <span style="color: #8e8e93; font-size: 14px;">Savings</span>
                                <span style="color: #007AFF; font-size: 20px; font-weight: 700;">$${formatMoney(summary.current_month.savings)}</span>
                            </div>
                        </div>
                        
                        <div class="card">
                            <h3 style="margin-bottom: 16px; font-size: 18px;">Quick Actions</h3>
                            <button class="btn" onclick="showScreen('transactions')" style="margin-bottom: 12px;">Add Transaction</button>
                            <button class="btn btn-secondary" onclick="showScreen('income')">Manage Income</button>
                            <button class="btn btn-secondary" onclick="showScreen('budgets')" style="margin-top: 12px;">Manage Budgets</button>
                        </div>
                        
                        <div class="card">
                            <h3 style="margin-bottom: 16px; font-size: 18px;">Recent Transactions</h3>
                            <div id="recentTransactions" class="loading">Loading</div>
                        </div>
                        
                        <div class="card">
                            <h3 style="margin-bottom: 16px; font-size: 18px;">Spending by Category</h3>
                            <canvas id="categoryChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                    <div style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 428px; background: white; border-top: 1px solid #e5e5ea; padding: 10px; display: flex; justify-content: space-around;">
                        <button onclick="showScreen('dashboard')" style="background: none; border: none; color: #007AFF; font-size: 12px; padding: 8px;">Dashboard</button>
                        <button onclick="showScreen('transactions')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Transactions</button>
                        <button onclick="showScreen('budgets')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Budgets</button>
                        <button onclick="showScreen('analytics')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Analytics</button>
                        <button onclick="logout()" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Logout</button>
                    </div>
                `;
                
                loadRecentTransactions();
                loadCategoryChart(summary.spending_by_category);
            } catch (error) {
                console.error('Dashboard load error:', error);
            }
        }
        
        async function apiCall(endpoint, options = {}) {
            try {
                const response = await fetch(`${API_URL}${endpoint}`, {
                    ...options,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${authToken}`,
                        ...options.headers
                    }
                });
                
                const data = await response.json();
                
                if (response.status === 401) {
                    // Token expired or invalid
                    localStorage.removeItem('authToken');
                    authToken = null;
                    showAuth();
                    throw new Error('Session expired. Please login again.');
                }
                
                if (!data.success) {
                    throw new Error(data.message || 'API error');
                }
                
                return data.data;
            } catch (error) {
                if (error instanceof TypeError && error.message.includes('fetch')) {
                    throw new Error('Network error. Please check your connection.');
                }
                throw error;
            }
        }
        
        function formatMoney(amount) {
            return parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        async function loadRecentTransactions() {
            try {
                const transactions = await apiCall('/transactions?limit=5');
                const container = document.getElementById('recentTransactions');
                
                if (transactions.length === 0) {
                    container.innerHTML = '<p style="color: #8e8e93; text-align: center; padding: 20px;">No transactions yet</p>';
                    return;
                }
                
                container.innerHTML = transactions.map(t => `
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e5ea;">
                        <div>
                            <div style="font-weight: 600;">${t.category}</div>
                            <div style="font-size: 12px; color: #8e8e93;">${t.description || 'No description'}</div>
                        </div>
                        <div style="color: ${t.type === 'expense' ? '#FF3B30' : '#34C759'}; font-weight: 600;">
                            ${t.type === 'expense' ? '-' : '+'}$${formatMoney(t.amount)}
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                document.getElementById('recentTransactions').innerHTML = '<p style="color: #FF3B30;">Error loading transactions</p>';
            }
        }
        
        function loadCategoryChart(data) {
            // Chart will be loaded with Chart.js
            const canvas = document.getElementById('categoryChart');
            if (!canvas || !window.Chart) return;
            
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: data.map(d => d.category),
                    datasets: [{
                        data: data.map(d => d.total),
                        backgroundColor: ['#007AFF', '#34C759', '#FF9500', '#FF3B30', '#5856D6', '#FF2D55']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
        
        function showScreen(screen) {
            if (screen === 'dashboard') {
                loadDashboard();
            } else if (screen === 'transactions') {
                loadTransactionsScreen();
            } else if (screen === 'budgets') {
                loadBudgetsScreen();
            } else if (screen === 'income') {
                loadIncomeScreen();
            } else if (screen === 'analytics') {
                loadAnalyticsScreen();
            }
        }
        
        function logout() {
            localStorage.removeItem('authToken');
            authToken = null;
            showAuth();
        }
        
        // Transactions screen
        async function loadTransactionsScreen() {
            const appScreen = document.getElementById('app-screen');
            
            appScreen.innerHTML = `
                <div class="header">
                    <h1>Transactions</h1>
                    <p>Track your spending</p>
                </div>
                <div class="content">
                    <button class="btn" onclick="showAddTransaction()" style="margin-bottom: 16px;">+ Add Transaction</button>
                    <div id="transactionsList" class="loading">Loading</div>
                </div>
                <div style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 428px; background: white; border-top: 1px solid #e5e5ea; padding: 10px; display: flex; justify-content: space-around;">
                    <button onclick="showScreen('dashboard')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Dashboard</button>
                    <button onclick="showScreen('transactions')" style="background: none; border: none; color: #007AFF; font-size: 12px; padding: 8px;">Transactions</button>
                    <button onclick="showScreen('budgets')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Budgets</button>
                    <button onclick="showScreen('analytics')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Analytics</button>
                    <button onclick="logout()" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Logout</button>
                </div>
            `;
            
            loadTransactionsList();
        }
        
        async function loadTransactionsList() {
            try {
                const transactions = await apiCall('/transactions');
                const container = document.getElementById('transactionsList');
                
                if (transactions.length === 0) {
                    container.innerHTML = '<div class="card"><p style="color: #8e8e93; text-align: center; padding: 20px;">No transactions yet</p></div>';
                    return;
                }
                
                container.innerHTML = transactions.map(t => `
                    <div class="card" style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">${t.category}</div>
                                <div style="font-size: 12px; color: #8e8e93; margin-bottom: 4px;">${t.description || 'No description'}</div>
                                <div style="font-size: 12px; color: #8e8e93;">${new Date(t.date).toLocaleDateString()}</div>
                            </div>
                            <div style="color: ${t.type === 'expense' ? '#FF3B30' : '#34C759'}; font-weight: 700; font-size: 18px;">
                                ${t.type === 'expense' ? '-' : '+'}$${formatMoney(t.amount)}
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                document.getElementById('transactionsList').innerHTML = '<div class="card"><p style="color: #FF3B30;">Error loading transactions</p></div>';
            }
        }
        
        function showAddTransaction() {
            const appScreen = document.getElementById('app-screen');
            appScreen.innerHTML = `
                <div class="header">
                    <h1>Add Transaction</h1>
                </div>
                <div class="content">
                    <div class="card">
                        <form id="transactionForm">
                            <div class="form-group">
                                <label>Type</label>
                                <select id="transactionType" required>
                                    <option value="expense">Expense</option>
                                    <option value="income">Income</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" id="transactionAmount" step="0.01" min="0" required>
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <input type="text" id="transactionCategory" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" id="transactionDescription">
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" id="transactionDate" value="${new Date().toISOString().split('T')[0]}" required>
                            </div>
                            <button type="submit" class="btn">Add Transaction</button>
                            <button type="button" class="btn btn-secondary" onclick="showScreen('transactions')" style="margin-top: 12px;">Cancel</button>
                            <div id="transactionError" class="error hidden"></div>
                        </form>
                    </div>
                </div>
            `;
            
            document.getElementById('transactionForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const errorDiv = document.getElementById('transactionError');
                const submitBtn = e.target.querySelector('button[type="submit"]');
                
                errorDiv.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Adding...';
                
                try {
                    await apiCall('/transactions', {
                        method: 'POST',
                        body: JSON.stringify({
                            amount: document.getElementById('transactionAmount').value,
                            category: document.getElementById('transactionCategory').value,
                            description: document.getElementById('transactionDescription').value,
                            date: document.getElementById('transactionDate').value,
                            type: document.getElementById('transactionType').value
                        })
                    });
                    
                    showScreen('transactions');
                } catch (error) {
                    errorDiv.textContent = error.message || 'Failed to add transaction';
                    errorDiv.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Add Transaction';
                }
            });
        }
        
        // Budgets screen
        async function loadBudgetsScreen() {
            const appScreen = document.getElementById('app-screen');
            
            appScreen.innerHTML = `
                <div class="header">
                    <h1>Budgets</h1>
                    <p>Manage your spending limits</p>
                </div>
                <div class="content">
                    <button class="btn" onclick="showAddBudget()" style="margin-bottom: 16px;">+ Add Budget</button>
                    <div id="budgetsList" class="loading">Loading</div>
                </div>
                <div style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 428px; background: white; border-top: 1px solid #e5e5ea; padding: 10px; display: flex; justify-content: space-around;">
                    <button onclick="showScreen('dashboard')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Dashboard</button>
                    <button onclick="showScreen('transactions')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Transactions</button>
                    <button onclick="showScreen('budgets')" style="background: none; border: none; color: #007AFF; font-size: 12px; padding: 8px;">Budgets</button>
                    <button onclick="showScreen('analytics')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Analytics</button>
                    <button onclick="logout()" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Logout</button>
                </div>
            `;
            
            loadBudgetsList();
        }
        
        async function loadBudgetsList() {
            try {
                const budgets = await apiCall('/budgets');
                const status = await apiCall('/budgets/status');
                const container = document.getElementById('budgetsList');
                
                if (budgets.length === 0) {
                    container.innerHTML = '<div class="card"><p style="color: #8e8e93; text-align: center; padding: 20px;">No budgets set yet</p></div>';
                    return;
                }
                
                const statusMap = {};
                status.forEach(s => {
                    statusMap[s.category] = s;
                });
                
                container.innerHTML = budgets.map(b => {
                    const stat = statusMap[b.category] || { spent: 0, budgeted: b.amount, percentage: 0 };
                    const percentage = Math.min(stat.percentage, 100);
                    const isOver = stat.spent > b.amount;
                    
                    return `
                        <div class="card" style="margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <div style="font-weight: 600; font-size: 16px;">${b.category}</div>
                                <div style="color: ${isOver ? '#FF3B30' : '#34C759'}; font-weight: 600;">
                                    $${formatMoney(stat.spent)} / $${formatMoney(b.amount)}
                                </div>
                            </div>
                            <div style="background: #e5e5ea; border-radius: 4px; height: 8px; overflow: hidden; margin-bottom: 8px;">
                                <div style="background: ${isOver ? '#FF3B30' : '#34C759'}; height: 100%; width: ${percentage}%; transition: width 0.3s;"></div>
                            </div>
                            <div style="font-size: 12px; color: #8e8e93;">
                                ${b.period} • ${percentage.toFixed(0)}% used
                            </div>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                document.getElementById('budgetsList').innerHTML = '<div class="card"><p style="color: #FF3B30;">Error loading budgets</p></div>';
            }
        }
        
        function showAddBudget() {
            const appScreen = document.getElementById('app-screen');
            appScreen.innerHTML = `
                <div class="header">
                    <h1>Add Budget</h1>
                </div>
                <div class="content">
                    <div class="card">
                        <form id="budgetForm">
                            <div class="form-group">
                                <label>Category</label>
                                <input type="text" id="budgetCategory" required>
                            </div>
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" id="budgetAmount" step="0.01" min="0" required>
                            </div>
                            <div class="form-group">
                                <label>Period</label>
                                <select id="budgetPeriod" required>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly" selected>Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <button type="submit" class="btn">Add Budget</button>
                            <button type="button" class="btn btn-secondary" onclick="showScreen('budgets')" style="margin-top: 12px;">Cancel</button>
                            <div id="budgetError" class="error hidden"></div>
                        </form>
                    </div>
                </div>
            `;
            
            document.getElementById('budgetForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const errorDiv = document.getElementById('budgetError');
                
                try {
                    await apiCall('/budgets', {
                        method: 'POST',
                        body: JSON.stringify({
                            category: document.getElementById('budgetCategory').value,
                            amount: document.getElementById('budgetAmount').value,
                            period: document.getElementById('budgetPeriod').value
                        })
                    });
                    
                    showScreen('budgets');
                } catch (error) {
                    errorDiv.textContent = error.message || 'Failed to add budget';
                    errorDiv.classList.remove('hidden');
                }
            });
        }
        
        // Income screen
        async function loadIncomeScreen() {
            const appScreen = document.getElementById('app-screen');
            
            appScreen.innerHTML = `
                <div class="header">
                    <h1>Income</h1>
                    <p>Manage your income streams</p>
                </div>
                <div class="content">
                    <button class="btn" onclick="showAddIncome()" style="margin-bottom: 16px;">+ Add Income</button>
                    <div id="incomeList" class="loading">Loading</div>
                </div>
            `;
            
            loadIncomeList();
        }
        
        async function loadIncomeList() {
            try {
                const income = await apiCall('/income');
                const container = document.getElementById('incomeList');
                
                if (income.length === 0) {
                    container.innerHTML = '<div class="card"><p style="color: #8e8e93; text-align: center; padding: 20px;">No income streams yet</p></div>';
                    return;
                }
                
                container.innerHTML = income.map(i => `
                    <div class="card" style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">${i.source}</div>
                                <div style="font-size: 12px; color: #8e8e93;">$${formatMoney(i.amount)} ${i.frequency}</div>
                            </div>
                            <div style="color: #34C759; font-weight: 700; font-size: 18px;">
                                $${formatMoney(i.amount)}
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                document.getElementById('incomeList').innerHTML = '<div class="card"><p style="color: #FF3B30;">Error loading income</p></div>';
            }
        }
        
        function showAddIncome() {
            const appScreen = document.getElementById('app-screen');
            appScreen.innerHTML = `
                <div class="header">
                    <h1>Add Income</h1>
                </div>
                <div class="content">
                    <div class="card">
                        <form id="incomeForm">
                            <div class="form-group">
                                <label>Source</label>
                                <input type="text" id="incomeSource" required>
                            </div>
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" id="incomeAmount" step="0.01" min="0" required>
                            </div>
                            <div class="form-group">
                                <label>Frequency</label>
                                <select id="incomeFrequency" required>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly" selected>Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" id="incomeStartDate" value="${new Date().toISOString().split('T')[0]}" required>
                            </div>
                            <button type="submit" class="btn">Add Income</button>
                            <button type="button" class="btn btn-secondary" onclick="showScreen('income')" style="margin-top: 12px;">Cancel</button>
                            <div id="incomeError" class="error hidden"></div>
                        </form>
                    </div>
                </div>
            `;
            
            document.getElementById('incomeForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const errorDiv = document.getElementById('incomeError');
                
                try {
                    await apiCall('/income', {
                        method: 'POST',
                        body: JSON.stringify({
                            source: document.getElementById('incomeSource').value,
                            amount: document.getElementById('incomeAmount').value,
                            frequency: document.getElementById('incomeFrequency').value,
                            start_date: document.getElementById('incomeStartDate').value
                        })
                    });
                    
                    showScreen('income');
                } catch (error) {
                    errorDiv.textContent = error.message || 'Failed to add income';
                    errorDiv.classList.remove('hidden');
                }
            });
        }
        
        // Analytics screen
        async function loadAnalyticsScreen() {
            const appScreen = document.getElementById('app-screen');
            
            appScreen.innerHTML = `
                <div class="header">
                    <h1>Analytics</h1>
                    <p>Insights into your finances</p>
                </div>
                <div class="content">
                    <div class="card">
                        <h3 style="margin-bottom: 16px; font-size: 18px;">Monthly Trends</h3>
                        <canvas id="trendsChart" style="max-height: 250px;"></canvas>
                    </div>
                    <div class="card">
                        <h3 style="margin-bottom: 16px; font-size: 18px;">Spending by Category</h3>
                        <canvas id="analyticsCategoryChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
                <div style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 428px; background: white; border-top: 1px solid #e5e5ea; padding: 10px; display: flex; justify-content: space-around;">
                    <button onclick="showScreen('dashboard')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Dashboard</button>
                    <button onclick="showScreen('transactions')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Transactions</button>
                    <button onclick="showScreen('budgets')" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Budgets</button>
                    <button onclick="showScreen('analytics')" style="background: none; border: none; color: #007AFF; font-size: 12px; padding: 8px;">Analytics</button>
                    <button onclick="logout()" style="background: none; border: none; color: #8e8e93; font-size: 12px; padding: 8px;">Logout</button>
                </div>
            `;
            
            loadAnalyticsCharts();
        }
        
        async function loadAnalyticsCharts() {
            try {
                const trends = await apiCall('/analytics/trends?months=6');
                const categories = await apiCall('/analytics/categories?months=3');
                
                // Trends chart
                const trendsCanvas = document.getElementById('trendsChart');
                if (trendsCanvas && window.Chart) {
                    new Chart(trendsCanvas, {
                        type: 'line',
                        data: {
                            labels: trends.map(t => t.month),
                            datasets: [
                                {
                                    label: 'Income',
                                    data: trends.map(t => t.income),
                                    borderColor: '#34C759',
                                    backgroundColor: 'rgba(52, 199, 89, 0.1)',
                                    tension: 0.4
                                },
                                {
                                    label: 'Expenses',
                                    data: trends.map(t => t.expenses),
                                    borderColor: '#FF3B30',
                                    backgroundColor: 'rgba(255, 59, 48, 0.1)',
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
                
                // Category chart
                const categoryCanvas = document.getElementById('analyticsCategoryChart');
                if (categoryCanvas && window.Chart) {
                    new Chart(categoryCanvas, {
                        type: 'bar',
                        data: {
                            labels: categories.map(c => c.category),
                            datasets: [{
                                label: 'Spending',
                                data: categories.map(c => c.total),
                                backgroundColor: '#007AFF'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Analytics load error:', error);
            }
        }
        
        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('Service Worker registered:', registration);
                    })
                    .catch((error) => {
                        console.log('Service Worker registration failed:', error);
                    });
            });
        }
        
        // Prevent pull-to-refresh on mobile
        let lastTouchY = 0;
        document.addEventListener('touchstart', (e) => {
            lastTouchY = e.touches[0].clientY;
        }, { passive: true });
        
        document.addEventListener('touchmove', (e) => {
            const touchY = e.touches[0].clientY;
            const touchDelta = touchY - lastTouchY;
            if (touchDelta > 0 && window.scrollY === 0) {
                e.preventDefault();
            }
        }, { passive: false });
        
        // Handle app install prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            // You can show a custom install button here
        });
        
        // Prevent zoom on double tap
        let lastTouchEnd = 0;
        document.addEventListener('touchend', (e) => {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                e.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
</body>
</html>

