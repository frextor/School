#!/usr/bin/env bash
#
# Déploiement d'une mise à jour sur le serveur (à lancer SUR le serveur) :
#
#   cd /var/www/school && bash _docs/deploy/deploy.sh
#
# Pourquoi un script plutôt que quelques commandes à la main : l'installation
# met en cache la configuration, les routes ET les vues (install-vps.sh, étape
# « Optimisations »). Un `git pull` suivi du seul `view:cache` laisse donc le
# cache de ROUTES périmé : toute route ajoutée par la mise à jour reste
# invisible et les pages qui l'utilisent tombent en erreur 500
# (« Route [x] not defined »), alors que les vues, elles, sont à jour — panne
# d'autant plus trompeuse qu'elle ne touche qu'une partie des écrans.
#
# Incident réel : les routes `referentiel.niveaux.options.*` puis `tuteurs.*`
# ont été livrées sans rafraîchir ce cache ; les écrans concernés renvoyaient
# 500 en production alors qu'ils fonctionnaient en local.
set -euo pipefail

cd "$(dirname "$0")/../.."

echo "==> Récupération du code"
git pull

echo "==> Dépendances PHP"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Migrations"
php artisan migrate --force

echo "==> Reconstruction des caches (config + routes + vues)"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Droits"
chown -R nginx:nginx .

echo "==> Déploiement terminé : $(git rev-parse --short HEAD)"
