# WordPress on Docker [the Docker way](https://www.docker.com/what-docker)!

This is a straightforward docker-compose template for setting up a local Wordpress development environment using docker. It has only one requirement, docker.

This template was originally designed for projects meant to deploy to [WP Engine](https://my.wpengine.com); as such, you might notice some WP Engine specific idiosyncracies like certain `.gitignore` additions or that the theme lives at the traditional `./wp-content/themes/you-theme/` location instead of something a bit more elegant like `./your-theme/`.

> But there are already a bunch of templates for developing WordPress on docker! Why not use one of those?

Most of the templates I found bake wp-cli, Xdebug, phpunit, etc into a single container. Some even give instructions on how to ssh into the active containers to install missing dependancies manually. *This is bad* as we end up with less reliable, less versatile, bloated images. This template lets you add/remove containers as you like and is pretty delete friendly. Don't need something? Erase it. Wanna add something? Go to town!

Also, most other image don't include a reverse proxy for developing on a custom local domain.

Aside: if you're into using a catch-all VM for WordPress development, take a look at [my WordPress Vagrant box](https://github.com/jerturowetz/homestead-wp) :: a fork of Laravel's homestead.

## Requirements

- [docker](https://www.docker.com)

I also like to use vscode, yarn, php, composer, styleline & eslint; but you dont need any of those things to get rocking, they're just bells and whistles.

## Container stack

- traefik
- adminer
- mysql
- wordpress (my own php7.0 image, its not much different than the official, only has an extra php extension)
- wp-cli
- composer x2 (one container for dev deps and the other specifically for plugins)

Items of note:

reverse proxy is amazing
My personal wordpress image but feel free to use the official image
composer to install plugins completely seperately from the dev deps, this keeps plugins in the volume with the wordpress core files (and away from your working directory)

Delete friendly: Dont need composer? kill it! Wanna commit plugins to your project, remove the gitignore entry and go to town. Dont need the reverse proxy? who cares!

## Includes

- `.editorconfig` for wordpress coding standards
- phpcs & wordpress coding standards as composer dependencies
- eslint & stylelint with wordpress standards as node dependancies
- vscode file excldes
- vscode rules for phpcs plugin

## Quick-start

- Put dbs to import in docker/mysql/ (make sure to adjust docker-compose with the right db name everywhere)

- Clone or download this package
- Download and place uploads in the `wp-content/uploads` folder
- Download a copy of the current database and place in `/docker/mysql/`
- Place your theme files at `wp-content/themes/mytheme` and change `mytheme` to whatever you want
- Edit `docker-compose.yml` with your project details
  - change `wp-content/themes/mytheme` to whatever your actual folder name is (or remove if youre working on a plugin)
  - change the traefik server to whatever domain you'd like to use for local development `traefik.frontend.rule=Host:wordpress.develop`
- Edit `docker/composer/plugins.json` with your required plugins
- Edit your hosts file to the trafeik server above (please note if you're using doocker-machine you can use `$ docker-machine ip` to get the machine ip)
- run `export COMPOSE_CONVERT_WINDOWS_PATHS=1` as present version of docker for windows is f-ed
- run `docker-compose up -d`

if you imported a database then:

    docker-compose run --rm wp-cli wp search-replace https://$OLDURL http://$DEVURL

if you are installing fresh then:

    docker-compose run --rm wp-cli wp core install --url=wordpress.develop --title=SomethingCool --admin_user=supervisor --admin_password=password --admin_email=info@example.com

- run `docker-compose run --rm wp-cli wp plugin activate --all`

## Using wp-cli

running wp-cli is `docker-compose run --rm wp-cli wp some-command`. You should def include the `--rm` tag so as to kill the container once your command is run and not create an orphaned container.

## To do

- Get docker-compose to run db rename tasks instead of needing to wait for wp-cli
- Change container stack to more closely mirror WP Engine configuration
  - PHP 5.5.9-1ubuntu4.24 (5.6)
  - PHP 7.0 FPM
  - nginx/1.11.3 >> Apache/2.4.7
  - mysql Ver 14.14 Distrib 5.6.39-83.1, for debian-linux-gnu using readline 6.3
  - Varnish: varnishd (varnish-3.0.7 revision f544cd8

change composer.jso  and plugins to specific to require and require-dev, even plugins

Plugins to find

- image compression
- security plugin
- SEO
- Gallery (is the native gallery good enough?)
- events
- enviragallery

Find a means of using markdown in the editor
Look in to using gutenberg
