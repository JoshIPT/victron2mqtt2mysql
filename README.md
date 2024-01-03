# victron2mqtt2mysql
Victron VE.Direct to MQTT and MySQL

# Requirements
Depending on your Debian version. replace the below PHP versions with the current equivalent (ie. `php8.1-cli` for example)
```
apt install php7.4-cli php7.4-mysql mariadb-server mosquitto supervisor
```
# Checkout
```
cd /opt
git clone https://github.com/JoshIPT/victron2mqtt2mysql.git
mv victron2mqtt2mysql victron
```
# MySQL configuration
If you need remote access to the MySQL database, modify `/etc/mysql/mariadb.conf.d/50-server` and adjust the `bind-address` to be `0.0.0.0`
```
mysql -u root
MariaDB [(none)]> create user victron identified by 'victron';
MariaDB [(none)]> create database victron;
MariaDB [(none)]> grant all privileges on victron.* to victron;
MariaDB [(none)]> \q
mysql -u root victron < /opt/victron/victron.sql
```
# Mosquitto configuration
If you don't want MQTT to be accessible remotely, then you can leave the configuration as is and set the username and password in `victron2mqtt.php` to `null`, otherwise edit `/etc/mosquitto/mosquitto.conf`
```
listener 1883 0.0.0.0
per_listener_settings true
allow_anonymous false
password_file /etc/mosquitto/pwfile
```
Create /etc/mosquitto/pwfile and add your user as such:
```
victron:victron
```
Then run `mosquitto_passwd /etc/mosquitto/pwfile` to encrypt the password. Now restart Mosquitto: `systemctl restart mosquitto`
# Supervisor
Create the file `/etc/supervisor/conf.d/victron.conf`. For MySQL add the following:
```
[program:victron]
command=/opt/victron/victron2mysql.php
autostart=true
autorestart=unexpected
```
For MQTT:
```
[program:victron]
command=/opt/victron/victron2mqtt.php
autostart=true
autorestart=unexpected
```
Then ask Supervisor to restart: `systemctl restart supervisor`
