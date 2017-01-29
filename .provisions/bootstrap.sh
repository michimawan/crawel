#! /usr/bin/env bash

###
# copied and modified from https://gist.github.com/rrosiek/8190550
#
# install_mysql.sh
#
# This script assumes your Vagrantfile has been configured to map the root of
# your application to /mybox and that your web root is the "public" folder
# (Laravel standard).  Standard and error output is sent to
# /vagrant/vm_build.log during provisioning.
#
###

# Variables
DBHOST=localhost
DBNAME=crawel
DBUSER=root
DBPASSWD=root

echo -e "\n--- Mkay, installing now... ---\n"

echo -e "\n--- Removing apache2... ---\n"
apt-get -y autoremove apache2

echo -e "\n--- Updating packages list ---\n"
sudo LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php >> /mybox/vm_build.log 2>&1
apt-get -qq update >> /mybox/vm_build.log 2>&1

echo -e "\n--- Install base packages ---\n"
apt-get -y install vim curl build-essential python-software-properties git supervisor >> /mybox/vm_build.log 2>&1

# echo -e "\n--- Add Node 6.x rather than 4 ---\n"
# curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash - >> /mybox/vm_build.log 2>&1

# MySQL setup for development purposes ONLY
echo -e "\n--- Install MySQL specific packages and settings ---\n"
debconf-set-selections <<< "mysql-server mysql-server/root_password password $DBPASSWD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $DBPASSWD"
debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $DBPASSWD"
debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $DBPASSWD"
debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $DBPASSWD"
debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect none"
apt-get -y install mysql-server-5.6 >> /mybox/vm_build.log 2>&1

echo -e "\n--- Setting up our MySQL user and db ---\n"
mysql -uroot -p$DBPASSWD -e "CREATE DATABASE $DBNAME" >> /mybox/vm_build.log 2>&1
mysql -uroot -p$DBPASSWD -e "grant all privileges on $DBNAME.* to '$DBUSER'@'localhost' identified by '$DBPASSWD'" > /mybox/vm_build.log 2>&1

echo -e "\n--- Installing NGINX-specific packages ---\n"
apt-get -y install nginx >> /mybox/vm_build.log 2>&1

echo -e "\n--- Installing PHP-specific packages ---\n"
apt-get -y install php7.1 php7.1-cli php7.1-curl php7.1-fpm php7.1-intl php7.1-json php7.1-mbstring php7.1-mcrypt php7.1-mysql php7.1-xml >> /mybox/vm_build.log 2>&1
sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/7.1/fpm/php.ini >> /mybox/vm_build.log 2>&1
phpenmod mcrypt >> /mybox/vm_build.log 2>&1
service php7.1-fpm restart >> /mybox/vm_build.log 2>&1

echo -e "\n--- Setup Nginx site ---\n"
rm /etc/nginx/sites-available/default
cp /mybox/.provisions/nginx/nginx.conf /etc/nginx/sites-available/site.conf >> /mybox/vm_build.log 2>&1
chmod 644 /etc/nginx/sites-available/site.conf >> /mybox/vm_build.log 2>&1
ln -s /etc/nginx/sites-available/site.conf /etc/nginx/sites-enabled/site.conf >> /mybox/vm_build.log 2>&1

echo -e "\n--- Restarting Nginx ---\n"
sudo service nginx restart >> /mybox/vm_build.log 2>&1

echo -e "\n--- Installing Composer for PHP package management ---\n"
curl --silent https://getcomposer.org/installer | php >> /mybox/vm_build.log 2>&1
mv composer.phar /usr/local/bin/composer

# crontab
echo -e "\n--- Preparing crontab ---\n"
sudo mkdir /var/spool/cron/crontabs/
sudo chmod 730 /var/spool/cron/crontabs/
sudo chmod +t /var/spool/cron/crontabs/
sudo chown root:crontab /var/spool/cron/crontabs/
sudo cp /mybox/.provisions/crontabs/vagrant /var/spool/cron/crontabs/vagrant
sudo chown vagrant:crontab /var/spool/cron/crontabs/vagrant
sudo chmod 600 /var/spool/cron/crontabs/vagrant
sudo service cron start

# echo -e "\n--- Installing NodeJS and NPM ---\n"
# apt-get -y install nodejs >> /mybox/vm_build.log 2>&1

# echo -e "\n--- Installing javascript components ---\n"
# npm install -g gulp bower >> /mybox/vm_build.log 2>&1

# echo -e "\n--- Updating project components and pulling latest versions ---\n"
cd /mybox

if [[ -s /mybox/composer.json ]] ;then
  composer install >> /mybox/vm_build.log 2>&1
fi

# if [[ -s /mybox/package.json ]] ;then
#   sudo -u mybox -H sh -c "npm install" >> /mybox/vm_build.log 2>&1
# fi
#
# if [[ -s /mybox/bower.json ]] ;then
#   sudo -u mybox -H sh -c "bower install -s" >> /mybox/vm_build.log 2>&1
# fi
#
# if [[ -s /mybox/gulpfile.js ]] ;then
#   sudo -u mybox -H sh -c "gulp" >> /mybox/vm_build.log 2>&1
# fi

echo -e "\n--- Creating a symlink for future phpunit use ---\n"
if [[ -x /mybox/vendor/bin/phpunit ]] ;then
  ln -fs /mybox/vendor/bin/phpunit /usr/local/bin/phpunit
fi

php artisan migrate
