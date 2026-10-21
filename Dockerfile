FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

WORKDIR /var/www/html

# Install Apache and PHP from Ubuntu's security-maintained repositories.
RUN apt-get update && apt-get install -y --no-install-recommends \
    apache2 \
    ca-certificates \
    git \
    libapache2-mod-php8.3 \
    php8.3-bcmath \
    php8.3-bz2 \
    php8.3-curl \
    php8.3-gd \
    php8.3-gmp \
    php8.3-intl \
    php8.3-ldap \
    php8.3-mysql \
    php8.3-opcache \
    php8.3-pgsql \
    php8.3-soap \
    php8.3-tidy \
    php8.3-xml \
    php8.3-zip \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

# PHP's Apache module requires the prefork MPM.
RUN (a2dismod mpm_event 2>/dev/null || true) \
    && a2enmod mpm_prefork php8.3 rewrite headers deflate ssl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set DocumentRoot to public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

COPY . /var/www/html

CMD ["apache2ctl", "-D", "FOREGROUND"]
