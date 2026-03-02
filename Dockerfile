FROM debian:bookworm-slim

ENV DEBIAN_FRONTEND=noninteractive

# ── Install Apache + PHP 8.2 + extensions ─────────────────────────
RUN apt-get update && apt-get install -y \
      apache2 \
      php8.2 \
      php8.2-mysql \
      libapache2-mod-php8.2 \
      php8.2-cli \
      php8.2-mbstring \
      php8.2-gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Enable mod_rewrite ────────────────────────────────────────────
RUN a2enmod rewrite

# ── Virtual host: index.php first, AllowOverride All ──────────────
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    DirectoryIndex index.php index.html\n\
    <Directory /var/www/html>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# ── Copy site files, remove default Apache page ───────────────────
COPY . /var/www/html/
RUN rm -f /var/www/html/index.html \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]
