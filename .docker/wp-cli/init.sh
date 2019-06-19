#!/bin/sh
# set -e # exit on error, even if in a while or whatever see https://www.gnu.org/software/bash/manual/bashref.html#The-Set-Builtin

# >&2 echo "Install WP as ${WITH_URL:?WITH_URL must be defined as an enviroment variable (or an .env file for sanity). Check docker-compose.yml as this variale might be dynamically built from the DEV_URL variable}"
# wp core install --url=${WITH_URL} --title="Some site" --admin_user=admin --admin_email=admin@whatever.com

printf "Import DB\n"
wp db import /var/www/html/.wp-cli/mysql.sql

printf "Updating URL from %s\n" "${REPLACE_URL:?REPLACE_URL must be defined as an enviroment variable (or an .env file for sanity). Check docker-compose.yml as this variale might be dynamically built from the PROD_URL variable} to ${WITH_URL:?WITH_URL must be defined as an enviroment variable (or an .env file for sanity). Check docker-compose.yml as this variale might be dynamically built from the DEV_URL variable}"
wp search-replace "${REPLACE_URL}" "${WITH_URL}"

printf "Delete any themes starting with twenty*\n"
# shellcheck disable=SC2046
wp theme delete $(wp theme list --format=csv --field=name | awk '/twenty/{ print $0 }' ORS=' ')

# printf "Deactivate old plugins\n"
# wp plugin deactivate akismet hello-dolly

# printf "Delete old plugins\n"
# wp plugin delete akismet hello-dolly

# printf "Activate dev plugins\n"
# wp plugin activate debug-bar debug-media

# printf "Delete old widget for rantline from sidebar and assign new to post content\n"
# wp widget delete text-11

printf "flushing permalinks\n"
wp rewrite flush

printf "flushing cache\n"
wp cache flush

if [ $# -ne 0 ]
  then
  printf "Running additional commands\n"
  exec "$@"
fi

exit 0
