FROM wordpress:php7.0-apache

# Install intl, this is mostly used on standard's install, but exists on wpengine
RUN set -ex; \
  \
  savedAptMark="$(apt-mark showmanual)"; \
  \
  apt-get update; \
  apt-get install -y --no-install-recommends \
    zlib1g-dev \
    libicu-dev \
    g++ \
  ; \
  \
  docker-php-ext-configure intl; \
  docker-php-ext-install intl; \
  \
  # reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
  apt-mark auto '.*' > /dev/null; \
  apt-mark manual $savedAptMark; \
  ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
    | awk '/=>/ { print $3 }' \
    | sort -u \
    | xargs -r dpkg-query -S \
    | cut -d: -f1 \
    | sort -u \
    | xargs -rt apt-mark manual; \
  \
  apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
  rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug \
  && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_port=9212" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.var_display_max_depth = 5" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.var_display_max_children = 256" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.var_display_max_data = 1024" >> /usr/local/etc/php/conf.d/xdebug.ini

# Set some php.ini defaults
RUN { \
    echo 'file_uploads = On'; \
    echo 'memory_limit = 512M'; \
    echo 'upload_max_filesize = 500M'; \
    echo 'post_max_size = 500M'; \
    echo 'max_execution_time = 3600'; \
  } > /usr/local/etc/php/conf.d/uploads.ini

# Reload apache server without killing the container
RUN kill -USR1 1
