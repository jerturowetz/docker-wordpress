#!bin/bash
>&2 echo "Export environment variables from .env"
export $(grep -v '^#' .env | xargs -0);

>&2 echo "Set AWS_DEFAULT_PROFILE to ${AWS_PROFILE:-default}"
export AWS_DEFAULT_PROFILE=${AWS_PROFILE}

>&2 echo "Update composer"
composer update

>&2 echo "Update node deps"
yarn install --update --dev

>&2 echo "Get remote DB"
MSYS_NO_PATHCONV=1 docker-compose run --rm rsync rsync -avz --delete --progress -e "ssh -T -o Compression=no -x" ${INSTALLNAME:?INSTALLNAME must be defined as an enviroment variable (or an .env file for sanity)}@${INSTALLNAME}.ssh.wpengine.net:sites/${INSTALLNAME}/wp-content/mysql.sql /wp-cli/

>&2 echo "Run wp-cli tasks (probably starting with a DB import)"
MSYS_NO_PATHCONV=1 docker-compose run --rm wp-cli /var/www/html/.wp-cli/init.sh

>&2 echo "Sync uploads folder"
MSYS_NO_PATHCONV=1 docker-compose run --rm rsync rsync -avz --delete --progress --iconv=utf8,utf8 -e "ssh -T -o Compression=no -x" "${INSTALLNAME:?INSTALLNAME must be defined as an enviroment variable (or an .env file for sanity)}"@"${INSTALLNAME}".ssh.wpengine.net:sites/"${INSTALLNAME}"/wp-content/uploads/ /mnt/

# >&2 echo "Sync AWS bucket"
# MSYS_NO_PATHCONV=1 aws s3 sync s3://largefs-"${INSTALLNAME:?INSTALLNAME must be defined as an enviroment variable (or an .env file for sanity)}"/"$INSTALLNAME"/wp-content/uploads/ ./wp-content/uploads/

# >&2 echo "Regen thumbnails"
# MSYS_NO_PATHCONV=1 docker-compose run --rm wp-cli /var/www/html/.wp-cli/regen-thumbs.sh
