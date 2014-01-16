#!/bin/bash

#########################
### SETUP VARIABLES #####
#########################
$DOMAIN=$(ip addr show eth0| sed -nr 's/.*inet ([^\/]+).*/\1/p')
    #Domain for the server, usually the local IP address.

$PGPASS='password'
    #Password for database user iddqd. You should change this.
$IDDQD_ADMIN_USER='admin'
    #User name of default admin user. You will log in with this account first.
$IDDQD_ADMIN_PASS='admin'
    #Password of default admin user. ***You should change this.***
$IDDQD_ADMIN_EMAIL=''
    #Email of default admin user.



######################################################################
#Check if root user
if [[ $EUID -ne 0 ]]; then
    echo "ERROR: Must be run with root privileges."
    exit 1
fi

#####################
####GET PACKAGES#####
#####################
echo "++++++Downloading Packages..."
sudo apt-get update

#If you plan to use a different HTTP server than apache2,
#then comment out the next section.
cat <<PACKAGES | xargs sudo apt-get install -y
apache2
libapache2-mod-php5
PACKAGES

#The following packages are required for iddqd to work.
cat <<PACKAGES | xargs sudo apt-get install -y
git
php5
php5-pgsql
openssl
postgresql
postgresql-contrib
python
python-psycopg2
openbabel
wkhtmltopdf
imagemagick
PACKAGES

#####################
#### PostgreSQL ##### 
#####################
echo "++++++Preparing PostgreSQL..."
while ! nc -vz localhost 5432; do
    sleep 1
done
query="SELECT COUNT(1) FROM pg_catalog.pg_database WHERE datname = 'iddqddb';"
DATABASE_EXISTS=$(sudo -u postgres psql -t -c "$query")
echo $DATABASE_EXISTS
if [ $DATABASE_EXISTS -ne 1 ]; then
    #Create user iddqd and database iddqddb
    cat <<PGSCRIPT | sudo -u postgres psql
CREATE USER iddqd WITH PASSWORD '$PGPASS';
\i iddqddb-schema.sql
INSERT INTO users (username,password,email,isadmin) VALUES('$IDDQD_ADMIN_USER',crypt('$IDDQD_ADMIN_PASS',gen_salt('bf')),'$IDDQD_ADMIN_EMAIL',true);
PGSCRIPT
fi

