#!/bin/bash
URL=""
FOLDER=""
if [ "$#" -gt 0 ] && [ $1 = "producao" ]; then
  URL="manager.influx.com.br"
  FOLDER="production/influx-manager"
  RESET_DATABASE="false"
elif [ "$#" -gt 0 ] && [ $1 = "homologacao" ]; then
  URL="managerbeta.influx.com.br"
  FOLDER="homolog/influx-manager"
  RESET_DATABASE="false"
fi

sh -c "tar -zcf project-files.tar.gz --exclude=src/Migrations/* --exclude=public/bundles --exclude=public/uploads --exclude=dependencias/windows bin config src deploy public dependencias docker templates tests translations composer.json composer.lock docker-compose.yml symfony.lock .env.dist" \
  && echo -e "\e[0;37m\e[42mBuild done!\e[0;0m\n\n" \
  || exit 1

ssh ubuntu@$URL "mkdir ~/$FOLDER; rm -rf ~/$FOLDER/public/build/; rm -rf ~/$FOLDER/src/"

sftp -b deploy/sftp.sh ubuntu@$URL:/home/ubuntu/$FOLDER/ \
  && echo -e "\e[0;37m\e[42mBuild files uploaded!\e[0;0m\n\n" \
  || exit 1

ssh ubuntu@$URL "chmod -R 777 ~/$FOLDER; cd ~/$FOLDER; tar -zxf project-files.tar.gz; ./deploy/copy-dependencies.sh; RESET_DATABASE=$RESET_DATABASE ./deploy/database.sh $1 || exit 1; exit" \
  && echo -e "\e[0;37m\e[42mDeploy complete!\e[0;0m" \
  || exit 1
