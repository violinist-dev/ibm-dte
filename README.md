# IBM DTE

## Getting a local set up.

1. Clone this repo.
2. Create an alias in your `/etc/hosts` for the local domain:
```
# If you are on Linux or Mac:
sudo sh -c "echo '127.0.0.1 local.ibm-dte.mybluemix.net' >> /etc/hosts"
```
3. Run `lando start` to start your local development environment.
4. Install an empty Drupal site:
```
lando drush si config_installer --sites-subdir=default
```
5. Navigate to [the local site](http://local.ibm-dte.mybluemix.net) in your browser.
6. To log in to the site:
```
lando drush uli
# On Mac, you can run:
open `lando drush uli`
```

## Develpment

### Pulling in upstream changes

If you pull down fresh code, you'll need to run the following steps:

```
git pull
lando composer install
lando drush config:import
lando drush updb
lando drush cache:rebuild
```

### Making config changes

```
# First step? Pull upstream changes! (see above)
# Then:
lando drush config:export
git add --patch config
```

### Adding modules or themes

To add a contrib module or theme:

```
lando composer require drupal/mymodule
git add composer.*
lando drush en mymodule
```


