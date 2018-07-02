#!/bin/bash

set -eo pipefail

lando ssh -c "git remote -v"

lando ssh -c "git fetch --all"
lando ssh -c "git checkout master"
lando ssh -c "git branch --set-upstream-to pantheon/master"
lando ssh -c "git status"

lando ssh -c "git add ."
lando ssh -c "git commit -m \"Deploy from Travis - `date +'%Y-%m-%d %H:%M:%S %Z'`\""
lando ssh -c "git push -q pantheon master"
