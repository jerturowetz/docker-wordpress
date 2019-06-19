#!/bin/bash
# Copyright (c) 2016-present Unito Inc.
# All Rights Reserved
#
# Script to build deps and fetch prod-hosted assets for local dev

printf "Export environment variables from .env\n"
# shellcheck disable=SC2046
export $(grep -v '^#' .env | xargs -0);

printf "Set AWS_DEFAULT_PROFILE to %s\n" "${AWS_PROFILE:-default}"
export AWS_DEFAULT_PROFILE="$AWS_PROFILE"

printf "Update composer\n"
composer update

printf "Update node deps\n"
npm install --update

printf "Sync uploads folder\n"
MSYS_NO_PATHCONV=1 docker-compose run --rm rsync rsync -avz --delete --progress --iconv=utf8,utf8 -e "ssh -T -o Compression=no -x" "${INSTALLNAME:?INSTALLNAME must be defined as an enviroment variable (or an .env file for sanity)}@${INSTALLNAME}.ssh.wpengine.net:sites/${INSTALLNAME}/wp-content/uploads/" /mnt/uploads/

# printf "Sync plugins folder\n"
# MSYS_NO_PATHCONV=1 docker-compose run --rm rsync rsync -avz --delete --progress --iconv=utf8,utf8 -e "ssh -T -o Compression=no -x" "${INSTALLNAME:?INSTALLNAME must be defined as an enviroment variable (or an .env file for sanity)}@${INSTALLNAME}.ssh.wpengine.net:sites/${INSTALLNAME}/wp-content/plugins/" /mnt/plugins/

printf "Get remote DB\n"
MSYS_NO_PATHCONV=1 docker-compose run --rm rsync rsync -avz --delete --progress -e "ssh -T -o Compression=no -x" "${INSTALLNAME:?INSTALLNAME must be defined as an enviroment variable (or an .env file for sanity)}@${INSTALLNAME}.ssh.wpengine.net:sites/${INSTALLNAME}/wp-content/mysql.sql" /wp-cli/

printf "Run wp-cli tasks (probably starting with a DB import)\n"
MSYS_NO_PATHCONV=1 docker-compose run --rm wp-cli /var/www/html/.wp-cli/init.sh

# printf "Sync AWS bucket\n"
# MSYS_NO_PATHCONV=1 aws s3 sync s3://largefs-"${INSTALLNAME:?INSTALLNAME must be defined as an enviroment variable (or an .env file for sanity)}"/"$INSTALLNAME"/wp-content/uploads/ ./wp-content/uploads/

# printf "Regen thumbnails\n"
# MSYS_NO_PATHCONV=1 docker-compose run --rm wp-cli /var/www/html/.wp-cli/regen-thumbs.sh

exit 0
