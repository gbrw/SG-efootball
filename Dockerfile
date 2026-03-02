FROM php:8.2-apache

# ── Install PostgreSQL + MySQL PDO extensions ──────────────────────
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Fix MPM conflict: remove all MPM configs then enable only prefork ──
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
          /etc/apache2/mods-enabled/mpm_*.conf \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf \
    && a2enmod rewrite

# ── Allow AllowOverride All so .htaccess works ────────────────────
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# ── Copy files ────────────────────────────────────────────────────
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
