# Sandawatha.lk 💑

<div align="center">

![Sandawatha Logo](public/assets/images/logo.svg)

[![Status](https://img.shields.io/badge/Status-In%20Development-yellow)](https://sandawatha.lk)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.4-blue)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

*The Modern Sri Lankan Matrimonial Platform* 🌟

[Live Demo](https://sandawatha.lk) • [Documentation](PROJECT_CONTEXT.md) • [Report Bug](https://github.com/yourusername/sandawatha/issues) • [Request Feature](https://github.com/yourusername/sandawatha/issues)

</div>

---

## 🚀 Quick Start

### Prerequisites

```bash
# Check PHP version (>= 7.4 required)
php -v

# Check MySQL version (>= 8.0 required)
mysql --version

# Check Node.js version (>= 14 required)
node -v

# Check NPM version
npm -v
```

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/sandawatha.git
cd sandawatha
```

2. **Set up the environment**
```bash
# Copy environment template
cp .env.example .env

# Update environment variables
nano .env
```

3. **Install dependencies**
```bash
# Install NPM packages
npm install

# Build assets
npm run build
```

4. **Set up the database**
```bash
# Run database migrations
php cli/migrate.php

# Seed initial data
php cli/seed.php
```

5. **Start the development server**
```bash
php cli/serve.php
```

Visit `http://localhost:8000` in your browser 🎉

## 🎯 Features

### Core Features
- 👥 User Profiles
- 💕 Match Making
- 🔮 Horoscope Matching
- 💬 Real-time Chat
- 🤖 AI-Powered Suggestions

### Premium Features
- ⭐ Advanced Search
- 💎 Priority Matching
- 🎁 Virtual Gifts
- 📊 Enhanced Analytics

## 🛠️ Development

### Directory Structure

```
📦 sandawatha
 ┣ 📂 api          # API Endpoints
 ┣ 📂 app          # Application Core
 ┣ 📂 cli          # CLI Tools
 ┣ 📂 config       # Configuration
 ┣ 📂 database     # Database Files
 ┣ 📂 public       # Public Assets
 ┣ 📂 setup        # Setup Scripts
 ┗ 📂 storage      # User Data
```

### Common Commands

```bash
# Start development server
php cli/serve.php

# Run database migrations
php cli/migrate.php

# Seed database
php cli/seed.php

# Build assets
npm run build

# Watch assets for changes
npm run watch
```

## 📚 Documentation

- [Project Context](PROJECT_CONTEXT.md)
- [API Documentation](docs/API.md)
- [Database Schema](docs/DATABASE.md)
- [Contributing Guide](CONTRIBUTING.md)

## 🔧 Configuration

### Database Configuration
```php
// config/database.php
return [
    'host' => getenv('DB_HOST', 'localhost'),
    'name' => getenv('DB_NAME', 'sandawatha'),
    'user' => getenv('DB_USER', 'root'),
    'pass' => getenv('DB_PASS', '')
];
```

### Server Requirements
- PHP >= 7.4
- MySQL >= 8.0
- Node.js >= 14
- Apache/Nginx
- mod_rewrite enabled

## 🤝 Contributing

We love your input! See our [Contributing Guide](CONTRIBUTING.md) for ways to get started.

### Development Process
1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Tailwind CSS](https://tailwindcss.com)
- [jQuery](https://jquery.com)
- [PHP](https://php.net)
- [MySQL](https://mysql.com)

## 📞 Support

Having trouble? Check out our:
- [FAQ](docs/FAQ.md)
- [Troubleshooting Guide](docs/TROUBLESHOOTING.md)
- [Issue Tracker](https://github.com/yourusername/sandawatha/issues)

---

<div align="center">

Made with ❤️ in Sri Lanka

[sandawatha.lk](https://sandawatha.lk) • [Twitter](https://twitter.com/sandawatha) • [Facebook](https://facebook.com/sandawatha)

</div>
