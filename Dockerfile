FROM    php:7.1.12-fpm-alpine

ENV     php_conf /usr/local/etc/php-fpm.conf
ENV     fpm_conf /usr/local/etc/php-fpm.d/www.conf
ENV     php_vars /usr/local/etc/php/conf.d/docker-vars.ini

ENV     NGINX_VERSION 1.13.7
ENV     LUA_MODULE_VERSION 0.10.11
ENV     DEVEL_KIT_MODULE_VERSION 0.3.0
ENV     LUAJIT_LIB=/usr/lib
ENV     LUAJIT_INC=/usr/include/luajit-2.0

# resolves #166
ENV     LD_PRELOAD /usr/lib/preloadable_libiconv.so php
RUN     apk add --no-cache --repository http://dl-3.alpinelinux.org/alpine/edge/testing gnu-libiconv 


RUN   echo @testing http://nl.alpinelinux.org/alpine/edge/testing >> /etc/apk/repositories && \
#     sed -i -e "s/v3.4/edge/" /etc/apk/repositories && \
      echo /etc/apk/respositories && \
      apk update && \
      apk add --no-cache bash \
      openssh-client \
      wget \
      supervisor \
      curl \
      libcurl \
      git \
      python \
      python-dev \
      py-pip \
      augeas-dev \
      openssl-dev \
      ca-certificates \
      dialog \
      autoconf \
      make \
      gcc \
      musl-dev \
      linux-headers \
      libmcrypt-dev \
      libpng-dev \
      icu-dev \
      libpq \
      libxslt-dev \
      libffi-dev \
      freetype-dev \
      sqlite-dev \
      libjpeg-turbo-dev && \
      docker-php-ext-configure gd \
        --with-gd \
        --with-freetype-dir=/usr/include/ \
        --with-png-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ && \
      #curl iconv session
      docker-php-ext-install pdo_mysql pdo_sqlite mysqli mcrypt gd exif intl xsl json soap dom zip opcache && \
      pecl install xdebug && \
      docker-php-source delete && \
      mkdir -p /etc/nginx && \
      mkdir -p /var/www/app && \
      mkdir -p /run/nginx && \
      mkdir -p /var/log/supervisor && \
      EXPECTED_COMPOSER_SIGNATURE=$(wget -q -O - https://composer.github.io/installer.sig) && \
      php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
      php -r "if (hash_file('SHA384', 'composer-setup.php') === '${EXPECTED_COMPOSER_SIGNATURE}') { echo 'Composer.phar Installer verified'; } else { echo 'Composer.phar Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
      php composer-setup.php --install-dir=/usr/bin --filename=composer && \
      php -r "unlink('composer-setup.php');"  && \
      pip install -U pip && \
      pip install -U certbot && \
      mkdir -p /etc/letsencrypt/webrootauth && \
      apk del gcc musl-dev linux-headers libffi-dev augeas-dev python-dev make autoconf
#     ln -s /usr/bin/php7 /usr/bin/php



# tweak php-fpm config

RUN   echo "cgi.fix_pathinfo=0" > ${php_vars} &&\
      echo "upload_max_filesize = 100M"  >> ${php_vars} &&\
      echo "post_max_size = 100M"  >> ${php_vars} &&\
      echo "variables_order = \"EGPCS\""  >> ${php_vars} && \
      echo "memory_limit = 128M"  >> ${php_vars} && \
      sed -i \
          -e "s/;catch_workers_output\s*=\s*yes/catch_workers_output = yes/g" \
          -e "s/pm.max_children = 5/pm.max_children = 4/g" \
          -e "s/pm.start_servers = 2/pm.start_servers = 3/g" \
          -e "s/pm.min_spare_servers = 1/pm.min_spare_servers = 2/g" \
          -e "s/pm.max_spare_servers = 3/pm.max_spare_servers = 4/g" \
          -e "s/;pm.max_requests = 500/pm.max_requests = 200/g" \
          -e "s/user = www-data/user = nginx/g" \
          -e "s/group = www-data/group = nginx/g" \
          -e "s/;listen.mode = 0660/listen.mode = 0666/g" \
          -e "s/;listen.owner = www-data/listen.owner = nginx/g" \
          -e "s/;listen.group = www-data/listen.group = nginx/g" \
          -e "s/^;clear_env = no$/clear_env = no/" \
          ${fpm_conf}
#     ln -s /etc/php7/php.ini /etc/php7/conf.d/php.ini && \
#     find /etc/php7/conf.d/ -name "*.ini" -exec sed -i -re 's/^(\s*)#(.*)/\1;\2/g' {} \;

RUN   addgroup -S nginx && \
      adduser -D -S -h /var/cache/nginx -s /sbin/nologin -G nginx nginx  