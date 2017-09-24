#!/bin/bash
## Set some Colors
red=`tput setaf 1`
green=`tput setaf 2`
blue=`tput setaf 4`
magenta=`tput setaf 5`
cyan=`tput setaf 6`
reset=`tput sgr0`

echo "${cyan}
        ###############################################################################
        #                                                                  
        # 					rConfig 3.8 upgrade script   
        # Please note that this upgrade is only supported when upgrading from 3.7.5                 
        #                                                                  
        # You may purchase support from rConfig.com to avail of upgrade assistance                                                                 
        #                                                                  
        #                    https://www.rconfig.com                                           
        #                                                                  
        ###############################################################################
	${reset}"
printf '\n'

echo -n "Do you want to proceed with the upgrade (y/n)? "
read answer
if echo "$answer" | grep -iq "^y" ;
then
    echo "${green} Ok...  ${reset}"
else
    exit
fi

### Check existing version
	echo "${cyan} UPGRADE Version Check: ,making sure you have 3.7.5 installed ${reset}"
	version=`cat /home/rconfig/config/config.inc.php | grep '$config_version' | cut -d \" -f 2`
		if [ "$version" == "3.7.5" ]
		then
			echo "${green} SUCCESS: Version check passed - Moving on...  ${reset}"
		else 
			echo "${red} FAILURE: Version check failed - Stopping Script. You need to have rConfig 3.7.5 install in order to use this script to upgrade ${reset}"
			exit
		fi

### set working dir to /home
	cd /home

### 1. download 3.8
	echo "${cyan} UPGRADE STEP 1: Downloading rConfig 3.8 ${reset}"
		curl -O http://www.rconfig.com/downloads/rconfig-3.8.0-beta.zip -A "Mozilla"
		file1="/home/rconfig-3.8.0-beta.zip"
		if [ -f "$file1" ]
		then
			echo "${green} SUCCESS: $file1 is found ${reset}"
		else
			 echo "${red} FAILURE: Take action $file1 is not found - Stopping Script ${reset}"
				 exit
		fi
	
### 2. backup 3.7
	echo "${cyan} UPGRADE STEP 2: Backing up existing rConfig installation ${reset}"
		mv rconfig rconfig.3.7.5.bak
		# run check on the backup
		file2="/home/rconfig.3.7.5.bak/config/config.inc.php"
		if [ -f "$file2" ]
		then
			echo "${green} SUCCESS: Backup of current installation is successful ${reset}"
		else
			echo "${red} FAILURE: $file2 is not found  - Stopping Script ${reset}"
			exit
		fi


### 3. backup sql DB
	echo "${cyan} UPGRADE STEP 3: Backing up existing rConfig Database ${reset}"

		# Database credentials
		DB_HOST=`cat /home/rconfig.3.7.5.bak/config/config.inc.php | grep DB_HOST | cut -d \' -f 4`
		DB_PORT=`cat /home/rconfig.3.7.5.bak/config/config.inc.php | grep DB_PORT | cut -d \' -f 4`
		DB_NAME=`cat /home/rconfig.3.7.5.bak/config/config.inc.php | grep DB_NAME | cut -d \' -f 4`
		DB_USER=`cat /home/rconfig.3.7.5.bak/config/config.inc.php | grep DB_USER | cut -d \' -f 4`
		DB_PASSWORD=`cat /home/rconfig.3.7.5.bak/config/config.inc.php | grep DB_PASSWORD | cut -d \' -f 4`
		# Other options
		backup_path="/home/rconfig.3.7.5.bak/DB_Backup"
		date=$(date +"%d-%b-%Y")
		# Set default file permissions
		umask 177
		# Dump database into SQL file
		mkdir /home/rconfig.3.7.5.bak/DB_Backup
		mysqldump --user=$DB_USER --password=$DB_PASSWORD -P $DB_PORT --host=$DB_HOST $DB_NAME >$backup_path/$DB_NAME-$date.sql
		
		# Delete files older than 5 days for safety
		find $backup_path/* -mtime +30 -exec rm {} \;
		
		# check if backup file exists
		file3=$backup_path/$DB_NAME-$date.sql
		if [ -f "$file3" ]
		then
			 echo "${green} SUCCESS: Backup of current database is successful ${reset}"
		else
			echo "${red} FAILURE: Backup database file is not found  - Stopping Script ${reset}"
			exit
		fi	

### 4. unzip rconfig-3.8.0-beta.zip
	echo "${cyan} UPGRADE STEP 4: Extracting rConfig 3.8 ${reset}"
		unzip rconfig-3.8.0-beta.zip
		mkdir /home/rconfig/tmp
		chown -R apache /home/rconfig
		# check if rconfig 3.8 file exists
		file4=/home/rconfig/www/install/rconfig.sql
		if [ -f "$file4" ]
		then
			 echo "${green} SUCCESS: Extracting of rConfig 3.8 is successful ${reset}"
		else
			echo "${red} FAILURE: Extracting of rConfig 3.8 failed  - Stopping Script ${reset}"
			exit
		fi	
	
### 5. cp data from old dir to 3.8
	echo "${cyan} UPGRADE STEP 5: Copying data, logs and other directories from old rConfig to rconfig 3.8 ${reset}"
		cp -rv /home/rconfig.3.7.5.bak/backups /home/rconfig/backups
		cp -rv /home/rconfig.3.7.5.bak/cronfeed /home/rconfig/cronfeed
		cp -rv /home/rconfig.3.7.5.bak/data /home/rconfig/data
		cp -rv /home/rconfig.3.7.5.bak/logs /home/rconfig/logs
		cp -rv /home/rconfig.3.7.5.bak/reports /home/rconfig/reports

	
### 6. update config file for 3.8
	echo "${cyan} UPGRADE STEP 6: Update config file for 3.8 ${reset}"
		sed "/DB_HOST/s/'[^']*'/'$DB_HOST'/2;/DB_PORT/s/'[^']*'/'$DB_PORT'/2;/DB_NAME/s/'[^']*'/'$DB_NAME'/2;/DB_USER/s/'[^']*'/'$DB_USER'/2;/DB_PASSWORD/s/'[^']*'/'$DB_PASSWORD'/2;" /home/rconfig/www/install/config.inc.php.template > /home/rconfig/config/config.inc.php
		config_db=`cat /home/rconfig.3.7.5.bak/config/config.inc.php | grep DB_NAME | cut -d \' -f 4`
		if [ "$config_db" == "$DB_NAME" ]
		then
			echo "${green} SUCCESS: Config file update is successful ${reset}"
		else 
			echo "${red} FAILURE: Config file update failed - Stopping Script ${reset}"
			exit
		fi

### 7. run mysql update file from 3.8 dir
	echo "${cyan} UPGRADE STEP 7: Update database rConfig 3.8 database ${reset}"
		mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME < /home/rconfig/updates/sqlupdate.sql

### final task
chown -R apache /home/rconfig

echo "${cyan}
        ###############################################################################
        #                                                                  
        # Congratulations!!! rConfig 3.8 upgrade is complete.             
        # Go to https://$HOSTNAME/ to use rConfig.38        
        #                                                                  
        # Please visit www.rconfig.com/help for latest info, releases etc..                                                   
        # 
		# You can raise bugs on our github repo https://github.com/rconfig/rconfig/issues
        # Enjoy this wonderful software - rConfig Team                     
        #                                                                  
        ###############################################################################
	${reset}"
printf '\n'
printf '\n'