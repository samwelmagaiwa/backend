#!/usr/bin/env bash
# Cross-platform-friendly dev runner for Git Bash/WSL
set -euo pipefail

# Start Laravel API server
php backend/artisan serve &
API_PID=$!

# Start Laravel queue worker
php backend/artisan queue:listen --tries=1 &
QUEUE_PID=$!

# Start Vue frontend (Vue CLI)
npm --prefix frontend run serve &
FRONT_PID=$!

# Cleanup on exit
trap "kill $API_PID $QUEUE_PID $FRONT_PID 2>/dev/null || true" EXIT

# Wait on processes
wait
