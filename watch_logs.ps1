# Script theo dõi Logs Realtime & Tự động tạo câu lệnh CURL tương ứng
Clear-Host
Write-Host "================================================================================" -ForegroundColor Yellow
Write-Host " 🔥 BỘ THEO DÕI LOGS REALTIME & BẮT LỆNH CURL - COOK TOGETHER API MONITOR" -ForegroundColor Green
Write-Host "================================================================================" -ForegroundColor Yellow
Write-Host " Đang lắng nghe tất cả các lượt gọi API và truy cập theo thời gian thực..." -ForegroundColor Cyan
Write-Host " Nhấn [Ctrl + C] bất cứ lúc nào để dừng lại.`n" -ForegroundColor DarkGray

$logPath = "C:\xampp\apache\logs\access.log"

if (!(Test-Path $logPath)) {
    Write-Host "Không tìm thấy file log tại $logPath" -ForegroundColor Red
    exit
}

Get-Content -Path $logPath -Tail 5 -Wait | ForEach-Object {
    $line = $_
    
    # Regex parse Apache Common Log Format
    # Ví dụ: ::1 - - [01/Oct/2026:17:25:05 +0700] "POST /recipe-sharing-platform/edit.php?recipe_id=107 HTTP/1.1" 200 ...
    if ($line -match '\[(.*?)\]\s+"([A-Z]+)\s+([^ ]+)\s+HTTP\/[0-9.]+"\s+(\d{3})') {
        $timeStr = $matches[1]
        $method  = $matches[2]
        $uri     = $matches[3]
        $status  = $matches[4]

        # Bỏ qua các file tĩnh như ảnh/css/js nếu chỉ muốn tập trung API/PHP (hoặc hiển thị ngắn)
        $isApiOrPhp = ($uri -match '\.(php|json)' -or $uri -match 'api' -or $uri -match '\?')

        # Chọn màu sắc theo HTTP Status & Method
        $statusColor = "Green"
        if ($status -ge 400) { $statusColor = "Red" }
        elseif ($status -ge 300) { $statusColor = "DarkYellow" }

        $methodColor = if ($method -eq "GET") { "Cyan" } else { "Magenta" }

        # Tạo lệnh CURL tương đương
        $fullLocalUrl = "http://localhost$uri"
        $curlCommand = "curl.exe -X $method `"$fullLocalUrl`""

        Write-Host "--------------------------------------------------------------------------------" -ForegroundColor DarkGray
        Write-Host "⏰ [$timeStr] " -NoNewline -ForegroundColor Gray
        Write-Host "[$method] " -NoNewline -ForegroundColor $methodColor
        Write-Host "Status: $status " -NoNewline -ForegroundColor $statusColor
        Write-Host "-> $uri" -ForegroundColor White

        Write-Host "📌 LỆNH CURL TƯƠNG ỨNG:" -ForegroundColor Yellow
        Write-Host "   $curlCommand" -ForegroundColor Green
    }
}
