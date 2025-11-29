FROM php:8.3-fpm

# Dependências Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev

# Extensões PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Remover .env local para evitar sobrescrever variáveis do Railway
RUN rm -f .env

# Copiar projeto
COPY . .

# Instalar dependências do Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Clear config cache
RUN php artisan config:clear || true

EXPOSE 8080

# CMD com MIGRATION AUTOMÁTICA
CMD sh -c "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080"
