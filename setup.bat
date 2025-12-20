@echo off
echo ============================================
echo      SETUP FRUITABLES PROJECT
echo ============================================

echo Step 1: Installing PHP dependencies with Docker...
docker run --rm -v "%cd%":/app -w /app composer:latest composer install --no-dev --optimize-autoloader

echo.
echo Step 2: Installing Node.js dependencies...
docker run --rm -v "%cd%":/app -w /app node:20-alpine npm install

echo.
echo Step 3: Building frontend assets...
docker run --rm -v "%cd%":/app -w /app node:20-alpine npm run build

echo.
echo Step 4: Building Docker images...
docker-compose build

echo.
echo Step 5: Starting containers...
docker-compose up -d

echo.
echo Step 6: Setting up database...
timeout /t 10 /nobreak > nul
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan storage:link

echo.
echo ============================================
echo     SETUP COMPLETE!
echo ============================================
echo Website: http://localhost:8000
echo Database: localhost:3306 (user: laravel, password: secret123)
echo.
echo Use these commands to manage:
echo   docker.bat logs     - View logs
echo   docker.bat shell    - Access container
echo   docker.bat down     - Stop containers
echo ============================================
