<h1>Forensics Database</h1>
<h2>Installation</h2>
<h4>Pre-Requisites</h4>
A Linux server (this version was tested and written on Ubuntu 14.04) running a LAMP server.
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
<h2>Description</h2>
This is an organization of php web pages that allow the access and organization of results from Forensics tournaments.
There is a specific description of each of the pages below.
<h3>NewTournament.php</h3>
<h2>Contact</h2>
The easiest way to contact me is to just send me a message on here or raise an issue.
