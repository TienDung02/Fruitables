# Fruitables Docker Setup

## Cấu trúc Docker

Dự án sử dụng Docker multi-container với:
- **App Container**: Laravel 12 + PHP 8.2 + Nginx
- **Database**: MySQL 8.0
- **Cache**: Redis 7

## Cách sử dụng

### 1. Build và chạy lần đầu:
```bash
# Build images
docker.bat build

# Start containers và chạy migrations
docker.bat up
```

### 2. Các lệnh quản lý:
```bash
# Xem logs
docker.bat logs

# Truy cập shell container
docker.bat shell

# Chạy migrations
docker.bat migrate

# Chạy seeders
docker.bat seed

# Fresh migrate + seed
docker.bat fresh

# Tối ưu hóa Laravel
docker.bat optimize

# Dừng containers
docker.bat down
```

### 3. Truy cập ứng dụng:
- **Website**: http://localhost:8000
- **Database**: localhost:3306 (user: laravel, password: secret123)

## Cấu hình quan trọng

### Environment Variables (.env.docker)
- Database: MySQL container với user `laravel`
- Cache: Redis container
- Session: Lưu trong Redis
- Queue: Sử dụng Redis

### Volumes
- `storage/app` và `storage/logs` được mount để persist data
- MySQL data được lưu trong volume `mysql_data`
- Redis data được lưu trong volume `redis_data`

### Ports
- **8000**: Laravel application
- **3306**: MySQL database
- **6379**: Redis (internal)

## Troubleshooting

### 1. Nếu có lỗi permission:
```bash
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chmod -R 755 /var/www/storage
```

### 2. Nếu cần rebuild hoàn toàn:
```bash
docker-compose down -v
docker.bat build
docker.bat up
```

### 3. Kiểm tra logs lỗi:
```bash
# Logs tất cả services
docker.bat logs

# Logs specific service
docker-compose logs app
docker-compose logs db
```

### 4. Truy cập database:
```bash
# Từ host machine
mysql -h 127.0.0.1 -P 3306 -u laravel -p fruitables

# Từ app container
docker-compose exec app mysql -h db -u laravel -p fruitables
```

### 5. Clear cache:
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear
```

## Production Notes

Dockerfile đã được tối ưu cho production với:
- Multi-stage build để giảm kích thước image
- Cached dependencies
- Optimized Laravel (config, route, view cache)
- Security headers
- Gzip compression
- Static file caching

Để deploy production, chỉ cần thay đổi environment variables trong docker-compose.yml.
