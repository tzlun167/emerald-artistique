# PHP 8.1 版本詳盡部署說明

## 📋 部署前準備

### 系統需求
- PHP 8.1 或更高版本
- Apache Web server
- 郵件系統（Postfix 或 Sendmail）
- 至少 50 MB 磁碟空間

---

## 🚀 完整部署步驟

### 1. 本地建置

```bash
npm run build
```

這會自動：
- 建置 Vue.js 前端應用
- 複製 `api/contact.php` 到 `dist/api/`
- 複製 `.htaccess` 到 `dist/`
- 複製部署文檔到 `dist/doc/`

### 2. 配置 API

編輯 `dist/api/contact.php`：

#### 2.1 設定 CORS 域名（第 12-16 行）

```php
$allowed_origins = [
    'https://emerald-artistique.com',
    'https://www.emerald-artistique.com',
    'http://localhost:5173',  // 開發用
    'http://localhost'         // 開發用
];
```

替換為您的域名：
```php
$allowed_origins = [
    'https://your-domain.com',
    'https://www.your-domain.com',
    'http://localhost:5173',  // 可選：保留以便本地測試
    'http://localhost'
];
```

#### 2.2 設定郵件收件人（第 97-98 行）

```php
$recipient = 'sales@emerald-artistique.com';
$from = 'noreply@emerald-artistique.com';
```

替換為您的實際信箱：
```php
$recipient = 'your-email@gmail.com';
$from = 'noreply@your-domain.com';
```

### 3. 上傳到生產主機

#### 3.1 使用 cPanel 檔案管理器

1. 登入 cPanel
2. 打開「檔案管理器」
3. 進入 `public_html/` 或您的網站根目錄
4. 上傳以下檔案/目錄：
   - `index.html`
   - `assets/`
   - `images/`
   - `api/`
   - `.htaccess`

#### 3.2 使用 FTP 客戶端（如 FileZilla）

1. 連接到您的 FTP 伺服器
2. 進入 `/public_html/` 或 `/www/`
3. 上傳 dist/ 的內容
4. 確保檔案權限正確：
   - `index.html`: 644
   - `assets/*.js, *.css`: 644
   - `api/contact.php`: 644
   - `.htaccess`: 644
   - 圖片檔案：644
   - 目錄：755

### 4. 配置 Apache（選用）

如果您的 Apache 已預設啟用模組，`.htaccess` 會自動工作。

檢查必要模組：
- `mod_rewrite` - URL 重寫
- `mod_deflate` - Gzip 壓縮
- `mod_expires` - 瀏覽器快取

如果模組未啟用，聯繫主機商啟用。

---

## ✅ 測試與驗證

### 測試 1：網站基本功能

1. 訪問您的域名
2. 確認網站正常載入
3. 測試語言切換功能
4. 確認所有圖片正常顯示

### 測試 2：API 端點

訪問 `https://your-domain.com/api/contact.php`

**預期回應**：
```json
{
  "success": false,
  "error": "Method not allowed"
}
```

如果看到這個 JSON = API 正常運行 ✅

如果看到空白頁或錯誤：
- 檢查 PHP 版本：`php -v`
- 檢查 .htaccess 是否上傳
- 檢查檔案權限

### 測試 3：聯絡表單

1. 填寫聯絡表單
2. 點擊送出
3. 檢查：
   - ✅ 網站顯示成功訊息
   - ✅ 管理員收到郵件
   - ✅ 使用者收到確認郵件

---

## 🔍 故障排除

### 問題 1：API 返回 500 錯誤

**原因**：PHP 語法錯誤或權限問題

**解決方案**：
```bash
# 檢查 PHP 錯誤日誌
tail -f /var/log/apache2/error_log

# 或 cPanel 主機
tail -f ~/public_html/error_log
```

### 問題 2：CORS 錯誤

**症狀**：瀏覽器 Console 顯示 CORS 錯誤

**解決方案**：
1. 檢查 `$allowed_origins` 是否包含您的域名
2. 清除瀏覽器快取
3. 檢查 HTTPS/HTTP 是否一致

### 問題 3：郵件無法發送

**症狀**：表單送出但沒收到郵件

**解決方案**：
```bash
# 1. 檢查 mail() 函數是否可用
php -r "echo function_exists('mail') ? 'Yes' : 'No';"

# 2. 檢查郵件日誌
tail -f /var/log/mail.log

# 3. 檢查服務狀態
sudo systemctl status postfix
sudo systemctl status sendmail

# 4. 測試郵件
echo "Test body" | mail -s "Test subject" your-email@gmail.com
```

### 問題 4：圖片無法載入

**症狀**：網站顯示但圖片缺失

**解決方案**：
1. 確認 `images/` 目錄已上傳
2. 檢查圖片檔案權限（644）
3. 檢查路徑是否正確（相對路徑）

### 問題 5：所有請求返回 404

**症狀**：訪問任何路徑都返回 404

**解決方案**：
1. 確認 `index.html` 已上傳
2. 檢查 `.htaccess` 存在且內容正確
3. 檢查 `mod_rewrite` 模組是否啟用
4. 檢查 Apache 設定允許覆寫（AllowOverride All）

---

## 📊 性能優化

### 1. 啟用 Gzip 壓縮

已在 `.htaccess` 中配置，確保 `mod_deflate` 模組啟用。

### 2. 瀏覽器快取

已在 `.htaccess` 中配置圖片長期快取。

### 3. CDN（選用）

對於大型流量網站，可考慮：
- Cloudflare
- AWS CloudFront
- 其他 CDN 服務

### 4. 圖片優化

使用工具壓縮圖片：
```bash
# 安裝 ImageMagick
sudo apt-get install imagemagick

# 批量優化 JPEG
mogrify -quality 85 -strip images/products/**/*.jpg
```

---

## 🔒 安全建議

### 1. 限制文件訪問

`.htaccess` 已配置禁止訪問敏感文件：
- `.htaccess`
- `.env`
- `package.json`
- `composer.json`

### 2. HTTPS 設定

強制使用 HTTPS，在 `.htaccess` 添加：
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### 3. 速率限制

API 已內建簡單的速率限制（每 IP 每 60 秒一次）。

### 4. 定期更新

保持 PHP 和依賴套件更新。

---

## 📝 維護

### 定期備份

建議使用主機商的自動備份功能，或：
```bash
# 手動備份腳本
#!/bin/bash
DATE=$(date +%Y%m%d)
tar -czf backup-$DATE.tar.gz public_html/
scp backup-$DATE.tar.gz user@backup-server:/backups/
```

### 監控日誌

定期檢查：
- Apache 錯誤日誌
- PHP 錯誤日誌
- 郵件日誌

### 更新網站內容

修改步驟：
1. 修改 `src/` 目錄中的檔案
2. 執行 `npm run build`
3. 上傳更新的 `dist/` 內容
4. 清除瀏覽器快取

---

## 📞 需要協助？

如果遇到無法解決的問題：

1. 檢查文檔：
   - `QUICK-START-PHP.md` - 快速開始
   - `DEPLOYMENT-CHECKLIST.md` - 檢查清單
   - `DEVELOPMENT-WORKFLOW.md` - 修改指南

2. 查看日誌：
   - 瀏覽器 Console
   - Apache 錯誤日誌
   - PHP 錯誤日誌

3. 主機商支援：
   - cPanel 主機：聯繫客戶服務
   - VPS/雲端：查看技術文檔

---

**部署成功！您的網站現已上線！** 🎉
