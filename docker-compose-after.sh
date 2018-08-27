#! bin/bash

echo 'Installing wordpress'
docker-compose run --rm wp-cli wp core install --url=wordpress.develop --title=SomethingCool --admin_user=supervisor --admin_password=password --admin_email=info@example.com

echo 'activate test theme'
docker-compose run --rm wp-cli wp theme activate _s

# echo 'Updating URL'
# docker-compose run --rm wp-cli wp search-replace https://$OLDURL http://$DEVURL

echo 'Activating plugins'
docker-compose run --rm wp-cli wp plugin activate --all

# echo 'setting home page template'
# docker-compose run --rm wp-cli wp post update 356 --page_template='templates/front-page.php'

# echo 'setting up energy audit page'
# docker-compose run --rm wp-cli wp post update 7156 --post_content='' --post_title='* Energy Audit Test' --page_template='templates/energy-audit-landing-page.php' --post_name='energy-audit-test' --post_status='publish'

echo 'flushing permalinks'
docker-compose run --rm wp-cli wp rewrite flush
