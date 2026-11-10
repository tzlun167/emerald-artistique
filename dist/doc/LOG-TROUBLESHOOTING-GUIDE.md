# 日誌查看與故障排除指南

## 📝 表單發送卡住的排查步驟

---

## 1️⃣ 查看前端瀏覽器 Console

### 步驟：
1. 在網站頁面按 `F12` 或右鍵 → 檢查
2. 切換到 `Console` 標籤
3. 送出表單
4. 查看是否有錯誤訊息

### 常見錯誤：
- **CORS 錯誤**：`Access to fetch at ... has been blocked by CORS policy`
- **502/504 錯誤**：服務器超時
- **網絡錯誤**：`Network Error`

---

## 2️⃣ 查看使用 cPanel 的主機

### 查看 PHP 錯誤日誌

```bash
# 通過 SSH 連接後：
tail -f ~/public_html/error_log

# 或瀏覽器訪問 cPanel：
# cPanel → Metrics → Errors
```

### 查看 Apache 錯誤日誌

```bash
# cPanel 日誌通常在：
tail -f /usr/local/apache/logs/error_log
```

### cPanel 界面查看：
1. 登入 cPanel
2. 找到 **Metrics** 或 **日誌**
3. 點擊 **Errors** 查看錯誤日誌
4. 點擊 **Raw Access Logs** 查看原始訪問日誌

---

## 3️⃣ 查看 VPS/雲端主機

### 查看 Apache 錯誤日誌

```bash
# Ubuntu/Debian
tail -f /var/log/apache2/error.log

# CentOS/RHEL
tail -f /var/log/httpd/error_log

# 查看最後 100 行
tail -n 100 /var/log/apache2/error.log

# 即時監控
tail -f /var/log/apache2/error.log
```

### 查看 Nginx 錯誤日誌（如果使用 Nginx）

```bash
tail -f /var/log/nginx/error.log
```

### 查看郵件日誌

```bash
# Postfix
tail -f /var/log/mail.log

# Sendmail
tail -f /var/log/maillog
```

---

## 4️⃣ 表單卡住的常見原因

### 原因 1：CORS 錯誤

**症狀**：瀏覽器 Console 顯示 CORS 錯誤

**解決方案**：
編輯 `api/contact.php`，檢查第 17-24 行：
```php
$allowed_origins = [
    'https://你的域名.com',
    'https://www.你的域名.com',
    'http://localhost:5173',  // 如果本地開發用
    'http://localhost'
];
```

確認你的域名在列表中！

---

### 原因 2：PHP mail() 函數不可用

**症狀**：送出後很久無回應，可能是郵件發送超時

**測試郵件功能**：
建立 `test-mail.php`：
```php
<?php
if (mail('your-email@gmail.com', 'Test', 'Test message')) {
    echo 'Email sent successfully!';
} else {
    echo 'Failed to send email';
}
?>
```

上傳到網站根目錄，訪問 `https://你的域名.com/test-mail.php`

**解決方案**：
- 聯繫主機商確認 `mail()` 函數是否開啟
- 或使用 PHPMailer（需要修改代碼）

---

### 原因 3：郵件服務未運行

**症狀**：PHP mail() 返回失敗

**檢查郵件服務狀態**：
```bash
# 檢查 Postfix
sudo systemctl status postfix

# 檢查 Sendmail
sudo systemctl status sendmail

# 如果未運行，啟動它
sudo systemctl start postfix
sudo systemctl enable postfix
```

---

### 原因 4：防火牆阻擋

**症狀**：無法連接到 API

**檢查防火牆**：
```bash
# UFW (Ubuntu)
sudo ufw status

# firewalld (CentOS)
sudo firewall-cmd --list-all

# iptables
sudo iptables -L -n
```

---

### 原因 5：PHP 執行超時

**症狀**：60 秒後超時

**解決方案**：
在 `api/contact.php` 下方增加：
```php
// 設定最大執行時間為 30 秒
set_time_limit(30);
```

並在 `.htaccess` 增加：
```apache
<IfModule mod_php.c>
    php_value max_execution_time 30
</IfModule>
```

---

## 5️⃣ 緊急修復：簡化 API 以快速診斷

暫時將 `api/contact.php` 改為簡化版本：

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// 只接受 POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// 取得數據
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// 驗證
if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
    echo json_encode(['success' => false, 'error' => '缺少必填欄位']);
    exit;
}

// 記錄到文件（用於診斷）
$logEntry = date('[Y-m-d H:i:s]') . " | Name: {$data['name']} | Email: {$data['email']}\n";
file_put_contents('form-submissions.log', $logEntry, FILE_APPEND);

// 測試郵件
$mailResult = mail('your-email@gmail.com', 'Test Subject', 'Test Message');

if ($mailResult) {
    echo json_encode(['success' => true, 'message' => '送出成功']);
} else {
    echo json_encode(['success' => false, 'error' => '郵件發送失敗']);
}
?>
```

這會：
1. 顯示所有錯誤
2. 記錄提交到 `form-submissions.log`
3. 測試郵件是否可發送

---

## 6️⃣ 查看 API 響應頭

```bash
# 使用 curl 測試
curl -v -X POST https://your-domain.com/api/contact.php \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","message":"Test message"}'

# 查看響應狀態碼
curl -I https://your-domain.com/api/contact.php
```

---

## 7️⃣ 檢查 PHP 配置

建立 `phpinfo.php`：
```php
<?php
phpinfo();
?>
```

上傳並訪問 `https://your域名.com/phpinfo.php`

檢查：
- **mail() 函數是否啟用**
- **post_max_size** (應夠大)
- **max_execution_time** (足夠)
- **max_input_time** (足夠)

**⚠️ 測試完成後立即刪除 phpinfo.php！**

---

## 8️⃣ cPanel 主機特定問題

### 問題：suexec 錯誤

**症狀**：500 錯誤，日誌顯示 suexec 錯誤

**解決方案**：
```bash
# 修正檔案權限
chmod 644 index.html
chmod 644 api/contact.php
chmod 755 .
chmod 644 assets/*
```

### 問題：mod_security 阻擋

**症狀**：403 Forbidden

**解決方案**：
在 `.htaccess` 加入：
```apache
<IfModule mod_security.c>
    SecRuleEngine Off
</IfModule>
```

---

## 📞 快速診斷流程圖

```
表單卡住
    ↓
1. 檢查瀏覽器 Console
    ├─ CORS 錯誤 → 修改 $allowed_origins
    ├─ 網絡錯誤 → 檢查網站是否可訪問
    └─ 無錯誤 → 繼續
        ↓
2. 查看伺服器錯誤日誌
    ├─ PHP 錯誤 → 修正 PHP 語法
    ├─ 502/504 檢查服務器負載
    └─ 無錯誤 → 繼續
        ↓
3. 直接測試 API
    ├─ curl 測試成功 → 問題在前端
    └─ curl 測試失敗 → 問題在後端
        ↓
4. 測試郵件功能
    ├─ mail() 失敗 → 聯繫主機商
    └─ mail() 成功 → 檢查其他設定
```

---

## 🔧 推薦故障排除工具

### 瀏覽器
- Chrome DevTools (F12)
- Firefox Developer Tools
- Postman (測試 API)

### 命令行
```bash
# 監控多個日誌
tail -f /var/log/apache2/error.log & tail -f /var/log/mail.log

# 搜索錯誤
grep "error" /var/log/apache2/error.log

# 實時查看網絡流
sudo tcpdump -i any port 80 -n
```

---

## 📝 報告問題時請提供：

1. ✅ 瀏覽器 Console 錯誤截圖或文字
2. ✅ 伺服器錯誤日誌相關部分
3. ✅ 測試 `curl` 命令的輸出
4. ✅ 主機類型 (cPanel/VPS)
5. ✅ PHP 版本
6. ✅ 使用的域名

---

**更多幫助**：
- `doc/DEPLOY-PHP.md` - 完整故障排除
- 聯繫主機商客戶服務
