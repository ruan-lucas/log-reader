# Imagem base PHP Alpine
FROM php:8.2-fpm-alpine

# Instalação de dependências
RUN apk add --no-cache \
    libzip-dev \
    zip \
    unzip \
    curl \
    wget \
    git \
    bash \
    supervisor \
    sqlite-dev \
    sqlite \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    nodejs \
    npm \
    $PHPIZE_DEPS

# Configuração e instalação das extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd pdo_mysql pdo_sqlite zip

# Instalação do Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configuração do Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# Copiar a configuração do PHP
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# Diretório de trabalho
WORKDIR /var/www/html