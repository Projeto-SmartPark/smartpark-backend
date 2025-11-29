FROM php:8.2-fpm

# Dependências do Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

# Extensões PHP necessárias
RUN docker-php-ext-install pdo pdo_mysql mbstring zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Diretório da aplicação
WORKDIR /var/www

# Copiar o código do backend
COPY . .

# Instalar dependências
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Limpar cache do Laravel
RUN php artisan config:clear || true

# Expor porta usada pelo Railway
EXPOSE 8080

# Executar Laravel
CMD ["php", "artisan", "serve"]()
