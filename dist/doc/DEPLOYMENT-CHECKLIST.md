# 部署步驟與檔案清單

## 📁 完整檔案結構

### 開發目錄
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
├── images/                     ← build 產生
├── api/
│   └── contact.php            ← 自動複製
├── .htaccess                   ← 自動複製
└── doc/                       ← 自動複製（參考用）
```

---

## 🚀 部署步驟

### 步驟 1：建置
```bash
npm run build
```

自動完成：
- ✅ 建置 Vue.js 前端
- ✅ 複製 api/contact.php
- ✅ 複製 .htaccess
- ✅ 複製部署文檔

### 步驟 2：修改設定
編輯 `dist/api/contact.php`：
- 第 12-16 行：修改域名
- 第 97-98 行：修改信箱

### 步驟 3：上傳
上傳 `dist/` 的 5 項必需檔案到生產主機

### 步驟 4：測試
1. 測試 API
2. 測試聯絡表單
3. 確認收到郵件

---

## ✅ 檢查清單

部署前：
- [ ] 已執行 `npm run build`
- [ ] 修改域名設定
- [ ] 修改信箱設定
- [ ] 準備上傳

部署後：
- [ ] 網站正常瀏覽
- [ ] API 測試通過
- [ ] 表單可送出
- [ ] 收到郵件

---

**完成後參閱：QUICK-START-PHP.md**
