#!/usr/bin/env bash
#
# 清理 dist/assets/ 中的孤兒檔案（未被 index.html 引用的舊版 JS/CSS）
#
# 產生日期：2026-11-10
# 對應問題：檢查報告-2026-11-10.md 的 P1-1
#           （assets/ 有 14 個檔案，index.html 只引用 2 個，其餘 12 個仍可線上存取）
#
# 用法：
#   ./clean-orphan-assets.sh <dist 目錄>          # 只列出，不刪（dry-run，預設）
#   ./clean-orphan-assets.sh <dist 目錄> --delete # 實際刪除
#
# 安全性：預設 dry-run；刪除前會逐檔印出。使用 trash 而非 rm（可復原）。

set -euo pipefail

DIST="${1:-}"
MODE="${2:-}"

if [[ -z "$DIST" ]]; then
  echo "用法：$0 <dist 目錄> [--delete]" >&2
  exit 1
fi

if [[ ! -f "$DIST/index.html" ]]; then
  echo "錯誤：$DIST 下找不到 index.html，請確認路徑。" >&2
  exit 1
fi

ASSETS_DIR="$DIST/assets"
if [[ ! -d "$ASSETS_DIR" ]]; then
  echo "錯誤：$ASSETS_DIR 不存在。" >&2
  exit 1
fi

# 1) 從 index.html 取出所有被引用的 assets 檔名
mapfile -t REFERENCED < <(
  grep -oE 'assets/[A-Za-z0-9._-]+\.(js|css)' "$DIST/index.html" \
    | sed 's#^assets/##' | sort -u
)

if [[ ${#REFERENCED[@]} -eq 0 ]]; then
  echo "錯誤：在 index.html 中找不到任何 assets 引用，為避免誤刪已中止。" >&2
  exit 1
fi

echo "index.html 引用 ${#REFERENCED[@]} 個檔案："
printf '  ✅ %s\n' "${REFERENCED[@]}"
echo

# 2) 找出未被引用的檔案
ORPHANS=()
while IFS= read -r f; do
  name="$(basename "$f")"
  hit=0
  for r in "${REFERENCED[@]}"; do
    [[ "$name" == "$r" ]] && { hit=1; break; }
  done
  [[ $hit -eq 0 ]] && ORPHANS+=("$f")
done < <(find "$ASSETS_DIR" -maxdepth 1 -type f \( -name '*.js' -o -name '*.css' \) | sort)

if [[ ${#ORPHANS[@]} -eq 0 ]]; then
  echo "✅ 沒有孤兒檔案，無需清理。"
  exit 0
fi

echo "發現 ${#ORPHANS[@]} 個孤兒檔案："
printf '  🗑  %s\n' "${ORPHANS[@]}"
echo

if [[ "$MODE" != "--delete" ]]; then
  echo "（dry-run，未刪除任何檔案。加上 --delete 才會實際刪除）"
  exit 0
fi

# 3) 刪除（優先使用 trash，可復原）
if command -v trash >/dev/null 2>&1; then
  DEL="trash"
else
  DEL="rm"
  echo "⚠️  找不到 trash，將使用 rm（不可復原）。5 秒後繼續，Ctrl-C 可中止。"
  sleep 5
fi

for f in "${ORPHANS[@]}"; do
  echo "刪除：$f"
  $DEL "$f"
done

echo
echo "✅ 完成，已移除 ${#ORPHANS[@]} 個檔案。"
