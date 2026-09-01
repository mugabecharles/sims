# Deploying SIMS to Railway

## Steps

### 1. Create Railway Account
Go to [railway.app](https://railway.app) and sign up (free tier available).

### 2. New Project from GitHub
- Click **New Project** → **Deploy from GitHub repo**
- Connect your GitHub account and select `mugabecharles/sims`
- Railway will auto-detect the Dockerfile

### 3. Add MySQL Database
- In your Railway project, click **+ New** → **Database** → **MySQL**
- Railway will automatically set `MYSQL_URL` / database variables

### 4. Set Environment Variables
In Railway → your service → **Variables**, add:

```
APP_NAME=SIMS - School Information Management System
APP_ENV=production
APP_DEBUG=false
APP_KEY=                          ← Railway will run: php artisan key:generate
APP_URL=https://your-app.railway.app
APP_TIMEZONE=Africa/Kampala

# Database (auto-filled by Railway MySQL plugin):
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQL_HOST}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQL_USER}}
DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}

SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 5. Generate App Key
After first deploy, open Railway Shell and run:
```bash
php artisan key:generate
```
Copy the key into the `APP_KEY` variable.

### 6. Done
Railway auto-deploys on every push to `main`. 🚀

## Free Tier Limits (Railway)
- $5 free credit per month
- Sleeps after inactivity on free plan
- For production schools, upgrade to Hobby ($5/mo)

## Alternative Free Hosts
| Platform | PHP | MySQL | Free |
|----------|-----|-------|------|
| Railway | ✅ | ✅ | $5 credit |
| Render | ✅ (Docker) | ✅ | ✅ (sleeps) |
| Fly.io | ✅ (Docker) | ✅ | ✅ limited |
| InfinityFree | ✅ | ✅ | ✅ full |
