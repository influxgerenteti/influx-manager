#! /bin/sh
FILES="$(git log --name-only --pretty=oneline --full-index HEAD^^..HEAD | grep -vE '^[0-9a-f]{40} ' | sort | grep -E '^[src|test]' | tr '\n' ' ')"
export FILES
