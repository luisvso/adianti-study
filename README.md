# How to Start the project with no problems (or with minor ones :) )


#### First Install all the requirements for the project with composer

```
composer install

```

### Start the configuration for ddev
`
ddev config
`

#### Change the database like this:
```json
name: template
type: php
docroot: .
php_version: "8.4"
webserver_type: nginx-fpm
xdebug_enabled: false
additional_hostnames: []
additional_fqdns: []
database:
    type: postgres <- change this to postgres
    version: "17"
use_dns_when_possible: true
composer_version: "2"
web_environment: []
nodejs_version: "24"
corepack_enable: false
```
### Finally run the application with:

```
ddev start && xdebug on

```


### To create the databases you need :

```
ddev psql -c "CREATE TABLE your_database;"

```


### To populate the database you created:

```
ddev import-db --database=you_database --file=path/to/file.sql

```
