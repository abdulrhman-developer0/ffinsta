# FFInsta — Instagram Follow Exchange Platform

FFInsta is a modern Laravel application that facilitates an Instagram follow exchange. Users can link their Instagram accounts, complete "follow" tasks to earn points, and use those points to request followers for their own accounts.

## Features
- **Role-Based Access**: Separation between standard users and system administrators.
- **Points Economy**: Earn points by following others, spend points to receive followers.
- **Referral System**: Invite friends via custom links to earn bonus points.
- **Gift Coupons**: Admins can generate promotional codes for users to redeem points.
- **Task Verification**: Integrated rate limiting, anti-fraud self-follow prevention, and task expiration logic.
- **Real-Time Notifications**: Instant WebSocket-based alerts via Laravel Reverb.
- **Multilingual Support**: Fully localized in English and Arabic, including RTL (Right-to-Left) layout support.
- **Dark Mode**: Built-in, persistent dark mode support using Tailwind CSS class strategy.
- **Admin Dashboard**: Comprehensive stats, user management, order processing, and system settings.

## Tech Stack
- **Framework**: [Laravel 11.x](https://laravel.com) / PHP 8.2+
- **Frontend**: [Livewire 3](https://livewire.laravel.com), [Tailwind CSS 3](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev)
- **Database**: MySQL / MariaDB
- **WebSockets**: [Laravel Reverb](https://reverb.laravel.com)

---

## Installation & Setup

1. **Clone the repository**
   ```bash
   git clone <repo-url>
   cd ffinsta
   ```

2. **Install Composer dependencies**
   ```bash
   composer install
   ```

3. **Install NPM dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   Copy the example environment file and generate an app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Configuration**
   Update the `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ffinsta
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run Migrations & Seeders**
   This will set up the database schema and populate it with initial settings and an admin user.
   ```bash
   php artisan migrate:fresh --seed
   ```
   > **Note:** The default admin credentials (if using the `AdminSeeder`) will be printed in the console or available in the seeder file.

7. **Build Frontend Assets**
   ```bash
   npm run build
   ```

## Local Development Execution

To run the application locally, you need three separate terminal windows running simultaneously:

**Terminal 1: PHP Development Server**
```bash
php artisan serve
```

**Terminal 2: Vite Asset Compilation (Hot Reloading)**
```bash
npm run dev
```

**Terminal 3: Laravel Reverb (WebSockets)**
```bash
php artisan reverb:start
```

---

## Scheduled Tasks (Cron)

The application relies on Laravel's scheduler to release expired "pending" tasks back into the pool.

In your production server, add the following Cron entry to run every minute:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

For local testing, you can run the schedule worker:
```bash
php artisan schedule:work
```

## Queue Workers

If you configure a database or Redis queue (for sending emails or processing heavy background jobs), ensure you have a queue worker running:
```bash
php artisan queue:work
```

---

## Security & Rate Limiting

- **Auth Throttling**: Login, registration, and password resets are rate-limited to prevent brute-force attacks.
- **Race Conditions**: Database transactions use pessimistic locking (`lockForUpdate()`) to ensure points balances are never corrupted by concurrent requests.

## Localization

To change the default language, update the `APP_LOCALE` variable in the `.env` file:
```env
APP_LOCALE=ar
```
The application will automatically switch to RTL (Right-to-Left) layout when Arabic is selected.

---

## License
Proprietary software. SOFTINGY All rights reserved.
