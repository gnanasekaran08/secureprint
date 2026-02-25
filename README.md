# PrintSecure

A secure print job management system built with Laravel and Svelte. Manage print shops, track print jobs with OTP verification, and handle document attachments securely.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Svelte 5, Inertia.js v2
- **Styling:** Tailwind CSS v4, DaisyUI
- **Authentication:** Laravel Fortify with 2FA support
- **Testing:** Pest

## Requirements

- PHP 8.2+
- Node.js 18+
- Composer
- SQLite/MySQL/PostgreSQL

## Installation

```bash
# Clone the repository
git clone https://github.com/gnanasekaran08/secureprint.git
cd printsecure

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate

# Build assets
npm run build
```

## Development

```bash
# Start development server
composer run dev

# Or run separately
php artisan serve
npm run dev
```

## Testing

```bash
php artisan test
```

## Code Quality

```bash
# Format PHP code
vendor/bin/pint

# Format frontend code
npm run format

# Lint frontend code
npm run lint

# Type check Svelte
npm run check
```

## License

MIT
