# School Result Management System — Laravel + MySQL

Laravel conversion of the original School Result System. The interface and workflows are being ported from the Node.js reference application.

## Requirements
- PHP 8.2+
- Composer 2
- MySQL 8+

## Setup
1. `composer install`
2. Copy `.env.example` to `.env`
3. Set `APP_KEY` with `php artisan key:generate`
4. Configure MySQL in `.env`
5. `php artisan migrate --seed`
6. `php artisan serve`

Default admin must be created using environment variables `ADMIN_USERNAME` and `ADMIN_PASSWORD` during initial migration. Never commit real credentials.

## Features
- Classes, subjects, exams
- Individual and Excel bulk result entry
- Bangladesh GPA-5 grading and 4th-subject bonus rules
- Draft/publish workflow, role-based admin and teacher access
- Merit list, tabulation, statistics, marksheets and exports
- Activity log and database backup

See deployment notes below before deploying to production.
