<?php

class Validator {
    
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function password($password, $minLength = 8) {
        return strlen($password) >= $minLength;
    }
    
    public static function amount($amount) {
        return is_numeric($amount) && $amount >= 0;
    }
    
    public static function date($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    public static function frequency($frequency) {
        return in_array($frequency, ['weekly', 'monthly', 'yearly']);
    }
    
    public static function transactionType($type) {
        return in_array($type, ['income', 'expense']);
    }
    
    public static function sanitizeString($string, $maxLength = 255) {
        $string = trim($string);
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        if ($maxLength > 0 && strlen($string) > $maxLength) {
            $string = substr($string, 0, $maxLength);
        }
        return $string;
    }
    
    public static function sanitizeAmount($amount) {
        return round(floatval($amount), 2);
    }
    
    public static function validateRequired($data, $fields) {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[] = ucfirst($field) . ' is required';
            }
        }
        return $errors;
    }
}

