<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Transaction.php';

class Analytics {
    private $conn;
    private $transaction;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->transaction = new Transaction();
    }
    
    public function getSpendingByCategory($userId, $months = 1) {
        $startDate = date('Y-m-d', strtotime("-$months months"));
        return $this->transaction->getByCategory($userId, $startDate);
    }
    
    public function getMonthlyTrends($userId, $months = 6) {
        $trends = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            list($year, $month) = explode('-', $date);
            
            $data = $this->transaction->getMonthlyTotal($userId, $year, $month);
            $trends[] = [
                'month' => $date,
                'income' => (float)($data['total_income'] ?? 0),
                'expenses' => (float)($data['total_expenses'] ?? 0),
                'savings' => (float)($data['total_income'] ?? 0) - (float)($data['total_expenses'] ?? 0)
            ];
        }
        
        return $trends;
    }
    
    public function getTopCategories($userId, $limit = 5) {
        $categories = $this->getSpendingByCategory($userId, 3);
        return array_slice($categories, 0, $limit);
    }
    
    public function getDailySpending($userId, $days = 30) {
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        $stmt = $this->conn->prepare(
            "SELECT date, SUM(amount) as total 
             FROM transactions 
             WHERE user_id = ? AND type = 'expense' AND date >= ? 
             GROUP BY date 
             ORDER BY date ASC"
        );
        
        $stmt->execute([$userId, $startDate]);
        return $stmt->fetchAll();
    }
    
    public function getFinancialSummary($userId) {
        $currentMonth = $this->transaction->getMonthlyTotal($userId);
        $lastMonth = $this->transaction->getMonthlyTotal(
            $userId, 
            date('Y', strtotime('last month')), 
            date('m', strtotime('last month'))
        );
        
        $spendingByCategory = $this->getSpendingByCategory($userId, 1);
        
        return [
            'current_month' => [
                'income' => (float)($currentMonth['total_income'] ?? 0),
                'expenses' => (float)($currentMonth['total_expenses'] ?? 0),
                'savings' => (float)($currentMonth['total_income'] ?? 0) - (float)($currentMonth['total_expenses'] ?? 0)
            ],
            'last_month' => [
                'income' => (float)($lastMonth['total_income'] ?? 0),
                'expenses' => (float)($lastMonth['total_expenses'] ?? 0),
                'savings' => (float)($lastMonth['total_income'] ?? 0) - (float)($lastMonth['total_expenses'] ?? 0)
            ],
            'spending_by_category' => $spendingByCategory,
            'trends' => $this->getMonthlyTrends($userId, 6)
        ];
    }
}

