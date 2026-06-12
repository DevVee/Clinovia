#!/bin/sh
set -e

# =============================================================================
# Clinovia — Docker container startup script
# Runs as the container's CMD (via supervisord) on every boot.
#
# Key responsibilities:
#   1. Validate and normalize APP_KEY (must be stable — never regenerate at boot)
#   2. Resolve APP_URL from explicit env var or Render's RENDER_EXTERNAL_URL
#   3. Force SESSION_SECURE_COOKIE=true when running on Render (HTTPS)
#   4. Substitute PORT into nginx config template
#   5. Cache Laravel config, routes, and views (bakes env vars into PHP cache)
#   6. Fix filesystem permissions
#   7. Start supervisord (manages nginx + php-fpm)
# =============================================================================

# ─── 0. Validate and normalize APP_KEY ────────────────────────────────────────
# APP_KEY MUST be set as a STABLE Render environment variable — a static value,
# NOT generateValue: true. Render's generateValue produces a new raw string on
# every deploy, and this script would wrap it in base64: each boot, producing
# a DIFFERENT key each time → invalidates all sessions + CSRF tokens → 419 errors.
#
# Correct setup in render.yaml:
#   - key: APP_KEY
#     value: "base64:YOUR_KEY_FROM_php_artisan_key_generate_show=="
#
# If APP_KEY is missing entirely, abort — do not generate a throwaway key.
if [ -z "$APP_KEY" ]; then
    echo "FATAL: APP_KEY is not set." >&2
    echo "       Add a static 'base64:...' key as a Render env var." >&2
    echo "       Generate one with: php artisan key:generate --show" >&2
    exit 1
fi

# If APP_KEY is a raw string (no base64: prefix), add the prefix.
# This handles the edge case where an operator pastes the raw bytes.
# The SAME raw bytes → SAME base64 → SAME effective key on every boot.
if ! echo "$APP_KEY" | grep -q "^base64:"; then
    APP_KEY="base64:${APP_KEY}"
    export APP_KEY
    echo "==> Normalized APP_KEY: added base64: prefix (key content unchanged)"
fi

# ─── 1. Resolve APP_URL ────────────────────────────────────────────────────────
# Priority: explicit APP_URL (set in render.yaml) > Render's auto URL > localhost.
# An explicit APP_URL is strongly preferred — RENDER_EXTERNAL_URL is only
# injected for web services, not cron jobs or workers, and may be absent
# during the first boot if the service URL hasn't been assigned yet.
APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost:8080}}"
export APP_URL

# ASSET_URL must match APP_URL so asset() helpers generate absolute HTTPS URLs.
# Without this, Vite assets may resolve to http://localhost:8080/build/...
ASSET_URL="${APP_URL}"
export ASSET_URL

# ─── 2. Force HTTPS cookies when running on Render ────────────────────────────
# RENDER_EXTERNAL_URL is only present when running inside a Render web service.
# Setting SESSION_SECURE_COOKIE=true here ensures HTTPS-only cookies even if
# the render.yaml env var is somehow missing.
if [ -n "$RENDER_EXTERNAL_URL" ]; then
    SESSION_SECURE_COOKIE=true
    export SESSION_SECURE_COOKIE
fi

echo "==> APP_URL       : ${APP_URL}"
echo "==> ASSET_URL     : ${ASSET_URL}"
echo "==> SESSION_DRIVER: ${SESSION_DRIVER:-database}"
echo "==> CACHE_STORE   : ${CACHE_STORE:-database}"
echo "==> LOG_LEVEL     : ${LOG_LEVEL:-error}"
echo "==> Vite manifest : $(ls /var/www/html/public/build/manifest.json 2>/dev/null && echo 'found' || echo 'MISSING!')"

# ─── 3. Wire nginx to the correct port ───────────────────────────────────────
# Render assigns a dynamic PORT; the nginx template uses ${PORT} as a placeholder.
PORT="${PORT:-8080}"
export PORT
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# ─── 4. Set SQLite WAL mode (permanent, stored in the .sqlite file header) ─────
# WAL allows concurrent readers while a writer is active — essential under
# nginx + php-fpm multi-worker. Without it, write contention produces
# "database is locked" 500 errors under simultaneous requests.
# The PRAGMA is idempotent: safe to run every boot even if already set.
DB_FILE="/var/www/html/database/database.sqlite"
if [ -f "$DB_FILE" ]; then
    sqlite3 "$DB_FILE" \
        "PRAGMA journal_mode=WAL; PRAGMA synchronous=NORMAL; PRAGMA busy_timeout=5000;" \
        2>/dev/null && echo "==> SQLite WAL mode: enabled" \
        || echo "==> SQLite WAL mode: sqlite3 not found — config-level fallback active"
fi

# ─── 5. Cache Laravel config / routes / views ────────────────────────────────
# This bakes the current environment variables (APP_URL, APP_KEY, SESSION_DRIVER,
# etc.) into serialized PHP caches in bootstrap/cache/.
# IMPORTANT: Run AFTER all env vars are finalized (steps 0–3 above).
# The pre-seeded .env was deleted at image build time; env vars come from Render.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ─── 5. Ensure storage:link exists ───────────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true

# ─── 6. Fix permissions (covers volume-mount edge cases) ──────────────────────
chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /var/www/html/database
chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache
chmod 664 /var/www/html/database/database.sqlite

# ─── 7. Start services via supervisor ────────────────────────────────────────
echo ""
echo "==================================================="
echo "  Clinovia is starting…"
echo "  URL  : ${APP_URL}"
echo "  Port : ${PORT}"
echo "==================================================="
echo ""

exec supervisord -c /etc/supervisord.conf
