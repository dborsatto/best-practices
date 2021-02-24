#!/bin/sh

git diff --name-only --cached --diff-filter=A HEAD | grep php | while read filename; do
  php bin/php-cs-fixer fix $filename --rules=declare_strict_types
done

git diff --name-only --cached --diff-filter=ACMRTUXB HEAD | grep php | while read filename; do
  php bin/php-cs-fixer fix $filename
done
