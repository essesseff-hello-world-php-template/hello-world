# Use official PHP runtime as base image
FROM php:8.5-cli-alpine

# Set working directory
WORKDIR /app

# Install system dependencies needed by Composer / extensions
RUN apk add --no-cache \
    curl \
    unzip

# Install Composer (equivalent to uv in the Python template)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy dependency manifest first (layer-cache friendly)
COPY composer.json ./

# Install PHP dependencies (production, no dev)
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-progress

# Copy application files
COPY . .

# Expose port 8080
EXPOSE 8080

# Set environment variables
ENV PORT=8080

# Run the application using PHP's built-in server
# -S  listen address   -t  document root
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t /app index.php"]
