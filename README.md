# SkillSwap - Modern Platform of Learning

A modern web application for skill exchange and learning, optimized for Mac and MAMP development environment.

## 🚀 Features

- **User Authentication**: Secure login and registration system
- **Skill Management**: Offer and seek skills with detailed descriptions
- **Category System**: Organized skill categorization
- **Messaging System**: Direct communication between users
- **Responsive Design**: Optimized for Mac and mobile devices
- **Dark/Light Theme**: Automatic theme switching with Mac preferences
- **Modern UI**: Clean, professional interface with Mac-specific optimizations

## 📁 Project Structure

```
PW-V_G01/
├── assets/                 # Static assets
│   ├── css/
│   │   └── style.css      # Main stylesheet with Mac optimizations
│   ├── js/
│   │   └── main.js        # JavaScript functionality
│   └── images/
│       ├── Logo/          # Application logos
│       └── front_page_background.png
├── config/                # Configuration files
│   ├── config.php        # Centralized configuration
│   └── connect.php       # Database connection handler
├── database/             # Database files
│   ├── complete_database_reset.sql
│   └── skillswap_db.sql
├── includes/             # Reusable PHP components
│   ├── header.php       # Common header
│   └── footer.php       # Common footer
├── pages/               # Additional pages (future use)
├── index.php           # Login page
├── dashboard.php       # Main dashboard
├── signup.php          # User registration
├── offer_skill.php     # Skill offering/seeking
├── view_skill.php      # Skill details
├── messages.php        # Messaging system
├── logout.php          # User logout
├── setup_database.php  # Database setup
└── reset_database.php  # Database reset
```

## 🛠️ Setup Instructions

### Prerequisites
- MAMP (Apache + MySQL + PHP)
- Modern web browser (Safari, Chrome, Firefox)

### Installation

1. **Clone/Download** the project to your MAMP htdocs folder
2. **Start MAMP** services (Apache and MySQL)
3. **Create Database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import `database/skillswap_db.sql` or run `setup_database.php`
4. **Configure** (if needed):
   - Edit `config/config.php` for custom settings
5. **Access** the application at `http://localhost/PW-V_G01`

### Database Setup Options

**Option 1: Manual Setup**
```sql
-- Import database/skillswap_db.sql in phpMyAdmin
```

**Option 2: Automated Setup**
```
http://localhost/PW-V_G01/setup_database.php
```

**Option 3: Complete Reset with Sample Data**
```
http://localhost/PW-V_G01/reset_database.php
```

## 🎨 Mac-Specific Optimizations

### CSS Features
- **Responsive Breakpoints**: Optimized for MacBook Air, MacBook Pro, iPad
- **High DPI Support**: Crisp graphics on Retina displays
- **Smooth Scrolling**: Enhanced trackpad scrolling experience
- **Hover States**: Mac-specific hover effects
- **Color Scheme**: Automatic dark/light mode detection

### JavaScript Features
- **Mac Detection**: Platform-specific optimizations
- **Theme Persistence**: Remembers user theme preference
- **Smooth Animations**: Optimized for Mac performance
- **Touch Gestures**: Enhanced trackpad support

## 🔧 Configuration

### Database Settings
Edit `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); // MAMP default
define('DB_NAME', 'skillswap');
```

### Security Settings
```php
define('PASSWORD_MIN_LENGTH', 6);
define('SESSION_TIMEOUT', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
```

## 📱 Responsive Design

The application is optimized for:
- **MacBook Air 11"** (1366px and below)
- **MacBook Pro 13"** (1440px and below)
- **MacBook Air 13"** (1280px and below)
- **iPad** (1024px and below)
- **iPhone** (768px and below)

## 🔒 Security Features

- **Prepared Statements**: SQL injection prevention
- **Password Hashing**: Secure password storage
- **Session Management**: Secure session handling
- **Input Validation**: Comprehensive form validation
- **XSS Protection**: Output escaping

## 🚀 Performance Optimizations

- **CSS Variables**: Efficient theming system
- **Resource Preloading**: Faster page loads
- **Optimized Images**: Compressed assets
- **Minimal JavaScript**: Lightweight interactions
- **Efficient Queries**: Optimized database operations

## 🎯 Default Login Credentials

After running the complete database reset:
- **Email**: john.smith@email.com
- **Password**: password

## 📞 Support

For issues or questions:
1. Check the database connection in `config/config.php`
2. Ensure XAMPP services are running
3. Verify database exists and has proper permissions
4. Check browser console for JavaScript errors

## 🔄 Updates

To update the database with fresh sample data:
```
http://localhost/PW-V_G01/reset_database.php
```

---

**SkillSwap** - Modern Learning Platform for Skill Exchange
Optimized for Mac and MAMP Development Environment
