# Configuration

This directory follows most of the conventions set forth by defautl icinga2/conf.d. It uses few of the default templates etc. (TODO list)

## commands.conf

wraps our plugins as commands, uses `LindatPluginDir`, introduces `check_http` which in contrast to `http` from [itl](https://github.com/Icinga/icinga2/blob/master/itl/command-plugins.conf) doesn't have defaults, similarly `check_dns` thought that one does not support all the parameters, imports `plugin-check-command`

## constants.conf

Path to files dir. TODO why not LindatPluginDir?

## groups.conf

Two groups "Our services" and "components", these are used by the .js in /en/monitoring. "Our services" assgined to services in `services.conf` based on `host.vars.services`. "components" assigned to lindat and quest host to everything not having group "Our services"

## hosts.conf

hosts definitions. Services are assigned based on vars being (not)set.
* 
* 
* 
