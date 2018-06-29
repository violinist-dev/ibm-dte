#!/bin/bash

set -eo pipefail

# Rename origin to upstream, set
git remote rename origin upstream
git remote add origin $PANTHEON_GIT
git branch --set-upstream-to origin

lando push --files=none --database=none -m "Deploy from Travis - `date +'%Y-%m-%d %H:%M:%S %Z'`"

git remote remove origin
git remote rename upstream origin
git branch --set-upstream-to origin
