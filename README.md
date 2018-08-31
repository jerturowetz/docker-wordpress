# _s for WordPress on Docker [the Docker way](https://www.docker.com/what-docker)

This project combines a slightly modified version of the [_s theme by Automattic](https://underscores.me) with a docker environment for local development. It's primary purpose is to help devs spend more time developing themes and less time tweaking environment configs.

The project assumes a working-level knowledge of docker, docker-compose, WordPress and modern development techniques.

> why not put the theme in the root and mount it to `/wp-content/` instead of burying it in `/wp-content/themes/` ?

I like working with [managed WordPress hosts](https://shareasale.com/r.cfm?b=394686&u=1811103&m=41388&urllink=&afftrack=) so you might notice some items geared towards deploying in such enviroments (inelegant folder structure for the theme, a WP Engine focused `bitbucket-pipelines.yml` example and managed-host style `.gitignore`).

> But there are already a bunch of templates for developing WordPress on docker! Why not use one of those?

Most of the templates I found bake wp-cli, xdebug, phpunit, etc into a single container, some even give instructions on how to ssh into the active containers to install missing dependancies manually (*this is bad*). Others are full-service but all the  Docker images are provider-specific. This project tries to use official docker images and remain as delete friendly as possible. Don't need something? Erase it. Wanna add something? Go to town! In all cases the official docs for each container will provide relevant information.

> F%#@ this, I want an old-school VM!

If you'd rather use a catch-all VM for WordPress development, take a look at my [WordPress Vagrant box](https://github.com/jerturowetz/homestead-wp).

## Deps & recommendations

The only requirement to get going is [Docker](https://www.docker.com) which manages all other deps by building containers for them. Optionally, to benefit from my bells and whistles, get [Node.js](https://nodejs.org/) installed and work with [VS Code](https://code.visualstudio.com/).

## Project features

- WordPress focused `.editorconfig` to match phpcs
- WordPress focused `.gitignore` for deploys on WP Engine
- example `bitbucket-pipelines.yml` for automating deploys (if you're in to that kind of thing)
- `composer.json` with phpcs & ruleset for WordPress
- `.vscode/settings.json` & `.vscode/extensions.json` with phpcs configured & useful file & search excludes (if you're using vscode of course)
- `package.json` with deps & WordPress style config for stylelint & eslint

### Theme-specific

If you'd like to learn about _s I suggest you check out [the official repo](https://github.com/Automattic/_s). Outlined below are ways my version of _s is _different_ from the official release.

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

### Docker-specific

Poke around in `docker-compose.yml` to get aquainted with individual settings for the container stack:

- `traefik` reverse proxy so that you can run _yousite.local_ instead of localhost
- `adminer` better than phpMyAdmin
- `mysql`
- `wordpress` nearly identical to the official WordPress image but with debugging turned on and the `intl` extension added
- `wp-cli` in its own container for one-off cli commands (the docker way)
- `composer` for instaling phpcs & wpcs locally
- `composer-plugins` for installing WordPress plugins listed in `composer-plugins/plugins.json` directly to the volume without cluttering up your local dev environment
- `wraith` [bbcnews/wraith](https://github.com/BBC-News/wraith) visual regression testing tool (off by default)

## Quick-start

- Clone or download this package
- point `wordpress.develop` to your docker machine ip in your system's hosts file (usually localhost)
- run `export COMPOSE_CONVERT_WINDOWS_PATHS=1` if you're on Windows because present version of Docker is f-ed
- run `docker-compose up`
- run `. docker-compose-after.sh` to install WordPress from scratch

## Other things you might wanna do

Change you dev url

- edit the wordpress container entry in `docker-compose.yml` to whatever url you like
- adjust your systems hosts file to suit

To load in an existing database:

- put a WordPress database in it in `docker/mysql/`
- replace all instances of `some_db_name` in `docker-compose.yml` with your db name
- use wp-cli to run a search-replace for the old url to the dev url

To change the automatically installed plugins

- edit `docker/composer-plugins/plugins.json` and add whatever plugins you like
- If you'd like to commit plugins directly to the repo, simply add them to `wp-content/plugins/` and specify the folder mounts in `docker-compose.yml` for both the `wordpress` & `wp-cli` containers

To add your own theme

- copy your theme to `wp-content/themes/` and replace/remove mounts for the `_s` theme in `docker-compose.yml`
 for both the `WordPress` & `wp-cli` containers

## Using wp-cli

The wp-cli container expects to be a run-once-and-die situation. The following command spins up the container (as defined in `docker-compose.yml`)

    # docker-compose run --rm wp-cli wp some-command
    # For example:
    docker-compose run --rm wp-cli wp plugin list

There are a few examples of how to do this in `docker-compose-after.sh` which, for the record, is a good place to put your always-necess steps after spinning up new volumes (ie running search and replace tasks for imported DBs).

You should always include the `--rm` tag so as to kill the container once your command has completed and not create an orphaned container.

## Caveats

If you run mutiple versions of this project at once on the same localhost you will get prot conflicts (so kill one before starting another).

## To do

- Get docker-compose to run db rename tasks instead of needing to wait for wp-cli
- Change container stack to more closely mirror WP Engine configuration
  - PHP 5.5.9-1ubuntu4.24 (5.6)
  - PHP 7.0 FPM
  - nginx/1.11.3 >> Apache/2.4.7
  - mysql Ver 14.14 Distrib 5.6.39-83.1, for debian-linux-gnu using readline 6.3
  - Varnish: varnishd (varnish-3.0.7 revision f544cd8
- Review `wp-content/themes/_s/inc/cleanup.php` and remove excessive items
- add gulp or webpack to build styles from sass
- Add init script to perform rename on _s theme & `docker-compose.yml`
  1. Search for: `'_s'` and replace with: `'megatherium-is-awesome'`
  2. Search for: `_s_` and replace with: `megatherium_is_awesome_`
  3. Search for: `Text Domain: _s` and replace with: `Text Domain: megatherium-is-awesome` in `style.css`.
  4. Search for: <code>&nbsp;_s</code> and replace with: <code>&nbsp;Megatherium_is_Awesome</code>
  5. Search for: `_s-` and replace with: `megatherium-is-awesome-`
  6. Update the stylesheet header in `style.css`, the links in `footer.php` with your own information and rename `_s.pot` from `languages` folder to use the theme's slug.
- should i uncomment the following in `inc/images.php`
    add_filter( 'image_send_to_editor', '_s_remove_thumbnail_dimensions', 10, 3 );
- Add better testing
- Add non-vscode focused linting
- Add working xdebug setup
- Manage theme assets with webpack
- Trigger image size updates automatically on theme install `update_option( 'large_size_w', 640 );` & `update_option( 'large_size_h', 640 );`
- Write wp-cli commands to set defaults (ie, turn off _Organize my uploads into month-and year-based folders_)

## Contributing

Just get in touch and let's collaborate!
