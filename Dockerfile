FROM php:8.2-fpm

# Install system dependencies + nginx
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure nginx for Laravel
RUN echo 'server {\n\
    listen __PORT__;\n\
    root /var/www/html/public;\n\
    index index.php index.html;\n\
    charset utf-8;\n\
\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
\n\
    location = /favicon.ico { access_log off; log_not_found off; }\n\
    location = /robots.txt  { access_log off; log_not_found off; }\n\
\n\
    error_page 404 /index.php;\n\
\n\
    location ~ \.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
    }\n\
\n\
    location ~ /\.(?!well-known).* {\n\
        deny all;\n\
    }\n\
}' > /etc/nginx/sites-available/default

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (no dev for production)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy package.json and install Node dependencies
COPY package.json package-lock.json* ./
RUN npm install

# Copy the rest of the application
COPY . .

# Run composer scripts after full copy
RUN composer dump-autoload --optimize

# Build Vite assets for production
RUN npm run build

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create startup script
RUN printf '#!/bin/bash\n\
set -e\n\
\n\
PORT=${PORT:-8080}\n\
\n\
# Update nginx port\n\
sed -i "s/__PORT__/${PORT}/g" /etc/nginx/sites-available/default\n\
\n\
# Laravel bootstrap\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan migrate --force\n\
\n\
# Run seeder only if DB is empty\n\
USER_COUNT=$(php artisan tinker --execute="echo \\App\\Models\\User::count();" 2>/dev/null | tail -1 | tr -d "\\r\\n ")\n\
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then\n\
  echo "[start.sh] Database kosong, menjalankan seeder..."\n\
  php artisan db:seed --force\n\
  echo "[start.sh] Seeder selesai!"\n\
else\n\
  echo "[start.sh] Database sudah ada $USER_COUNT user, skip seeder."\n\
fi\n\
\n\
# Start PHP-FPM in background\n\
php-fpm -D\n\
\n\
# Start nginx in foreground\n\
echo "[start.sh] Starting nginx on port ${PORT}..."\n\
exec nginx -g "daemon off;"\n\
' > /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]
