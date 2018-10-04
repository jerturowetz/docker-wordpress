#! bin/bash

printf "\n%s\n" "Installing wordpress"
docker-compose run --rm wp-cli wp core install --url=wordpress.develop --title=SomethingCool --admin_user=supervisor --admin_password=password --admin_email=info@example.com

printf "\n%s\n" "activate test theme"
docker-compose run --rm wp-cli wp theme activate _s

# printf "\n%s\n" "Updating URL"
# docker-compose run --rm wp-cli wp search-replace https://$OLDURL http://$DEVURL

# printf "\n%s\n" "Activating plugins"
# docker-compose run --rm wp-cli wp plugin activate --all

# printf "\n%s\n" "setting home page template"
# docker-compose run --rm wp-cli wp post update 356 --page_template='templates/front-page.php'

# printf "\n%s\n" "setting up energy audit page"
# docker-compose run --rm wp-cli wp post update 7156 --post_content='' --post_title='* Energy Audit Test' --page_template='templates/energy-audit-landing-page.php' --post_name='energy-audit-test' --post_status='publish'

# printf "\n%s\n" "Pluck an option"
# docker-compose run --rm wp-cli wp option patch update sitedefaults_options featured_products [\"9999\",\"9991\"] --format=json

# printf "\n%s\n" "Pluck an option"
# docker-compose run --rm wp-cli wp option pluck sitedefaults_options featured_products

# printf "\n%s\n" "Patch an option"
# docker-compose run --rm wp-cli wp option patch update sitedefaults_options featured_products [\"9999\",\"9991\"] --format=json

# printf "\n%s\n" "Have wp-cli run a file"
# docker-compose run --rm wp-cli wp eval-file wp-cli/list-urls.php

# printf "\n%s\n" "Running Wraith"
# docker-compose run --rm wraith capture config.yaml

# printf "\n%s\n" "Gets array of values from one option and pipes it to another option"
# MSYS_NO_PATHCONV=1 docker-compose run --rm wp-cli /bin/sh -c "wp option pluck sitedefaults_options featured_products --format=json | xargs wp option patch insert sp_products_opti ons featured_products --format=json"

printf "\n%s\n" "flushing permalinks"
docker-compose run --rm wp-cli wp rewrite flush
