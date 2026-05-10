FROM nginx:alpine

#RUN chmod -R a+rwX /var/www

COPY ./docker/nginx/default.conf /etc/nginx/nginx.conf

WORKDIR /var/www/public

EXPOSE 3001
CMD ["nginx", "-g", "daemon off;"]
