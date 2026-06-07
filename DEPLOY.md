# 🚀 Clinovia — Production Deployment Guide (Railway)

## Stack
| Layer      | Service                          | Cost            |
|------------|----------------------------------|-----------------|
| App        | Railway Web Service (Docker)     | ~$0–$3 / month  |
| Database   | Railway PostgreSQL plugin        | ~$1–$2 / month  |
| Total      |                                  | **Free $5 credit covers it** |

> Railway gives **$5 free credit / month** (no credit card required on Hobby plan).
> A dormant portfolio app with occasional demo traffic fits comfortably within this.

---

## Prerequisites

1. **Push to GitHub** — Railway deploys from a GitHub repo.
2. **Get your APP_KEY** — run this locally and copy the output:
   ```bash
   php artisan key:generate --show
   # → base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
   ```
3. **Create Railway account** at https://railway.app (sign in with GitHub)

---

## Step-by-step Deploy

### 1 — Create a new Railway project

1. Go to https://railway.app/new
2. Click **"Deploy from GitHub repo"**
3. Authorize Railway and select your **Clinovia** repository
4. Railway detects the `Dockerfile` automatically → click **Deploy**

> The first build takes ~3–5 minutes (downloading PHP, Node, dependencies).

---

### 2 — Add PostgreSQL

1. In your Railway project dashboard, click **"+ New"**
2. Select **"Database" → "PostgreSQL"**
3. Railway creates a Postgres instance and **auto-injects** these variables
   into your project's environment:
   - `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD`
   - `DATABASE_URL` (full connection string)

---

### 3 — Set Environment Variables

Go to your **web service → Variables** tab and add these:

#### Required
| Key | Value |
|-----|-------|
| `APP_KEY` | `base64:...` (from Step 0 above) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://your-app.up.railway.app` (copy after first deploy) |
| `APP_TIMEZONE` | `Asia/Manila` |

#### Database — use Railway variable references
| Key | Value (click "Add Reference") |
|-----|-------------------------------|
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | `${{Postgres.PGHOST}}` |
| `DB_PORT` | `${{Postgres.PGPORT}}` |
| `DB_DATABASE` | `${{Postgres.PGDATABASE}}` |
| `DB_USERNAME` | `${{Postgres.PGUSER}}` |
| `DB_PASSWORD` | `${{Postgres.PGPASSWORD}}` |

> **How to add references:** In the Variables tab, type the key name, then in the value
> field click **"Add Reference"** and pick the Postgres service variable.

#### Session & Cache
| Key | Value |
|-----|-------|
| `SESSION_DRIVER` | `database` |
| `SESSION_LIFETIME` | `120` |
| `SESSION_ENCRYPT` | `true` |
| `SESSION_SECURE_COOKIE` | `true` |
| `SESSION_SAME_SITE` | `strict` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |

#### Security & Proxy
| Key | Value |
|-----|-------|
| `TRUST_PROXIES` | `*` |
| `ALLOW_REGISTRATION` | `false` |

#### Logging
| Key | Value |
|-----|-------|
| `LOG_CHANNEL` | `stderr` |
| `LOG_LEVEL` | `warning` |

#### Optional (if you have these)
| Key | Value |
|-----|-------|
| `GROQ_API_KEY` | Your Groq key |
| `SEMAPHORE_API_KEY` | Your Semaphore key |
| `SEMAPHORE_SENDER_NAME` | `CLINOVIA` |

#### Default admin credentials (first deploy only)
| Key | Value |
|-----|-------|
| `ADMIN_EMAIL` | `admin@clinovia.app` |
| `ADMIN_PASSWORD` | A strong password |
| `NURSE_EMAIL` | `nurse@clinovia.app` |
| `NURSE_PASSWORD` | A strong password |

---

### 4 — Trigger a Redeploy

After setting all variables, go to **Deployments → "Redeploy"**.
The entrypoint script will:
- Wait for Postgres to be ready
- Run `php artisan migrate --force`
- Cache config, routes, views
- Start Nginx + PHP-FPM via Supervisor

---

### 5 — Run the Database Seeder (first time only)

Once the app is live, open Railway's shell (web service → **"Shell"** tab):

```bash
php artisan db:seed
```

This creates the default admin, nurse, and staff accounts.

---

### 6 — Set your live URL

1. Copy your Railway URL: `https://clinovia-production.up.railway.app`
2. Go back to Variables → update `APP_URL` to this URL
3. Optionally, add a custom domain under **Settings → Domains**

---

## ⚠️ Important Notes

### File Uploads (Avatars)
Railway's filesystem is **ephemeral** — uploaded files (avatars, etc.) are lost
on every redeploy. For a portfolio demo this is acceptable. For production use:

**Option A — Railway Volume** (easiest, ~$0.25/GB/month)
1. Go to your web service → **"+ Volume"**
2. Mount path: `/var/www/html/storage/app`
3. That's it — uploads now survive redeploys.

**Option B — Cloudinary** (free 25GB)
1. Sign up at https://cloudinary.com
2. Install: `composer require cloudinary-labs/cloudinary-laravel`
3. Set `CLOUDINARY_URL` env var and change `FILESYSTEM_DISK=cloudinary`

### Session Encryption
`SESSION_ENCRYPT=true` means sessions are encrypted with `APP_KEY`.
If you rotate `APP_KEY`, all existing sessions are invalidated (users get logged out).

### Free Credit Monitoring
Check Railway's usage dashboard. If you approach $5:
- Scale down the Postgres instance to the smallest tier
- Or upgrade to the $20/month Developer plan

---

## Local → Production Checklist

- [ ] `APP_KEY` generated and set in Railway
- [ ] `APP_URL` set to the Railway URL
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] All DB variables pointing to Railway Postgres
- [ ] `SESSION_ENCRYPT=true`
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `ALLOW_REGISTRATION=false`
- [ ] `TRUST_PROXIES=*`
- [ ] Seeder run once via Railway shell
- [ ] Tested login at `/login`

---

## Useful Railway CLI Commands

```bash
# Install CLI
npm install -g @railway/cli

# Login
railway login

# Link to your project (run in project dir)
railway link

# View live logs
railway logs

# Open a shell in the running container
railway shell

# Run a one-off command (e.g. artisan)
railway run php artisan tinker
```
