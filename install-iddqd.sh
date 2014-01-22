#!/bin/bash

######################### 
###### VARIABLES ########
######################### 
#   Please modify these variables before installation.
#   After installation, most can be edited in the file
#   config/iddqd-config.json 

#Directory for installation
IDDQD_DIR='/var/www/iddqd'

#Source for IDDQD for git
IDDQD_SOURCE=ssh://faver@192.168.25.55/var/www/ddb/iddqd

#User name of default iddqd admin user. You will log in with this account first.
IDDQD_ADMIN_USER='admin'

#Password of default admin user. ***You should change this.***
IDDQD_ADMIN_PASS='defaultpass'

#Email of default admin user. For password recovery of admin account.
IDDQD_ADMIN_EMAIL='defaultemail'

#Outgoing email account for application to use for sending invites, password requests.
IDDQD_SYSTEM_EMAIL_ADDRESS=''
IDDQD_SYSTEM_EMAIL_USER=''
IDDQD_SYSTEM_EMAIL_PASS=''
IDDQD_SYSTEM_EMAIL_HOST=''
IDDQD_SYSTEM_EMAIL_PORT=587

#Domain for the server, usually the local IP address. 
DOMAIN=$(ip addr show eth0| sed -nr 's/.*inet ([^\/]+).*/\1/p')

#Postgresql will be downloaded and ran on the localhost.
#A new user for postgresql will be created named iddqd.
#We will use a new database called iddqddb
#Password for database user iddqd. *You should change this.*
PGPASS='password'

###############################
#####   END OF VARIABLES ######
###############################

######################################################################
#Check if root user
if [[ $EUID -ne 0 ]]; then
    echo "ERROR: Must be run with root privileges."
    exit 1
fi
if [ ! $IDDQD_ADMIN_PASS ];then
    echo "ERROR: Must edit IDDQD_ADMIN_PASS."
    exit 1
fi
if [ ! $IDDQD_ADMIN_EMAIL ];then
    echo "ERROR: Must edit IDDQD_ADMIN_EMAIL."
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
imagemagick
PACKAGES

echo "++++++++++Downloading wkhtmltopdf..."
#wkhtmltopdf for exporting reports.
wget http://wkhtmltopdf.googlecode.com/files/wkhtmltopdf-0.11.0_rc1-static-amd64.tar.bz2
tar -xf wkhtmltopdf-0.11.0_rc1-static-amd64.tar.bz2
mv wkhtmltopdf-amd64 /usr/bin/wkhtmltopdf
rm wkhtmltopdf-0.11.0_rc1-static-amd64.tar.bz2


#########################
### GET IDDQD CODE ######
#########################
echo "++++++++++Downloading IDDQD..."
if [ ! -d $IDDQD_DIR ];then
    cd /var/www
    git clone $IDDQD_SOURCE
else
    echo "$IDDQD_DIR exists already!"
    exit 1
fi

cd $IDDQD_DIR

###########################
#### Setup PostgreSQL ##### 
###########################
echo "++++++Preparing PostgreSQL..."
while ! nc -vz localhost 5432; do
    sleep 1
done
query="SELECT COUNT(1) FROM pg_catalog.pg_database WHERE datname = 'iddqddb';"
DATABASE_EXISTS=$(sudo -u postgres psql -t -c "$query")
echo $DATABASE_EXISTS
if [ $DATABASE_EXISTS -ne 1 -a $IDDQD_ADMIN_EMAIL ]; then
    #Create user iddqd and database iddqddb
    cat <<PGSCRIPT | sudo -u postgres psql
CREATE USER iddqd WITH PASSWORD '$PGPASS';
\i iddqddb-schema.sql
INSERT INTO users (username,password,email,isadmin) VALUES('$IDDQD_ADMIN_USER',crypt('$IDDQD_ADMIN_PASS',gen_salt('bf')),'$IDDQD_ADMIN_EMAIL',true);
PGSCRIPT
fi

#################################
### Generate SSL certificate  ###
#################################
#Feel free to change the values under req_distinguished_name in config.txt below.
if [ $IDDQD_ADMIN_EMAIL ]; then
    cat <<sslconfig > config.txt
    [req]
    default_bits            = 2048
    default_keyfile         = iddqd.key
    distinguished_name      = req_distinguished_name
    encrypt_key             = no
    prompt                  = no
    string_mask             = nombstr
    
    [ req_distinguished_name ]
    countryName             = US
    stateOrProvinceName     = CT
    localityName            = New Haven 
    0.organizationName      = IDDQD
    emailAddress            = $IDDQD_ADMIN_EMAIL
    commonName              = $DOMAIN
sslconfig
    openssl req -nodes -new -x509 -days 3650 -keyout iddqd.key -out iddqd.crt -config config.txt
    chmod 400 iddqd.key
    chmod 444 iddqd.crt
    mv iddqd.key $IDDQD_DIR/config
    mv iddqd.crt $IDDQD_DIR/config
    rm config.txt
else
    echo 'IDDQD_ADMIN_EMAIL must be set for certificate generation.'
fi

##################################
#### Configure Apache ############
##################################

a2enmod ssl
cat <<VHOST > /etc/apache2/sites-available/iddqd
<VirtualHost *:443>
	ServerAdmin $IDDQD_ADMIN_EMAIL
	ServerName iddqd
    SSLEngine on
        SSLCertificateFile $IDDQD_DIR/config/iddqd.crt
        SSLCertificateKeyFile $IDDQD_DIR/config/iddqd.key
    DocumentRoot $IDDQD_DIR/public
    ScriptAlias /cgi-bin/ $IDDQD_DIR/cgi-bin/
	<Directory "$IDDQD_DIR/public">
		AllowOverride None
		Options -ExecCGI -MultiViews +SymLinksIfOwnerMatch -Indexes
		Order allow,deny
		Allow from all
	</Directory>
    LogLevel warn
	ErrorLog /var/log/apache2/iddqd-error.log
	CustomLog /var/log/apache2/iddqd-access.log combined
</VirtualHost>
VHOST
a2ensite iddqd
service apache2 restart

###############################
#### Upadate IDDQD config #####
###############################
cd $IDDQD_DIR/config
cat <<CONFIG > iddqd-config.json
{
    "domain":"https://$DOMAIN",
    "babeldir":"/usr/bin/",
    "wkhtmltopdfdir":"/usr/bin/",
    "convertdir":"/usr/bin/",
    "postgresql": {
        "host":"localhost",
        "port":5432,
        "database":"iddqddb",
        "user":"iddqd",
        "pass":"$PGPASS"
    },
    "email": {
        "from_address":"$IDDQD_SYSTEM_EMAIL_ADDRESS",
        "host":"$IDDQD_SYSTEM_EMAIL_HOST",
        "port":$IDDQD_SYSTEM_EMAIL_PORT,
        "user":"$IDDQD_SYSTEM_EMAIL_USER",
        "pass":"$IDDQD_SYSTEM_EMAIL_PASS"
    }
}
CONFIG
chmod -R 777 $IDDQD_DIR/public/uploads
chmod 773 $IDDQD_DIR/log
