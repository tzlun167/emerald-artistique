# 建置與部署完整指南

## 📌 重要說明

**這個專案的兩個部分：**
1. **Vue.js 前端**（原始專案）→ 通過 `npm run build` 生成
2. **PHP 部署檔案**（為部署而建立）→ 需要手動加入 dist/ 目錄

---

## 🔄 正確的建置流程

### 步驟 1：執行 build

```bash
npm run build
```

這會生成：
- `dist/index.html` - 網站主頁
- `dist/assets/` - CSS 和 JS 檔案
- `dist/images/` - 圖片（從 `public/` 目錄複製）

### 步驟 2：手動加入 PHP 部署檔案

在 build 後，需要手動將以下檔案加入 `dist/` 目錄：

#### 必需的部署檔案（不在原始專案中）：

```bash
# 1. 建立 api/ 目錄並加入 PHP API
mkdir -p dist/api
# 將 contact.php 放置到 dist/api/

# 2. 加入 .htaccess 檔案
# 將 .htaccess 放置到 dist/

# 3. 建立 doc/ 目錄並加入部署說明
mkdir -p dist/doc
# 將部署說明文件放置到 dist/doc/
```

---

## 📁 完整的部署目錄結構

```
dist/
├── index.html              ← build 產生
├── assets/                 ← build 產生
│   ├── *.css
│   └── *.js
├── images/                 ← build 產生（從 public/ 複製）
│   └── products/...
│
├── api/                    ← ⚠️ 需要手動加入（部署用）
│   └── contact.php         ← PHP 聯絡表單 API
│
├── .htaccess               ← ⚠️ 需要手動加入（部署用）← Apache 設定
│
└── doc/                    ← ⚠️ 需要手動加入（參考用，上傳可選）
    ├── QUICK-START-PHP.md
    ├── DEPLOYMENT-CHECKLIST.md
    ├── DEPLOY-PHP.md
    └── DEVELOPMENT-WORKFLOW.md
```

---

## 🚀 一鍵建置部署腳本（推薦）

建立 `build-and-deploy.sh` 腳本，自動完成所有步驟：

```bash
#!/bin/bash

echo "===== 開始建置與準備部署 ====="

# 1. 執行 build
echo "步驟 1: 執行 npm run build..."
npm run build

# 2. 建立部署目錄
echo "步驟 2: 建立部署目錄..."
mkdir -p dist/api dist/doc

# 3. 複製 PHP API
echo "步驟 3: 複製 PHP API..."
cp api/contact.php dist/api/

# 4. 複製 .htaccess
echo "步驟 4: 複製 .htaccess..."
cp .htaccess dist/

# 5. 複製部署文件
echo "步驟 5: 複製部署文件..."
cp doc/*.md dist/doc/

# 6. 更新 README
cp dist/doc/README-deploy.md dist/README.md 2>/dev/null || echo "README 已存在"

echo "===== 完成！部署檔案已準備在 dist/ 目錄 ====="
ls -lh dist/
echo ""
echo "現在可以上傳 dist/ 目錄到生產主機！"
```

使用方法：
```bash
chmod +x build-and-deploy.sh
./build-and-deploy.sh
```

---

## 📋 手動操作清單（不使用腳本）

每次修改後需要部署時：

### 1. 修改原始檔案
- 修改 `src/App.vue`、`src/i18n.js` 等

### 2. 執行 build
```bash
npm run build
```

### 3. 手動複製部署檔案到 dist/
```bash
# 如果這些檔案還沒在 dist/ 中，需要建立

# 建立 api/ 目錄並複製 PHP API
mkdir -p dist/api
# 如果有原始的 api/contact.php，複製到 dist/api/

# 複製 .htaccess
# 如果有原始的 .htaccess，複製到 dist/

# 建立 doc/ 目錄並複製部署文件
mkdir -p dist/doc
# 如果有部署文件，複製到 dist/doc/
```

### 4. 上傳到生產主機
- 使用 FTP 或 cPanel 上傳 `dist/` 目錄的內容

---

## 💡 建議的工作流程

### 開發階段
1. 修改 `src/` 目錄中的檔案
2. 使用 `npm run dev` 本地測試
3. 確認無誤後執行 `npm run build`

### 部署準備
1. 執行 `npm run build`
2. **每次 build 後都需要手動加入 PHP 部署檔案**
   - `api/contact.php`
   - `.htaccess`
   - `doc/` 目錄（參考用）

3. 上傳 `dist/` 到生產主機

---

## ⚠️ 關鍵提示

### 為什麼每次都要手動加入 PHP 檔案？

因為這些檔案（`api/`、`.htaccess`、`doc/`）**不是 Vue.js 前端的一部分**：
- `api/contact.php` - PHP 後端，與 Vue.js 完全分離
- `.htaccess` - Apache 伺服器配置
- `doc/` - 部署說明文件

`npm run build` 只會處理 Vue.js 相關的檔案，不會自動包含這些額外的部署檔案。

### 如何避免每次都手動？

**方案 A**：使用自動化腳本（見上方的 `build-and-deploy.sh`）
**方案 B**：將 PHP 部署檔案保留在原始專案結構中，每次只需複製

---

## 📦 檢查清單

部署前確認：
- [ ] 已執行 `npm run build`
- [ ] `dist/` 目錄包含 index.html、assets、images
- [ ] 已加入 `api/contact.php` 到 dist/api/
- [ ] 已加入 `.htaccess` 到 dist/
- [ ] 已修改 `api/contact.php` 中的域名和信箱設定
- [ ] 已上傳到生產主機

---

## 🔧 修改後的完整流程

```bash
# 1. 修改檔案
# 編輯 src/App.vue、src/i18n.js 等

# 2. 本地測試（選用）
npm run dev

# 3. 執行 build
npm run build

# 4. 加入 PHP 部署檔案（⚠️ 重要！）
mkdir -p dist/api dist/doc
cp api/contact.php dist/api/
cp .htaccess dist/
cp doc/*.md dist/doc/

# 5. 上傳到生產主機
# 使用 FTP 或 cPanel 上傳 dist/ 內容
```

---

**總結**：
- `npm run build` 只處理 Vue.js 前端
- PHP 部署檔案需要手動加入 dist/ 目錄
- 建議使用自動化腳本簡化流程
- 或將 PHP 檔案保留在原始專案中，每次只需複製
