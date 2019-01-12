#! bin/bash

PROD_SITES=( yoursite.com yoursite.wpengine.com )
SUBSITES=( blog d365 )
OLD_CURRENT_SITE=yoursite.wpengine.com # this will get reset at some point and fuck me up
DEV_SITE=yoursite.develop # right now the script assumes we're using non-www target domains

# Go to table wp_blogs
# Replace the domain and path fields with the new values.
printf "\n%s\n" "Adjust the root site url to match DOMAIN_CURRENT_SITE and avoid confusion"
docker-compose run --rm wp-cli wp search-replace ${OLD_CURRENT_SITE} ${DEV_SITE} wp_blogs wp_site

# Go to table wp_options
# In this table, change the fields site_url and home - AUTOMATIC VIA SCRIPTS BELOW

# Go to table wp_sitemeta
# Change the site_url field  - AUTOMATIC VIA SCRIPTS BELOW

# wp_x_options: - AUTOMATIC VIA SCRIPTS BELOW
#   home
#   siteurl
#   upload_path
#   upload_url_path


replace_www() {
  local URL=$1
  if [[ $URL == "www."* ]];
  then
    URL=${URL/www./}
  fi
  echo "$URL"
}

search_replace() {
  local PROTOCOLS=( https:// http:// )
  local SEARCH_FOR=$1
  local REPLACE_WITH=$2
  for protocol in "${PROTOCOLS[@]}"
  do
    printf "\n" "Updating URL from ${protocol}${SEARCH_FOR} to http://${REPLACE_WITH}"
    docker-compose run --rm wp-cli wp search-replace ${protocol}${SEARCH_FOR} http://${REPLACE_WITH} --recurse-objects --network
  done
}

network_search_replace() {
  local SEARCH_FOR=$(replace_www ${1})
  local REPLACE_WITH=$2

  # runs search replace on main domain & www version if not a wpengine domain
  search_replace ${SEARCH_FOR} ${REPLACE_WITH}
  if [[ $SEARCH_FOR != *".wpengine."* ]];
  then
    search_replace www.${SEARCH_FOR} ${REPLACE_WITH}
  fi

  # runs search replace on sub domains
  local NEW=$(replace_www ${REPLACE_WITH})
  for slug in "${SUBSITES[@]}"
  do
    search_replace ${slug}.${SEARCH_FOR} ${slug}.${NEW}
  done

}

for site in "${PROD_SITES[@]}"
do
  printf "\n%s\n" "Running search replace tasks on ${site} to ${DEV_SITE}"
  network_search_replace ${site} ${DEV_SITE}
done

printf "\n%s\n" "flushing permalinks"
docker-compose run --rm wp-cli wp rewrite flush
