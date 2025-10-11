FROM trafex/php-nginx:3.9.0 AS base

USER root
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN addgroup -g 1000 -S user && \
	adduser -h /var/www/html -u 1000 -D user -G user

# Install requirements
RUN apk add --no-cache \
	git bash nano curl sudo openssh-client \
	nodejs yarn \
	php84 php84-ctype php84-dom php84-fileinfo php84-pdo php84-pdo_mysql php84-session \
	php84-pecl-redis php84-tokenizer php84-xml php84-sodium php84-gd php84-xmlwriter php84-simplexml \
	php84-iconv php84-pdo_sqlite \
	wireguard-tools sudo

WORKDIR /var/www/html

# Copy composer binary
COPY --from=composer:2.8.11 /usr/bin/composer /usr/bin/composer

# Copy custom NGINX config over
COPY docker/nginx-conf.conf /etc/nginx/conf.d/default.conf

# Copy custom PHP FPM Pool config over
COPY docker/php-fpm-www.conf /etc/php84/php-fpm.d/www.conf

# Copy Supervisor config
COPY docker/supervisor.conf /etc/supervisor.conf

# Copy entrypoint
COPY docker/entrypoint.sh /entrypoint.sh

# Add user directive to nginx configuration
RUN sed -i '1s/^/user user;\n/' /etc/nginx/nginx.conf

# Copy sudoers file to allow user to control Wireguard tunnels
COPY docker/sudoers-user-wg /etc/sudoers.d/

FROM base AS production

# Install composer dependencies
COPY . .
RUN composer install --no-autoloader

# Change file and directory ownership
RUN chown -R user:www-data /var/www/html
