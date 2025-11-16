<?php

require_once __DIR__ . '/../config/database.php';

class Income {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create($userId, $amount, $source, $frequency, $startDate) {
        $stmt = $this->conn->prepare(
            "INSERT INTO income (user_id, amount, source, frequency, start_date, created_at) 
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        
        $stmt->execute([$userId, $amount, $source, $frequency, $startDate]);
        return $this->conn->lastInsertId();
    }
    
    public function getAll($userId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM income WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getById($id, $userId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM income WHERE id = ? AND user_id = ?"
        );
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }
    
    public function update($id, $userId, $amount, $source, $frequency, $startDate) {
        $stmt = $this->conn->prepare(
            "UPDATE income SET amount = ?, source = ?, frequency = ?, start_date = ? 
             WHERE id = ? AND user_id = ?"
        );
        
        return $stmt->execute([$amount, $source, $frequency, $startDate, $id, $userId]);
    }
    
    public function delete($id, $userId) {
        $stmt = $this->conn->prepare(
            "DELETE FROM income WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$id, $userId]);
    }
    
    public function getTotalMonthlyIncome($userId) {
        $incomes = $this->getAll($userId);
        $total = 0;
        
        foreach ($incomes as $income) {
            switch ($income['frequency']) {
                case 'weekly':
                    $total += $income['amount'] * 4.33; // Average weeks per month
                    break;
                case 'monthly':
                    $total += $income['amount'];
                    break;
                case 'yearly':
                    $total += $income['amount'] / 12;
                    break;
            }
        }
        
        return round($total, 2);
    }
}

