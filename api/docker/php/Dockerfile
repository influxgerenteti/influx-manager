FROM php:7.2-fpm-stretch

RUN mkdir -p /.composer
RUN mkdir -p /usr/share/man/man1
RUN chmod -R 0777 /.composer

ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpcre3-dev \
        libxrender1 \
        libxext6 \
        libfontconfig1 \
        wget \
        unzip \
        default-jdk \
        mysql-client \
        libmysql-java \
        xvfb \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip opcache \
    && docker-php-ext-enable gd pdo_mysql zip

# RUN pecl install xdebug-2.7.2 \
#   && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

RUN cd /tmp \
  && wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.4/wkhtmltox-0.12.4_linux-generic-amd64.tar.xz \
  && tar xf wkhtmltox-0.12.4_linux-generic-amd64.tar.xz \
  && cd wkhtmltox/bin \
  && mv * /usr/local/bin
