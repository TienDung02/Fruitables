Chọn Ngôn ngữ / Choose Language / 言語を選択 / Выберите язык

- 🇻🇳 Tiếng Việt (mặc định)
- 🇺🇸 [English](README.en.md)
- 🇯🇵 [日本語](README.ja.md)
- 🇷🇺 [Русский](README.ru.md)





<p align="center">
  <h1 align="center">Fruitables</h1>
  <p align="center">
    Đây là dự án starter kit xây dựng trên Laravel 12 + Vue 3 + Inertia.js, hướng tới việc phát triển nhanh các ứng dụng web hiện đại với kiến trúc SPA nhưng vẫn giữ được sức mạnh của Laravel phía backend.
    <br />
    <strong>Xem website » <a href="https://fruitable.site/">https://fruitable.site</a></strong>
  </p>
</p>

---

## Mục lục
<details>
  <summary>Nhấn để mở</summary>
  <ol>
    <li><a href="#overview">Giới thiệu dự án</a></li>
    <li><a href="#features">Chức năng</a></li>
    <li><a href="#advancedfeatures">Tính năng nâng cao</a></li>
    <li><a href="#technology">Công nghệ sử dụng</a></li>
    <li><a href="#database">Cơ sở dữ liệu</a></li>
    <li><a href="#setup">Hướng dẫn cài đặt</a></li>
    <li><a href="#docker">Docker</a></li>
    <li><a href="#status">Trạng thái & định hướng</a></li>
    <li><a href="#contact">Liên hệ</a></li>
  </ol>
</details>

---

## Mục tiêu dự án <a id="objectives"></a>

**Fruitables** là một dự án cá nhân được thực hiện với mục đích **học tập và thực hành các công nghệ web hiện đại**, đặc biệt là hệ sinh thái **Laravel + Vue (Inertia)**.

Dự án tập trung vào việc xây dựng **một website thương mại điện tử bán trái cây**, trong đó người dùng có thể:
- Duyệt sản phẩm
- Thêm vào giỏ hàng
- Đặt hàng và thanh toán online

Hiện tại, dự án đang ở trạng thái **demo / đang phát triển**, tập trung chủ yếu vào **chức năng phía người dùng (user)**.


---

## Chức năng <a id="features"></a>

### Người dùng (User)
- Đăng ký / đăng nhập tài khoản
- Xem danh sách sản phẩm
- Xem chi tiết sản phẩm
- Quản lý giỏ hàng
- Đặt hàng
- Thanh toán online
- Quản lý địa chỉ giao hàng
- Đánh giá sản phẩm
- Lưu sản phẩm yêu thích (wishlist)
- Nhận thông báo người dùng

## Tính năng nổi bật <a id="advancedfeatures"></a>
- Xác thực người dùng
- Đa ngôn ngữ
- Bảo vệ API bằng Sanctum
- Thanh toán online (SePay)
- Quản lý session & queue bằng database

---

> Chức năng **admin** hiện đang trong quá trình phát triển và **chưa được đưa vào phạm vi README**.

---

## Sơ đồ thực thể - quan hệ (ERD) <a id="erd"></a>

![Entity Relationship Diagram](https://github.com/TienDung02/Fruitables/blob/main/ERD.png)

---

## Công nghệ sử dụng <a id="technology"></a>

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
- SQLite (mặc định cho môi trường local)
- Có thể chuyển sang MySQL / MariaDB

---

## Hướng dẫn cài đặt dự án Laravel <a id="setup"></a>

### Yêu cầu hệ thống

Trước khi bắt đầu, hãy đảm bảo bạn đã cài đặt:

- **PHP** >= 8.2
- **Composer**
- **MySQL / MariaDB**
- **Node.js & npm**
- **MySQL hoặc MariaDB**
- **Git**

---

### Bước 1: Clone dự án
```bash
https://github.com/TienDung02/Fruitables.git
```
```bash
cd Fruitables
```

### Bước 2: Cài đặt backend
```bash
composer install
```
### Bước 3: Cấu hình môi trường
```bash
cp .env.example .env
```

```bash
php artisan key:generate
```
Chỉnh .env:
```bash
# Cấu hình Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```
- Có thể bỏ quá nếu không sử dụng. 
```bash
# Cấu hình Bank Account liên kết Sepay (Optional)
BANK_ACCOUNT_NUMBER=
BANK_ACCOUNT_NAME=
BANK_CODE=
BANK_NAME=
```
```bash
# Cấu hình SEPAY (Optional)
SEPAY_API_TOKEN=
SEPAY_SECRET_KEY=
SEPAY_ACCOUNT_NUMBER=
SEPAY_BANK_CODE=
SEPAY_ACCOUNT_NAME=
```
```bash
# Cấu hình gửi mail với Resend (Optional)
MAIL_MAILER=resend 
RESEND_KEY=
MAIL_FROM_ADDRESS="no-reply@fruitable.site"
MAIL_FROM_NAME="${APP_NAME}"
```
### Bước 4: Migrate & Seed
Tạo các bảng cần thiết
```bash
php artisan migrate
```
Tạo dữ liệu mẫu cho các bảng vừa tạo
```bash
php artisan db:seed
```

### Bước 5: Cài đặt frontend
```bash
npm install
```

### Bước 6: Build frontend
Chạy cho production / demo local
```bash
npm run build
```
- Build JS/CSS vào public/build
- Không cần chạy dev server → phù hợp máy RAM thấp



Chạy cho phát triển (dev)
```bash
npm run dev
```
```bash
php artisan serve
```
- Khởi động Vite dev server với hot reload
- Chỉ dùng khi muốn thay đổi code frontend liên tục
### Bước 7: Chạy ứng dụng
```bash
php artisan serve --host=localhost --port=8000
```
Truy cập: http://localhost:8000

## Docker <a id="docker"></a>
### Yêu cầu
- Docker >= 24
- Docker Compose (tuỳ chọn nếu muốn multi-container)
- Port 8080 trống trên máy host

### Cấu trúc Docker
Project sử dụng multi-stage Docker build:
1. Stage 1 – Backend build: Cài Composer, cài dependencies PHP, copy toàn bộ source.
2. Stage 2 – Frontend build: Dùng Node 20 + npm build assets (Vite + Tailwind).
3. Stage 3 – Runtime: PHP-FPM + Nginx + supervisord chạy song song, serve Laravel + frontend.

### Cài đặt & chạy
1. Build image:
```bash
docker build -t fruitables:latest .
```
2. Xoá các container/image cũ (nếu cần):
```bash
docker container prune
```
```bash
docker image prune -a
```
3. Chạy container:
```bash
docker run -it -p 8080:80 --name fruitables fruitables:latest
```
4. Vào container để cài đặt Laravel lần đầu:
```bash
docker exec -it fruitables sh
```
5. Tạo file .env và sinh app key:
```bash
cp .env.example .env
```
```bash
php artisan key:generate
```
6. Đặt quyền:
```bash
chown -R www-data:www-data storage bootstrap/cache
```
```bash
chmod -R 775 storage bootstrap/cache
```
7. Clear cache (nếu cần):
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```
8. Mở trình duyệt: truy cập http://localhost:8080.

### Lưu ý
- Nếu gặp lỗi 500 Server Error, kiểm tra file .env, database config và permissions storage/bootstrap/cache.
- Frontend đã build sẵn trong /public/build. Không cần chạy npm run dev trong container runtime.

### Thư mục quan trọng
- app/: Code Laravel
- resources/: Frontend resources (CSS, JS, Vue)
- public/build: Frontend build output
- storage/, bootstrap/cache/: cache & logs
- docker/: cấu hình Nginx, supervisord, php.ini

## Trạng thái & định hướng <a id="status"></a>
Trạng thái hiện tại:
- DEMO / đang phát triển

Định hướng tiếp theo:
- Tối ưu bảo mật & phân quyền
- Hoàn thiện hệ thống quản trị (admin)
- Cải thiện hiệu năng & UX

## Liên hệ <a id="contact"></a>
- docker/: cấu hình Nginx, supervisord, php.ini
- Tên dự án: Fruitables
- Mục đích: Học tập / đồ án cá nhân
- Email: nongtiendung2309@gmail.com

