FROM debian AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www

COPY . /var/www

RUN npm install && npm run build

FROM nginx:alpine

COPY ./docker/nginx/default.conf /etc/nginx/nginx.conf

COPY --from=builder /var/www/public /var/www/public
COPY --from=builder /var/www/public/build /var/www/build

RUN ln -s /var/www/storage/app/public /var/www/public/storage

WORKDIR /var/www/public

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
