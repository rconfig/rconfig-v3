#rough layout for yaml file 
connection timeout? 
port number/ connection type 
headers not set for original class, maybe need option to add for devices such as Cisco WLC 
prompt _(enable or non-enable mode) (regex??)_ 
username prompt 
password prompt 
enable mode? 
enableModePassword? 
prompt _(enable or non-enable mode) (regex??)_ 
* disable paging yes/no  
  * check out the termLen funcs in connection.class 
disable paging command 
pager prompt i/e/ '--more--' _(and option on what to do at this prompt i.e. space ' '  or enter 'TELNET-ENTER' etc..)_ 
lines 376 - 369 in the conn class, set options to remove certain text from the generated text 
logout command? i.e. quit, exit, loggoff 

  
#Devices to test on 
Cisco IOS 
Mikrotek 
Brocade 


NOTE: 
added new deviceEnablePrompt field to DB. Make sure and add to build
clean up nodes table of unused fields for new installs mainly
Device Connection Templates Page
    May need to re-install menuPages table
Add Templates Table
clean up templates folder for packaging