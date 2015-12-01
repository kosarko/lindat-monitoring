# Plugins

These are custom made nagios plugins

## Prerequisites

* define LindatPluginDir in icinga2 constants.conf (TODO review)
* `check_resources.sh` requires `hxwls` (from html-xml-utils) and must be in the same dir as `check_url_status`
* `check_url_status` requires LWP::Simple (libwww-perl)
* `check_http_enhanced` requires Switch.pm (libswitch-perl)
