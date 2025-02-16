# 🎫 Espada-Eventbrite

A comprehensive event management platform built with PHP MVC, PostgreSQL, and AJAX, developed by Team Espada.

## 🌟 Features

### User Management
- Secure registration and login (email/password with bcrypt hashing)
- Role-based access control (Organizer, Participant, Admin)
- User profiles with avatars and event history
- Email notifications and site alerts

### Event Management
- Create and modify events (title, description, date, venue, pricing, capacity)
- Category and tag management
- Promotional media upload (images and videos)
- Admin validation system
- Featured/sponsored events

### Booking & Payment
- Multiple ticket types (free, paid, VIP, early bird)
- Secure payment integration (Stripe/PayPal - sandbox mode)
- QR code ticket validation
- Refund and cancellation system
- PDF ticket generation

## 🛠️ Technologies

### Backend
- PHP 8.x
- PostgreSQL
- PDO
- Twig Template Engine

### Composer Dependencies
- symfony/*: Symfony components for robust framework features
- twig/twig: Template engine for PHP
- monolog/monolog: Logging for PHP
- stripe/stripe-php: Payment processing
- firebase/*: Firebase integration
- paragonie/*: Security components
- phpoptio/*: PHP optimization tools
- vlucas/phpdotenv: Environment variable management
- guzzlehttp/guzzlehttp: HTTP client

# Composer Installation Commands

## Initial Setup
```bash
# Initialize a new composer project
composer init

# Install all dependencies from composer.json
composer install
```

## Framework & Core Components
```bash
# Install Symfony components
composer require symfony/http-foundation
composer require symfony/routing
composer require symfony/http-kernel
composer require symfony/dependency-injection
composer require symfony/dotenv
composer require symfony/yaml

# Install Twig template engine
composer require twig/twig

# Install Monolog for logging
composer require monolog/monolog

# Install environment variable handler
composer require vlucas/phpdotenv
```

## Database & ORM
```bash
# Install database components
composer require symfony/orm-pack
composer require symfony/doctrine-bridge
```

## HTTP & API
```bash
# Install Guzzle for HTTP requests
composer require guzzlehttp/guzzle

# Install Bacon HTTP components
composer require bacon/bacon-qr-code
```

## Security
```bash
# Install security components
composer require paragonie/random_compat
composer require phpseclib/phpseclib

# Install Firebase components
composer require firebase/php-jwt
```

## Development Tools
```bash
# Install development dependencies
composer require --dev symfony/var-dumper
composer require --dev symfony/debug-bundle
```

## Payment Integration
```bash
# Install Stripe for payment processing
composer require stripe/stripe-php
```

## Utility Libraries
```bash
# Install additional utility libraries
composer require ralouphie/getallheaders
composer require psr/http-message
composer require endroid/qr-code
```

## Development Commands
```bash
# Update all dependencies
composer update

# Install dependencies in production mode (no dev dependencies)
composer install --no-dev --optimize-autoloader

# Clear composer cache
composer clear-cache

# Validate composer.json
composer validate
```

## Important Notes:
1. Run `composer install` after cloning the project to install all dependencies
2. Use `composer update` carefully in production as it might update to incompatible versions
3. Always commit both `composer.json` and `composer.lock` to version control
4. Use `--no-dev` flag when installing dependencies in production environment

## Troubleshooting:
If you encounter any issues:
1. Clear composer cache: `composer clear-cache`
2. Remove vendor directory and reinstall: 
```bash
rm -rf vendor/
composer install
```
3. Update composer itself: `composer self-update`



### Frontend
- HTML5, CSS3, JavaScript (ES6)
- Bootstrap 5/TailwindCSS
- AJAX (Fetch API & jQuery)

## 🚀 Getting Started

### Prerequisites
- PHP 8.x
- PostgreSQL
- Composer
- Web server (Apache/Nginx)

### Installation

1. Clone the repository
```bash
git clone https://github.com/Youcode-Classe-E-2024-2025/Espada-Eventbrite.git
cd Espada-Eventbrite
```

2. Install dependencies
```bash
composer install
```

3. Configure the database
- Create a PostgreSQL database
- Copy `.env.example` to `.env`
- Update database credentials in `.env`

4. Run database migrations
```bash
php migrations/run.php
```

5. Start the development server
```bash
php -S localhost:8240
```

### Registration and Account Creation
To use the platform:
1. Visit `http://localhost:8240`
2. Click on "Register" to create a new account
3. Choose your role (Organizer or Participant)
4. Complete the registration form
5. Verify your email (if implemented)

## 💡 Usage

After registration, you can:
- As a Participant:
  - Browse available events
  - Book tickets
  - Manage your bookings
  
- As an Organizer:
  - Create new events
  - Manage your events
  - View booking statistics
  
- As an Admin:
  - Manage users
  - Moderate events
  - View platform statistics

## 🔒 Security Features

- CSRF protection on all forms
- Prepared SQL statements via PDO
- Password hashing with bcrypt
- XSS protection
- Secure session management
- Input validation and sanitization

## 🎯 Performance Optimizations

- PostgreSQL query optimization with indexes
- AJAX-based lazy loading
- Efficient session handling
- Optimized database schema

## 👥 Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/AmazingFeature`
3. Commit your changes: `git commit -m 'Add some AmazingFeature'`
4. Push to the branch: `git push origin feature/AmazingFeature`
5. Open a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Team Espada

This project is developed by Team Espada as part of Youcode Class E 2024-2025.

## 📞 Support

For support, please create an issue in the [GitHub repository](https://github.com/Youcode-Classe-E-2024-2025/Espada-Eventbrite/issues)