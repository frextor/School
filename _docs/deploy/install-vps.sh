#!/usr/bin/env bash
# Installation de Scoleo (frextor/School) sur un VPS AlmaLinux/RHEL 9 vierge.
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

echo "==> 1/9 Dépôts et paquets système (EPEL + Remi pour PHP 8.1)"
dnf install -y epel-release
dnf install -y https://rpms.remirepo.net/enterprise/remi-release-9.rpm
dnf module reset -y php
dnf module enable -y php:remi-8.1
dnf install -y nginx mysql-server git unzip curl policycoreutils-python-utils \
    php php-cli php-fpm php-mysqlnd php-mbstring php-xml php-curl php-zip \
    php-gd php-bcmath php-intl php-dom php-common

echo "==> 2/9 Installation de Composer"
if ! command -v composer >/dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "==> 3/9 Base de données MySQL (structure seulement, mot de passe généré)"
systemctl enable --now mysqld
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
chown -R nginx:nginx "$APP_DIR"
find "$APP_DIR" -type d -exec chmod 755 {} \;
find "$APP_DIR" -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# SELinux (actif par défaut sur AlmaLinux) : autoriser nginx/php-fpm à écrire
# dans storage/bootstrap-cache et à se connecter au réseau (base de données, etc.)
if command -v semanage >/dev/null; then
    semanage fcontext -a -t httpd_sys_rw_content_t "${APP_DIR}/storage(/.*)?" || true
    semanage fcontext -a -t httpd_sys_rw_content_t "${APP_DIR}/bootstrap/cache(/.*)?" || true
    restorecon -Rv "$APP_DIR" || true
    setsebool -P httpd_can_network_connect 1 || true
    setsebool -P httpd_can_network_connect_db 1 || true
fi

echo "==> 9/9 Configuration Nginx"
mkdir -p /etc/nginx/conf.d
cat > /etc/nginx/conf.d/school.conf <<NGINX
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
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX

# php-fpm (paquet Remi) tourne par défaut en utilisateur "nginx" avec un socket
# /run/php-fpm/www.sock — cohérent avec le vhost ci-dessus, rien à changer sauf
# vérifier que le pool existe bien.
if [ -f /etc/php-fpm.d/www.conf ]; then
    sed -i "s#^user = .*#user = nginx#" /etc/php-fpm.d/www.conf
    sed -i "s#^group = .*#group = nginx#" /etc/php-fpm.d/www.conf
fi

nginx -t
systemctl enable --now php-fpm nginx
systemctl restart php-fpm nginx

if command -v firewall-cmd >/dev/null && systemctl is-active --quiet firewalld; then
    firewall-cmd --permanent --add-service=http
    firewall-cmd --permanent --add-service=ssh
    firewall-cmd --reload
fi

echo ""
echo "=================================================================="
echo " Installation terminée."
echo " URL de l'application : http://${SERVER_IP}/"
echo " Base de données      : ${DB_NAME} (utilisateur ${DB_USER})"
echo " Mot de passe DB      : ${DB_PASS}"
echo " (conservez ce mot de passe — il n'est écrit qu'ici et dans .env)"
echo "=================================================================="
