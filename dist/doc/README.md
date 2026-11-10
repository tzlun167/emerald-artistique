# 水玉藝文工作室 - 部署說明

## 🚀 快速開始

### 1. 執行建置
```bash
npm run build
```

這會自動：
- 建置 Vue.js 前端
- 複製 `api/contact.php`（PHP API）
- 複製 `.htaccess`（Apache 設定）
- 複製部署文件到 this

### 2. 修改 API 設定

編輯 `dist/api/contact.php`：

#### 設定域名（第 12-16 行）
```php
$allowed_origins = [
    'https://你的域名.com',
    'https://www.你的域名.com'
];
```

#### 設定信箱（第 97-98 行）
```php
$recipient = '你的實際信箱@gmail.com';
$from = 'noreply@你的域名.com';
```

### 3. 上傳到生產主機

將 `dist/` 目錄的這些檔案上傳：
- ✅ index.html
- ✅ assets/
- ✅ images/
- ✅ api/contact.php
- ✅ .htaccess

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
├── .htaccess           ← Apache 設定
└── doc/                ← 本文件（參考用）
```

---

## 📖 其他文檔

- `DEVELOPMENT-WORKFLOW.md` - 後續修改與開發流程

---

**更多詳情請參閱其他部署文件。**
