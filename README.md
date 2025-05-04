# Kissan Agro Foods Website

A responsive website for Kissan Agro Foods, a company with two industry-level mills - one for wheat flour and another for puffed rice, located at MV37+9JJ, Pipra 45700, Khairba, Mahottari, Nepal. The company delivers products to Mahottari and Dhanusha districts.

## Features

- Responsive design for mobile and desktop
- Product showcase for both mills
- Admin panel with CRUD operations
- Contact form for inquiries
- About page with company information

## Installation

1. Clone the repository to your local machine or server.
2. Import the `database.sql` file into your MySQL database using phpMyAdmin.
3. Configure the database connection in `config/database.php` if needed.
4. Access the website through your web server.

## Admin Access

- URL: `/admin`
- Username: `admin`
- Password: `admin123`

## Directory Structure

```
├── admin/                  # Admin panel files
│   ├── includes/           # Admin includes
│   ├── categories.php      # Manage categories
│   ├── dashboard.php       # Admin dashboard
│   ├── index.php           # Admin login
│   ├── inquiries.php       # Manage inquiries
│   ├── logout.php          # Logout functionality
│   ├── orders.php          # Manage orders
│   ├── products.php        # Manage products
│   ├── profile.php         # User profile
│   ├── settings.php        # Site settings
│   └── users.php           # Manage users
├── assets/                 # Assets directory
│   ├── css/                # CSS files
│   ├── images/             # Image files
│   └── js/                 # JavaScript files
├── config/                 # Configuration files
│   └── database.php        # Database configuration
├── includes/               # Include files
│   ├── auth.php            # Authentication functions
│   ├── footer.php          # Footer template
│   ├── functions.php       # Common functions
│   └── header.php          # Header template
├── uploads/                # Uploaded files directory
├── about.php               # About page
├── contact.php             # Contact page
├── database.sql            # Database schema
├── index.php               # Homepage
├── products.php            # Products page
└── README.md               # This file
```

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

## Usage

1. **Frontend**:
   - Homepage: Displays featured products and company information
   - Products: Lists all products with filtering by category
   - About: Shows information about the company and its mills
   - Contact: Contact form for inquiries

2. **Admin Panel**:
   - Dashboard: Overview of products, inquiries, users, and orders
   - Products: Add, edit, and delete products
   - Categories: Manage product categories
   - Inquiries: View and manage customer inquiries
   - Orders: Manage customer orders
   - Users: Add, edit, and delete admin users
   - Settings: Configure website settings

## Customization

- Edit the CSS files in `assets/css/` to customize the appearance
- Update the content in the PHP files to change the text
- Replace placeholder images in `assets/images/` with actual images

## Credits

- Bootstrap 5: https://getbootstrap.com/
- Font Awesome: https://fontawesome.com/
- jQuery: https://jquery.com/

## License

This project is licensed under the MIT License.
