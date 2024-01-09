FROM silintl/php8:8.1

ENV REFRESHED_AT 2020-06-14

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

# get s3-expand
RUN curl https://raw.githubusercontent.com/silinternational/s3-expand/1.5/s3-expand -fo /usr/local/bin/s3-expand
RUN chmod a+x /usr/local/bin/s3-expand

# get runny
RUN curl https://raw.githubusercontent.com/silinternational/runny/0.1/runny -fo /usr/local/bin/runny
RUN chmod a+x /usr/local/bin/runny

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
ENTRYPOINT ["/usr/local/bin/s3-expand"]
CMD ["/data/run.sh"]
