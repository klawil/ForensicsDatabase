#!/bin/bash
mysqluser="" # User to create database with
mysqlpass="" # Password
mysqlwebuser="" # Username for php user
mysqlwebpass="" # Password for php user
mysqldbname="" # Database name
mysqlcommand="mysql --user=$mysqluser --password=$mysqlpass"
$mysqlc -e "create database $mysqldbname;"
if [ "$?" != "0" ]; then
	echo -en "Error creating database $mysqldbname. Continue anyway (y/n)? "
	read continuevar
	if [ "$continuevar" != "y" ]; then
		exit
	fi
fi
$mysqlc --database=$mysqldbname < TemplateDB.sql
if [ "$?" != "0" ]; then
	echo -en "Error creating tables. Continue anyway (y/n)? "
	read continuevar
	if [ "$continuevar" != "y" ]; then
		exit
	fi
fi
$mysqlc --database=$mysqldbname -e "create user '"$mysqlwebuser"'@'localhost' identified by '"$mysqlwebpass"';"
if [ "$?" != "0" ]; then
	echo -en "Error creating web user. Continue anyway (y/n)? "
	read continuevar
	if [ "$continuevar" != "y" ]; then
		exit
	fi
fi
$mysqlc --database=$ysqldbname -e "grant all on $mysqldbname.* to '"$mysqlwebuser"'@'localhost';"
if [ "$?" != "0" ]; then
	echo -en "Error granting web user permissions. Continue anyway (y/n)? "
	read continuevar
	if [ "$continuevar" != "y" ]; then
		exit
	fi
fi
