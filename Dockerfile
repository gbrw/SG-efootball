FROM php:8.2-apache

# ── Install PostgreSQL + MySQL PDO extensions ──────────────────────
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Enable mod_rewrite, disable conflicting MPM modules ───────────
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite

# ── Allow AllowOverride All so .htaccess works ────────────────────
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# ── Set document root ─────────────────────────────────────────────
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
