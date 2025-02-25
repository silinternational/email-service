FROM silintl/php8:8.1

ENV REFRESHED_AT 2023-01-11

RUN apt-get update -y \
    && apt-get install -y \
        cron \
        make \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /data

# Copy in cron configuration
COPY dockerbuild/email-cron /etc/cron.d/
RUN chmod 0644 /etc/cron.d/email-cron

WORKDIR /data

# Install/cleanup composer dependencies
COPY application/composer.json /data/
COPY application/composer.lock /data/
RUN composer install --prefer-dist --no-interaction --no-dev --optimize-autoloader

# It is expected that /data is = application/ in project folder
COPY application/ /data/

# Fix folder permissions
RUN chown -R www-data:www-data \
    console/runtime/

COPY dockerbuild/vhost.conf /etc/apache2/sites-enabled/

# ErrorLog inside a VirtualHost block is ineffective for unknown reasons
RUN sed -i -E 's@ErrorLog .*@ErrorLog /proc/self/fd/2@i' /etc/apache2/apache2.conf

EXPOSE 80

CMD ["/data/run.sh"]
