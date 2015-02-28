#!/bin/bash
mysqluser="" # MySQL username
mysqlpass="" # MySQL password
mysqlwebuser="" # MySQL username used by the website
mysqlwebpass="" # MySQL password used by the website
mysqldbname="Forensics_2015" # Forensics database name
mysqlc="mysql --user=$mysqluser --password=$mysqlpass"
$mysqlc -e "create database $mysqldbname;"
if [ "$?" != "0" ]; then
	echo "Error creating database $mysqldbname"
	exit
fi
$mysqlc -D $mysqldbname -e "create table Ballots ( RID int not null, Round int, Judge int, Rank int not null, Qual int );"
if [ "$?" != "0" ]; then
	echo "Error creating table Ballots"
	exit
fi
$mysqlc -D $mysqldbname -e "create table Events ( EID int not null auto_increment, EName varchar(30), Partner int not null, primary key (EID) );"
if [ "$?" != "0" ]; then
	echo "Error creating table Events"
	exit
fi
$mysqlc -D $mysqldbname -e "create table Results ( SID int not null, EID int not null, TID int not null, broke int not null, State int not null, place int, RID int not null auto_increment, SID2 int, PRanks int, PQuals int, NumberRounds int, FRanks int, NumberJudges int, primary key (RID) );"
if [ "$?" != "0" ]; then
	echo "Error creating table Results"
	exit
fi
$mysqlc -D $mysqldbname -e "create table Students ( LName varchar(50) not null, FName varchar(50) not null, SID int not null auto_increment, Year int not null, primary key (SID) );"
if [ "$?" != "0" ]; then
	echo "Error creating table Students"
	exit
fi
$mysqlc -D $mysqldbname -e "create table Tournaments ( TName varchar(50) not null, TID int not null auto_increment, NumRounds int not null, NumFinalsJudges int not null, Date date not null, primary key (TID) );"
if [ "$?" != "0" ]; then
	echo "Error creating table Tournaments"
	exit
fi
$mysqlc -D $mysqldbname -e "create table users ( UName varchar(20) not null, FName varchar(30) not null, LName varchar(30) not null, password varchar(255) not null, Email varchar(255), primary key (UName) );"
if [ "$?" != "0" ]; then
	echo "Error creating table users"
	exit
fi
$mysqlc -e "create user '"$mysqlwebuser"'@'localhost' identified by '"$mysqlwebpass"';"
if [ "$?" != "0" ]; then
	echo "Error creating user $mysqlwebuser"
	exit
fi
$mysqlc -e "grant all on $mysqldbname.* to '"$mysqlwebuser"'@'localhost';"
if [ "$?" != "0" ]; then
	echo "Error granting $mysqlwebuser proper permissions"
	exit
fi
echo "MySQL Database setup is complete."
