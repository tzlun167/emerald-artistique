# Gmail SMTP 設置指南 - 解決郵件無法發送問題

## 📌 問題診斷

您提供的郵件日誌顯示：
- ✅ Postfix 和 Dovecot 正常運行
- ✅ 郵件伺服器在檢查信箱
- ❌ **沒有任何 PHP mail() 送出的郵件記錄**

**結論**：PHP mail() 函數沒有實際調用 Postfix。

---

## ✅ 推薦解決方案：使用 SMTP

使用 SMTP 發送郵件比 PHP mail() 更可靠，送達率更高。

---

## 🚀 安裝步驟

### 步驟 1：安裝 PHPMailer

在您的伺服器上執行：

```bash
# 進入網站目錄
cd /var/www/emerald-casa

# 安裝 PHPMailer
composer require phpmailer/phpmailer

# 或手動下載
wget https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.8.1.tar.gz
tar -xzf v6.8.1.tar.gz
cp -r PHPMailer-6.8.1/src vendor/
```

### 步驟 2：獲取 Gmail 應用程式密碼

因為 Google 不再允許使用密碼登入，需要生成應用程式密碼：

1. **登入 Gmail**
2. **進入帳號設定**
   - 點擊右上角頭像
   - 管理 Google 帳號
   
3. **啟用兩步驟驗證**
   - 左側選單：安全性
   - 兩步驟驗證：啟用（如果尚未啟用）
   
4. **生成應用程式密碼**
   - 滾動到「兩步驟驗證」下方
   - 點擊「應用程式密碼」
   - 選擇：郵件 → 其他（自訂名稱）
   - 輸入名稱：Emerald Artistique
   - 點擊「生成」
   - **複製 16 位密碼**（格式：xxxx xxxx xxxx xxxx）

### 步驟 3：配置 SMTP API

編輯 `api/contact-smtp.php`，修改第 118-121 行：

```php
// SMTP 配置
$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;
$smtpUsername = 'your-gmail@gmail.com';      // 改成你的 Gmail
$smtpPassword = 'your-app-password';         // 改成剛生成的 16 位密碼
$fromEmail = 'your-gmail@gmail.com';
$fromName = '水玉藝文工作室';
$adminEmail = 'sales@emerald-artistique.com'; // 收件人信箱
```

### 步驟 4：刪除安全檢查（配置完成後）

在 `api/contact-smtp.php` 中找到並刪除或註釋這幾行：

```php
// ⚠️ 重要：修改以上設定後，刪除此行註釋
// echo json_encode(['success' => false, 'error' => '請先修改 contact-smtp.php 中的 SMTP 配置（第 118-121 行）']);
// exit;
```

或執行：
```bash
sed -i '128,131s/^/\/\/ /' api/contact-smtp.php
```

### 步驟 5：上傳並測試

1. **上傳檔案**：
```bash
# 上傳到生產主機
scp api/contact-smtp.php user@你的主機:/path/to/website/api/
```

2. **測試 API**：
```bash
curl -X POST https://www.emerald-artistique.com/api/contact-smtp.php \
  -H "Content-Type: application/json" \
  -d '{"name":"SMTP 測試","email":"test@test.com","message":"這是 SMTP 測試訊息"}'
```

3. **檢查收件箱**：
- 查看管理員信箱：sales@emerald-artistique.com
- 查看使用者信箱：test@test.com

---

## 🔄 替換現有 API

### 方案 A：直接替換（推薦）

```bash
# 備份原 API
cp api/contact.php api/contact-backup.php

# 使用 SMTP 版本
cp api/contact-smtp.php api/contact.php

# 測試
curl -X POST https://www.emerald-artistique.com/api/contact.php \
  -H "Content-Type: application/json" \
  -d '{"name":"測試","email":"test@test.com","message":"測試訊息"}'
```

### 方案 B：保持兩個版本同時運行

修改 `.htaccess`：
```apache
# 原有配置
RewriteRule ^api/contact$ /api/contact.php [L]

# 新增路徑
RewriteRule ^api/contact-smtp$ /api/contact-smtp.php [L]
```

然後修改前端，切換使用 `/api/contact-smtp`

---

## 🛠️ 故障排除

### 問題 1：SMTP 連接失敗

**錯誤**：`SMTP connect() failed`

**解決方案**：
1. 檢查 Gmail 應用程式密碼是否正確
2. 確認兩步驟驗證已啟用
3. 檢查伺服器防火牆：
```bash
# 允許 587 端口
sudo ufw allow 587/tcp
```

### 問題 2：認證失敗

**錯誤**：`SMTP Error: Could not authenticate`

**解決方案**：
1. 重新生成應用程式密碼
2. 檢查 SMTP 用戶名稱（完整 Gmail 地址）
3. 確認沒有空格或多餘字符

### 問題 3：郵件被標記為垃圾郵件

**解決方案**：
1. 將發件信箱加入白名單
2. 降低發送頻率（已有 60 秒限制）
3. 考慮使用企業信箱域名

### 問題 4：PHPMailer 未安裝

**錯誤**：`PHPMailer 未安裝`

**解決方案**：
```bash
# 方法 1：使用 Composer
composer require phpmailer/phpmailer

# 方法 2：手動下載
cd /path/to/website/api
mkdir -p vendor/PHPMailer/src
wget https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.8.1.tar.gz
tar -xzf v6.8.1.tar.gz
cp -r PHPMailer-6.8.1/src/* vendor/PHPMailer/
```

---

## 📊 送達率比較

| 方式 | 送達率 | 可靠性 | 设置難度 |
|------|--------|--------|----------|
| PHP mail() | 低 ⭐⭐ | 低 | 簡單 |
| Gmail SMTP | 高 ⭐⭐⭐⭐⭐ | 高 | 中等 |
| 專業 SMTP 服務 | 最高 ⭐⭐⭐⭐⭐ | 最高 | 中等 |

---

## 💡 其他 SMTP 服務推薦

如果不使用 Gmail，可以考慮：

### 1. SendGrid
- 免費層：100 封/天
- 註冊：https://sendgrid.com/
- 配置：只需要 SMTP 憑證

### 2. Mailgun
- 免費層：5,000 封/月
- 註冊：https://www.mailgun.com/
- 配置：自動驗證域名

### 3. Amazon SES
- 免費部份：62,000 封/月
- 需要驗證發件域名
- 最高信譽度

---

## 📝 完整安裝腳本

```bash
#!/bin/bash

echo "開始安裝 SMTP 支援..."

# 1. 檢查 Composer
if ! command -v composer &> /dev/null; then
    echo "請先安裝 Composer"
    exit 1
fi

# 2. 進入目錄
cd /var/www/emerald-casa

# 3. 安裝 PHPMailer
composer require phpmailer/phpmailer

# 4. 設置權限
chmod 644 api/contact-smtp.php
chown www-data:www-data api/vendor -R

echo "✅ PHPMailer 安裝完成！"
echo ""
echo "接下來："
echo "1. 獲取 Gmail 應用程式密碼"
echo "2. 編輯 api/contact-smtp.php 設定 SMTP"
echo "3. 測試 API"
```

保存為 `install-smtp.sh`，然後執行：
```bash
chmod +x install-smtp.sh
./install-smtp.sh
```

---

## ✅ 完成檢查清單

- [ ] PHPMailer 已安裝
- [ ] Gmail 兩步驟驗證已啟用
- [ ] 應用程式密碼已生成並複製
- [ ] contact-smtp.php 已配置 SMTP 資訊
- [ ] 已刪除安全檢查代碼
- [ ] 檔案已上傳到生產主機
- [ ] API 測試成功
- [ ] 實際收到郵件

---

## 🎉 完成！

設置完成後，聯絡表單會通過 Gmail SMTP 可靠地發送郵件！

有其他問題請參閱 `doc/LOG-TROUBLESHOOTING-GUIDE.md`
