# Stage 1: Build Node assets
FROM node:18-alpine AS node-builder
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

# Set permissions
RUN chown -R application:application /app/storage /app/bootstrap/cache

# Change port to 7860 for Hugging Face Spaces compatibility
RUN find /opt/docker/etc/nginx/ -type f -exec sed -i 's/listen 80/listen 7860/g' {} +

# Make run.sh executable and set as command
RUN chmod +x /app/run.sh
CMD ["/app/run.sh"]
