<?php
header('Content-Type: application/json; charset=utf-8');

$type = isset($_GET['type']) ? $_GET['type'] : 'access';
$lines = isset($_GET['lines']) ? intval($_GET['lines']) : 15;
if ($lines <= 0 || $lines > 100) $lines = 15;

$logFile = ($type === 'error') ? "C:\\xampp\\apache\\logs\\error.log" : "C:\\xampp\\apache\\logs\\access.log";

if (!file_exists($logFile)) {
    echo json_encode(["error" => "Không tìm thấy file log tại: $logFile"]);
    exit();
}

$content = file($logFile);
$total = count($content);
$slice = array_slice($content, max(0, $total - $lines));

$formattedLogs = [];
foreach ($slice as $line) {
    $line = trim($line);
    if (empty($line)) continue;

    if ($type === 'access') {
        if (preg_match('/\[(.*?)\]\s+"([A-Z]+)\s+([^ ]+)\s+HTTP\/[0-9.]+"\s+(\d{3})/', $line, $m)) {
            $method = $m[2];
            $uri = $m[3];
            $formattedLogs[] = [
                'time' => $m[1],
                'method' => $method,
                'status' => intval($m[4]),
                'uri' => $uri,
                'curl_command' => "curl.exe -X {$method} \"http://localhost{$uri}\"",
                'raw' => $line
            ];
            continue;
        }
    }
    $formattedLogs[] = ['raw' => $line];
}

echo json_encode([
    'total_returned' => count($formattedLogs),
    'log_type' => $type,
    'logs' => $formattedLogs
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>
