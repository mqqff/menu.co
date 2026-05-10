#!/bin/sh
set -e

# Initialize storage directory if empty
if [ ! "$(ls -A /var/www/storage 2>/dev/null)" ]; then
    echo "Initializing storage directory..."

    cp -R /var/www/storage-init/. /var/www/storage

    chown -R www-data:www-data /var/www/storage
    chmod -R 775 /var/www/storage
fi

# Remove temp init directory
rm -rf /var/www/storage-init

rm -rf rm /var/www/public/storage
php artisan storage:link

# Laravel optimization
php artisan optimize

# Run main container command
exec "$@"
