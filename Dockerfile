FROM phpswoole/swoole
RUN apt-get update && apt-get install -y libpq-dev git && \
    cd /opt && \
    git clone https://github.com/swoole/ext-postgresql.git && \
    cd ext-postgresql && \
    phpize && \
    ./configure && \
    make && make install && \
    docker-php-ext-enable swoole_postgresql && \
    pecl install pcov && docker-php-ext-enable pcov