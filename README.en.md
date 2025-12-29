Chọn Ngôn ngữ / Choose Language / 言語を選択 / Выберите язык

- 🇻🇳 [Tiếng Việt](README.md)
- 🇺🇸 English (current)
- 🇯🇵 [日本語](README.jp.md)
- 🇷🇺 [Русский](README.ru.md)




<p align="center">
  <h1 align="center">Fruitables</h1>
  <p align="center">
    This is a starter kit project built on Laravel 12 + Vue 3 + Inertia.js, aimed at rapid development of modern web applications with SPA architecture while maintaining the power of Laravel on the backend.
    <br />
    <strong>View Website » <a href="https://fruitable.site/">https://fruitable.site</a></strong>
  </p>
</p>

---

## Table of Contents
<details>
  <summary>Click to expand</summary>
  <ol>
    <li><a href="#overview">Project Overview</a></li>
    <li><a href="#features">Features</a></li>
    <li><a href="#advancedfeatures">Advanced Features</a></li>
    <li><a href="#technology">Technologies Used</a></li>
    <li><a href="#database">Database</a></li>
    <li><a href="#setup">Installation Guide</a></li>
    <li><a href="#docker">Docker</a></li>
    <li><a href="#status">Status & Roadmap</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>

---

## Project Objectives <a id="objectives"></a>

**Fruitables** is a personal project created with the purpose of **learning and practicing modern web technologies**, especially the **Laravel + Vue (Inertia)** ecosystem.
The project focuses on building **an e-commerce website for selling fruits**, where users can:

- Browse products
- Add items to cart
- Place orders and make online payments

Currently, the project is in **demo / development status**, focusing mainly on **user-side functionality**.

---

## Features <a id="features"></a>

### User Features
- Register / login account
- View product list
- View product details
- Manage shopping cart
- Place orders
- Online payment
- Manage delivery addresses
- Review products
- Save favorite products (wishlist)
- Receive user notifications

## Advanced Features <a id="advancedfeatures"></a>
- User authentication
- Multi-language support
- API protection with Sanctum
- Online payment (SePay)
- Session & queue management using database





---

> **Admin** features are currently under development and **not yet included in the README scope**.

---

## Entity Relationship Diagram (ERD) <a id="erd"></a>

![Entity Relationship Diagram](https://github.com/TienDung02/Fruitables/blob/main/ERD.png)

---

## Technologies Used <a id="technology"></a>

### Backend
- **Laravel 12**
- PHP **8.2**
- Laravel Sanctum (Authentication)
- Queue, Session, Cache: **Database**
- Thanh toán online: **SePay**

### Frontend
- **Vue 3**
- **Inertia.js**
- **Vite**
- Tailwind CSS
- Bootstrap 5
- Pinia (state management)
- Vue I18n
- Axios

### Database
- MySQL / MariaDB

---

## Laravel Project Installation Guide <a id="setup"></a>

### System Requirements
Before starting, ensure you have installed:

- **PHP** >= 8.2
- **Composer**
- **MySQL / MariaDB**
- **Node.js & npm**
- **MySQL or MariaDB**
- **Git**

---

### Step 1: Clone the project
```bash
https://github.com/TienDung02/Fruitables.git
```
```bash
cd Fruitables
```

### Step 2: Install backend
```bash
composer install
```
### Step 3: Configure environment
```bash
cp .env.example .env
```

```bash
php artisan key:generate
```
Edit .env:
```bash
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```
- Can be skipped if not used.
```bash
# Bank Account Configuration linked to Sepay (Optional)
BANK_ACCOUNT_NUMBER=
BANK_ACCOUNT_NAME=
BANK_CODE=
BANK_NAME=
```
```bash
# SEPAY Configuration (Optional)
SEPAY_API_TOKEN=
SEPAY_SECRET_KEY=
SEPAY_ACCOUNT_NUMBER=
SEPAY_BANK_CODE=
SEPAY_ACCOUNT_NAME=
```
```bash
# Email Configuration with Resend (Optional)
MAIL_MAILER=resend 
RESEND_KEY=
MAIL_FROM_ADDRESS="no-reply@fruitable.site"
MAIL_FROM_NAME="${APP_NAME}"
```
### Step 4: Migrate & Seed
Create necessary tables
```bash
php artisan migrate
```
Generate sample data for newly created tables
```bash
php artisan db:seed
```

### Step 5: Install frontend
```bash
npm install
```

### Step 6: Build frontend
Run for production / local demo
```bash
npm run build
```
- Build JS/CSS vào public/build
- No need to run dev server → suitable for low RAM machines



Run for development
```bash
npm run dev
```
```bash
php artisan serve
```
- Start Vite dev server with hot reload
- Only use when continuously changing frontend code
### Step 7: Run the application
```bash
php artisan serve --host=localhost --port=8000
```
Open browser: Access http://localhost:8000

## Docker <a id="docker"></a>
### Requirements
- Docker >= 24
- Docker Compose (optional for multi-container)
- Port 8080 available on host machine

### Docker Structure
Project uses multi-stage Docker build:
1. Stage 1 – Backend build: Install Composer, install PHP dependencies, copy all source.
2. Stage 2 – Frontend build: Use Node 20 + npm build assets (Vite + Tailwind).
3. Stage 3 – Runtime: PHP-FPM + Nginx + supervisord running concurrently, serving Laravel + frontend.

### Installation & Running
1. Build image:
```bash
docker build -t fruitables:latest .
```
2. Remove old containers/images (if needed):
```bash
docker container prune
```
```bash
docker image prune -a
```
3. Run container:
```bash
docker run -it -p 8080:80 --name fruitables fruitables:latest
```
4. Enter container for first-time Laravel setup:
```bash
docker exec -it fruitables sh
```
5. Create .env file and generate app key:
```bash
cp .env.example .env
```
```bash
php artisan key:generate
```
6. Set permissions:
```bash
chown -R www-data:www-data storage bootstrap/cache
```
```bash
chmod -R 775 storage bootstrap/cache
```
7. Clear cache (if needed):
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```
8. Open browser: access http://localhost:8080.

### Notes
- If encountering 500 Server Error, check .env file, database config and storage/bootstrap/cache permissions.
- Frontend already built in /public/build. No need to run npm run dev in runtime container.

### Important Directories
- app/: Code Laravel
- resources/: Frontend resources (CSS, JS, Vue)
- public/build: Frontend build output
- storage/, bootstrap/cache/: cache & logs
- docker/: Nginx, supervisord, php.ini configuration

## Status & Roadmap <a id="status"></a>
Current Status:
- DEMO / In Development

Future Plans:
- Optimize security & authorization
- Complete admin management system
- Improve performance & UX

## Contact <a id="contact"></a>
- Project Name: Fruitables
- Purpose: Learning / Personal Project
- Email: nongtiendung2309@gmail.com
