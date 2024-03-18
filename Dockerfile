# Utilisez l'image PHP 8.1
FROM php:8.1-fpm

# Répertoire de travail dans le conteneur
WORKDIR /var/www/html

# Installez les dépendances nécessaires (ajustez si nécessaire)
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copiez le contenu de votre application Symfony dans le conteneur
COPY . .

# Installez les dépendances de Symfony
RUN composer install --no-dev --optimize-autoloader

# Commande par défaut pour exécuter PHP-FPM
CMD ["php-fpm"]
