# 水玉藝文工作室 - 部署說明

## 🚀 快速開始

### 1. 執行建置
```bash
npm run build
```

這會：
- 建置 Vue.js 前端（`index.html`、`assets/`）
- 從 `public/` 複製 `images/`

> ⚠️ **不會**自動複製 `api/`、`.htaccess`、`doc/`。
> 這三項需手動放入 `dist/`，詳見 `DEVELOPMENT-WORKFLOW.md`。
> 另：本專案目前**沒有** `src/` 與 `package.json`，`npm run build` 無法執行。

### 2. 修改 API 設定

編輯 `dist/api/contact.php`：

#### 設定域名（第 17-24 行）
```php
$allowed_origins = [
    'https://你的域名.com',
    'https://www.你的域名.com'
];
```

#### 設定信箱（第 119-120 行）
```php
$recipient = '你的實際信箱@gmail.com';
$from = 'www@你的域名.com';
```

> 註：現行檔案中 `$from` 的值為 `www@emerald-artistique.com`（非 `noreply@`）。

### 3. 上傳到生產主機

將 `dist/` 目錄的這些檔案上傳：
- ✅ index.html
- ✅ assets/
- ✅ images/
- ✅ api/contact.php

> ⚠️ `.htaccess` 為 Apache 專用。正式站台使用 **nginx**，該檔不會生效，**不要上傳**（上傳後可被公開下載）。

### 4. 測試

1. 測試 API：`https://你的域名.com/api/contact.php`
   - 應返回：`{"success": false, "error": "Method not allowed"}`
   
2. 測試表單：填寫並送出聯絡表單
   - 收到管理員通知郵件
   - 收到使用者確認郵件

---

## 📁 檔案結構

```
dist/
├── index.html          ← 網站主頁
├── assets/             ← CSS 和 JS
├── images/             ← 圖片
├── api/
│   └── contact.php     ← PHP 聯絡表單 API
├── .htaccess           ← Apache 設定（nginx 主機不適用，勿上傳）
└── doc/                ← 本文件（參考用）
```

---

## 📖 其他文檔

- `DEVELOPMENT-WORKFLOW.md` - 後續修改與開發流程

---

**更多詳情請參閱其他部署文件。**
