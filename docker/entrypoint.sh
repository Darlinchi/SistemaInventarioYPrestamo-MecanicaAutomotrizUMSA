#!/bin/bash
set -e

echo "🚀 Iniciando SistemaInventarioPrestamo..."

WORK=/var/www

# -----------------------------------------------------------------
# 1. Copiar .env desde .env.docker si no existe
# -----------------------------------------------------------------
if [ ! -f "$WORK/.env" ]; then
    echo "📄 Creando .env desde .env.docker..."
    if [ -f "$WORK/.env.docker" ]; then
        cp "$WORK/.env.docker" "$WORK/.env"
    else
        echo "⚠️ .env.docker no encontrado. Copiando desde .env.example..."
        cp "$WORK/.env.example" "$WORK/.env"
    fi
fi

# -----------------------------------------------------------------
# 2. Asegurar APP_KEY
# -----------------------------------------------------------------
CURRENT_KEY=$(grep "^APP_KEY=" "$WORK/.env" | cut -d '=' -f2-)
if [ -z "$CURRENT_KEY" ]; then
    echo "🔑 Generando APP_KEY..."
    php "$WORK/artisan" key:generate --force
fi

# -----------------------------------------------------------------
# 3. Base de datos SQLite
# -----------------------------------------------------------------
DB_FILE="$WORK/database/database.sqlite"
mkdir -p "$WORK/database"

if [ ! -f "$DB_FILE" ]; then
    echo "🗃️ Creando base de datos SQLite..."
    touch "$DB_FILE"
    echo "📦 Ejecutando migraciones y seeders iniciales..."
    php "$WORK/artisan" migrate:fresh --force --seed
else
    echo "📦 Ejecutando migraciones pendientes..."
    php "$WORK/artisan" migrate --force
fi

# -----------------------------------------------------------------
# 4. Enlace de Storage y Limpieza de Caché
# -----------------------------------------------------------------
php "$WORK/artisan" storage:link --force 2>/dev/null || true
php "$WORK/artisan" config:clear
php "$WORK/artisan" route:clear
php "$WORK/artisan" view:clear

echo "✅ Aplicación lista en http://0.0.0.0:8000"

# -----------------------------------------------------------------
# 5. Servidor Laravel
# -----------------------------------------------------------------
exec php "$WORK/artisan" serve --host=0.0.0.0 --port=8000
