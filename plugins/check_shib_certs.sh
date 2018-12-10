#!/bin/bash
set -euo pipefail
LINDAT=https://lindat.mff.cuni.cz/Shibboleth.sso/Metadata
URL=${1:-$LINDAT} 
MONTH_IN_SEC=$(( 30 * 24 * 60 * 60 ))
MIN_VALID_SEC=${2:-$MONTH_IN_SEC}
TMP_DIR=`mktemp -d`
FAIL=0
pushd $TMP_DIR > /dev/null
curl -s "$URL" > metadata.xml
for i in $(seq 1 $(xmllint --xpath 'count(//*[local-name()="X509Certificate"])' metadata.xml));do
    xmllint --xpath "//*[local-name()='KeyDescriptor'][$i]//*[local-name()='X509Certificate']/text()" metadata.xml | sed -e '1i-----BEGIN CERTIFICATE-----' -e '$a-----END CERTIFICATE-----'  > $i.pem
    if openssl x509 -checkend $MIN_VALID_SEC -noout -in $i.pem; then
        true
    else
        openssl x509 -enddate -noout -in $i.pem
        FAIL=2
    fi
done
popd > /dev/null
rm -rf $TMP_DIR
exit $FAIL
