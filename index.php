<?php
/**
 * PAK SIM DATABASE - Premium Golden Edition
 * Powered by OLD-STUDIO
 * Real Database Integration for Authentic Statistics
 */

// Disable error display for a cleaner user experience in production
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Include configuration
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
} elseif (file_exists('config.php')) {
    require_once 'config.php';
} else {
    die("Error: config.php not found. Please ensure config.php is in the same directory as index.php.");
}

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$result = null;
$error = null;

if (!empty($query)) {
    // Basic validation for phone or CNIC
    if (!preg_match('/^[0-9]{10,13}$/', $query)) {
        $error = "Please enter a valid Phone Number or CNIC (digits only).";
    } else {
        // Build API URL - New Worker API
        $apiUrl = "https://sim-info-api.wasif-ali.workers.dev/?search=" . urlencode($query);
        
        // Try CURL first
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
            $response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            if ($response === FALSE || !empty($curl_error)) {
                $error = "Unable to connect to the database. Please try again later.";
            } else {
                $result = json_decode($response, true);
                // Map new API structure to internal structure
                if ($result && isset($result['success']) && $result['success'] === true && isset($result['data'])) {
                    $result['records'] = $result['data'];
                }
                
                if (!$result || (empty($result['success']) && empty($result['records']))) {
                    $error = "No records found for this search.";
                    $result = null;
                } else {
                    // Log the search to database
                    $results_count = isset($result['records']) ? count($result['records']) : 0;
                    if (function_exists('logSearch')) {
                        logSearch($query, $results_count);
                    }
                }
            }
        } else {
            // Fallback to file_get_contents if curl is not available
            $opts = array(
                'http' => array(
                    'method'  => 'GET',
                    'header'  => "Content-type: application/json\r\nUser-Agent: Mozilla/5.0",
                    'timeout' => 15
                ),
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                )
            );
            $context = stream_context_create($opts);
            $response = @file_get_contents($apiUrl, false, $context);
            if ($response !== FALSE) {
                $result = json_decode($response, true);
                // Map new API structure to internal structure
                if ($result && isset($result['success']) && $result['success'] === true && isset($result['data'])) {
                    $result['records'] = $result['data'];
                }
                
                if (!$result || (empty($result['success']) && empty($result['records']))) {
                    $error = "No records found for this search.";
                    $result = null;
                } else {
                    $results_count = isset($result['records']) ? count($result['records']) : 0;
                    if (function_exists('logSearch')) {
                        logSearch($query, $results_count);
                    }
                }
            } else {
                $error = "Unable to connect to the database. Please try again later.";
            }
        }
    }
}

// Get real statistics from database
$total_checks = (isset($db_error) || !function_exists('getTotalChecks')) ? 0 : getTotalChecks();
$total_users = (isset($db_error) || !function_exists('getTotalUsers')) ? 0 : getTotalUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAK SIM DATABASE | Premium Golden Edition</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.php">
</head>
<body class="p-3 md:p-4">
    <div class="max-w-2xl mx-auto">
        <header class="text-center mb-4">
            <h1 class="premium-font text-2xl md:text-3xl gold-text font-bold mb-0.5">PAK SIM DATABASE</h1>
            <p class="text-gray-500 tracking-widest uppercase text-xs">Premium Golden Information Portal</p>
            <div class="w-12 h-0.5 bg-gradient-to-r from-transparent via-gold-primary to-transparent mx-auto mt-1" style="background: linear-gradient(to right, transparent, #D4AF37, transparent);"></div>
        </header>

        <div class="card p-3 mb-4 gold-border">
            <form action="index.php" method="GET" class="flex flex-col sm:flex-row gap-2" id="searchForm">
                <input type="text" name="query" id="searchInput" value="<?php echo htmlspecialchars($query); ?>" placeholder="Enter Phone or CNIC (e.g. 03001234567)" class="flex-grow py-2 px-3 text-xs transition-all" required>
                <button type="submit" class="gold-button px-4 py-2 rounded-md text-xs">Search</button>
                <?php if (!empty($query)): ?>
                    <a href="index.php" class="gold-button px-4 py-2 rounded-md text-xs text-center" style="background-color: #D4AF37; color: #000; text-decoration: none; display: inline-block;">Clear</a>
                <?php endif; ?>
            </form>
            
            <!-- Loading Bar Container -->
            <div id="loadingContainer" class="hidden mt-3">
                <div class="flex justify-between mb-1">
                    <span class="text-[10px] text-gold-primary uppercase tracking-tighter">Searching Database...</span>
                    <span id="loadingPercent" class="text-[10px] text-gold-primary">0%</span>
                </div>
                <div class="w-full bg-black/40 rounded-full h-1.5 border border-gold-primary/20 overflow-hidden">
                    <div id="loadingBar" class="bg-gradient-to-r from-gold-primary to-gold-light h-full w-0 transition-all duration-300 shadow-[0_0_10px_rgba(212,175,55,0.5)]"></div>
                </div>
            </div>

            <?php if ($error): ?><p class="error text-center"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
            <?php if (isset($db_error) && $db_error): ?><p class="error text-center" style="color: #ffb700; font-size: 11px; margin-top: 10px;">Database: Using File Logs (Fallback Mode)</p><?php endif; ?>
        </div>

        <?php if ($result && isset($result['records']) && count($result['records']) > 0): ?>
            <div class="card p-3 gold-border overflow-hidden mb-4">
                <div class="flex justify-between items-center mb-2 border-b border-gold-primary/20 pb-1.5">
                    <h2 class="premium-font text-sm gold-text">Search Results</h2>
                    <span class="text-gold-light text-xs uppercase font-bold"><?php echo count($result['records']); ?> Found</span>
                </div>

                <div class="space-y-1">
                    <?php foreach ($result['records'] as $record): 
                        $name = $record['name'] ?? $record['Name'] ?? $record['NAME'] ?? 'N/A';
                        $cnic = $record['cnic'] ?? $record['CNIC'] ?? $record['id'] ?? 'N/A';
                        $num = $record['mobile'] ?? $record['Mobile'] ?? $record['NUMBER'] ?? $record['phone'] ?? 'N/A';
                        $network = $record['network'] ?? $record['Network'] ?? $record['NETWORK'] ?? 'Unknown';
                        $addr = $record['address'] ?? $record['Address'] ?? $record['ADDRESS'] ?? 'N/A';
                        
                        $jsonRecord = json_encode([
                            'Name' => $name,
                            'CNIC' => $cnic,
                            'Phone' => $num,
                            'Network' => $network,
                            'Address' => $addr
                        ]);
                    ?>
                        <div class="result-item">
                            <div class="grid grid-cols-1 gap-2" style="display: grid; grid-template-columns: 1fr; gap: 8px;">
                                <div>
                                    <div class="label-small">Name</div>
                                    <div class="value-small"><?php echo htmlspecialchars($name); ?></div>
                                </div>
                                <div>
                                    <div class="label-small">Phone Number</div>
                                    <div class="value-small text-gold-light"><?php echo htmlspecialchars($num); ?></div>
                                </div>
                                <div>
                                    <div class="label-small">ID Number (CNIC)</div>
                                    <div class="value-small text-gold-light"><?php echo htmlspecialchars($cnic); ?></div>
                                </div>
                                <div>
                                    <div class="label-small">Network</div>
                                    <div class="value-small text-gold-light"><?php echo htmlspecialchars($network); ?></div>
                                </div>
                                <div>
                                    <div class="label-small">Address</div>
                                    <div class="value-small text-gray-300 leading-tight"><?php echo htmlspecialchars($addr); ?></div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 mt-2">
                                <button class="action-button" onclick='copyToClipboard(this, <?php echo htmlspecialchars($jsonRecord, ENT_QUOTES, "UTF-8"); ?>)'>Copy Info</button>
                                <button class="action-button" onclick="shareRecord('<?php echo htmlspecialchars(addslashes($name)); ?>', '<?php echo htmlspecialchars(addslashes($num)); ?>')">Share</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif ($result && isset($result['records'])): ?>
            <div class="card p-3 gold-border overflow-hidden mb-4">
                <div class="text-center py-4">
                    <p class="text-gray-500 text-xs italic">No records found. Try another query.</p>
                </div>
            </div>
        <?php endif; ?>

        <footer class="mt-8 text-center">
            <div class="grid gap-2 mb-4 max-w-sm mx-auto" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; max-width: 384px; margin: 0 auto;">
                <div class="stat-box"><div class="stat-number"><?php echo number_format($total_users); ?></div><div class="stat-label">Total Users</div></div>
                <div class="stat-box"><div class="stat-number"><?php echo number_format($total_checks); ?></div><div class="stat-label">Total Checks</div></div>
                <div class="stat-box"><div class="stat-number text-green-500">LIVE</div><div class="stat-label">Status</div></div>
            </div>
            
            <p class="mb-2 mt-6 text-gold-primary/60 font-bold text-xs premium-font tracking-widest">POWERED BY OLD-STUDIO</p>
            <div class="flex justify-center gap-3 mb-3">
                <a href="https://whatsapp.com/channel/0029VavHzv259PwTIz1XxJ09" target="_blank" class="bg-green-600/10 text-green-400 border border-green-600/20 px-3 py-1 rounded-full text-xs hover:bg-green-600/20 transition flex items-center gap-1" style="background-color: rgba(22, 163, 74, 0.1); color: #4ade80; border: 1px solid rgba(22, 163, 74, 0.2); padding: 4px 12px; border-radius: 9999px; font-size: 9px; display: flex; align-items: center; gap: 4px; text-decoration: none; transition: all 0.2s;">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp Channel
                </a>
            </div>
            <p class="text-[10px] text-gray-600 max-w-xs mx-auto leading-tight">This tool is for educational purposes and uses public data sources. Please use responsibly and respect privacy regulations.</p>
        </footer>
    </div>

    <script>
        // Form submission loading effect
        document.getElementById('searchForm').addEventListener('submit', function() {
            const loadingContainer = document.getElementById('loadingContainer');
            const loadingBar = document.getElementById('loadingBar');
            const loadingPercent = document.getElementById('loadingPercent');
            
            loadingContainer.classList.remove('hidden');
            
            let width = 0;
            const interval = setInterval(() => {
                if (width >= 95) {
                    clearInterval(interval);
                } else {
                    width += Math.random() * 15;
                    if (width > 95) width = 95;
                    loadingBar.style.width = width + '%';
                    loadingPercent.innerText = Math.floor(width) + '%';
                }
            }, 200);
        });

        // Copy to clipboard function
        function copyToClipboard(btn, data) {
            const text = `Name: ${data.Name}\nPhone: ${data.Phone}\nCNIC: ${data.CNIC}\nNetwork: ${data.Network}\nAddress: ${data.Address}`;
            navigator.clipboard.writeText(text).then(() => {
                const originalText = btn.innerText;
                btn.innerText = 'Copied!';
                btn.style.color = '#4ade80';
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.color = '';
                }, 2000);
            });
        }

        // Share function
        function shareRecord(name, phone) {
            if (navigator.share) {
                navigator.share({
                    title: 'PAK SIM Record',
                    text: `Record found for ${name} (${phone}) on PAK SIM DATABASE.`,
                    url: window.location.href
                });
            } else {
                alert(`Record: ${name} (${phone})`);
            }
        }
    </script>

    <style>
        :root {
            --gold-primary: #D4AF37;
            --gold-light: #F9E29C;
            --gold-dark: #996515;
            --bg-dark: #0a0a0a;
            --card-bg: #121212;
        }

        body {
            background-color: var(--bg-dark);
            color: #e5e7eb;
            font-family: 'Poppins', sans-serif;
            background-image: radial-gradient(circle at 50% 0%, #1a1a1a 0%, #0a0a0a 100%);
            min-height: 100vh;
        }

        .premium-font {
            font-family: 'Cinzel', serif;
        }

        .gold-text {
            background: linear-gradient(to bottom, var(--gold-light) 0%, var(--gold-primary) 50%, var(--gold-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .gold-border {
            border: 1px solid rgba(212, 175, 55, 0.3);
            position: relative;
        }

        .gold-border::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border: 1px solid transparent;
            background: linear-gradient(45deg, var(--gold-dark), transparent, var(--gold-light)) border-box;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            pointer-events: none;
            opacity: 0.2;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        input[type="text"] {
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 6px;
            color: white;
            outline: none;
        }

        input[type="text"]:focus {
            border-color: var(--gold-primary);
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.2);
        }

        .gold-button {
            background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold-primary) 50%, var(--gold-light) 100%);
            color: black;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .gold-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }

        .result-item {
            background: rgba(255,255,255,0.03);
            border-radius: 6px;
            padding: 10px;
            border-left: 2px solid var(--gold-primary);
            margin-bottom: 8px;
        }

        .label-small {
            font-size: 8px;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }

        .value-small {
            font-size: 11px;
            font-weight: 500;
        }

        .action-button {
            font-size: 9px;
            padding: 3px 8px;
            border-radius: 4px;
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold-light);
            border: 1px solid rgba(212, 175, 55, 0.2);
            transition: all 0.2s;
        }

        .action-button:hover {
            background: var(--gold-primary);
            color: black;
        }

        .stat-box {
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(212, 175, 55, 0.1);
            padding: 6px;
            border-radius: 6px;
        }

        .stat-number {
            font-size: 12px;
            font-weight: 700;
            color: var(--gold-primary);
        }

        .stat-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .error {
            color: #ef4444;
            font-size: 10px;
            margin-top: 8px;
        }
    </style>
</body>
</html>
