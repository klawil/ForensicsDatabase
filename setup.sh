#!/bin/bash
if [ "$1" == "" ]; then
	echo "No database given"
	exit
fi
mysqluser="root" # User to create database with
mysqlwebuser="forensics" # Username for php user
mysqlwebpass="A15j89%%8JsTk991LexzQ#" # Password for php user
mysqldbname=$1 # Database name
mysqlcommand="mysql --user=$mysqluser -p"
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
#$mysqlc --database=$mysqldbname -e "create user '"$mysqlwebuser"'@'localhost' identified by '"$mysqlwebpass"';"
#if [ "$?" != "0" ]; then
#	echo -en "Error creating web user. Continue anyway (y/n)? "
#	read continuevar
#	if [ "$continuevar" != "y" ]; then
#		exit
#	fi
#fi
$mysqlc --database=$ysqldbname -e "grant all on $mysqldbname.* to '"$mysqlwebuser"'@'localhost';"
if [ "$?" != "0" ]; then
	echo -en "Error granting web user permissions. Continue anyway (y/n)? "
	read continuevar
	if [ "$continuevar" != "y" ]; then
		exit
	fi
fi
