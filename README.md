# School Result Management System — Laravel + MySQL

This repository is the Laravel/PHP + MySQL port of School-Result-System-New. It reuses the original HTML, CSS and JavaScript pages so the screen layout, navigation and user workflows remain familiar. The Node/Express/PostgreSQL backend has been replaced with Laravel routes, controllers, MySQL migrations and Laravel sessions.

## Included workflows

- Admin and Teacher accounts, role checks, password change/reset and active/inactive users
- Classes, subjects and exams
- Individual result create/edit, marks validation and GPA-5 grading
- Fourth-subject bonus rule (extra grade points above 2; GPA capped at 5)
- Draft/publish/unpublish, single and whole-exam operations
- Excel .xlsx preview/import, duplicate skip/update and template download
- Public published-result lookup, QR verification URL and PDF marksheet
- Merit list, tabulation, statistics, class PDF marksheets
- CSV/Excel export, activity log and JSON database backup

The original grading thresholds and API response field names are retained where the front-end depends on them.

## Local setup

Requirements: PHP 8.2+, Composer 2, MySQL 8+, and PHP extensions pdo_mysql, mbstring, xml, gd, zip, intl.

1. Run composer install.
2. Copy .env.example to .env.
3. Run php artisan key:generate.
4. Create an empty MySQL database and set DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env.
5. Set ADMIN_USERNAME=admin and ADMIN_PASSWORD to a unique password of at least 8 characters.
6. Run php artisan migrate --seed.
7. Run php artisan serve and open http://127.0.0.1:8000.

Remove ADMIN_PASSWORD from the environment after the first admin account is created. Never commit .env or real credentials.

## Render deployment

The repository includes Dockerfile, docker-entrypoint.sh, and render.yaml. Create a Render Web Service from this repository and provide a reachable external MySQL 8 database. Render's standard web service does not provide a managed MySQL database automatically.

Set these Render environment variables:

- APP_KEY: generate locally with php artisan key:generate --show and paste the full value
- APP_URL: the deployed HTTPS URL
- DB_CONNECTION=mysql
- DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD: from the external MySQL provider
- ADMIN_USERNAME, ADMIN_PASSWORD: initial admin credentials (password at least 8 characters)
- APP_ENV=production, APP_DEBUG=false
- Optional: SCHOOL_NAME, SCHOOL_ADDRESS, SCHOOL_EIIN, MERIT_MODE=marks|gpa, VERIFY_SECRET, SESSION_SECRET, PUBLIC_BASE_URL

The container runs migrations and the initial database seeder at startup. Keep APP_KEY and VERIFY_SECRET stable after generating/printing QR-enabled marksheets.

## Important

- This is a framework conversion, not a copy of the original Node.js runtime. The original public UI files are carried over; the backend is Laravel and the database schema is MySQL.
- The target repository is a fresh application. Existing PostgreSQL data is not automatically copied. If you need to migrate live student records from the Node/PostgreSQL database, export and import them as a separate controlled migration.
- Run a real browser test after deployment for login, adding a class/subject/exam, individual result, bulk Excel import, publish, public search, PDF and exports before using it for an actual examination.
