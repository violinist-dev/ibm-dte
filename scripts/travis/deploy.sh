#!/bin/bash

set -eo pipefail

git checkout -b deploy`date +'%Y-%m-%d-%H-%M-%S-%Z'`
git status

# git add .
# git commit -m "Deploy from Travis - `date +'%Y-%m-%d %H:%M:%S %Z'`"
lando ssh -c "git push pantheon master"
