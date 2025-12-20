@echo off
echo ============================================
echo      FRUITABLES DOCKER MANAGEMENT
echo ============================================

if "%1"=="build" goto build
if "%1"=="up" goto up
if "%1"=="down" goto down
if "%1"=="logs" goto logs
if "%1"=="shell" goto shell
if "%1"=="migrate" goto migrate
if "%1"=="seed" goto seed
if "%1"=="fresh" goto fresh
if "%1"=="rebuild" goto rebuild
if "%1"=="clean" goto clean
if "%1"=="optimize" goto optimize
goto help

:build
echo Building Docker images...
docker-compose build --no-cache
goto end

:up
echo Starting Docker containers...
docker-compose up -d
echo Waiting for MySQL to be ready...
:wait_mysql_up
docker-compose exec db mysqladmin ping -h localhost -u root -proot --silent 2>nul
if errorlevel 1 (
    echo MySQL not ready yet, waiting 3 seconds...
    timeout /t 3 /nobreak > nul
    goto wait_mysql_up
)
echo MySQL is ready! Running migrations...
docker-compose exec app php artisan migrate --force
echo Linking storage...
docker-compose exec app php artisan storage:link
echo Application is ready at http://localhost:8080
goto end

:down
echo Stopping Docker containers...
docker-compose down
goto end

:logs
echo Showing logs...
docker-compose logs -f
goto end

:shell
echo Connecting to app container...
docker-compose exec app sh
goto end

:migrate
echo Running migrations...
docker-compose exec app php artisan migrate --force
goto end

:seed
echo Running seeders...
docker-compose exec app php artisan db:seed --force
goto end

:fresh
echo Stopping containers completely...
docker-compose down -v --remove-orphans
echo Force removing any remaining containers...
docker rm -f fruitables-app fruitables-db fruitables-mysql fruitables-redis 2>nul || echo No containers to remove
echo Removing database volumes...
docker volume rm fruitables_mysql_data fruitables_redis_data 2>nul || echo Some volumes not found
echo Starting containers with fresh database...
docker-compose up -d
echo Waiting for MySQL to be ready...
:wait_mysql_fresh
docker-compose exec db mysqladmin ping -h localhost -u root -proot --silent 2>nul
if errorlevel 1 (
    echo MySQL not ready yet, waiting 3 seconds...
    timeout /t 3 /nobreak > nul
    goto wait_mysql_fresh
)
echo MySQL is ready! Running fresh migrations...
docker-compose exec app php artisan migrate:fresh --seed --force
echo Linking storage...
docker-compose exec app php artisan storage:link
echo Fresh setup completed! Application ready at http://localhost:8080
goto end

:rebuild
echo Stopping and removing containers completely...
docker-compose down -v --remove-orphans
echo Force removing any remaining containers...
docker rm -f fruitables-app fruitables-db fruitables-mysql fruitables-redis 2>nul || echo No containers to remove
echo Removing all project volumes...
docker volume rm fruitables_mysql_data fruitables_redis_data 2>nul || echo Some volumes not found
echo Building Docker images from scratch...
docker-compose build --no-cache
echo Starting containers with fresh database...
docker-compose up -d
echo Waiting for MySQL to be ready...
:wait_mysql_rebuild
docker-compose exec db mysqladmin ping -h localhost -u root -proot --silent 2>nul
if errorlevel 1 (
    echo MySQL not ready yet, waiting 3 seconds...
    timeout /t 3 /nobreak > nul
    goto wait_mysql_rebuild
)
echo MySQL is ready! Running fresh migrations...
docker-compose exec app php artisan migrate:fresh --force
echo Running seeders...
docker-compose exec app php artisan db:seed --force
echo Linking storage...
docker-compose exec app php artisan storage:link
echo Complete rebuild finished! Application ready at http://localhost:8080
goto end

:clean
echo Stopping and removing containers...
docker-compose down
echo Removing all stopped containers...
docker rm -f $(docker ps -aq) 2>nul || echo No containers to remove
echo Removing all unused volumes...
docker volume prune -f
echo Cleaning up dangling images...
docker image prune -f
echo Cleanup completed!
goto end

:optimize
echo Optimizing application...
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan optimize
goto end

:help
echo Usage: docker.bat [command]
echo.
echo Commands:
echo   build     - Build Docker images
echo   up        - Start containers and run migrations
echo   down      - Stop containers
echo   logs      - Show container logs
echo   shell     - Access app container shell
echo   migrate   - Run database migrations
echo   seed      - Run database seeders
echo   fresh     - Stop containers, clear database, restart and migrate fresh
echo   rebuild   - Complete rebuild with fresh database and images
echo   clean      - Stop and remove containers, remove unused volumes and dangling images
echo   optimize  - Optimize Laravel application
echo.

:end
