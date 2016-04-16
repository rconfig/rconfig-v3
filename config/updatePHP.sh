#!/bin/sh
sed -i 's/upload_max_filesize.*/upload_max_filesize = 50M/g' /etc/php.ini
sed -i 's/post_max_size.*/post_max_size = 50M/g' /etc/php.ini
sed -i 's/max_input_time =.*/max_input_time = 300/g' /etc/php.ini
sed -i 's/max_execution_time.*/max_execution_time = 300/g' /etc/php.ini
service httpd restart
rm -f /home/rconfig/config/3.1.0-ONE-TIME-README.txt
rm -f /home/rconfig/config/updatePHP.sh
