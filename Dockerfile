# Stage 1: Build Node assets
FROM node:22-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Final production image
FROM webdevops/php-nginx:8.2
WORKDIR /app

# Set Environment for Nginx document root
ENV WEB_DOCUMENT_ROOT=/app/public

# Copy application
COPY . .

# Copy built assets from node-builder
COPY --from=node-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions (Chmod 777 is needed for random container UIDs on Hugging Face)
RUN chmod -R 777 /app/storage /app/bootstrap/cache

# Copy CA certificate to app directory for PHP-FPM readability and open_basedir compatibility
RUN cp /etc/ssl/certs/ca-certificates.crt /app/ca-certificates.crt && chmod 777 /app/ca-certificates.crt

# Change port to 7860 for Hugging Face Spaces compatibility
RUN find /opt/docker/etc/nginx/ -type f -exec sed -i 's/listen 80/listen 7860/g' {} +

# Make run.sh executable and set as command
RUN chmod +x /app/run.sh
CMD ["/app/run.sh"]
