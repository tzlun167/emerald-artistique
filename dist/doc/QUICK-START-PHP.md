# 🚀 快速開始指南 - PHP 8.1 版本部署

## 3 步部署流程

### 📝 步驟 1：執行建置

```bash
npm run build
```

這會完成：
- ✅ 建置 Vue.js 前端
- ✅ 複製 `images/`

⚠️ **不會**複製 `api/contact.php`、`.htaccess`、`doc/`，這三項需手動放入 `dist/`。
⚠️ 本專案目前**無** `src/` 與 `package.json`，`npm run build` 無法執行。

---

### ⚙️ 步驟 2：修改 API 設定

編輯 `dist/api/contact.php`，修改以下兩處：

#### A. 設定允許的域名（第 17-24 行）

```php
$allowed_origins = [
    'https://emerald-artistique.com',
    'https://www.emerald-artistique.com'
];
```

將域名替換為您的實際域名，例如：
```php
$allowed_origins = [
    'https://yoursite.com',
    'https://www.yoursite.com'
];
```

#### B. 設定收件人信箱（第 119-120 行）

```php
$recipient = 'sales@emerald-artistique.com';
$from = 'www@emerald-artistique.com';
```

> 註：檔案實際值為 `www@emerald-artistique.com`（非 `noreply@`）。

替換為您的實際信箱：
```php
$recipient = 'your-email@gmail.com';
$from = 'noreply@yoursite.com';
```

---

### 📤 步驟 3：上傳到生產主機

使用 cPanel 檔案管理器或 FTP 客戶端，上傳 `dist/` 目錄的內容：

#### 必需檔案（4 項）：
```
✅ index.html
✅ assets/
✅ images/
✅ api/contact.php
```

> ⚠️ `.htaccess` 適用於 Apache；正式站台為 **nginx**，該檔不生效，**請勿上傳**。

#### 參考文件（不上傳）：
```
❌ doc/ (部署說明，只需本地參考)
```

上傳目標：
- `public_html/`（如果使用 cPanel）
- `www/`（如果使用其他主機）

---

### ✅ 步驟 4：測試並上線

#### 測試 1：API 端點

在瀏覽器訪問：
```
https://你的域名.com/api/contact.php
```

**預期結果**：
```json
{
  "success": false,
  "error": "Method not allowed"
}
```

看到這個 = API 正常運行！✅

#### 測試 2：聯絡表單

1. 訪問網站主頁
2. 填寫聯絡表單
3. 送出表單

**預期結果**：
- ✅ 在您的信箱收到 **管理員通知郵件**
- ✅ 使用者收到 **確認郵件**
- ✅ 網站顯示成功訊息

全部通過 = 部署成功！🎉

---

## 📋 部署檢查清單

部署前確認：
- [ ] （若有原始碼）已執行 `npm run build`
- [ ] 已手動放入 `api/contact.php`（build 不會複製）
- [ ] 已修改 `dist/api/contact.php` 中的域名設定（第 17-24 行）
- [ ] 已修改 `dist/api/contact.php` 中的信箱設定（第 119-120 行）
- [ ] 已上傳所有必需檔案（index.html、assets/、images/、api/）
- [ ] 確認**未**上傳 `.htaccess`

部署後測試：
- [ ] API 測試通過（訪問 /api/contact.php 看到錯誤訊息）
- [ ] 聯絡表單可正常送出
- [ ] 管理員收到通知郵件
- [ ] 使用者收到確認郵件

---

## ⚠️ 常見問題

### Q: API 返回空白頁面？
**A**: 檢查 PHP 版本是否為 8.1+，並確認 .htaccess 已上傳

### Q: 表單無法送出？
**A**: 檢查 `$allowed_origins` 是否包含您的域名

### Q: 沒有收到郵件？
**A**: 
1. 檢查 PHP `mail()` 函數是否可用
2. 檢查信箱是否被標記為垃圾郵件
3. 確認 Postfix 或 Sendmail 已啟動

### Q: CORS 錯誤？
**A**: 檢查 `$allowed_origins` 是否包含所有需要的域名

---

## 🔍 故障排除

### 檢查 PHP 錯誤日誌
```bash
# cPanel 主機通常在：
/home/你的帳號/public_html/error_log
```

### 檢查 Apache 錯誤日誌
```bash
# VPS/雲端主機通常在：
/var/log/apache2/error.log
```

### 測試 PHP mail() 函數
建立 `test-mail.php`：
```php
<?php
if (mail('test@example.com', 'Test', 'Test message')) {
    echo 'Email sent successfully!';
} else {
    echo 'Failed to send email';
}
?>
```
訪問 `https://你的域名.com/test-mail.php` 測試

---

## 📞 需要協助？

如果遇到無法解決的問題：

1. 查看其他部署文檔：
   - `DEPLOYMENT-CHECKLIST.md` - 詳細步驟
   - `DEPLOY-PHP.md` - 完整說明
   - `DEVELOPMENT-WORKFLOW.md` - 後續修改指南

2. 檢查瀏覽器 Console 的錯誤訊息

3. 檢查伺服器錯誤日誌

---

## 🎉 恭喜！

如果所有測試都通過，您的網站已成功部署到 PHP 8.1 生產主機！

**後續修改網站**：參閱 `DEVELOPMENT-WORKFLOW.md`

---

**部署文件位置**: `doc/QUICK-START-PHP.md`
