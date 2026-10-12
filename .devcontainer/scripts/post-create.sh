#!/usr/bin/env bash

set -euo pipefail

cd /var/www/ukcp

step() {
    echo
    echo "==> $1"
}

git config --global --add safe.directory /var/www/ukcp
git config --global push.autoSetupRemote true

if [ ! -f .env ]; then
    step "Creating .env"
    cp .env.example .env
else
    step ".env already exists - leaving it untouched"
fi

if gh auth status >/dev/null 2>&1; then
    step "Handing your GitHub token to Composer (avoids API rate limits)"
    composer config -g github-oauth.github.com "$(gh auth token)" || true
else
    step "Skipping Composer GitHub token - run 'gh auth login' then re-run this script if Composer hits rate limits"
fi

step "Installing Composer dependencies"
composer install --no-interaction

if grep -qE '^APP_KEY=$' .env; then
    step "Generating the application key"
    php artisan key:generate
fi

step "Installing frontend dependencies and building assets"
yarn install --frozen-lockfile
yarn build

step "Running database migrations"
php artisan migrate --force

step "Installing git hooks"
prek install || true

cat <<'BANNER'

The app is served at https://ukcp.devapp

Useful commands inside the container:

    yarn dev           # Vite dev server with live rebuilds
    php artisan test   # test suite

Run 'gh auth login' once if you want the GitHub CLI - it persists
in the gh-config volume across rebuilds.

BANNER
