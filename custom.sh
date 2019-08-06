#!/bin/sh
/bin/sleep 5
/bin/cp -r /etc/asterisk/gw/sms_receive/www /tmp/web
/bin/php /etc/asterisk/gw/sms_receive/recv_sms.php &