FROM nginx:alpine

COPY ./docker/nginx/default.conf /etc/nginx/nginx.conf

COPY . /var/www

WORKDIR /var/www/public

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
