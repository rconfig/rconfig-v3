#rough layout for yaml file 
-connection timeout? 
-port number/ connection type 
xx-headers not set for original class, maybe need option to add for devices such as Cisco WLC 
xx-prompt _(enable or non-enable mode) (regex??)_ (done from DB)
-username prompt 
-password prompt 
-enable mode? 
xx-enableModePassword?  - from Db
xx-prompt _(enable or non-enable mode) (regex??)_ - from Db
- disable paging yes/no  
  - check out the termLen funcs in connection.class 
-disable paging command 
-pager prompt i/e/ '--more--' _(and option on what to do at this prompt i.e. space ' '  or enter 'TELNET-ENTER' etc..)_ 
lines 376 - 369 in the conn class, set options to remove certain text from the generated text 
-logout command? i.e. quit, exit, loggoff 

#Devices to test on 
Cisco IOS 
Mikrotek 
Brocade 

-remove mandatory user/pass fields
-remove confrim field
-remove enable checkbox
-remove access method & port
add enable mode prompt field
fix default user/pass tick box

-Check backups backup templates
-add backup button to templates
-updated favicon.ico
defaultEnablePassword not saving on add edit
deviceEnablePrompt field to DB. Make sure and add to webform

NOTES: 
nodes - added new deviceEnablePrompt field to DB. Make sure and add to webform
nodes - added templateId
clean up nodes table of unused fields for new installs mainly
Device Connection Templates Page
    May need to re-install menuPages table
Added templates var to config.inc
Add Templates Table
clean up templates folder for packaging
nodes - termLength field removed
nodes - deviceEnableMode field removed
nodes - deviceAccessMethodId field removed
delete - devicesaccessmethod table
reset ID in templates
Clean up templates folder
and add a standard template or two
clear out error logs, data dir etc... make sure its not being uploaded to git
Pre-req to yum install some php extensions.???
Remove old phpseclib
