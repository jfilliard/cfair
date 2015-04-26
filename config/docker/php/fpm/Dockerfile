FROM php:5.6-fpm

RUN apt-get update
RUN apt-get install -y git file libicu-dev php5-dev
RUN docker-php-ext-install intl mysql pdo pdo_mysql
RUN cd /tmp/ && git clone git://github.com/xdebug/xdebug.git
RUN cd /tmp/xdebug && git checkout XDEBUG_2_3_2 && phpize && ./configure --enable-xdebug && make && cp modules/xdebug.so /usr/local/lib/php/extensions/

ADD conf.d/ /usr/local/etc/php/conf.d/
ADD php-fpm.conf /usr/local/etc/

EXPOSE 9000
