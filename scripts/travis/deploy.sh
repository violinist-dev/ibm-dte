#!/bin/bash

set -eo pipefail

# Did Skip Cleanup Not Work?
lando composer install
lando composer prepare-for-pantheon

# Rename origin to upstream, set
git remote rename origin upstream
git remote add origin $PANTHEON_GIT
git remote -v

git add .
git commit -m "Deploy from Travis - `date +'%Y-%m-%d %H:%M:%S %Z'`"
lando ssh -c "git -C push origin master"

git remote remove origin
git remote rename upstream origin
