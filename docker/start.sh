#!/bin/sh
set -e

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
