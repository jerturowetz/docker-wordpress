
#!bin/bash
# set -e # exit on any errors (implies linear flow)

# Check for required enviroment variables
${INSTALLNAME:?INSTALLNAME must be defined as an enviroment variable (or an .env file for sanity)}
${AWS_PROFILE:-default}

# remove requirement for prod url in docker-compose up
>&2 echo "Set AWS_DEFAULT_PROFILE to ${AWS_PROFILE}"
export AWS_DEFAULT_PROFILE=AWS_PROFILE

>&2 echo "Update composer"
composer update

>&2 echo "Update node deps"
yarn install --update --dev

>&2 echo "Get remote DB"
MSYS_NO_PATHCONV=1 docker-compose run --rm rsync rsync -avz --delete --progress -e "ssh -T -o Compression=no -x" ${INSTALLNAME}@${INSTALLNAME}.ssh.wpengine.net:sites/${INSTALLNAME}/wp-content/mysql.sql /wp-cli/

>&2 echo "Re-init DB and run wp-cli tasks"
MSYS_NO_PATHCONV=1 docker-compose run --rm wp-cli /var/www/html/.wp-cli/init.sh

>&2 echo "Sync uploads folder"
MSYS_NO_PATHCONV=1 docker-compose run --rm rsync rsync -avz --delete --progress --iconv=utf8,utf8 -e "ssh -T -o Compression=no -x" "${INSTALLNAME}"@"${INSTALLNAME}".ssh.wpengine.net:sites/"${INSTALLNAME}"/wp-content/uploads/ /mnt/

#>&2 echo "Sync AWS bucket"
# MSYS_NO_PATHCONV=1 aws s3 sync s3://largefs-"$INSTALLNAME"/"$INSTALLNAME"/wp-content/uploads/ ./wp-content/uploads/

# >&2 echo "Regen thumbnails"
# MSYS_NO_PATHCONV=1 docker-compose run --rm wp-cli /var/www/html/.wp-cli/regen-thumbs.sh
