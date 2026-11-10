# 部署步驟與檔案清單

## 📁 完整檔案結構

### 開發目錄

> ⚠️ **現況：本專案沒有開發目錄。**
> 原始 Vue 專案（`src/`、`public/`、`package.json`、`vite.config.js`）目前不存在，
> 倉庫中只有建置產物 `dist/`。下表為原始專案應有的結構，供日後取回原始碼時對照。

```
emerald-casa/
├── src/                       # Vue.js 源碼
│   ├── App.vue
│   ├── i18n.js
│   └── style.css
├── public/
│   └── images/                # 圖片源
│       ├── products/media/
│       ├── products/stationery/
│       └── products/accessories/
├── api/
│   └── contact.php            # PHP API（新增）
├── .htaccess                  # Apache 設定（新增）
├── doc/                       # 部署文檔（新增）
│   ├── README.md
│   ├── QUICK-START-PHP.md
│   ├── DEPLOYMENT-CHECKLIST.md
│   └── DEVELOPMENT-WORKFLOW.md
├── package.json
└── vite.config.js
```

### 部署目錄（dist/）
```
dist/
├── index.html                  ← build 產生
├── assets/                     ← build 產生
├── images/                     ← build 產生（從 public/ 複製）
├── api/
│   └── contact.php            ← 手動放入
├── .htaccess                   ← 手動放入（nginx 主機不適用，勿上傳）
└── doc/                       ← 手動放入（參考用）
```

---

## 🚀 部署步驟

### 步驟 1：建置
```bash
npm run build
```

只會完成：
- ✅ 建置 Vue.js 前端
- ✅ 複製 `images/`

⚠️ **不會**複製 `api/`、`.htaccess`、`doc/`，這三項需手動放入。
⚠️ 本專案目前無 `src/` 與 `package.json`，`npm run build` 無法執行。

### 步驟 2：修改設定
編輯 `dist/api/contact.php`：
- 第 17-24 行：修改域名
- 第 119-120 行：修改信箱（`$from` 現值為 `www@emerald-artistique.com`）

### 步驟 3：上傳
上傳 `dist/` 的 5 項必需檔案到生產主機

### 步驟 4：測試
1. 測試 API
2. 測試聯絡表單
3. 確認收到郵件

---

## ✅ 檢查清單

部署前：
- [ ] （若有原始碼）已執行 `npm run build`
- [ ] 已手動放入 `api/contact.php`（`npm run build` 不會複製）
- [ ] 修改域名設定（第 17-24 行）
- [ ] 修改信箱設定（第 119-120 行）
- [ ] 確認**未**上傳 `.htaccess`（nginx 主機不適用）
- [ ] 準備上傳

部署後：
- [ ] 網站正常瀏覽
- [ ] API 測試通過
- [ ] 表單可送出
- [ ] 收到郵件

---

**完成後參閱：QUICK-START-PHP.md**
