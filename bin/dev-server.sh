#!/bin/bash
set -e
cd -- "$(dirname "$0")/.." >/dev/null 2>&1

docker compose -f docker-compose.dev.yml up --build