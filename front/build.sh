#!/bin/bash
echo "script de build para deploy"



if [ $# -eq 0 ]; then
    printf "Utilizacao:\n"
    printf "$0 [[--env|-e] <DEV|PROD|BETA>] \n"
    exit 1
fi

while [ $# -gt 0 ]; do
  case "$1" in
    --env|-e)
      export environment="${2}"
      shift
      ;;    
    *)
      printf "ERRO: Parametros invalidos\n"
      printf "Execute sem parametros para a sintaxe.\n"
      exit 1
  esac
  shift
done

npm run build
echo "configing environment"

case $environment in

  "prod" | "PROD")
   contents="$(jq '.ENV = "PROD"' ./environment.config.json)" && echo -E "${contents}" > ./environment.config.json
    ;;

  "dev" | "DEV")
    contents="$(jq '.ENV = "DEV"' ./environment.config.json)" && echo -E "${contents}" > ./environment.config.json
    ;;
  "beta" | "BETA")
    contents="$(jq '.ENV = "BETA"' ./environment.config.json)" && echo -E "${contents}" > ./environment.config.json
    ;;

esac

cp ./environment.config.json ./dist/environment.config.json

# echo "DEV env restored"

# contents="$(jq '.ENV = "DEV"' ./environment.config.json)" && echo -E "${contents}" > ./environment.config.json


echo "build finish in /dist folder"