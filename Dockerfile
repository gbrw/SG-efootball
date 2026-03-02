FROM debian:bookworm-slim

ENV DEBIAN_FRONTEND=noninteractive

# ── Install Apache + PHP 8.2 + extensions ─────────────────────────
RUN apt-get update && apt-get install -y \
      apache2 \
      php8.2 \
      php8.2-pgsql \
      php8.2-mysql \
      libapache2-mod-php8.2 \
      php8.2-cli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Enable only mod_rewrite (mpm_prefork already default) ─────────
RUN a2enmod rewrite php8.2

# ── Apache config: serve from /var/www/html, allow .htaccess ──────
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# ── Copy site files ───────────────────────────────────────────────
COPY . /var/www/html/
RUN rm -f /var/www/html/index.html \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]
