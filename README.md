<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## Daret App Setup & Usage

This project implements a Moroccan-style rotating savings circle ("daret") on top of Laravel and Breeze.

### Requirements

- PHP 8.2+
- Composer
- Node.js & NPM
- A database supported by Laravel (MySQL, PostgreSQL, SQLite, etc.)

### Installation

1. Install PHP dependencies:

   ```bash
   composer install
   ```

2. Install front-end assets:

   ```bash
   npm install
   npm run build   # or `npm run dev` during development
   ```

3. Create your environment file and generate an app key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure the following in `.env`:

- `DB_*` for your database connection
- `QUEUE_CONNECTION` (e.g. `database` for queued PDF stamping & notifications)
- `MAIL_MAILER` and related mail settings (for email notifications)
- `FILESYSTEM_DISK=public` (for media uploads)

5. Run migrations and seed demo data (admin and user accounts, roles):

   ```bash
   php artisan migrate --seed
   ```

6. Serve the application and run the queue worker:

   ```bash
   php artisan serve
   php artisan queue:work
   ```

You can then log in using the seeded accounts:

- Admin: `admin@example.com` / `password`
- User: `user@example.com` / `password`

### Core Features

- User registration & login (Breeze), with email verification
- Roles & permissions using Spatie Permission (`admin`, `user`)
- Profile management with phone, city, bio, avatar, and Revolut QR image uploads
- Create & join darets with:
  - Contribution amount
  - Period (weekly or monthly)
  - Total members
  - Start date & auto-generated cycles
- Cycle tracking with per-cycle recipient and progress
- Upload PDF bank transfer receipts per member per cycle
- Confirmation / rejection workflow by owner or admin, with optional admin override
- Queued PDF stamping of confirmed receipts using DomPDF
- Email + in-app notifications when receipts are uploaded or their status changes
- JSON API endpoints for darets, contributions, and member profiles

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
