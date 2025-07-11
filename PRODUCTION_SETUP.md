# BookNGO Production Setup Guide

This guide provides comprehensive instructions for deploying the BookNGO bus booking system to production.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Server Requirements](#server-requirements)
3. [Payment Gateway Setup](#payment-gateway-setup)
4. [Environment Configuration](#environment-configuration)
5. [Database Setup](#database-setup)
6. [SSL Certificate Setup](#ssl-certificate-setup)
7. [Deployment Methods](#deployment-methods)
8. [Performance Optimization](#performance-optimization)
9. [Monitoring and Logging](#monitoring-and-logging)
10. [Backup Strategy](#backup-strategy)
11. [Security Considerations](#security-considerations)
12. [Troubleshooting](#troubleshooting)

## Prerequisites

- Ubuntu 20.04 LTS or higher
- Root or sudo access
- Domain name pointed to your server
- SSL certificate (Let's Encrypt recommended)

## Server Requirements

### Minimum Requirements
- **CPU**: 2 cores
- **RAM**: 4GB
- **Storage**: 50GB SSD
- **Bandwidth**: 100Mbps

### Recommended Requirements
- **CPU**: 4 cores
- **RAM**: 8GB
- **Storage**: 100GB SSD
- **Bandwidth**: 1Gbps

### Software Requirements
- PHP 8.2+
- MySQL 8.0+
- Redis 7.0+
- Nginx 1.18+
- Node.js 18+
- Composer 2.0+

## Payment Gateway Setup

### eSewa Integration

1. **Test Environment**
   ```bash
   ESEWA_MERCHANT_ID=EPAYTEST
   ESEWA_SECRET_KEY="8gBm/:&EnhH.1/q"
   ESEWA_BASE_URL=https://uat.esewa.com.np
   ```

2. **Production Environment**
   - Contact eSewa for production credentials
   - Update environment variables:
   ```bash
   ESEWA_MERCHANT_ID=your_merchant_id
   ESEWA_SECRET_KEY=your_secret_key
   ESEWA_BASE_URL=https://esewa.com.np
   ```

### Khalti Integration (Future)

1. **Test Environment**
   ```bash
   KHALTI_PUBLIC_KEY=test_public_key
   KHALTI_SECRET_KEY=test_secret_key
   ```

2. **Production Environment**
   - Register at Khalti merchant portal
   - Get production credentials
   - Update environment variables

## Environment Configuration

1. **Copy production environment file**
   ```bash
   cp .env.production .env
   ```

2. **Generate application key**
   ```bash
   php artisan key:generate
   ```

3. **Update critical settings**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   DB_PASSWORD=secure_password
   REDIS_PASSWORD=secure_redis_password
   ```

## Database Setup

### MySQL Installation and Configuration

1. **Install MySQL**
   ```bash
   sudo apt update
   sudo apt install mysql-server
   sudo mysql_secure_installation
   ```

2. **Create database and user**
   ```sql
   CREATE DATABASE bookngo_production;
   CREATE USER 'bookngo_user'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT ALL PRIVILEGES ON bookngo_production.* TO 'bookngo_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. **Run migrations**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=ProductionSeeder
   ```

### Database Optimization

1. **Configure MySQL for production**
   ```bash
   sudo cp docker/mysql.cnf /etc/mysql/conf.d/bookngo.cnf
   sudo systemctl restart mysql
   ```

2. **Create database indexes**
   ```bash
   php artisan db:index
   ```

## SSL Certificate Setup

### Using Let's Encrypt (Recommended)

1. **Install Certbot**
   ```bash
   sudo apt install certbot python3-certbot-nginx
   ```

2. **Obtain certificate**
   ```bash
   sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
   ```

3. **Auto-renewal setup**
   ```bash
   sudo crontab -e
   # Add: 0 12 * * * /usr/bin/certbot renew --quiet
   ```

## Deployment Methods

### Method 1: Traditional Deployment

1. **Run deployment script**
   ```bash
   chmod +x deploy.sh
   sudo ./deploy.sh
   ```

2. **Manual steps if needed**
   ```bash
   # Clone repository
   git clone https://github.com/yourusername/bookngo.git /var/www/bookngo
   
   # Install dependencies
   composer install --no-dev --optimize-autoloader
   npm ci --production
   npm run build
   
   # Set permissions
   sudo chown -R www-data:www-data /var/www/bookngo
   sudo chmod -R 755 /var/www/bookngo
   sudo chmod -R 775 /var/www/bookngo/storage
   sudo chmod -R 775 /var/www/bookngo/bootstrap/cache
   ```

### Method 2: Docker Deployment

1. **Install Docker and Docker Compose**
   ```bash
   curl -fsSL https://get.docker.com -o get-docker.sh
   sudo sh get-docker.sh
   sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
   sudo chmod +x /usr/local/bin/docker-compose
   ```

2. **Deploy with Docker Compose**
   ```bash
   # Set environment variables
   export DB_PASSWORD=secure_password
   export DB_ROOT_PASSWORD=secure_root_password
   export REDIS_PASSWORD=secure_redis_password
   
   # Deploy
   docker-compose up -d
   ```

3. **Run initial setup**
   ```bash
   docker-compose exec app php artisan migrate --force
   docker-compose exec app php artisan db:seed --class=ProductionSeeder
   ```

## Performance Optimization

### PHP Optimization

1. **Install OPcache**
   ```bash
   sudo apt install php8.2-opcache
   ```

2. **Configure PHP-FPM**
   ```bash
   sudo cp docker/php.ini /etc/php/8.2/fpm/conf.d/99-bookngo.ini
   sudo systemctl restart php8.2-fpm
   ```

### Laravel Optimization

1. **Cache configuration**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Queue workers**
   ```bash
   # Install supervisor
   sudo apt install supervisor
   
   # Configure queue workers
   sudo cp docker/supervisord.conf /etc/supervisor/conf.d/bookngo.conf
   sudo supervisorctl reread
   sudo supervisorctl update
   ```

### Database Optimization

1. **Enable query cache**
   ```sql
   SET GLOBAL query_cache_type = ON;
   SET GLOBAL query_cache_size = 67108864;
   ```

2. **Optimize tables**
   ```bash
   php artisan db:optimize
   ```

## Monitoring and Logging

### Application Monitoring

1. **Install monitoring tools**
   ```bash
   # Enable monitoring profile
   docker-compose --profile monitoring up -d
   ```

2. **Configure log rotation**
   ```bash
   sudo cp docker/logrotate.conf /etc/logrotate.d/bookngo
   ```

### Health Checks

1. **Application health endpoint**
   ```
   GET /health
   ```

2. **Database health check**
   ```bash
   php artisan health:check
   ```

## Backup Strategy

### Automated Backups

1. **Database backup script**
   ```bash
   #!/bin/bash
   BACKUP_DIR="/var/backups/bookngo"
   DATE=$(date +%Y%m%d_%H%M%S)
   
   # Create backup directory
   mkdir -p $BACKUP_DIR
   
   # Backup database
   mysqldump -u bookngo_user -p bookngo_production > $BACKUP_DIR/db_$DATE.sql
   
   # Backup files
   tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/bookngo/storage
   
   # Clean old backups (keep 30 days)
   find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
   find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
   ```

2. **Schedule backups**
   ```bash
   sudo crontab -e
   # Add: 0 2 * * * /path/to/backup-script.sh
   ```

## Security Considerations

### Server Security

1. **Firewall configuration**
   ```bash
   sudo ufw enable
   sudo ufw allow 22/tcp
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   ```

2. **Fail2ban setup**
   ```bash
   sudo apt install fail2ban
   sudo cp docker/jail.local /etc/fail2ban/jail.local
   sudo systemctl restart fail2ban
   ```

### Application Security

1. **Security headers**
   - Already configured in Nginx
   - CSP, HSTS, XSS protection enabled

2. **Rate limiting**
   - API endpoints: 100 requests/minute
   - Login attempts: 5 attempts/minute

## Troubleshooting

### Common Issues

1. **Permission errors**
   ```bash
   sudo chown -R www-data:www-data /var/www/bookngo
   sudo chmod -R 775 /var/www/bookngo/storage
   ```

2. **Queue not processing**
   ```bash
   sudo supervisorctl restart bookngo-worker:*
   ```

3. **Database connection issues**
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

### Log Locations

- **Application logs**: `/var/www/bookngo/storage/logs/`
- **Nginx logs**: `/var/log/nginx/`
- **PHP-FPM logs**: `/var/log/php8.2-fpm.log`
- **MySQL logs**: `/var/log/mysql/`

### Performance Monitoring

1. **Check application performance**
   ```bash
   php artisan horizon:status
   php artisan queue:monitor
   ```

2. **Database performance**
   ```sql
   SHOW PROCESSLIST;
   SHOW STATUS LIKE 'Slow_queries';
   ```

## Support

For technical support and deployment assistance:
- Email: support@bookngo.com.np
- Documentation: https://docs.bookngo.com.np
- GitHub Issues: https://github.com/yourusername/bookngo/issues

---

**Note**: This is a production system handling financial transactions. Ensure all security measures are properly implemented and regularly updated.
