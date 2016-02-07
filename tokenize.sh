#!/bin/bash
# Make sure this file is executable
# chmod +x tokenize.sh

if [[ `echo "$@" | grep '\-\-verbose'` ]] || [[ `echo "$@" | grep '\-v'` ]]; then
    VERBOSE=1
else
    VERBOSE=0
fi

if [[ "$VERBOSE" == 1 ]]; then
	php tools/tokenize.php $1 -v
else
    php tools/tokenize.php $1
fi

exit 0
