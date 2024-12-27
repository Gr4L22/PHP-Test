#!/bin/bash

echo "Starting PHP project..."

BACKEND_PORT=8000
BACKEND_PATH="./back/public"

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