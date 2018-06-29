#!/bin/bash

set -eo pipefail

git remote -v

git fetch --all
git checkout master
git branch --set-upstream-to pantheon/master
git status

git add .
git commit -m "Deploy from Travis - `date +'%Y-%m-%d %H:%M:%S %Z'`"
lando ssh -c "git push pantheon master"
