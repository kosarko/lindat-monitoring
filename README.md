# lindat-monitoring
[Icinga2](https://www.icinga.org/icinga/icinga-2/) configuration to monitor lindat services

## Installation

Installation should be quite easy. First install the prerequisites and then just include the configs.


### Prerequisites
On ubuntu `apt-get install nagios-plugins-basic nagios-plugins-contrib`
see plugins/README.md for the list of prerequisites

### Install
Just include_recursive the checkout dir in `icinga2.conf` and add path to plugins dir to constants

Eg. if you cloned to `/opt/lindat-monitoring`

```
echo 'include_recursive "/opt/lindat-monitoring/ufal.d"' >> /etc/icinga2/icinga2.conf
echo 'const LindatPluginDir = "/opt/lindat-monitoring/plugins"' >> /etc/icinga2/conf.d/constants.conf
```

restart icinga2

### vagrant

or check [lindat-monitoring-vagrant](https://github.com/ufal/lindat-monitoring-vagrant)
