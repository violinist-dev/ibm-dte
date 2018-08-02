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
lando drush site:install standard --sites-subdir=default -y
```
5. Navigate to [the local site](http://local.ibm-dte.mybluemix.net) in your browser.
