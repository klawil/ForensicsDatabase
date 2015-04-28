<h1>Forensics Database</h1>
<h2>Installation</h2>
<h4>Pre-Requisites</h4>
A LAMP server (this version was tested and written on Ubuntu 14.04).
<h4>Cloning the Repository</h4>
Clone the repository into your web directory using:
````
git clone https://github.com/klawil/ForensicsDatabase.git
````
<h4>Setting up the MySQL Database</h4>
Create a database in MySQL, hereafter $DBName.
Modify the setup.sh file to include $DBName as the database.
Make setup.sh executable and then run it. On Linux:
````
sudo chmod +x setup.sh
./setup.sh
````
<h4>Setting up the Apache Server</h4>
Because some of the files aren't meant to be served, put the following information in the configuration file for your apache server (for me this is located in '/etc/apache2/sites-available/000-default.conf'):
````
<VirtualHost *:80>
  ServerName $ServerName
  DocumentRoot $AbsolutePathToGitFolder
  <Files ~ "^\*(\.inc|\.md|\.sh)$">
    Order allow,deny
    Deny from all
  </Files>
</VirtualHost>
````

Open the Apache main configuration and find the directory the website is in (for me, it's /var/www/ForensicsDatabase). It should look like:
````
<Directory /var/www/>
	AllowOverride All
	# Your other options here
</Directory>
````
The line "AllowOverride All" allows the .htaccess to redirect to the custom error pages.
<h2>Description</h2>
This is an organization of php web pages that allow the access and organization of results from Forensics tournaments.
There is a specific description of each of the pages below.
<h3>NewTournament.php</h3>
<h2>Contact</h2>
The easiest way to contact me is to just send me a message on here or raise an issue.
