#!/bin/bash

set -eo pipefail
pwd
git remote -v

git fetch -q --all
git checkout master
git branch --set-upstream-to pantheon/master

git add . 1>/dev/null 2>&2
git commit -m \"Deploy from Travis - `date +'%Y-%m-%d %H:%M:%S %Z'`\" 1>/dev/null 2>&2
git push -f pantheon master 1>/dev/null 2>&2

# Update Database and Sync Config
lando terminus aliases
lando drush @pantheon.ibm-dte.dev updatedb | grep "ssh: not found"
lando drush @pantheon.ibm-dte.dev cim -y
