# Email Setup Guide for BookNGO

## 🚨 Current Issue
Your email is configured to use `array` driver, which means emails are stored in memory but not actually sent. To send real emails, you need to configure a proper email service.

## 📧 Option 1: Gmail SMTP (Recommended for Development)

### Step 1: Enable 2-Factor Authentication
1. Go to your Google Account settings
2. Enable 2-Factor Authentication if not already enabled

### Step 2: Generate App Password
1. Go to Google Account → Security → 2-Step Verification
2. Scroll down to "App passwords"
3. Select "Mail" and your device
4. Copy the 16-character app password (e.g., `abcd efgh ijkl mnop`)

### Step 3: Update .env File
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-character-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="BookNGO"
```

### Step 4: Test Email
Run this command to test:
```bash
php artisan tinker
Mail::raw('Test email from BookNGO', function($message) {
    $message->to('test@example.com')->subject('Test Email');
});
```

## 📧 Option 2: Mailtrap (Recommended for Testing)

### Step 1: Create Mailtrap Account
1. Go to https://mailtrap.io
2. Sign up for free account
3. Create a new inbox

### Step 2: Get SMTP Credentials
1. Go to your inbox
2. Click "SMTP Settings"
3. Copy the credentials

### Step 3: Update .env File
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@bookngo.com"
MAIL_FROM_NAME="BookNGO"
```

## 📧 Option 3: SendGrid (Production Ready)

### Step 1: Create SendGrid Account
1. Go to https://sendgrid.com
2. Sign up for free account (100 emails/day free)

### Step 2: Create API Key
1. Go to Settings → API Keys
2. Create new API key with "Mail Send" permissions
3. Copy the API key

### Step 3: Update .env File
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="BookNGO"
```

## 🔧 After Configuration

### Clear Config Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Test Email Sending
```bash
php artisan tinker
```
Then run:
```php
Mail::raw('Test email from BookNGO', function($message) {
    $message->to('your-email@gmail.com')->subject('Test Email');
});
```

## 🚀 Quick Setup for Development

If you want to test immediately with Gmail:

1. **Use your Gmail account**
2. **Enable 2FA and generate app password**
3. **Update .env with your credentials**
4. **Clear cache and test**

## 📝 Important Notes

- **Gmail**: Requires app password, good for development
- **Mailtrap**: Perfect for testing, emails don't go to real recipients
- **SendGrid**: Best for production, reliable delivery
- **Always clear config cache** after changing .env
- **Test email sending** before going live

## 🔍 Troubleshooting

### Common Issues:
1. **"Authentication failed"** → Check username/password
2. **"Connection refused"** → Check host/port
3. **"TLS error"** → Try `MAIL_ENCRYPTION=ssl` or remove encryption
4. **Gmail blocking** → Make sure 2FA is enabled and using app password

### Debug Email Issues:
```bash
php artisan queue:work --verbose
tail -f storage/logs/laravel.log
```
