FROM php:8.4-cli

# Laravel の起動に最低限必要な拡張のみインストール
RUN apt-get update && apt-get install -y \
    unzip \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install mbstring xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer をインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 依存パッケージをインストール
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts

# アプリのコードをコピー
COPY . .

# ストレージの権限を付与
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
