<?php

require_once __DIR__ . '/../config/database.php';

class Transaction {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create($userId, $amount, $category, $description, $date, $type = 'expense') {
        $stmt = $this->conn->prepare(
            "INSERT INTO transactions (user_id, amount, category, description, date, type, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );
        
        $stmt->execute([$userId, abs($amount), $category, $description, $date, $type]);
        return $this->conn->lastInsertId();
    }
    
    public function getAll($userId, $limit = 100, $offset = 0) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM transactions WHERE user_id = ? 
             ORDER BY date DESC, created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getById($id, $userId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM transactions WHERE id = ? AND user_id = ?"
        );
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }
    
    public function update($id, $userId, $amount, $category, $description, $date, $type) {
        $stmt = $this->conn->prepare(
            "UPDATE transactions SET amount = ?, category = ?, description = ?, date = ?, type = ? 
             WHERE id = ? AND user_id = ?"
        );
        
        return $stmt->execute([abs($amount), $category, $description, $date, $type, $id, $userId]);
    }
    
    public function delete($id, $userId) {
        $stmt = $this->conn->prepare(
            "DELETE FROM transactions WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$id, $userId]);
    }
    
    public function getByCategory($userId, $startDate = null, $endDate = null) {
        $query = "SELECT category, SUM(amount) as total, COUNT(*) as count 
                  FROM transactions 
                  WHERE user_id = ? AND type = 'expense'";
        
        $params = [$userId];
        
        if ($startDate) {
            $query .= " AND date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $query .= " AND date <= ?";
            $params[] = $endDate;
        }
        
        $query .= " GROUP BY category ORDER BY total DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getMonthlyTotal($userId, $year = null, $month = null) {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');
        
        $stmt = $this->conn->prepare(
            "SELECT 
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expenses
             FROM transactions 
             WHERE user_id = ? AND YEAR(date) = ? AND MONTH(date) = ?"
        );
        
        $stmt->execute([$userId, $year, $month]);
        return $stmt->fetch();
    }
}

