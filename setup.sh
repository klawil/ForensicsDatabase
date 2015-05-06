#!/bin/bash
mysqluser="root" # User to create database with
echo -en "Enter the password for MySQL user root: "
read mysqlpass
echo -en "Enter the database name desired: "
read mysqldbname
mysqlwebuser="forensics" # Username for php user
mysqlwebpass="A15j89%%8JsTk991LexzQ#" # Password for php user
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
$mysqlc --database=$mysqldbname -e "grant all on $mysqldbname.* to '"$mysqlwebuser"'@'localhost';"
if [ "$?" != "0" ]; then
	echo -en "Error granting web user permissions. Continue anyway (y/n)? "
	read continuevar
	if [ "$continuevar" != "y" ]; then
		exit
	fi
fi
echo -en "What is the subdomain to register? "
read subdomain
echo -en "What is the admin email? "
read AdminEmail
echo -en "What is the school name? "
read SchoolName
$mysqlc --database=Schools -e "insert into Subdomains set Subdomain='"$subdomain"', GeneralAccess=1, AdminEmail='"$AdminEmail"', SchoolName='"$SchoolName"', DBName='"$mysqldbname"';"
if [ "$?" != "0" ]; then
	echo -en "Error inserting school into Schools database. Continue anyway (y/n)? "
	read continuevar
	if [ "$continuevar" != "y" ]; then
		exit
	fi
fi
