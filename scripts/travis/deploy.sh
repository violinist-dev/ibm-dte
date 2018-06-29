#!/bin/bash

set -eo pipefail

git add vendor web
git commit -m "Deploy from Travis - `date +'%Y-%m-%d %H:%M:%S %Z'`"
lando ssh -c "git -C push pantheon master"
