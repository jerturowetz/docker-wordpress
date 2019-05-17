#!/bin/sh
# set -e # exit on error, even if in a while or whatever see https://www.gnu.org/software/bash/manual/bashref.html#The-Set-Builtin

${REPLACE_URL:?REPLACE_URL must be defined as an enviroment variable (or an .env file for sanity). Check docker-compose.yml as this variale might be dynamically built from the PROD_URL variable}
${WITH_URL:?WITH_URL must be defined as an enviroment variable (or an .env file for sanity). Check docker-compose.yml as this variale might be dynamically built from the DEV_URL variable}

>&2 echo "Import DB"
wp db import /var/www/html/.wp-cli/mysql.sql

>&2 echo "Updating URL"
wp search-replace ${REPLACE_URL} ${WITH_URL}

# >&2 echo "Deactivate old plugins"
# wp plugin deactivate

# >&2 echo "Delete old plugins"
# wp plugin delete akismet hello-dolly

# >&2 echo "Activate dev plugins"
# wp plugin activate debug-bar debug-media

# >&2 echo "Delete old widget for rantline from sidebar and assign new to post content"
# wp widget delete text-11

>&2 echo "flushing permalinks"
wp rewrite flush

if [ $# -ne 0 ]
  then
  >&2 echo "Running additional commands"
  exec "$@"
fi
