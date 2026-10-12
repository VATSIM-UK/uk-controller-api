#!/usr/bin/env bash

set -euo pipefail

cd /var/www/ukcp

step() {
    echo
    echo "==> $1"
}

# NB: deliberately no `git config --global` here. The Dev Containers extension
# copies the host's ~/.gitconfig into the container *after* this script runs,
# but skips that copy if ~/.gitconfig already exists. System-scope defaults
# (safe.directory / push.autoSetupRemote) are set in the Dockerfile instead.
if [ -z "$(git config user.email 2>/dev/null || true)" ]; then
    echo
    echo "warning: git user.email is not set, so commits will fail. VS Code copies it"
    echo "         from your host ~/.gitconfig when the container starts; if that did"
    echo "         not happen, set it once with:"
    echo "           git config --global user.name  \"Your Name\""
    echo "           git config --global user.email \"you@example.com\""
fi

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
