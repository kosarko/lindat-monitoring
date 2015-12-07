# Configuration

This directory follows most of the conventions set forth by defautl icinga2/conf.d. It uses few of the default templates etc. (TODO list)

* plugin-check-command
* mail-service-notification
* mail-host-notification
* icingaadmins (UserGroup)

## commands.conf

wraps our plugins as commands, uses `LindatPluginDir`, introduces `check_http` which in contrast to `http` from [itl](https://github.com/Icinga/icinga2/blob/master/itl/command-plugins.conf) doesn't have defaults, similarly `check_dns` thought that one does not support all the parameters, imports `plugin-check-command`

## constants.conf

Path to files dir, required by some command/plugins. LindatPluginDir is set in `/etc/icinga2/icinga2.conf` (during installation) as it contains the checkout path.

## groups.conf

Two groups "Our services" and "components", these are used by the .js on lindat.cz/en/monitoring. "Our services" assgined to services in `services.conf` based on `host.vars.services`. "components" assigned to lindat and quest host to everything not having group "Our services"

## hosts.conf

hosts definitions. Services are assigned based on vars being (not)set.
* vars.using_http_check - run http_check on the url with the provided setting (http/https in time, status, contains string...). Check services.conf for more details.
* vars.service - basically the same as http_check, but different defaults and a group is assigned
* vars.no_notify - disable notification for host
* vars.http_vhost - among other things triggers certificate check
* vars.no_ssl - disable certificate check

## notifications.conf

Notifications send with 9m interval, for the first 2h after issue.

requires:
* icingaadmins UserGroup

imports:
* mail-service-notification
* mail-host-notification

## services.conf

Assign services mostly to hosts with specific variable set, defines few "one host only" checks.

* certificate-health - check certificat for hosts having `vars.http_vhost && !`vars.no_ssl`
* a service is assigned to each member of the `vars.using_http_check` dictionary, the defaults are: enabled ssl, vhost = vars.http_vhost, check interval = 1m. These can be overriden
* a service is assigned to each member of the `vars.services` dictionary, the defaults are: enabled ssl, vhost = vars.http_vhost, check interval = 1m. expect HTTP/1.1 200, warn_time 5s, critical_time 10s. These can be overriden
