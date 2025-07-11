#!/bin/bash

# BookNGO Production Deployment Script
# This script automates the deployment process for the BookNGO bus booking system

set -e  # Exit on any error

# Configuration
APP_NAME="BookNGO"
APP_DIR="/var/www/bookngo"
BACKUP_DIR="/var/backups/bookngo"
LOG_FILE="/var/log/bookngo-deploy.log"
NGINX_CONFIG="/etc/nginx/sites-available/bookngo"
PHP_VERSION="8.2"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1" >> $LOG_FILE
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
    echo "[ERROR] $1" >> $LOG_FILE
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
    echo "[WARNING] $1" >> $LOG_FILE
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
    echo "[INFO] $1" >> $LOG_FILE
}

# Check if running as root
check_root() {
    if [[ $EUID -eq 0 ]]; then
        error "This script should not be run as root for security reasons"
    fi
}

# Check system requirements
check_requirements() {
    log "Checking system requirements..."
    
    # Check PHP version
    if ! command -v php &> /dev/null; then
        error "PHP is not installed"
    fi
    
    PHP_CURRENT=$(php -r "echo PHP_VERSION;" | cut -d. -f1,2)
    if [[ "$PHP_CURRENT" != "$PHP_VERSION" ]]; then
        warning "PHP version mismatch. Expected: $PHP_VERSION, Found: $PHP_CURRENT"
    fi
    
    # Check required PHP extensions
    REQUIRED_EXTENSIONS=("mbstring" "xml" "bcmath" "curl" "gd" "mysql" "zip" "redis")
    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if ! php -m | grep -q "$ext"; then
            error "Required PHP extension '$ext' is not installed"
        fi
    done
    
    # Check Composer
    if ! command -v composer &> /dev/null; then
        error "Composer is not installed"
    fi
    
    # Check Node.js and npm
    if ! command -v node &> /dev/null; then
        error "Node.js is not installed"
    fi
    
    if ! command -v npm &> /dev/null; then
        error "npm is not installed"
    fi
    
    log "System requirements check passed"
}

# Create backup
create_backup() {
    log "Creating backup..."
    
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_PATH="$BACKUP_DIR/backup_$TIMESTAMP"
    
    mkdir -p "$BACKUP_PATH"
    
    # Backup application files
    if [ -d "$APP_DIR" ]; then
        tar -czf "$BACKUP_PATH/app_files.tar.gz" -C "$APP_DIR" .
        log "Application files backed up"
    fi
    
    # Backup database
    if [ ! -z "$DB_DATABASE" ]; then
        mysqldump -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_PATH/database.sql"
        log "Database backed up"
    fi
    
    # Backup environment file
    if [ -f "$APP_DIR/.env" ]; then
        cp "$APP_DIR/.env" "$BACKUP_PATH/.env.backup"
        log "Environment file backed up"
    fi
    
    log "Backup created at: $BACKUP_PATH"
}

# Deploy application
deploy_application() {
    log "Starting application deployment..."
    
    # Navigate to application directory
    cd "$APP_DIR" || error "Cannot access application directory: $APP_DIR"
    
    # Pull latest code from repository
    log "Pulling latest code from repository..."
    git fetch origin
    git reset --hard origin/main
    
    # Install/update Composer dependencies
    log "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Install/update npm dependencies
    log "Installing npm dependencies..."
    npm ci --production
    
    # Build assets
    log "Building production assets..."
    npm run build
    
    # Clear and cache configuration
    log "Optimizing application..."
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan route:cache
    php artisan view:clear
    php artisan view:cache
    
    # Run database migrations
    log "Running database migrations..."
    php artisan migrate --force
    
    # Clear application cache
    php artisan cache:clear
    php artisan queue:restart
    
    log "Application deployment completed"
}

# Set proper permissions
set_permissions() {
    log "Setting proper file permissions..."
    
    # Set ownership
    sudo chown -R www-data:www-data "$APP_DIR"
    
    # Set directory permissions
    find "$APP_DIR" -type d -exec chmod 755 {} \;
    
    # Set file permissions
    find "$APP_DIR" -type f -exec chmod 644 {} \;
    
    # Set executable permissions for artisan
    chmod +x "$APP_DIR/artisan"
    
    # Set writable permissions for storage and cache
    chmod -R 775 "$APP_DIR/storage"
    chmod -R 775 "$APP_DIR/bootstrap/cache"
    
    log "File permissions set"
}

# Configure web server
configure_nginx() {
    log "Configuring Nginx..."
    
    # Create Nginx configuration if it doesn't exist
    if [ ! -f "$NGINX_CONFIG" ]; then
        sudo tee "$NGINX_CONFIG" > /dev/null <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name bookngo.com.np www.bookngo.com.np;
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name bookngo.com.np www.bookngo.com.np;
    
    root $APP_DIR/public;
    index index.php index.html index.htm;
    
    # SSL Configuration
    ssl_certificate /etc/ssl/certs/bookngo.crt;
    ssl_certificate_key /etc/ssl/private/bookngo.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php$PHP_VERSION-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|txt|tar|woff|svg|ttf|eot|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # Health check endpoint
    location /health {
        access_log off;
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }
}
EOF
        
        # Enable the site
        sudo ln -sf "$NGINX_CONFIG" /etc/nginx/sites-enabled/
        
        # Test Nginx configuration
        sudo nginx -t || error "Nginx configuration test failed"
        
        log "Nginx configured"
    fi
}

# Start services
start_services() {
    log "Starting services..."
    
    # Reload Nginx
    sudo systemctl reload nginx
    
    # Restart PHP-FPM
    sudo systemctl restart php$PHP_VERSION-fpm
    
    # Start queue workers
    php artisan queue:work --daemon --sleep=3 --tries=3 &
    
    # Start Laravel Horizon (if using)
    if [ -f "$APP_DIR/artisan" ] && php artisan list | grep -q horizon; then
        php artisan horizon &
    fi
    
    log "Services started"
}

# Health check
health_check() {
    log "Performing health check..."
    
    # Check if application is responding
    if curl -f -s "https://bookngo.com.np/health" > /dev/null; then
        log "Application health check passed"
    else
        error "Application health check failed"
    fi
    
    # Check database connection
    if php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; then
        log "Database connection check passed"
    else
        error "Database connection check failed"
    fi
}

# Main deployment function
main() {
    log "Starting $APP_NAME deployment..."
    
    # Load environment variables
    if [ -f "$APP_DIR/.env" ]; then
        source "$APP_DIR/.env"
    fi
    
    check_root
    check_requirements
    create_backup
    deploy_application
    set_permissions
    configure_nginx
    start_services
    health_check
    
    log "$APP_NAME deployment completed successfully!"
    info "Application is now available at: https://bookngo.com.np"
}

# Run main function
main "$@"
