# deploy/ — 待套用的伺服器設定

這個目錄放的是**為 emerald-artistique.com 準備的伺服器設定**，用途是修掉
`檢查報告-2026-11-10.md` 裡的問題。

> ⚠️ **這些檔案不會自動生效。** 它們是待套用的範本，需要有人把它們放到伺服器上。

---

## 為什麼放在這裡而不是直接改線上？

2026-11-10 檢查時確認，生產主機 `202.39.243.216`（emerald-artistique.com）
**沒有任何可用的部署通道**：

| 通道 | 狀態 |
|---|---|
| SSH (22) | 關閉 |
| FTP (21) | 關閉 |
| cPanel (2082/2083) | 關閉 |
| 2083 / 2222 | 關閉 |
| HTTP/HTTPS (80/443) | 開放（僅網頁） |

本機也沒有任何該主機的憑證（無 `.netrc`、無對應 SSH 金鑰）。
因此無法由這個環境直接套用設定，只能準備好檔案，交由有主機存取權的人執行。

---

## 檔案清單

| 檔案 | 對應問題 | 說明 |
|---|---|---|
| `nginx/emerald-artistique.conf` | P0-2、P0-3、P1-1、P2-2、P2-3 | nginx 站台設定範本 |
| `robots.txt` | P2-2 | 上傳至網站根目錄 |
| `sitemap.xml` | P2-2 | 上傳至網站根目錄 |
| `scripts/clean-orphan-assets.sh` | P1-1 | 清理 12 個孤兒 asset |

---

## 套用步驟

**套用前務必先備份現有設定。**

### 1. 備份現況

```bash
sudo cp /etc/nginx/sites-available/emerald-artistique \
        /etc/nginx/sites-available/emerald-artistique.bak-$(date +%Y%m%d)
```

### 2. 合併 nginx 設定

**不要整檔覆蓋。** `nginx/emerald-artistique.conf` 是完整範本，
現有設定可能已有其他規則（SSL、log、其他 location）。

請逐段比對後合併，重點在這四項：

1. `location ~ /\.` 的 deny 區塊 → 封住 `.htaccess` 外洩
2. `location /` 的 `try_files $uri $uri/ /index.html` → 修 SPA 404
3. `expires` / `Cache-Control` → 補資產快取
4. `location ~ ^/(doc|deploy)/` 的 deny → 封住部署文件

依實際 PHP-FPM socket 版本調整 `fastcgi_pass`（此機為 PHP 8.1）。

### 3. 驗證與重載

```bash
sudo nginx -t          # 必須通過才繼續
sudo systemctl reload nginx
```

### 4. 從線上移除 `.htaccess`

`.htaccess` 是 Apache 設定，在 nginx 上無作用，而且目前可被公開下載。
請將它從網站根目錄刪除（倉庫中保留一份即可）。

### 5. 上傳 robots.txt / sitemap.xml / favicon.ico

放到網站根目錄。

### 6. 清理孤兒 asset

```bash
# 先 dry-run 確認清單
./scripts/clean-orphan-assets.sh /var/www/emerald-artistique

# 確認無誤後實際刪除
./scripts/clean-orphan-assets.sh /var/www/emerald-artistique --delete
```

腳本會從 `index.html` 讀出真正被引用的檔案，只刪除其餘的。

### 7. 驗收

```bash
B=https://emerald-artistique.com

# 應該 403 或 404（不能再是 200）
curl -s -o /dev/null -w "%{http_code}\n" "$B/.htaccess"

# 應該 200（原本 404）
for p in /contact /about /shop; do
  curl -s -o /dev/null -w "$p %{http_code}\n" "$B$p"
done

# 應該 200
for p in /robots.txt /sitemap.xml /favicon.ico; do
  curl -s -o /dev/null -w "$p %{http_code}\n" "$B$p"
done

# 應該有 Cache-Control
curl -sI "$B/assets/index-DG6OSFf7.js" | grep -i cache-control
```

---

## 尚未處理的項目

| 項目 | 原因 |
|---|---|
| 原始碼遺失（P0-1） | 需要有人提供 Vue 原始專案，無法由本環境產生 |
| SMTP 郵件方案（P1-4） | 需要 PHPMailer 與 SMTP 憑證，且需決定是否採用 |
| 前端防護重評（P2-3 附帶） | 需改 `index.html`，但改產物無法重新建置，建議等原始碼取回 |
