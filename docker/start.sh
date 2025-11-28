#!/bin/bash

echo "🚀 Iniciando Fluxo..."

# Fix storage permissions (needed for mounted volumes)
echo "🔧 Configurando permissões..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Wait for PostgreSQL to be ready
echo "⏳ Aguardando PostgreSQL..."
until pg_isready -h postgres -p 5432 -U ${DB_USERNAME:-fluxo}
do
  echo "Waiting for PostgreSQL..."
  sleep 2
done
echo "✅ PostgreSQL está pronto!"

# Wait for Redis to be ready
echo "⏳ Aguardando Redis..."
until redis-cli -h redis ping 2>/dev/null | grep -q PONG
do
  echo "Waiting for Redis..."
  sleep 2
done
echo "✅ Redis está pronto!"

# Run migrations
echo "🔄 Executando migrations..."
php artisan migrate --force

# Cache configuration
echo "📦 Cacheando configurações..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🎉 Fluxo iniciado com sucesso!"

# Start supervisor
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
