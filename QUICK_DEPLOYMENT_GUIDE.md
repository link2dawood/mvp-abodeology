# ⚡ Quick Deployment Guide

## 🎯 Essential Steps for Going Live

### 1. Update .env File (CRITICAL)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_HOST=your_production_host
DB_DATABASE=your_production_db
DB_USERNAME=your_production_user
DB_PASSWORD=your_secure_password

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### 2. Run These Commands

```bash
# Generate application key (if not set)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache for production (IMPORTANT!)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. File Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Set Up Cron Job

```bash
* * * * * cd /path/to/ABODEOLOGY && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Set Up Queue Worker (if using queues)

Use Supervisor or systemd to run:
```bash
php artisan queue:work --tries=3
```

### 6. SSL Certificate

- Install SSL certificate (Let's Encrypt recommended)
- Update APP_URL to use `https://`
- Force HTTPS in your web server config

### 7. Test Everything

- [ ] Login/Logout
- [ ] Registration
- [ ] File uploads
- [ ] Email sending
- [ ] Admin functions
- [ ] All forms

## ⚠️ Critical Security Checks

1. **APP_DEBUG must be FALSE**
2. **Use HTTPS everywhere**
3. **Strong database passwords**
4. **Never commit .env file**
5. **Set proper file permissions**

## 📧 Email Addresses to Update

Found these email addresses in the codebase:
- `support@abodeology.co.uk` (in email templates)
- `admin@abodeology.co.uk` (in seeders)
- `agent@abodeology.co.uk` (in seeders)

Update these if you're using a different domain.

## 🔗 Domain References

The codebase uses `abodeology.co.uk` in:
- Email templates (`resources/views/emails/valuation-login-credentials.blade.php`)
- Database seeders (`database/seeders/DatabaseSeeder.php`)

Update these if using a different domain.

## 🚨 Common Issues

1. **500 Error**: Check file permissions and APP_DEBUG=false
2. **Database Connection Error**: Verify DB credentials in .env
3. **Email Not Sending**: Check MAIL configuration
4. **Slow Performance**: Run cache commands and enable OPcache

## 📞 Need Help?

Refer to `DEPLOYMENT_CHECKLIST.md` for detailed instructions.

