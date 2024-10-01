#!/bin/sh

/usr/bin/mysqldump --column-statistics=0 $@
