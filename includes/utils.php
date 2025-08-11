<?php
// Utility functions for the cash flow application
// This file contains general helper functions

require_once __DIR__ . '/../config/database.php';

/**
 * Sanitize input data to prevent SQL injection and XSS
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize_input($data) {
    $conn = get_db_connection();
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

/**
 * Format currency in Indonesian Rupiah format
 * @param int|float $amount Amount to format
 * @return string Formatted currency
 */
function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date to Indonesian format
 * @param string $date Date string
 * @param string $format Output format
 * @return string Formatted date
 */
function format_date($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

/**
 * Format datetime to Indonesian format
 * @param string $datetime Datetime string
 * @return string Formatted datetime
 */
function format_datetime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

/**
 * Check if user is logged in
 * @return void Redirects to login if not logged in
 */
function check_login() {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header("Location: ../auth/login.php");
        exit();
    }
}

/**
 * Validate date format
 * @param string $date Date to validate
 * @param string $format Expected format
 * @return bool True if valid, false otherwise
 */
function validate_date($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * Get month name in Indonesian
 * @param int $month_number Month number (1-12)
 * @return string Month name in Indonesian
 */
function get_month_name($month_number) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    return $months[(int)$month_number] ?? '';
}

/**
 * Generate unique transaction ID
 * @return string Unique transaction ID
 */
function generate_transaction_id() {
    return 'TXN' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Log activity to database or file
 * @param string $action Action performed
 * @param string $description Description of the action
 * @param int $user_id User ID (optional)
 * @return bool Success status
 */
function log_activity($action, $description, $user_id = null) {
    $conn = get_db_connection();
    
    $action = sanitize_input($action);
    $description = sanitize_input($description);
    $user_id = $user_id ?? ($_SESSION['user_id'] ?? 1);
    $timestamp = date('Y-m-d H:i:s');
    
    $query = "INSERT INTO activity_log (user_id, action, description, timestamp) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("isss", $user_id, $action, $description, $timestamp);
        return $stmt->execute();
    }
    
    return false;
}

/**
 * Handle errors and log them
 * @param string $error_message Error message
 * @param bool $display_error Whether to display error (for development)
 */
function handle_error($error_message, $display_error = false) {
    error_log($error_message);
    
    if ($display_error || (defined('DEBUG') && DEBUG)) {
        echo "Error: " . htmlspecialchars($error_message);
    }
}

/**
 * Generate random string for tokens
 * @param int $length Length of random string
 * @return string Random string
 */
function generate_random_string($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $string;
}

/**
 * Validate email format
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Indonesian format)
 * @param string $phone Phone number to validate
 * @return bool True if valid, false otherwise
 */
function validate_phone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if phone number starts with 08 or +628 (Indonesian format)
    return preg_match('/^(08|628)[0-9]{8,11}$/', $phone);
}

/**
 * Convert phone number to standard format
 * @param string $phone Phone number to convert
 * @return string Standardized phone number
 */
function format_phone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    if (substr($phone, 0, 1) === '8') {
        $phone = '0' . $phone;
    } elseif (substr($phone, 0, 3) === '628') {
        $phone = '0' . substr($phone, 2);
    }
    
    return $phone;
}

/**
 * Calculate percentage
 * @param float $part Part value
 * @param float $total Total value
 * @param int $precision Decimal precision
 * @return float Percentage
 */
function calculate_percentage($part, $total, $precision = 2) {
    if ($total == 0) return 0;
    return round(($part / $total) * 100, $precision);
}

/**
 * Get current month and year
 * @return array Array with current month and year
 */
function get_current_month_year() {
    return [
        'month' => (int)date('n'),
        'year' => (int)date('Y')
    ];
}

/**
 * Get months for dropdown selection
 * @return array Array of months
 */
function get_months_array() {
    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[$i] = get_month_name($i);
    }
    return $months;
}

/**
 * Get years for dropdown selection
 * @param int $start_year Starting year (default: 2020)
 * @param int $end_year Ending year (default: current year + 2)
 * @return array Array of years
 */
function get_years_array($start_year = 2020, $end_year = null) {
    $end_year = $end_year ?? (date('Y') + 2);
    $years = [];
    
    for ($year = $start_year; $year <= $end_year; $year++) {
        $years[$year] = $year;
    }
    
    return $years;
}

/**
 * Create breadcrumb navigation
 * @param array $items Breadcrumb items
 * @return string HTML breadcrumb
 */
function create_breadcrumb($items) {
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    $count = count($items);
    foreach ($items as $index => $item) {
        if ($index == $count - 1) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($item['title']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['title']) . '</a></li>';
        }
    }
    
    $html .= '</ol></nav>';
    return $html;
}
?>