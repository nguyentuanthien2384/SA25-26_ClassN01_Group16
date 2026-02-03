FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libcurl4-openssl-dev \
    && docker-php-ext-install pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files
COPY . /app

# Install PHP dependencies (if composer.json exists)
RUN if [ -f "composer.json" ]; then composer install --no-interaction --no-dev --prefer-dist || true; fi

# Expose port
EXPOSE 9004

# Start a simple HTTP server for health check
CMD ["php", "-S", "0.0.0.0:9004", "-t", "/app"]
