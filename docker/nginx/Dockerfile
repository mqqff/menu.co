FROM nginx:alpine

COPY ./docker/nginx/default.conf /etc/nginx/nginx.conf

COPY . /var/www

RUN ln -s /var/www/storage/app/public /var/www/public/storage

WORKDIR /var/www/public

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
