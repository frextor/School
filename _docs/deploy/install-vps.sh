#!/usr/bin/env bash
# Installation de Scoleo (frextor/School) sur un VPS Ubuntu/Debian vierge.
# À exécuter en tant que root : bash install-vps.sh
#
# Accès prévu : http://<IP-du-serveur>/ (pas de nom de domaine pour l'instant,
# donc pas de certificat HTTPS — à ajouter plus tard avec certbot une fois
# un domaine pointé dessus).
#
# Base de données : structure seulement (_docs/miracle_structure.sql, déjà
# dans le dépôt), aucune donnée réelle importée — cohérent avec l'environnement local.

set -euo pipefail

REPO_URL="https://github.com/frextor/School.git"
APP_DIR="/var/www/school"
DB_NAME="miracle"
DB_USER="scoleo"
DB_PASS="$(openssl rand -base64 24 | tr -dc 'A-Za-z0-9' | head -c 24)"
SERVER_IP="$(curl -s ifconfig.me || hostname -I | awk '{print $1}')"

echo "==> 1/9 Mise à jour du système et installation des paquets"
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y software-properties-common ca-certificates lsb-release apt-transport-https gnupg2 curl unzip git

# PHP 8.1+ (dépôt ondrej/php pour Ubuntu, au cas où la version du dépôt par défaut serait trop ancienne)
if ! command -v php >/dev/null || ! php -r 'exit(version_compare(PHP_VERSION, "8.1.0", ">=") ? 0 : 1);'; then
    add-apt-repository -y ppa:ondrej/php || true
    apt-get update -y
fi

apt-get install -y \
    nginx \
    mysql-server \
    php8.1-fpm php8.1-cli php8.1-mysql php8.1-mbstring php8.1-xml \
    php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath php8.1-intl php8.1-dom

echo "==> 2/9 Installation de Composer"
if ! command -v composer >/dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "==> 3/9 Base de données MySQL (structure seulement, mot de passe généré)"
systemctl enable --now mysql
mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

echo "==> 4/9 Récupération du projet depuis GitHub"
mkdir -p "$(dirname "$APP_DIR")"
if [ -d "$APP_DIR/.git" ]; then
    git -C "$APP_DIR" pull
else
    git clone "$REPO_URL" "$APP_DIR"
fi
cd "$APP_DIR"

echo "==> 5/9 Import du schéma (structure seulement, sans données)"
mysql -u root "${DB_NAME}" < _docs/miracle_structure.sql

echo "==> 6/9 Dépendances PHP"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> 7/9 Configuration .env"
cp -n .env.example .env
sed -i "s#^APP_ENV=.*#APP_ENV=production#" .env
sed -i "s#^APP_DEBUG=.*#APP_DEBUG=false#" .env
sed -i "s#^APP_URL=.*#APP_URL=http://${SERVER_IP}#" .env
sed -i "s#^DB_DATABASE=.*#DB_DATABASE=${DB_NAME}#" .env
sed -i "s#^DB_USERNAME=.*#DB_USERNAME=${DB_USER}#" .env
sed -i "s#^DB_PASSWORD=.*#DB_PASSWORD=${DB_PASS}#" .env
php artisan key:generate --force

echo "==> 8/9 Migrations Laravel (tables propres au projet, ex: demandes_demo — pas le schéma legacy déjà importé)"
php artisan migrate --force

echo "==> Permissions et cache"
chown -R www-data:www-data "$APP_DIR"
find "$APP_DIR" -type d -exec chmod 755 {} \;
find "$APP_DIR" -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> 9/9 Configuration Nginx"
cat > /etc/nginx/sites-available/school <<NGINX
server {
    listen 80 default_server;
    server_name ${SERVER_IP};
    root ${APP_DIR}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX

ln -sf /etc/nginx/sites-available/school /etc/nginx/sites-enabled/school
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl enable --now php8.1-fpm nginx
systemctl restart php8.1-fpm nginx

if command -v ufw >/dev/null; then
    ufw allow 22/tcp || true
    ufw allow 80/tcp || true
fi

echo ""
echo "=================================================================="
echo " Installation terminée."
echo " URL de l'application : http://${SERVER_IP}/"
echo " Base de données      : ${DB_NAME} (utilisateur ${DB_USER})"
echo " Mot de passe DB      : ${DB_PASS}"
echo " (conservez ce mot de passe — il n'est écrit qu'ici et dans .env)"
echo "=================================================================="
