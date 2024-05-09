#!/bin/bash
isProd=0
if [ $# -gt 0 ] && [ "$1" = "gati-homolog" ] || [ "$1" = "influx-homolog" ] || [ "$1" = "homolog-cl9" ] || [ "$1" = "homologacao" ] || [ "$1" = "producao" ]; then
  isProd=1
fi

if [ $RESET_DATABASE = "true" ]; then
  isProd=0
fi

composer install

php ./bin/console cache:clear

rm -rf var/cache
[ "$(ls src/Migrations)" != "" ] && rm src/Migrations/*

# if [ "$isProd" -eq 0 ]; then
#   php ./bin/console doctrine:database:drop --force --connection base_principal
#   php ./bin/console doctrine:database:drop --force --connection base_log
#   php ./bin/console doctrine:database:create --connection base_principal
#   php ./bin/console doctrine:database:create --connection base_log
# fi

php ./bin/console doctrine:migrations:diff --em=base_log
php ./bin/console doctrine:migrations:migrate --em=base_log --no-interaction
[ "$(ls src/Migrations)" != "" ] && rm src/Migrations/*
php ./bin/console doctrine:migrations:diff --em=base_principal
php ./bin/console doctrine:migrations:migrate --em=base_principal --no-interaction

# if [ "$isProd" -eq 0 ]; then
#   php ./bin/console doctrine:fixtures:load --em=base_principal --no-interaction
# fi

php ./bin/console cache:clear

[[ -d public/uploads ]] || mkdir -m777 public/uploads

if [ "$isProd" -eq 1 ]; then
  composer dump-autoload --optimize --no-dev --classmap-authoritative
fi
