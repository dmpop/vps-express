#!/usr/bin/env bash

if [ $(lsb_release -si) != "Ubuntu" ];
then
    echo "This doesn't seem to be an Ubuntu-based system."
	exit 1
fi

if [ "$(whoami)" != "root" ]; then
	echo "You must run this script as root"
	exit 1
fi

apt update
apt upgrade
apt install apache2 php libapache2-mod-php
apt install certbot python3-certbot-apache
apt install git fail2ban

echo "----------------------------------------"
echo " Specify new user name (example: monkey)"
echo " Press ENTER"
echo "----------------------------------------"
echo

read newuser

useradd -m $newuser
passwd $newuser
apt install sudo
usermod -aG sudo $newuser

echo "-----------------------------------------"
echo " Enter a domain name (example: hello.xyz)"
echo " Press ENTER"
echo "-----------------------------------------"
echo
read domain_name

touch  /etc/apache2/sites-available/$domain_name.conf

echo "<VirtualHost *:80>" > /etc/apache2/sites-available/$domain_name.conf
echo "" >> /etc/apache2/sites-available/$domain_name.conf
echo "DocumentRoot /var/www/html/$domain_name" >> /etc/apache2/sites-available/$domain_name.conf
echo "ServerName $domain_name" >> /etc/apache2/sites-available/$domain_name.conf
echo "ServerAlias $domain_name" >> /etc/apache2/sites-available/$domain_name.conf
echo "" >> /etc/apache2/sites-available/$domain_name.conf
echo "<Directory /var/www/html/$domain_name/>" >> /etc/apache2/sites-available/$domain_name.conf
echo "Options FollowSymlinks" >> /etc/apache2/sites-available/$domain_name.conf
echo "AllowOverride All" >> /etc/apache2/sites-available/$domain_name.conf
echo "Require all granted" >> /etc/apache2/sites-available/$domain_name.conf
echo "</Directory>" >> /etc/apache2/sites-available/$domain_name.conf
echo "" >> /etc/apache2/sites-available/$domain_name.conf
echo 'ErrorLog ${APACHE_LOG_DIR}/error.log' >> /etc/apache2/sites-available/$domain_name.conf
echo 'CustomLog ${APACHE_LOG_DIR}/access.log combined' >> /etc/apache2/sites-available/$domain_name.conf
echo "" >> /etc/apache2/sites-available/$domain_name.conf
echo "</VirtualHost>" >> /etc/apache2/sites-available/$domain_name.conf

sudo a2dissite 000-default
sudo a2ensite $domain_name.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
sudo certbot --apache
sudo mkdir /var/www/html/$domain_name
sudo chown www-data:www-data -R /var/www/html/$domain_name

# Install and configure MariaDB
sudo apt install mariadb-server
sudo mysql_secure_installation

# Create new user and database
echo "-------------------------------------"
echo " Enter the MariaDB root user password"
echo " Press ENTER"
echo "-------------------------------------"
echo
read -s rootpasswd
echo "--------------------------------"
echo " Enter a name for a new database"
echo " Press ENTER"
echo "--------------------------------"
echo
read database
echo "-------------------------------------"
echo " Enter a name for a new database user"
echo " Press ENTER"
echo "-------------------------------------"
echo
read username
echo "-------------------------------------------"
echo " Enter a password for the new database user"
echo " Press ENTER"
echo "-------------------------------------------"
echo
read -s userpass
mariadb -uroot -p${rootpasswd} -e "CREATE USER '${username}'@'localhost' IDENTIFIED BY '${userpass}';"
mariadb -uroot -p${rootpasswd} -e "GRANT ALL ON *.* TO '${username}'@'localhost' IDENTIFIED BY '${userpass}' WITH GRANT OPTION;"
mariadb -uroot -p${rootpasswd} -e "FLUSH PRIVILEGES;"
mariadb -uroot -p${rootpasswd} -e "CREATE DATABASE '${database}' /*\!40100 DEFAULT CHARACTER SET utf8 */;" 
