#!/bin/bash
set -e

# ==============================================================================
# SKRIP DEPLOYMENT OTOMATIS - AKTAMA COFFEE / KOPI DISTRO (CPANEL / LINUX)
# ==============================================================================
# Pola arsitektur yang sama dengan sistem Aktama Digital:
# Mengupdate kode dari GitHub, install dependency, migrate database,
# dan optimasi cache produksi.
# ==============================================================================

# Direktori Proyek di Server Niagahoster / cPanel (Pola Aktama Digital)
# Default: Source code di 'kopi-source' (atau 'coffee-source') dan web publik di 'coffee'
if [ -d "/home/u107454400/domains/aktama.com/public_html/kopi-source" ]; then
    DEFAULT_APP_ROOT="/home/u107454400/domains/aktama.com/public_html/kopi-source"
else
    DEFAULT_APP_ROOT="/home/u107454400/domains/aktama.com/public_html/coffee-source"
fi
DEFAULT_WEB_ROOT="/home/u107454400/domains/aktama.com/public_html/coffee"

# Auto-deteksi jika skrip dijalankan dari dalam direktori source proyek
CURRENT_SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
if [ -f "$CURRENT_SCRIPT_DIR/artisan" ]; then
    APP_ROOT="${APP_ROOT:-$CURRENT_SCRIPT_DIR}"
else
    APP_ROOT="${APP_ROOT:-$DEFAULT_APP_ROOT}"
fi
WEB_ROOT="${WEB_ROOT:-$DEFAULT_WEB_ROOT}"

echo "=========================================================="
echo "  MEMULAI DEPLOYMENT AKTAMA COFFEE KE SERVER HOSTING"
echo "  Waktu       : $(date '+%Y-%m-%d %H:%M:%S')"
echo "  Lokasi App  : $APP_ROOT"
echo "  Lokasi Web  : $WEB_ROOT (Folder: coffee)"
echo "=========================================================="

cd "$APP_ROOT"

# 1. Deteksi Binary PHP (Dukungan CloudLinux Alt-PHP Niagahoster & Standar cPanel)
PHP_BIN="php"
if [ -x "/opt/alt/php83/usr/bin/php" ]; then
    PHP_BIN="/opt/alt/php83/usr/bin/php"
elif [ -x "/opt/alt/php82/usr/bin/php" ]; then
    PHP_BIN="/opt/alt/php82/usr/bin/php"
elif [ -x "/usr/local/bin/php" ]; then
    PHP_BIN="/usr/local/bin/php"
fi
echo "==> Menggunakan PHP: $($PHP_BIN -v | head -n 1)"

# 2. Deteksi Binary Composer
COMPOSER_BIN="composer"
if command -v composer2 >/dev/null 2>&1; then
    COMPOSER_BIN="$(command -v composer2)"
elif command -v composer >/dev/null 2>&1; then
    COMPOSER_BIN="$(command -v composer)"
elif [ -f "$APP_ROOT/composer.phar" ]; then
    COMPOSER_BIN="$PHP_BIN $APP_ROOT/composer.phar"
fi

# 3. Tarik update kode terbaru dari repositori GitHub
echo "==> [1/6] Mengambil update terbaru dari Git (git pull)..."
if [ -d ".git" ]; then
    git pull origin main
else
    echo "    Bukan repository git atau .git tidak ada, melewati git pull."
fi

# 3.5 Pastikan file .env dan APP_KEY siap
if [ ! -f "$APP_ROOT/.env" ]; then
    if [ -f "$APP_ROOT/.env.production.example" ]; then
        echo "==> Membuat file .env baru dari template .env.production.example..."
        cp "$APP_ROOT/.env.production.example" "$APP_ROOT/.env"
    fi
fi

# Pastikan folder penyimpanan tersedia
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Generate APP_KEY jika belum ada (penyebab utama Laravel 500 Server Error)
if [ -f "$APP_ROOT/.env" ]; then
    if ! grep -q "^APP_KEY=base64:" "$APP_ROOT/.env" 2>/dev/null; then
        echo "==> Menghasilkan APP_KEY produksi (mengatasi 500 Server Error)..."
        "$PHP_BIN" artisan key:generate --force || true
    fi
fi

# 4. Install PHP Dependencies Produksi
echo "==> [2/6] Memperbarui paket Composer (tanpa dev)..."
if command -v "$COMPOSER_BIN" >/dev/null 2>&1 || [ -f "$APP_ROOT/composer.phar" ]; then
    "$PHP_BIN" $COMPOSER_BIN install --no-dev --prefer-dist --optimize-autoloader --no-interaction
else
    echo "    Composer tidak ditemukan, melewati composer install."
fi

# 5. Jalankan Migrasi Database
echo "==> [3/6] Menjalankan migrasi database..."
"$PHP_BIN" artisan migrate --force

# Jika database baru dan produk masih kosong, otomatis isi data resmi & akun staf
if ! "$PHP_BIN" artisan tinker --execute="exit(App\Models\Product::count() > 0 ? 0 : 1);" >/dev/null 2>&1; then
    echo "    Mengisi data awal produk resmi & akun staf (db:seed)..."
    "$PHP_BIN" artisan db:seed --force || true
fi

# 6. Pembersihan dan Pembangunan Cache Produksi (High Performance)
echo "==> [4/6] Membangun cache performa tinggi (optimize & cache)..."
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

# 7. Penanganan Symlink Storage
echo "==> [5/6] Memastikan symlink storage publik terhubung..."
"$PHP_BIN" artisan storage:link || true

# 8. Sinkronisasi Web Root jika dipisahkan (Metode Folder Terpisah seperti di Aktama)
if [ "$WEB_ROOT" != "$APP_ROOT/public" ] && [ -d "$WEB_ROOT" ]; then
    echo "==> [6/6] Menyinkronkan file publik ke web root ($WEB_ROOT)..."
    cp -r public/. "$WEB_ROOT"/

    # Buat file index.php terarah jika belum ada
    APP_DIR_NAME="$(basename "$APP_ROOT")"
    cat > "$WEB_ROOT/index.php" <<PHP
<?php
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists(\$maintenance = __DIR__.'/../$APP_DIR_NAME/storage/framework/maintenance.php')) {
    require \$maintenance;
}

require __DIR__.'/../$APP_DIR_NAME/vendor/autoload.php';

\$app = require_once __DIR__.'/../$APP_DIR_NAME/bootstrap/app.php';

\$app->handleRequest(Request::capture());
PHP

    # Pastikan symlink storage terhubung di web root
    rm -rf "$WEB_ROOT/storage"
    ln -s "$APP_ROOT/storage/app/public" "$WEB_ROOT/storage" || true
fi

# 9. Pastikan permission folder storage dan bootstrap/cache dapat ditulis
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "=========================================================="
echo "  DEPLOYMENT AKTAMA COFFEE BERHASIL DISELESAIKAN!        "
echo "  Website siap melayani pesanan mitra dan pelanggan.     "
echo "=========================================================="
