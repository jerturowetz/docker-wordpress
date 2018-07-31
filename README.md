# WordPress on Docker [the Docker way](https://www.docker.com/what-docker)

This is a straightforward if opinionated docker-compose template for setting up a local Wordpress development environments using docker. It has only one requirement, [docker](https://www.docker.com).

It uses a  variation of the official wordpress image but turns on a bunch of debugging stuff (read more here) as well as some useful plugins for both prod and development

This template was originally designed for projects meant to deploy to [WP Engine](https://my.wpengine.com); as such, you might notice some WP Engine specific idiosyncracies like certain `.gitignore` additions or that the theme lives at the traditional `./wp-content/themes/you-theme/` location instead of something a bit more elegant like `./your-theme/`.

> But there are already a bunch of templates for developing WordPress on docker! Why not use one of those?

Most of the templates I found bake wp-cli, xdebug, phpunit, etc into a single container, some even give instructions on how to ssh into the active containers to install missing dependancies manually. I am of the opinion that *this is bad* - we end up with less reliable, less versatile, bloated images. This template lets you add/remove containers as you like and is pretty delete friendly. Don't need something? Erase it. Wanna add something? Go to town!

Also, most other image don't include a reverse proxy for developing on a custom local domain.

Aside: if you'd rather use a catch-all VM for WordPress development, take a look at [my WordPress Vagrant box](https://github.com/jerturowetz/homestead-wp).

## Requirements

- [docker](https://www.docker.com)

I also like to use vscode, yarn, php, composer, styleline & eslint; but you dont need any of those things to get rocking, they're just bells and whistles.

## Container stack

- traefik
- adminer
- mysql
- wordpress (my own php7.0 image, its not much different than the official, only has an extra php extension)
- wp-cli
- composer x2 (one container for local deps and the other specifically for plugins)

Items of note:

Traefik is super cool and I'd recommend you reading the docs
We're using my personal wordpress image but feel free to use the official image, they're nearly identical; thought my image turns on a bunch of debugging stuff
composer to install plugins completely seperately from the dev deps, this keeps plugins in the volume with the wordpress core files (and away from your working directory)

## Included extras

- `.editorconfig` for wordpress coding standards
- phpcs & wordpress coding standards as composer dependencies
- eslint & stylelint with wordpress standards as node dependancies
- vscode file excldes
- vscode rules for phpcs plugin

## Included WordPress plugins (`docker/composer/plugins.json`)

- cmb2
- enable-media-replace
- jetpack
- regenerate-thumbnails
- tiny-compress-images
- wordpress-seo
- wp-slimstat

### Dev plugins

- debug-bar
- developer
- log-deprecated-notices
- monster-widget
- query-monitor
- theme-check
- user-switching

## Quick-start

- Clone or download this package
- Download and place uploads in the `wp-content/uploads` folder
- Put dbs to import in `docker/mysql/` (make sure to adjust docker-compose with the right db name everywhere)
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
