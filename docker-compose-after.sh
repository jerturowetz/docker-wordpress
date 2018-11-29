#! bin/bash

printf "\n%s\n" "Updating URL"
docker-compose run --rm wp-cli wp search-replace https://www.yoursite.com http://yoursite.develop

printf "\n%s\n" "flushing permalinks"
docker-compose run --rm wp-cli wp rewrite flush
