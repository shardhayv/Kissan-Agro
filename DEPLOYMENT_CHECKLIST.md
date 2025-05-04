# Kissan Agro Foods - Deployment Checklist

Use this checklist to ensure a successful deployment of the Kissan Agro Foods website to production.

## Pre-Deployment

- [ ] Backup the development database
- [ ] Backup all files
- [ ] Run tests to ensure all functionality works correctly
- [ ] Check for any hardcoded paths or URLs
- [ ] Verify all forms are working correctly
- [ ] Ensure all images are optimized for web

## Database Setup

- [ ] Create a new database in cPanel called `shardhay_kissan_agro_foods`
- [ ] Import the `database.sql` file into the new database
- [ ] Create a database user with all privileges on the database
- [ ] Run `test_db_connection.php` to verify the connection

## File Transfer

- [ ] Connect to the server using WinSCP
- [ ] Upload all files to the public_html directory
- [ ] Set correct file permissions (755 for directories, 644 for files)
- [ ] Verify all files were uploaded correctly

## Post-Deployment Checks

- [ ] Visit the website to ensure all pages load correctly
- [ ] Test the admin login functionality
- [ ] Test the product listing and cart functionality
- [ ] Verify all images are displaying correctly
- [ ] Check that all links are working
- [ ] Test the contact form
- [ ] Test the order tracking functionality
- [ ] Verify that the site is mobile responsive

## Security Checks

- [ ] Delete the `test_db_connection.php` file
- [ ] Change the admin password
- [ ] Verify that sensitive directories are protected
- [ ] Check that error reporting is disabled in production
- [ ] Verify that the .htaccess file is working correctly

## Final Steps

- [ ] Set up regular database backups
- [ ] Document any issues encountered during deployment
- [ ] Provide admin login credentials to the client
- [ ] Verify Google Analytics or other tracking is working (if applicable)

## Notes

- The website is configured to automatically detect whether it's running in development or production mode based on the hostname.
- If the hostname contains `shardhayvatshyayan.com`, it will use production settings.
- All URLs are generated using the `site_url()` and `asset_url()` functions, which handle the correct paths for both development and production environments.
