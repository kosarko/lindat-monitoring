#!/bin/bash
set -o pipefail
SCRIPTPATH=$(dirname $0)
BASE=$1
URLPATH=$2
PATTERN=$3
DEBUG=$4
wget --quiet -O - "$BASE/$URLPATH" | hxwls -b "$BASE" 2>&1 | egrep "$PATTERN" |
while read line; do
  if [ ! -z $DEBUG ]; then echo $line; fi
  if ! $SCRIPTPATH/check_url_status -U $line ;then
	echo $line
	exit 1
  fi
done

