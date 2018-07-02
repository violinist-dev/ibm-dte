#!/bin/bash

set -eo pipefail

lando ssh -c "git remote -v"

lando ssh -c "git fetch -q --all"
lando ssh -c "git checkout master"
lando ssh -c "git branch --set-upstream-to pantheon/master"

lando ssh -c "git pull pantheon master 1>/dev/null 2>&2"

lando ssh -c "git add . 1>/dev/null 2>&2"
lando ssh -c "git commit -m \"Deploy from Travis - `date +'%Y-%m-%d %H:%M:%S %Z'`\" 1>/dev/null 2>&2"
lando ssh -c "git push pantheon master 1>/dev/null 2>&2"
