#!/bin/bash
JASPER_DIR="vendor/geekcom/phpjasper/bin/jasperstarter/jdbc/"
cp ./dependencias/jasper_por_no_vendor/mysql_connector/* $JASPER_DIR
cp ./dependencias/jasper_por_no_vendor/fonts/* $JASPER_DIR
sudo cp ./docker/mysql/timezone.cnf /etc/mysql/conf.d

# Install Java dependencies (see docker/php/Dockerfile)
sudo apt-get install -y default-jdk mysql-client libmysql-java
