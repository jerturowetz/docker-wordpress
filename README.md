# Docker-WordPress for WPEngine development

This project combines a slightly modified version of the [_s theme by Automattic](https://underscores.me) with a docker environment for local development. It's primary purpose is to help devs spend more time developing themes and less time tweaking environment configs.

The project assumes a working-level knowledge of docker, docker-compose, WordPress and modern development techniques.

> why not put the theme in the root and mount it to `/wp-content/` instead of burying it in `/wp-content/themes/` ?

I like working with [managed WordPress hosts](https://shareasale.com/r.cfm?b=394686&u=1811103&m=41388&urllink=&afftrack=) so you might notice some items geared towards deploying in such enviroments (inelegant folder structure for the theme, a WPEngine focused `bitbucket-pipelines.yml` example and managed-host style `.gitignore`).

> But there are already a bunch of templates for developing WordPress on docker! Why not use one of those?

Most of the templates I found bake wp-cli, xdebug, phpunit, etc into a single container, some even give instructions on how to ssh into the active containers to install missing dependancies manually (*this is bad*). Others are full-service but all the  Docker images are provider-specific. This project tries to use official docker images and remain as delete friendly as possible. Don't need something? Erase it. Wanna add something? Go to town! In all cases the official docs for each container will provide relevant information.

> F%#@ this, I want an old-school VM!

If you'd rather use a catch-all VM for WordPress development, take a look at my [WordPress Vagrant box](https://github.com/jerturowetz/homestead-wp).

## Requirements

The only requirement for local development is [Docker](https://www.docker.com).

To fully benefit from the included tools (wpcs, gulp, esling, styleint) it is recommended you have [Node.js](https://nodejs.org/) installed, work in [VS Code](https://code.visualstudio.com/) and use the recommended extensions (`.vscode/extensions.json`).

## Project features

- WordPress focused `.editorconfig` to match phpcs
- WordPress focused `.gitignore` for deploys on WPEngine
- `bitbucket-pipelines.yml` for automated deploys
- `composer.json` with phpcs & ruleset for WordPress
- `.vscode/settings.json` & `.vscode/extensions.json` with phpcs configured & useful file & search excludes
- `package.json` with deps & WordPress style config for stylelint & eslint

### Theme-specific features

If you'd like to learn about _s I suggest you check out [the official repo](https://github.com/Automattic/_s). Outlined below are ways my version of _s is _different_ from the official release.

Please note that after being initialized, the theme has likely been renamed as well as all prefixed functions listed below.

- files removed:
  - git & github related details
  - `/inc/wpcom.php`
  - `.jscsrc`
  - `.jshintignore`
  - `.travis.yml`
  - `phpcs.xml.dist`
  - `README.md`
  - `readme.txt`
  - `LICENSE`
- instead of one-by-one, `functions.php` modified to auto include all `*.php` files in `inc/`
- login page tweaks added (`inc/login.php`, `sass/login.scss` & `login.css`)
- admin area tweaks added (`admin.js`, `admin.scss` & `admin.css`)
- other customizations added in `inc/images.php` & `inc/cleanup.php`
- added [`inc/cmb2/`](https://github.com/CMB2/CMB2) and included in `functions.php`
- excerpts added to pages in `functions.php`
- `inc/wpml.php` & `inc/wpseo.php` (yoast) tweaks added
- wrappers added to check if plugin is active in `inc/jetpack.php`, `inc/woocommerce.php`, `inc/wpml.php`, `inc/wpseo.php`
- helper functions added:
  - `_s_get_theme_version`
  - `_s_turn_delimited_string_into_an_array`
  - `_s_remove_empty_strings_from_array`
  - `_s_post_exists`

### Docker-specific features

Poke around in `docker-compose.yml` to get aquainted with individual settings for the container stack:

- `traefik` reverse proxy so that you can run _yoursite.develop_ instead of localhost
- `adminer` better than phpMyAdmin
- `mysql`
- `wordpress` nearly identical to the official WordPress image but with debugging turned on and the `intl` extension added
- `wp-cli` in its own container for one-off cli commands (the docker way)
- `composer` for instaling phpcs & wpcs locally
- `composer-plugins` for installing WordPress plugins listed in `composer-plugins/plugins.json` directly to the volume without cluttering up your local dev environment

There are several variables defined to minimize your need to edit `docker-compose.yml`, to make your life easier I recommend you manage them via an `.env` file.

#### Required enviroment variables

- `INSTALLNAME`
- `PROD_URL`
- `DEV_URL`
- `THEME_FOLDER`
- `AWS_PROFILE`

## Quick-start

- Clone or download this repo
- add an `.env` file to the project root and define the details for your site
- In your system's hosts file point `yoursite.develop` to your docker machine ip (usually localhost)
- Set up ssh for your local machine
- If on Windows, run `export COMPOSE_CONVERT_WINDOWS_PATHS=1` (as the present version of Docker is f-ed)
- Run `docker-compose up` and wait until the mysql container imports the db and the wordpress container is able to connect to it
- Run `. docker-compose-after.sh` to run database rename tasks
- Go to `yoursite.develop` to access the site

## Using linting tools

Use `npm install` or `yarn install` to install node deps locally
Use `composer install` to set up phpcs locally

## Using wp-cli

The wp-cli container is designed to run a single action and die (re the docker way of things). Accessing the machine via docker-compose is pretty straightforward:

    # docker-compose run --rm wp-cli wp some-command
    # For example:
    docker-compose run --rm wp-cli wp plugin list

You can find a bunch of examples on how to run the wp-cli container in `docker-compose-after.sh` which is also a good place to store always-use commands (like the db rename task you'll need to run on each fresh `docker-compose up`).

Please note the use of the `--rm` tag, which guarantees that the container will be killed once your command has completed and not create an orphaned container.

## Changing automatically installed plugins

- Edit `docker/composer-plugins/plugins.json` and add whatever plugins you like. If running, you'll need to tear down your container stack and start afresh.
- If you'd like to commit plugins directly to the repo, simply add them to `wp-content/plugins/` and mounts the folders in `docker-compose.yml` for both the `wordpress` & `wp-cli` containers

## Workflow & deployments

The `bitbucket-pipelines.yml` file is set up to automatically `master`, `staging` and `dev` branches to their respective WPEngine installs. As such, it is recommended to work via feature branches locally, merge them to the `dev` or `staging` for testing and then merge to `master` for deployment. Please note this workflow prefers you ignore WPEngine's legacy staging environments.

Accessible via the [WPEngine admin console](https://my.wpengine.com/), the environments are:

- [PROD: yoursite.com](https://yoursite.com)
- [STAGE: yoursitestage.wpengine.com](https://yoursitestage.wpengine.com)
- [DEV: yoursitedev.wpengine.com](https://yoursitedev.wpengine.com)

## Caveats

If you run mutiple versions of this project at once on the same host system you will get port conflicts. Kill one before starting another.

## To-do

- Get docker-compose to run db rename tasks instead of needing to wait for wp-cli
- Change container stack to more closely mirror WP Engine configuration
  - PHP 5.5.9-1ubuntu4.24 (5.6)
  - PHP 7.0 FPM
  - nginx/1.11.3 >> Apache/2.4.7
  - mysql Ver 14.14 Distrib 5.6.39-83.1, for debian-linux-gnu using readline 6.3
  - Varnish: varnishd (varnish-3.0.7 revision f544cd8
- Review `wp-content/themes/_s/inc/cleanup.php` and remove excessive items
- should i uncomment the following in `inc/images.php`
    `add_filter( 'image_send_to_editor', '_s_remove_thumbnail_dimensions', 10, 3 );`
- Add working xdebug setup
- Manage theme assets with webpack
- Trigger image size updates automatically on theme install `update_option( 'large_size_w', 640 );` & `update_option( 'large_size_h', 640 );`
- Write wp-cli commands to set defaults (ie, turn off _Organize my uploads into month-and year-based folders_)


Home page disscusion
