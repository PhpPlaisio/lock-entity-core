#!/bin/bash -e -x

cat test/ddl/0010_create_database.sql      | mysql -v -u root      -h 127.0.0.1
cat test/ddl/0020_create_user.sql          | mysql -v -u root      -h 127.0.0.1
cat test/ddl/0100_create_tables.sql        | mysql -v -u root test -h 127.0.0.1
cat lib/ddl/0100_create_tables.sql         | mysql -v -u root test -h 127.0.0.1
cat test/ddl/0300_abc_lock_entity_name.sql | mysql -v -u root test -h 127.0.0.1

./bin/stratum -vv stratum test/etc/stratum.ini

./bin/phpunit
# Runs all unit second time with filed lock table
./bin/phpunit
