<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database configuration
$host = 'localhost';
$dbname = 'ip_management'; // Change to your database name
$username = 'root'; // Change to your MySQL username
$password = ''; // Change to your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$endpoint = $request[0] ?? '';

switch ($endpoint) {
    case 'branches':
        handleBranches($pdo, $method);
        break;
    case 'device-types':
        handleDeviceTypes($pdo, $method);
        break;
    case 'ips':
        handleIPs($pdo, $method, $request);
        break;
    case 'ping':
        handlePing($method, $request);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
}

function handleBranches($pdo, $method) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM branches ORDER BY name");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO branches (name) VALUES (?)");
            $stmt->execute([$data['name']]);
            echo json_encode(['id' => $pdo->lastInsertId(), 'message' => 'Branch created successfully']);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handleDeviceTypes($pdo, $method) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM device_types ORDER BY name");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO device_types (name) VALUES (?)");
            $stmt->execute([$data['name']]);
            echo json_encode(['id' => $pdo->lastInsertId(), 'message' => 'Device type created successfully']);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handleIPs($pdo, $method, $request) {
    switch ($method) {
        case 'GET':
            if (isset($_GET['branch_id'])) {
                $stmt = $pdo->prepare("
                    SELECT i.*, b.name as branch_name, dt.name as device_type_name 
                    FROM ips i 
                    JOIN branches b ON i.branch_id = b.id 
                    JOIN device_types dt ON i.device_type_id = dt.id 
                    WHERE i.branch_id = ? 
                    ORDER BY INET_ATON(i.ip_address)
                ");
                $stmt->execute([$_GET['branch_id']]);
            } else {
                $stmt = $pdo->query("
                    SELECT i.*, b.name as branch_name, dt.name as device_type_name 
                    FROM ips i 
                    JOIN branches b ON i.branch_id = b.id 
                    JOIN device_types dt ON i.device_type_id = dt.id 
                    ORDER BY INET_ATON(i.ip_address)
                ");
            }
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            try {
                $stmt = $pdo->prepare("INSERT INTO ips (ip_address, device_name, device_type_id, branch_id, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['ip_address'],
                    $data['device_name'],
                    $data['device_type_id'],
                    $data['branch_id'],
                    $data['description'] ?? ''
                ]);
                echo json_encode(['id' => $pdo->lastInsertId(), 'message' => 'IP created successfully']);
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    http_response_code(400);
                    echo json_encode(['error' => 'IP address already exists']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                }
            }
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $request[1] ?? null;
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'IP ID is required']);
                return;
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE ips SET ip_address = ?, device_name = ?, device_type_id = ?, branch_id = ?, description = ? WHERE id = ?");
                $stmt->execute([
                    $data['ip_address'],
                    $data['device_name'],
                    $data['device_type_id'],
                    $data['branch_id'],
                    $data['description'] ?? '',
                    $id
                ]);
                echo json_encode(['message' => 'IP updated successfully']);
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    http_response_code(400);
                    echo json_encode(['error' => 'IP address already exists']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                }
            }
            break;
            
        case 'DELETE':
            $id = $request[1] ?? null;
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'IP ID is required']);
                return;
            }
            
            $stmt = $pdo->prepare("DELETE FROM ips WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['message' => 'IP deleted successfully']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handlePing($method, $request) {
    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $ip = $data['ip_address'] ?? '';
    
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid IP address']);
        return;
    }
    
    // Cross-platform ping implementation
    $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    $output = [];
    $return_var = 0;
    
    if ($isWindows) {
        // Windows ping command
        $command = "ping -n 1 -w 3000 " . escapeshellarg($ip) . " 2>&1";
    } else {
        // Linux/Unix ping command
        $command = "ping -c 1 -W 3 " . escapeshellarg($ip) . " 2>&1";
    }
    
    exec($command, $output, $return_var);
    
    $isOnline = ($return_var === 0);
    $responseTime = null;
    
    if ($isOnline) {
        // Extract response time from ping output
        $outputString = implode(' ', $output);
        
        if ($isWindows) {
            // Windows format: time=XXXms or time<1ms
            if (preg_match('/time[<=]([0-9]+)ms/i', $outputString, $matches)) {
                $responseTime = floatval($matches[1]);
            }
        } else {
            // Linux/Unix format: time=XXX.XXX ms
            if (preg_match('/time=([0-9.]+)\s*ms/', $outputString, $matches)) {
                $responseTime = floatval($matches[1]);
            }
        }
    }
    
    // Additional network diagnostics
    $diagnostics = [];
    if (!$isOnline) {
        // Try to determine why ping failed
        $outputString = implode(' ', $output);
        if (stripos($outputString, 'unreachable') !== false) {
            $diagnostics['reason'] = 'Host unreachable';
        } elseif (stripos($outputString, 'timeout') !== false) {
            $diagnostics['reason'] = 'Request timeout';
        } elseif (stripos($outputString, 'unknown host') !== false || stripos($outputString, 'cannot resolve') !== false) {
            $diagnostics['reason'] = 'Cannot resolve hostname';
        } else {
            $diagnostics['reason'] = 'Connection failed';
        }
    }
    
    echo json_encode([
        'ip_address' => $ip,
        'online' => $isOnline,
        'response_time' => $responseTime,
        'timestamp' => date('Y-m-d H:i:s'),
        'diagnostics' => $diagnostics,
        'raw_output' => $output // Include raw output for debugging
    ]);
}
?>