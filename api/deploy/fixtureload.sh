#!/bin/bash
./bin/console doctrine:schema:drop --force
./bin/console doctrine:schema:update --force
./bin/console doctrine:fixtures:load --no-interaction
./bin/console cache:clear
