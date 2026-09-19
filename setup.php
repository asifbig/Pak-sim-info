<?php
/**
 * PAK SIM DATABASE - Database Setup (ENHANCED)
 * Run this file once to create the required tables
 * FIXES: Added indexes for real-time queries, proper constraints
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database Configuration - Provided by User
$db_host = 'sql100.infinityfree.com';
$db_user = 'if0_41676041';
$db_pass = 'OVIdcGsi3gnB';
$db_name = 'if0_41676041_testing76';

echo "<div style='font-family: Arial; max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px;'>";
echo "<h1>Database Setup Tool (Enhanced)</h1>";

try {
    // Create connection
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    echo "<p style='color:green'>✓ Connected to database successfully!</p>";

    // Create search_logs table with enhanced schema
    $sql = "CREATE TABLE IF NOT EXISTS search_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_ip VARCHAR(45) NOT NULL,
        search_query VARCHAR(20) NOT NULL,
        results_found INT DEFAULT 0,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_ip (user_ip),
        INDEX idx_timestamp (timestamp),
        INDEX idx_results_found (results_found),
        INDEX idx_composite (user_ip, timestamp)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    if ($conn->query($sql) === TRUE) {
        echo "<div style='background: #e6fffa; color: #2c7a7b; padding: 15px; border-radius: 5px; border: 1px solid #81e6d9;'>";
        echo "<h2>✓ Table Created Successfully!</h2>";
        echo "<p>The <strong>search_logs</strong> table is now ready with enhanced indexes.</p>";
        echo "<p><strong>Database Schema:</strong></p>";
        echo "<ul>";
        echo "<li><strong>id</strong> - Auto-increment primary key</li>";
        echo "<li><strong>user_ip</strong> - User's IP address (indexed for fast lookups)</li>";
        echo "<li><strong>search_query</strong> - Search query (phone or CNIC)</li>";
        echo "<li><strong>results_found</strong> - Number of results returned</li>";
        echo "<li><strong>timestamp</strong> - When the search was performed</li>";
        echo "</ul>";
        echo "<p><strong>Indexes for Real-Time Performance:</strong></p>";
        echo "<ul>";
        echo "<li>idx_user_ip - For counting unique users</li>";
        echo "<li>idx_timestamp - For time-based queries</li>";
        echo "<li>idx_results_found - For filtering valid searches</li>";
        echo "<li>idx_composite - For combined IP and timestamp queries</li>";
        echo "</ul>";
        echo "<p><strong>Next Steps:</strong></p>";
        echo "<ol>";
        echo "<li>Delete this <code>setup.php</code> file</li>";
        echo "<li>Go to <a href='index.php'>index.php</a></li>";
        echo "<li>The system will now track:<br/>";
        echo "   - <strong>Total Users:</strong> COUNT(DISTINCT user_ip)<br/>";
        echo "   - <strong>Total Checks:</strong> COUNT(*) WHERE results_found > 0<br/>";
        echo "   - <strong>Permanent Save:</strong> All data saved to database + file fallback<br/>";
        echo "   - <strong>Real-Time Display:</strong> Stats update on each page load";
        echo "</li>";
        echo "</ol>";
        echo "</div>";
    }
    $conn->close();

} catch (Exception $e) {
    echo "<div style='background: #fff5f5; color: #c53030; padding: 15px; border-radius: 5px; border: 1px solid #feb2b2;'>";
    echo "<h2>✗ Setup Failed</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>If this tool continues to fail, please create the table manually using phpMyAdmin in your InfinityFree control panel.</p>";
    echo "<h3>Manual SQL Script:</h3>";
    echo "<pre style='background: #eee; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
    echo "CREATE TABLE search_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_ip VARCHAR(45) NOT NULL,
    search_query VARCHAR(20) NOT NULL,
    results_found INT DEFAULT 0,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_ip (user_ip),
    INDEX idx_timestamp (timestamp),
    INDEX idx_results_found (results_found),
    INDEX idx_composite (user_ip, timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    echo "</pre>";
    echo "</div>";
}

echo "</div>";
?>
