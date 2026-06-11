#!/bin/sh
set -e

# ─── 0. Ensure APP_KEY is in Laravel's required  base64:...  format ──────────
# Render's generateValue: true produces a plain random string (e.g. "AbCd1234"),
# NOT the "base64:XXXX==" format Laravel needs for AES-256 encryption.
# We generate a proper key with pure PHP — no .env file required.
if [ -z "$APP_KEY" ] || ! echo "$APP_KEY" | grep -q "^base64:"; then
    APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
    export APP_KEY
    echo "==> Generated valid Laravel APP_KEY (Render key was not in base64: format)"
fi

# ─── 1. Resolve APP_URL from Render's injected URL ────────────────────────────
# Render automatically sets RENDER_EXTERNAL_URL = https://<service>.onrender.com
APP_URL="${RENDER_EXTERNAL_URL:-${APP_URL:-http://localhost:8080}}"
export APP_URL

# Ensure cookies are only sent over HTTPS when running on Render
if [ -n "$RENDER_EXTERNAL_URL" ]; then
    SESSION_SECURE_COOKIE=true
    export SESSION_SECURE_COOKIE
fi

# ─── 2. Wire nginx to the correct port ───────────────────────────────────────
PORT="${PORT:-8080}"
export PORT
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# ─── 3. Cache Laravel config / routes / views ────────────────────────────────
# This bakes the runtime env vars (APP_URL, APP_KEY, etc.) into the cache.
# The pre-seeded .env was deleted at build time; env vars come from Render.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ─── 4. Ensure storage symlink exists ────────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true

# ─── 5. Fix permissions (cover volume-mount edge cases) ──────────────────────
chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /var/www/html/database
chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache
chmod 664 /var/www/html/database/database.sqlite

# ─── 6. Start services via supervisor ────────────────────────────────────────
echo ""
echo "==================================================="
echo "  Clinovia is starting…"
echo "  URL  : ${APP_URL}"
echo "  Port : ${PORT}"
echo "==================================================="
echo ""

exec supervisord -c /etc/supervisord.conf
