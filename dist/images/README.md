# 圖片目錄說明

將您的本地圖片放入此目錄後，可在網站中使用。

## 目錄結構

```
public/images/
├── hero/              # 首頁大圖區圖片
│   └── hero-bg.jpg    # Hero 背景圖
├── products/          # 產品圖片
│   ├── stationery/    # 文教用品
│   ├── accessories/   # 樂器配件
│   └── media/         # 影音產品
└── logo/              # Logo 圖片
```

## 建議的圖片尺寸

- **Hero 背景圖**：1920x1080px 或更大
- **產品圖片**：500x350px (橫向 3:2 比例)
- **Logo**：適合網頁展示的尺寸

## 如何使用本地圖片

### 選項 1：替換線上圖片連結

更新 `src/i18n.js` 中的圖片路徑：

```javascript
// 從線上連結
{ img: 'https://images.unsplash.com/...', ... }

// 改為本地路徑
{ img: '/images/products/media/001.jpg', ... }
```

### 選項 2：使用 Vite 的 assets 目錄

1. 將圖片放入 `src/assets/images/` 目錄
2. 在組件中 import 並使用：

```javascript
import heroBg from '@/assets/images/hero-bg.jpg'
```

## 目前使用的線上圖片來源

- **Unsplash** - 高品質免費圖片庫
- 所有圖片均為免費商業使用授權
- 您可以在 Unsplash.com 搜尋相同類型的圖片

## 將來替換為本地圖片的步驟

1. 準備您的圖片檔案
2. 放入對應的子目錄
3. 更新 `src/i18n.js` 中的圖片路徑
4. 如果需要，更新 `src/App.vue` 中的靜態圖片路徑
