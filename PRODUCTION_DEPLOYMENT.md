# Kissan Agro Foods - Production Deployment Guide

This guide provides instructions for deploying the Kissan Agro Foods website to a production environment.

## Prerequisites

- Web hosting with PHP 7.4+ and MySQL 5.7+
- cPanel access
- FTP access (using WinSCP)

## Database Setup

1. Create a new database in cPanel called `shardhay_kissan_agro_foods`
2. Import the `database.sql` file into the new database
3. Create a database user with all privileges on the database

## File Transfer

1. Connect to your server using WinSCP
2. Upload all files to the public_html directory (or your desired location)
3. Make sure to maintain the directory structure

## Configuration

The website is configured to automatically detect whether it's running in development or production mode based on the hostname. If the hostname contains `shardhayvatshyayan.com`, it will use production settings.

### Production Database Credentials

The production database credentials are already configured in `config/environment.php`:

```php
// Production settings
define('DB_HOST', 'localhost');
define('DB_USER', 'shardhay');
define('DB_PASS', 'Mahakali@5254');
define('DB_NAME', 'shardhay_kissan_agro_foods');
```

## Testing

1. After uploading all files, run the `test_db_connection.php` script to verify the database connection
2. Visit the website to ensure all pages load correctly
3. Test the admin login functionality
4. Test the product listing and cart functionality

## Troubleshooting

If you encounter any issues:

1. Check the server error logs
2. Verify that all file permissions are set correctly (755 for directories, 644 for files)
3. Make sure the database credentials are correct
4. Ensure that the .htaccess file was uploaded correctly

## Security Considerations

1. Delete the `test_db_connection.php` file after successful deployment
2. Change the admin password after the first login
3. Consider setting up SSL if not already configured

## Maintenance

Regular maintenance tasks:

1. Backup the database regularly
2. Keep PHP and MySQL updated
3. Monitor server logs for any issues

## Contact

If you encounter any issues during deployment, please contact the developer for assistance.
