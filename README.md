# Php Subscription Reminder For Dolibarr

A small php single script app to send subscription reminder to dolibarr user

## Install
```bash
git checkout ...
composer install
```

## a crontab setup exemple
```bash
0 6 * * * /usr/bin/php7.2 ~PATH-TO~/sendReminderEmail.php $(date -d "+30 days" +\%Y-\%m-\%d) ~PATH-TO~/jmoins30.txt >> /var/log/sendReminderEmail.log
0 6 * * * /usr/bin/php7.2 ~PATH-TO~/sendReminderEmail.php $(date -d "+3 days" +\%Y-\%m-\%d) ~PATH-TO~/jmoins3.txt >> /var/log/sendReminderEmail.log
0 6 * * * /usr/bin/php7.2 ~PATH-TO~/sendReminderEmail.php $(date -d "-15 days" +\%Y-\%m-\%d) ~PATH-TO~/jplus15.txt >> /var/log/sendReminderEmail.log
```