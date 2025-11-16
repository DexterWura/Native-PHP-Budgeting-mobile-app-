<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Income.php';
require_once __DIR__ . '/../core/Budget.php';
require_once __DIR__ . '/../core/Transaction.php';
require_once __DIR__ . '/../core/Analytics.php';
require_once __DIR__ . '/../core/Validator.php';

// Helper function to get auth token from request
function getAuthToken() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

// Helper function to send JSON response
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Helper function to get request body
function getRequestBody() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$path = rtrim($path, '/') ?: '/';

// Route handling
try {
    // Auth routes (no token required)
    if ($path === '/auth/register' && $method === 'POST') {
        $data = getRequestBody();
        $errors = Validator::validateRequired($data, ['email', 'password', 'name']);
        
        if (!empty($errors)) {
            sendResponse(['success' => false, 'message' => implode(', ', $errors)], 400);
        }
        
        if (!Validator::email($data['email'])) {
            sendResponse(['success' => false, 'message' => 'Invalid email format'], 400);
        }
        
        if (!Validator::password($data['password'])) {
            sendResponse(['success' => false, 'message' => 'Password must be at least 8 characters'], 400);
        }
        
        $data['name'] = Validator::sanitizeString($data['name'], 255);
        
        $auth = new Auth();
        $result = $auth->register($data['email'], $data['password'], $data['name']);
        sendResponse(['success' => true, 'data' => $result]);
    }
    
    if ($path === '/auth/login' && $method === 'POST') {
        $data = getRequestBody();
        $errors = Validator::validateRequired($data, ['email', 'password']);
        
        if (!empty($errors)) {
            sendResponse(['success' => false, 'message' => implode(', ', $errors)], 400);
        }
        
        if (!Validator::email($data['email'])) {
            sendResponse(['success' => false, 'message' => 'Invalid email format'], 400);
        }
        
        $auth = new Auth();
        $result = $auth->login($data['email'], $data['password']);
        sendResponse(['success' => true, 'data' => $result]);
    }
    
    // Protected routes (require token)
    $token = getAuthToken();
    if (!$token) {
        sendResponse(['success' => false, 'message' => 'Unauthorized'], 401);
    }
    
    $auth = new Auth();
    $user = $auth->getCurrentUser($token);
    if (!$user) {
        sendResponse(['success' => false, 'message' => 'Invalid token'], 401);
    }
    
    $userId = $user['id'];
    
    // User profile route
    if ($path === '/user/profile' && $method === 'GET') {
        sendResponse(['success' => true, 'data' => $user]);
    }
    
    // Income routes
    if ($path === '/income' && $method === 'GET') {
        $income = new Income();
        $result = $income->getAll($userId);
        sendResponse(['success' => true, 'data' => $result]);
    }
    
    if ($path === '/income' && $method === 'POST') {
        $data = getRequestBody();
        $errors = Validator::validateRequired($data, ['amount', 'source', 'frequency', 'start_date']);
        
        if (!empty($errors)) {
            sendResponse(['success' => false, 'message' => implode(', ', $errors)], 400);
        }
        
        if (!Validator::amount($data['amount'])) {
            sendResponse(['success' => false, 'message' => 'Invalid amount'], 400);
        }
        
        if (!Validator::frequency($data['frequency'])) {
            sendResponse(['success' => false, 'message' => 'Invalid frequency'], 400);
        }
        
        if (!Validator::date($data['start_date'])) {
            sendResponse(['success' => false, 'message' => 'Invalid date format'], 400);
        }
        
        $income = new Income();
        $id = $income->create(
            $userId,
            Validator::sanitizeAmount($data['amount']),
            Validator::sanitizeString($data['source']),
            $data['frequency'],
            $data['start_date']
        );
        sendResponse(['success' => true, 'data' => ['id' => $id]], 201);
    }
    
    // Income update/delete routes
    if (preg_match('/^\/income\/(\d+)$/', $path, $matches)) {
        $incomeId = $matches[1];
        $income = new Income();
        
        if ($method === 'PUT') {
            $data = getRequestBody();
            $errors = Validator::validateRequired($data, ['amount', 'source', 'frequency', 'start_date']);
            
            if (!empty($errors)) {
                sendResponse(['success' => false, 'message' => implode(', ', $errors)], 400);
            }
            
            if (!Validator::amount($data['amount'])) {
                sendResponse(['success' => false, 'message' => 'Invalid amount'], 400);
            }
            
            if (!Validator::frequency($data['frequency'])) {
                sendResponse(['success' => false, 'message' => 'Invalid frequency'], 400);
            }
            
            if (!Validator::date($data['start_date'])) {
                sendResponse(['success' => false, 'message' => 'Invalid date format'], 400);
            }
            
            $income->update(
                $incomeId,
                $userId,
                Validator::sanitizeAmount($data['amount']),
                Validator::sanitizeString($data['source']),
                $data['frequency'],
                $data['start_date']
            );
            sendResponse(['success' => true, 'message' => 'Income updated']);
        }
        
        if ($method === 'DELETE') {
            $income->delete($incomeId, $userId);
            sendResponse(['success' => true, 'message' => 'Income deleted']);
        }
    }
    
    // Budget routes
    if ($path === '/budgets' && $method === 'GET') {
        $budget = new Budget();
        $result = $budget->getAll($userId);
        sendResponse(['success' => true, 'data' => $result]);
    }
    
    if ($path === '/budgets' && $method === 'POST') {
        $data = getRequestBody();
        $errors = Validator::validateRequired($data, ['category', 'amount', 'period']);
        
        if (!empty($errors)) {
            sendResponse(['success' => false, 'message' => implode(', ', $errors)], 400);
        }
        
        if (!Validator::amount($data['amount'])) {
            sendResponse(['success' => false, 'message' => 'Invalid amount'], 400);
        }
        
        if (!Validator::frequency($data['period'])) {
            sendResponse(['success' => false, 'message' => 'Invalid period'], 400);
        }
        
        $budget = new Budget();
        $id = $budget->create(
            $userId,
            Validator::sanitizeString($data['category']),
            Validator::sanitizeAmount($data['amount']),
            $data['period']
        );
        sendResponse(['success' => true, 'data' => ['id' => $id]], 201);
    }
    
    // Budget update/delete routes
    if (preg_match('/^\/budgets\/(\d+)$/', $path, $matches)) {
        $budgetId = $matches[1];
        $budget = new Budget();
        
        if ($method === 'PUT') {
            $data = getRequestBody();
            $errors = Validator::validateRequired($data, ['category', 'amount', 'period']);
            
            if (!empty($errors)) {
                sendResponse(['success' => false, 'message' => implode(', ', $errors)], 400);
            }
            
            if (!Validator::amount($data['amount'])) {
                sendResponse(['success' => false, 'message' => 'Invalid amount'], 400);
            }
            
            if (!Validator::frequency($data['period'])) {
                sendResponse(['success' => false, 'message' => 'Invalid period'], 400);
            }
            
            $budget->update(
                $budgetId,
                $userId,
                Validator::sanitizeString($data['category']),
                Validator::sanitizeAmount($data['amount']),
                $data['period']
            );
            sendResponse(['success' => true, 'message' => 'Budget updated']);
        }
        
        if ($method === 'DELETE') {
            $budget->delete($budgetId, $userId);
            sendResponse(['success' => true, 'message' => 'Budget deleted']);
        }
    }
    
    // Transaction routes
    if ($path === '/transactions' && $method === 'GET') {
        $transaction = new Transaction();
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $result = $transaction->getAll($userId, $limit, $offset);
        sendResponse(['success' => true, 'data' => $result]);
    }
    
    if ($path === '/transactions' && $method === 'POST') {
        $data = getRequestBody();
        $errors = Validator::validateRequired($data, ['amount', 'category', 'date', 'type']);
        
        if (!empty($errors)) {
            sendResponse(['success' => false, 'message' => implode(', ', $errors)], 400);
        }
        
        if (!Validator::amount($data['amount'])) {
            sendResponse(['success' => false, 'message' => 'Invalid amount'], 400);
        }
        
        if (!Validator::transactionType($data['type'])) {
            sendResponse(['success' => false, 'message' => 'Invalid transaction type'], 400);
        }
        
        if (!Validator::date($data['date'])) {
            sendResponse(['success' => false, 'message' => 'Invalid date format'], 400);
        }
        
        $transaction = new Transaction();
        $id = $transaction->create(
            $userId,
            Validator::sanitizeAmount($data['amount']),
            Validator::sanitizeString($data['category']),
            Validator::sanitizeString($data['description'] ?? '', 500),
            $data['date'],
            $data['type']
        );
        sendResponse(['success' => true, 'data' => ['id' => $id]], 201);
    }
    
    // Transaction update/delete routes
    if (preg_match('/^\/transactions\/(\d+)$/', $path, $matches)) {
        $transactionId = $matches[1];
        $transaction = new Transaction();
        
        if ($method === 'PUT') {
            $data = getRequestBody();
            $errors = Validator::validateRequired($data, ['amount', 'category', 'date', 'type']);
            
            if (!empty($errors)) {
                sendResponse(['success' => false, 'message' => implode(', ', $errors)], 400);
            }
            
            if (!Validator::amount($data['amount'])) {
                sendResponse(['success' => false, 'message' => 'Invalid amount'], 400);
            }
            
            if (!Validator::transactionType($data['type'])) {
                sendResponse(['success' => false, 'message' => 'Invalid transaction type'], 400);
            }
            
            if (!Validator::date($data['date'])) {
                sendResponse(['success' => false, 'message' => 'Invalid date format'], 400);
            }
            
            $transaction->update(
                $transactionId,
                $userId,
                Validator::sanitizeAmount($data['amount']),
                Validator::sanitizeString($data['category']),
                Validator::sanitizeString($data['description'] ?? '', 500),
                $data['date'],
                $data['type']
            );
            sendResponse(['success' => true, 'message' => 'Transaction updated']);
        }
        
        if ($method === 'DELETE') {
            $transaction->delete($transactionId, $userId);
            sendResponse(['success' => true, 'message' => 'Transaction deleted']);
        }
    }
    
    // Analytics routes
    if ($path === '/analytics/summary' && $method === 'GET') {
        $analytics = new Analytics();
        $result = $analytics->getFinancialSummary($userId);
        sendResponse(['success' => true, 'data' => $result]);
    }
    
    if ($path === '/analytics/categories' && $method === 'GET') {
        $analytics = new Analytics();
        $months = isset($_GET['months']) ? (int)$_GET['months'] : 1;
        $result = $analytics->getSpendingByCategory($userId, $months);
        sendResponse(['success' => true, 'data' => $result]);
    }
    
    if ($path === '/analytics/trends' && $method === 'GET') {
        $analytics = new Analytics();
        $months = isset($_GET['months']) ? (int)$_GET['months'] : 6;
        $result = $analytics->getMonthlyTrends($userId, $months);
        sendResponse(['success' => true, 'data' => $result]);
    }
    
    // Budget status route
    if ($path === '/budgets/status' && $method === 'GET') {
        $budget = new Budget();
        $period = $_GET['period'] ?? 'monthly';
        $result = $budget->getBudgetStatus($userId, $period);
        sendResponse(['success' => true, 'data' => $result]);
    }
    
    // 404
    sendResponse(['success' => false, 'message' => 'Not found'], 404);
    
} catch (Exception $e) {
    sendResponse(['success' => false, 'message' => $e->getMessage()], 400);
}

