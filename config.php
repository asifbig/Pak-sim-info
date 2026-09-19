<?php
/**
 * PAK SIM DATABASE - Database Configuration (ENHANCED)
 * For InfinityFree Hosting
 * FIXES: Real-time stats, proper IP counting, permanent save
 */

// Database Configuration - Provided by User
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_USER', 'if0_41676041');
define('DB_PASS', 'OVIdcGsi3gnB');
define('DB_NAME', 'if0_41676041_testing76');

// Initialize global variables
$conn = null;
$db_error = null;

// Create connection with error suppression to prevent fatal errors
try {
    // Set mysqli to not throw exceptions for some compatibility
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn && $conn->connect_error) {
        $db_error = "Database connection failed: " . $conn->connect_error;
        $conn = null;
    } elseif (!$conn) {
        $db_error = "Database connection failed: Unable to initialize mysqli.";
    } else {
        $conn->set_charset("utf8");
    }
} catch (Exception $e) {
    $db_error = "Database connection error: " . $e->getMessage();
    $conn = null;
}

// Function to check if a table exists
function tableExists($tableName) {
    global $conn;
    if (!$conn || !($conn instanceof mysqli) || $conn->connect_error) return false;
    $result = $conn->query("SHOW TABLES LIKE '". $conn->real_escape_string($tableName) ."'");
    return ($result && $result->num_rows > 0);
}

// Function to get total checks from database - REAL DATA ONLY (FIXED)
function getTotalChecks() {
    global $conn;
    
    // Try to reconnect if not connected
    if (!$conn || !($conn instanceof mysqli) || $conn->connect_error) {
        try {
            mysqli_report(MYSQLI_REPORT_OFF);
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (!$conn || $conn->connect_error) {
                // Fallback to file logs
                $stats = getStatsFromFileLogs();
                return $stats['total_checks'];
            }
            $conn->set_charset('utf8');
        } catch (Exception $e) {
            // Fallback to file logs
            $stats = getStatsFromFileLogs();
            return $stats['total_checks'];
        }
    }
    
    // Check if table exists before querying
    if (!tableExists('search_logs')) {
        // Fallback to file logs
        $stats = getStatsFromFileLogs();
        return $stats['total_checks'];
    }
    
    // Query to count ALL searches (total checks)
    $result = $conn->query('SELECT COUNT(*) as total FROM search_logs WHERE results_found > 0');
    if ($result) {
        $row = $result->fetch_assoc();
        return intval($row['total'] ?? 0);
    }
    
    // Fallback to file logs if query fails
    $stats = getStatsFromFileLogs();
    return $stats['total_checks'];
}

// Function to get total unique users from database - REAL DATA ONLY (FIXED)
// Counts unique IP addresses (one IP = one user)
function getTotalUsers() {
    global $conn;
    
    // Try to reconnect if not connected
    if (!$conn || !($conn instanceof mysqli) || $conn->connect_error) {
        try {
            mysqli_report(MYSQLI_REPORT_OFF);
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (!$conn || $conn->connect_error) {
                // Fallback to file logs
                $stats = getStatsFromFileLogs();
                return $stats['total_users'];
            }
            $conn->set_charset('utf8');
        } catch (Exception $e) {
            // Fallback to file logs
            $stats = getStatsFromFileLogs();
            return $stats['total_users'];
        }
    }
    
    // Check if table exists before querying
    if (!tableExists('search_logs')) {
        // Fallback to file logs
        $stats = getStatsFromFileLogs();
        return $stats['total_users'];
    }
    
    // Query to count UNIQUE IP addresses (one IP = one user)
    $result = $conn->query('SELECT COUNT(DISTINCT user_ip) as total FROM search_logs WHERE user_ip IS NOT NULL AND user_ip != "0.0.0.0"');
    if ($result) {
        $row = $result->fetch_assoc();
        return intval($row['total'] ?? 0);
    }
    
    // Fallback to file logs if query fails
    $stats = getStatsFromFileLogs();
    return $stats['total_users'];
}

// Function to get user stats by IP (for real-time display)
function getUserStatsByIP($ip) {
    global $conn;
    
    if (!$conn || !($conn instanceof mysqli) || $conn->connect_error) {
        return null;
    }
    
    if (!tableExists('search_logs')) {
        return null;
    }
    
    $stmt = $conn->prepare('SELECT COUNT(*) as checks, MAX(timestamp) as last_check FROM search_logs WHERE user_ip = ?');
    if ($stmt) {
        $stmt->bind_param('s', $ip);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row;
    }
    return null;
}

// Function to log a search - ALWAYS LOG (FIXED FOR PERMANENT SAVE)
function logSearch($query, $results_found) {
    global $conn;
    
    // Get user IP (handle proxies)
    $user_ip = $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '0.0.0.0';
    if (strpos($user_ip, ',') !== false) {
        $user_ip = explode(',', $user_ip)[0];
    }
    $user_ip = trim($user_ip);
    $timestamp = date('Y-m-d H:i:s');
    
    // If no connection, try to reconnect
    if (!$conn || !($conn instanceof mysqli) || $conn->connect_error) {
        try {
            mysqli_report(MYSQLI_REPORT_OFF);
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (!$conn || $conn->connect_error) {
                // Log to file as fallback
                logSearchToFile($user_ip, $query, $results_found, $timestamp);
                return;
            }
            $conn->set_charset('utf8');
        } catch (Exception $e) {
            // Log to file as fallback
            logSearchToFile($user_ip, $query, $results_found, $timestamp);
            return;
        }
    }
    
    // Check if table exists before inserting
    if (!tableExists('search_logs')) {
        // Log to file as fallback
        logSearchToFile($user_ip, $query, $results_found, $timestamp);
        return;
    }
    
    // Prepare and execute the insert with error handling
    $stmt = $conn->prepare('INSERT INTO search_logs (user_ip, search_query, results_found, timestamp) VALUES (?, ?, ?, ?)');
    if ($stmt) {
        $stmt->bind_param('ssis', $user_ip, $query, $results_found, $timestamp);
        $success = $stmt->execute();
        $stmt->close();
        
        // If database insert fails, log to file as fallback
        if (!$success) {
            logSearchToFile($user_ip, $query, $results_found, $timestamp);
        }
    } else {
        // Log to file as fallback
        logSearchToFile($user_ip, $query, $results_found, $timestamp);
    }
}

// Fallback function to log searches to file if database is unavailable
function logSearchToFile($user_ip, $query, $results_found, $timestamp) {
    $log_dir = __DIR__ . '/logs';
    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/search_logs_' . date('Y-m-d') . '.txt';
    $log_entry = json_encode([
        'user_ip' => $user_ip,
        'search_query' => $query,
        'results_found' => $results_found,
        'timestamp' => $timestamp
    ]) . "\n";
    
    @file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Function to get stats from file logs (fallback)
function getStatsFromFileLogs() {
    $log_dir = __DIR__ . '/logs';
    if (!is_dir($log_dir)) {
        return ['total_checks' => 0, 'total_users' => 0];
    }
    
    $total_checks = 0;
    $unique_ips = [];
    
    $log_files = glob($log_dir . '/search_logs_*.txt');
    foreach ($log_files as $file) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $data = json_decode($line, true);
            if ($data && isset($data['user_ip']) && $data['results_found'] > 0) {
                $total_checks++;
                $unique_ips[$data['user_ip']] = true;
            }
        }
    }
    
    return [
        'total_checks' => $total_checks,
        'total_users' => count($unique_ips)
    ];
}
?>
