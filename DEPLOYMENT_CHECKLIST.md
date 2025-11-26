# 🚀 Production Deployment Checklist

## 📋 Pre-Deployment Checklist

### 1. Environment Configuration (.env file)

Update your `.env` file with production values:

```env
# Application
APP_NAME="Abodeology"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com  # ⚠️ Change to your live domain

# Database (Production Database)
DB_CONNECTION=mysql
DB_HOST=your_production_db_host
DB_PORT=3306
DB_DATABASE=your_production_database
DB_USERNAME=your_production_db_user
DB_PASSWORD=your_secure_db_password  # ⚠️ Use strong password

# Mail Configuration
MAIL_MAILER=smtp  # or ses, postmark, etc.
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Session & Cache
SESSION_DRIVER=file  # or redis for better performance
SESSION_LIFETIME=120
CACHE_DRIVER=file  # or redis for better performance

# Queue (if using queues)
QUEUE_CONNECTION=database  # or redis, sqs, etc.

# File Storage
FILESYSTEM_DISK=local  # or s3 for production
AWS_ACCESS_KEY_ID=your_aws_key  # if using S3
AWS_SECRET_ACCESS_KEY=your_aws_secret  # if using S3
AWS_DEFAULT_REGION=your_aws_region  # if using S3
AWS_BUCKET=your_s3_bucket  # if using S3
AWS_USE_PATH_STYLE_ENDPOINT=false  # if using S3

# Security
APP_KEY=base64:your_generated_key  # ⚠️ Generate new key: php artisan key:generate
```

### 2. Security Settings

- [ ] **APP_DEBUG must be `false`** - Never enable debug mode in production
- [ ] **Generate new APP_KEY** - Run `php artisan key:generate` on production
- [ ] **Use HTTPS** - Update APP_URL to use `https://`
- [ ] **Strong database passwords** - Use complex, unique passwords
- [ ] **Secure file permissions** - Set proper permissions:
  ```bash
  chmod -R 755 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache
  ```

### 3. Database Migration

- [ ] **Backup existing database** (if upgrading)
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed initial data if needed: `php artisan db:seed --class=YourSeeder`

### 4. Cache & Optimization

Run these commands on production:

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache for production (IMPORTANT for performance)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 5. File Storage

- [ ] **Uploaded files location** - Decide between:
  - **Local storage**: Files stored on server
  - **S3/Cloud storage**: Files stored on AWS S3 (recommended for production)
  
- [ ] If using S3, update `.env`:
  ```env
  FILESYSTEM_DISK=s3
  AWS_ACCESS_KEY_ID=your_key
  AWS_SECRET_ACCESS_KEY=your_secret
  AWS_DEFAULT_REGION=us-east-1
  AWS_BUCKET=your-bucket-name
  ```

### 6. Email Configuration

- [ ] Configure production email service:
  - **SMTP**: Gmail, SendGrid, Mailgun, etc.
  - **SES**: Amazon SES
  - **Postmark**: Postmark API
  - **Resend**: Resend API

- [ ] Test email sending after deployment

### 7. Domain & URL Configuration

- [ ] Update `APP_URL` in `.env` to your live domain
- [ ] Update any hardcoded URLs in code (if any)
- [ ] Configure domain DNS settings
- [ ] Set up SSL certificate (Let's Encrypt, Cloudflare, etc.)

### 8. Server Configuration

#### Web Server (Nginx/Apache)

**Nginx Example:**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    root /var/www/ABODEOLOGY/public;
    index index.php;
    
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Apache Example (.htaccess in public folder):**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 9. Queue Workers (if using queues)

- [ ] Set up supervisor or systemd service for queue workers:
  ```bash
  php artisan queue:work --tries=3
  ```

**Supervisor Config Example:**
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ABODEOLOGY/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ABODEOLOGY/storage/logs/worker.log
stopwaitsecs=3600
```

### 10. Scheduled Tasks (Cron Jobs)

- [ ] Set up cron job for Laravel scheduler:
  ```bash
  * * * * * cd /var/www/ABODEOLOGY && php artisan schedule:run >> /dev/null 2>&1
  ```

### 11. Logging

- [ ] Configure log rotation
- [ ] Set appropriate log level: `LOG_LEVEL=error` (in production)
- [ ] Monitor logs: `storage/logs/laravel.log`

### 12. Backup Strategy

- [ ] Set up automated database backups
- [ ] Set up file storage backups (if using local storage)
- [ ] Test backup restoration process

### 13. Performance Optimization

- [ ] Enable OPcache (PHP)
- [ ] Enable Redis for cache/sessions (optional but recommended)
- [ ] Set up CDN for static assets (optional)
- [ ] Optimize images before upload
- [ ] Enable gzip compression

### 14. Monitoring & Error Tracking

- [ ] Set up error tracking (Sentry, Bugsnag, etc.)
- [ ] Set up uptime monitoring
- [ ] Configure log monitoring
- [ ] Set up performance monitoring

### 15. Testing

- [ ] Test all major user flows:
  - [ ] User registration
  - [ ] Login/logout
  - [ ] Property creation
  - [ ] File uploads
  - [ ] Email sending
  - [ ] Payment processing (if applicable)
  - [ ] Admin functions

### 16. Code Deployment

**Recommended deployment process:**

```bash
# 1. Pull latest code
git pull origin main

# 2. Install/update dependencies
composer install --no-dev --optimize-autoloader

# 3. Run migrations
php artisan migrate --force

# 4. Clear and cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 5. Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Restart queue workers (if using)
php artisan queue:restart

# 7. Restart web server
sudo systemctl restart nginx  # or apache2
```

### 17. Post-Deployment Verification

- [ ] Verify site loads correctly
- [ ] Test login functionality
- [ ] Test file uploads
- [ ] Test email sending
- [ ] Check error logs
- [ ] Verify SSL certificate
- [ ] Test on mobile devices
- [ ] Check page load speeds

### 18. Security Headers

Add security headers to your web server configuration:

```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
```

### 19. Environment-Specific Code

Check for any hardcoded development URLs or settings:
- [ ] Search for `127.0.0.1` or `localhost` in code
- [ ] Search for `http://` (should use `https://` in production)
- [ ] Check for development API keys

### 20. Documentation

- [ ] Document production server details
- [ ] Document database credentials (securely)
- [ ] Document deployment process
- [ ] Document rollback procedure

## 🔄 Rollback Plan

If something goes wrong:

```bash
# 1. Revert code
git checkout <previous-commit>

# 2. Clear caches
php artisan config:clear
php artisan cache:clear

# 3. Restart services
sudo systemctl restart nginx
php artisan queue:restart
```

## 📝 Important Notes

1. **Never commit `.env` file** - It's already in `.gitignore`
2. **Use environment variables** - Never hardcode sensitive data
3. **Test in staging first** - Always test changes in staging before production
4. **Backup before deployment** - Always backup database and files before major updates
5. **Monitor after deployment** - Watch logs and error tracking for 24-48 hours

## 🆘 Emergency Contacts

- Server Admin: [Contact Info]
- Database Admin: [Contact Info]
- DevOps: [Contact Info]

---

**Last Updated:** [Date]
**Deployed By:** [Name]
**Version:** [Version Number]

