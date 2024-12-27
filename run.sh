#!/bin/bash

echo "Starting PHP project..."

BACKEND_PORT=8000
COMPOSER_PATH="./back"
BACKEND_PATH="./public"

cd "$COMPOSER_PATH"
if [ -f composer.json ]; then
  if ! composer install; then
    echo "Error installing dependencies. Exiting."
    exit 1
  fi
else
  echo "composer.json not found. Skipping composer install."
fi

if [ ! -d "$BACKEND_PATH" ]; then
  echo "Error: Directory '$BACKEND_PATH' does not exist."
  exit 1
fi

cd "$BACKEND_PATH" || {
  echo "Error changing to directory '$BACKEND_PATH'."
  exit 1
}

echo "Starting PHP server at http://localhost:$BACKEND_PORT"
php -S localhost:"$BACKEND_PORT" &

echo "PHP server started. Press Ctrl+C to stop."
while true; do
  sleep 1
done