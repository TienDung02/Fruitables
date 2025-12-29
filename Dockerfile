########## STAGE 1: BACKEND BUILD ##########
FROM php:8.2-cli-alpine AS backend

WORKDIR /app

# Cài các tools cần thiết, bao gồm git để composer fallback
RUN apk add --no-cache bash curl git libpng-dev oniguruma-dev libxml2-dev mysql-client zip unzip

# Cài Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer files và cài dependencies
COPY composer.json composer.lock ./

# Copy toàn bộ source code
COPY . .

# Cài dependencies PHP
RUN composer install --no-dev --optimize-autoloader --prefer-dist

########## STAGE 2: FRONTEND BUILD ##########
FROM node:20-alpine AS frontend

WORKDIR /app

# Copy những thứ cần thiết từ backend
COPY --from=backend /app/resources resources
COPY --from=backend /app/vendor vendor
COPY --from=backend /app/public/lib public/lib
COPY package*.json ./
COPY vite.config.* tailwind.config.* postcss.config.* ./

RUN npm install
RUN npm run build

########## STAGE 3: RUNTIME ##########
FROM php:8.2-fpm-alpine

WORKDIR /var/www

# Cài các extension và tools cần thiết, bao gồm libzip-dev cho zip extension
RUN apk add --no-cache \
    libpng-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    zlib-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    bash \
    curl \
    git \
    mysql-client \
    nginx \
    supervisor \
    zip \
    unzip

# Cài PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    bcmath \
    gd \
    intl \
    zip

# Copy backend
COPY --from=backend /app /var/www

# Copy built frontend
COPY --from=frontend /app/public/build /var/www/public/build

# Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/php.ini

# Permissions cho thư mục log
RUN mkdir -p /var/log/supervisor \
    && chown -R www-data:www-data /var/log/supervisor

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
