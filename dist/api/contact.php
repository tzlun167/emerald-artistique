<?php
/**
 * 聯絡表單 API - PHP 8.1+
 * 
 * 處理網站聯絡表單提交，發送郵件到管理員和使用者
 */

// 設定錯誤報告
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// 設定標頭
header('Content-Type: application/json; charset=utf-8');

// CORS 設定
$allowed_origins = [
    'https://emerald-artistique.com',
    'https://www.emerald-artistique.com',
    'http://localhost:5173',
    'http://localhost'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 處理 OPTIONS 預檢請求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 只接受 POST 請求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
    exit;
}

// 簡單的速率限制
function checkRateLimit($ip) {
    $lockFile = sys_get_temp_dir() . '/contact_rate_limit_' . md5($ip);
    if (file_exists($lockFile)) {
        $lastSubmitTime = file_get_contents($lockFile);
        $minInterval = 60;
        if (time() - $lastSubmitTime < $minInterval) {
            return false;
        }
    }
    file_put_contents($lockFile, time());
    return true;
}

$clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
if (!checkRateLimit($clientIp)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => '請求過於頻繁，請稍後再試']);
    exit;
}

// 取得並驗證數據
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => '無效的數據格式']);
    exit;
}

// 驗證必填欄位
$required = ['name', 'email', 'message'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "欄位 {$field} 不能為空"]);
        exit;
    }
}

// 驗證 email 格式
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => '無效的電子郵件格式']);
    exit;
}

// 清理輸入
$name = htmlspecialchars(strip_tags($data['name']));
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(strip_tags($data['message']));
$phone = isset($data['phone']) ? htmlspecialchars(strip_tags($data['phone'])) : '';

// 檢測語言
$isZhChinese = preg_match('/[\x{4e00}-\x{9fa5}]/u', $message);

// 組織郵件內容
$subject = $isZhChinese ? "新訊息 - {$name}" : "New Message - {$name}";

$bodyAdmin = $isZhChinese ? 
    "姓名：{$name}\n Email：{$email}\n" . ($phone ? "電話：{$phone}\n" : "") . "\n訊息：\n{$message}" :
    "Name: {$name}\nEmail: {$email}\n" . ($phone ? "Phone: {$phone}\n" : "") . "\nMessage:\n{$message}";

$bodyUser = $isZhChinese ?
    "親愛的 {$name}，\n\n感謝您的聯絡！我們已收到您的訊息，會盡快回覆。\n\n您的訊息：\n{$message}\n\n祝您有美好的一天！\n水玉藝文工作室" :
    "Dear {$name},\n\nThank you for contacting us! We have received your message and will reply as soon as possible.\n\nYour message:\n{$message}\n\nHave a wonderful day!\nEmerald Artistique";

// 郵件標頭
$headers = "From: {$email}\r\nReply-To: {$email}\r\nContent-Type: text/plain; charset=UTF-8\r\n";

// 發送郵件到管理員
$recipient = 'sales@emerald-artistique.com';
$from = 'www@emerald-artistique.com';
$headersAdmin = "From: {$from}\r\nReply-To: {$email}\r\nContent-Type: text/plain; charset=UTF-8\r\n";

$successAdmin = mail($recipient, $subject, $bodyAdmin, $headersAdmin);

// 發送確認郵件給使用者（中文版）
$successUser = mail($email, $isZhChinese ? '感謝您的聯絡' : 'Thank You for Contacting Us', $bodyUser, "From: {$from}\r\nContent-Type: text/plain; charset=UTF-8\r\n");

if ($successAdmin) {
    echo json_encode([
        'success' => true,
        'message' => $isZhChinese ? '訊息已成功送出！我們會盡快回覆。' : 'Message sent successfully! We will reply soon.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $isZhChinese ? '送出失敗，請稍後再試' : 'Failed to send message, please try again later'
    ]);
}
