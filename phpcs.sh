#!/bin/bash
# Make sure this file is executable
# chmod +x phpcs.sh

if [ `echo "$@" | grep '\-\-fix'` ] || [ `echo "$@" | grep '\-f'` ]; then
    FIX=1
else
    FIX=0
fi

if [ "$FIX" = 1 ]; then
	# Sniff only
	vendor/bin/phpcbf --standard=PSR2R/ruleset.xml -v --ignore=psr2r-sniffer/vendor/,tests/files/ ./
else
	# Sniff and fix
	vendor/bin/phpcs --standard=PSR2R/ruleset.xml -v --ignore=psr2r-sniffer/vendor/,tests/files/ ./
fi
