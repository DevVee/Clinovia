#!/bin/sh
set -e

cd /var/www/html

# Railway injects PORT (default 8080). Nginx template uses ${PORT}.
export PORT="${PORT:-8080}"

# ── 1. Generate Nginx config with the correct PORT ──────────────────────────
envsubst '${PORT}' \
  < /etc/nginx/templates/default.conf.template \
  > /etc/nginx/http.d/default.conf

echo "[entrypoint] Nginx configured on port ${PORT}"

# ── 2. Wait for the database to accept connections ───────────────────────────
echo "[entrypoint] Waiting for database..."
MAX_TRIES=30
TRIES=0
until php artisan db:show --json > /dev/null 2>&1; do
    TRIES=$((TRIES + 1))
    if [ "$TRIES" -ge "$MAX_TRIES" ]; then
        echo "[entrypoint] ERROR: database never became ready. Aborting."
        exit 1
    fi
    echo "[entrypoint] Database not ready — retry $TRIES/$MAX_TRIES..."
    sleep 2
done
echo "[entrypoint] Database is ready."

# ── 3. Run migrations ────────────────────────────────────────────────────────
echo "[entrypoint] Running migrations..."
php artisan migrate --force

# ── 4. Production optimisations ──────────────────────────────────────────────
echo "[entrypoint] Caching config / routes / views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 5. Public storage symlink ────────────────────────────────────────────────
echo "[entrypoint] Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

# ── 6. Hand off to Supervisor (nginx + php-fpm) ──────────────────────────────
echo "[entrypoint] Starting services..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
