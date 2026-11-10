# 水玉藝文工作室（Emerald Artistique Casa）

官方網站專案｜線上站點：<https://emerald-artistique.com>

## 專案說明

水玉藝文工作室官方網站，內容包含品牌介紹、產品展示（文教用品／樂器配件／影音產品）與聯絡表單。

網站為 Vue 3 單頁應用（SPA），聯絡表單由 PHP 後端接收並發送郵件通知。

## 目錄結構

```
.
├── dist/                        # 部署目錄（內容即網站根目錄）
│   ├── index.html               # 網站入口
│   ├── assets/                  # 前端建置產物（JS / CSS）
│   ├── images/                  # 圖片素材
│   │   ├── hero/                # 首頁大圖
│   │   ├── logo/                # Logo
│   │   └── products/            # 產品圖（media / stationery / accessories）
│   ├── api/contact.php          # 聯絡表單 API（PHP 8.1+，原生 mail()）
│   ├── doc/                     # 部署與維護文件
│   └── .htaccess                # Apache 設定（僅 Apache 主機適用）
└── Gemini_Generated_Image_*.png # 設計參考圖（Gemini 生成）
```

## 部署方式

1. 將 `dist/` 內容上傳至網站根目錄
2. 確認 `api/contact.php` 的 CORS 允許來源（`$allowed_origins`）與收件信箱（`$recipient` / `$from`）設定正確
3. 網站伺服器：nginx

## 維護注意事項

- `dist/` 為前端建置產物；原始 Vue 專案（`src/`、`package.json`、`vite.config.js`）目前不在本儲存庫中，**無法直接重新建置**。
- `.htaccess` 為 Apache 設定檔；若部署於 nginx，該檔案不會生效。
- 部署與維護細節見 `dist/doc/` 內文件（部分內容與行號已有落差，待校正）。
