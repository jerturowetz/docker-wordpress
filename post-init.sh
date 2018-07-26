#! bin/bash

echo 'Installing wordpress'
docker-compose run --rm wp-cli wp core install --url=wordpress.develop --title=SomethingCool --admin_user=supervisor --admin_password=password --admin_email=info@example.com

# echo 'Updateing database'
# docker-compose run --rm wp-cli wp search-replace https://$OLDURL http://$DEVURL

echo 'Activating plugins'
docker-compose run --rm wp-cli wp plugin activate --all

