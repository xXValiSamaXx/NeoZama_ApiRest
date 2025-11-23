# Imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . /var/www/html

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Habilitar módulos de Apache necesarios
RUN a2enmod rewrite headers

# Copiar configuración de Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar configuración de PHP personalizada
COPY docker/production/php.ini /usr/local/etc/php/conf.d/custom.ini

# Exponer puerto
EXPOSE 8080

# Variables de entorno por defecto
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV PORT=8080

# Actualizar configuración de Apache para usar el puerto de Railway
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Crear script de inicio
RUN echo '#!/bin/bash\n\
    echo "🚀 Iniciando aplicación..."\n\
    echo "⚙️  Ejecutando migraciones..."\n\
    php artisan migrate --force\n\
    echo "📝 Generando cache de configuración..."\n\
    php artisan config:cache\n\
    echo "👁️  Generando cache de vistas..."\n\
    php artisan view:cache\n\
    echo "📚 Generando documentación Swagger..."\n\
    php artisan l5-swagger:generate\n\
    echo "✅ Aplicación lista!"\n\
    echo "🌐 Swagger UI disponible en: /api/documentation"\n\
    apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Comando de inicio
CMD ["/usr/local/bin/start.sh"]
