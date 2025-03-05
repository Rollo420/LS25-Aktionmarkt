FROM composer:latest as composer
RUN git config --global --add safe.directory /var/www/html

RUN mkdir /var/www/html -p 

COPY ../ /var/www/html/

WORKDIR /var/www/html
RUN composer install


FROM node:latest as node

WORKDIR /app
COPY ../package.json /app/package.json
COPY ../package-lock.json /app/package-lock.json

RUN npm install

COPY ../docker/copy-vendor-moduls.sh /app/copy-vendor-moduls.sh
RUN chmod +x /app/copy-vendor-moduls.sh
#RUN chmod -R 777 /app/copy-vendor-moduls.sh
COPY --from=composer /var/www/html/vendor /app/vendor

#RUN chmod -R 777 /va r/www/html/node_modules

ENTRYPOINT [ "/app/copy-vendor-moduls.sh" ]