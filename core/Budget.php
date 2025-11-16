<?php

require_once __DIR__ . '/../config/database.php';

class Budget {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create($userId, $category, $amount, $period) {
        $stmt = $this->conn->prepare(
            "INSERT INTO budgets (user_id, category, amount, period, created_at) 
             VALUES (?, ?, ?, ?, NOW())"
        );
        
        $stmt->execute([$userId, $category, $amount, $period]);
        return $this->conn->lastInsertId();
    }
    
    public function getAll($userId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM budgets WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getById($id, $userId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM budgets WHERE id = ? AND user_id = ?"
        );
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }
    
    public function update($id, $userId, $category, $amount, $period) {
        $stmt = $this->conn->prepare(
            "UPDATE budgets SET category = ?, amount = ?, period = ? 
             WHERE id = ? AND user_id = ?"
        );
        
        return $stmt->execute([$category, $amount, $period, $id, $userId]);
    }
    
    public function delete($id, $userId) {
        $stmt = $this->conn->prepare(
            "DELETE FROM budgets WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$id, $userId]);
    }
    
    public function getBudgetStatus($userId, $period = 'monthly') {
        $budgets = $this->getAll($userId);
        $status = [];
        
        foreach ($budgets as $budget) {
            if ($budget['period'] === $period) {
                $stmt = $this->conn->prepare(
                    "SELECT COALESCE(SUM(amount), 0) as spent 
                     FROM transactions 
                     WHERE user_id = ? AND category = ? 
                     AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')"
                );
                $stmt->execute([$userId, $budget['category']]);
                $spent = $stmt->fetch()['spent'];
                
                $status[] = [
                    'category' => $budget['category'],
                    'budgeted' => $budget['amount'],
                    'spent' => $spent,
                    'remaining' => $budget['amount'] - $spent,
                    'percentage' => $budget['amount'] > 0 ? round(($spent / $budget['amount']) * 100, 2) : 0
                ];
            }
        }
        
        return $status;
    }
}

