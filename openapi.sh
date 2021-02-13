#!/usr/bin/sh

vendor/bin/openapi --output web/docs/openapi.json --exclude vendor --exclude tests --exclude web --format json ./