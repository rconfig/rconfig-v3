#rough layout for yaml file <br />
connection timeout? <br />
port number/ connection type <br />
headers not set for original class, maybe need option to add for devices such as Cisco WLC <br />
prompt _(enable or non-enable mode) (regex??)_ <br />
username prompt <br />
password prompt <br />
enable mode? <br />
enableModePassword? <br />
prompt _(enable or non-enable mode) (regex??)_ <br />
* disable paging yes/no  <br />
  * check out the termLen funcs in connection.class <br />
disable paging command <br />
pager prompt i/e/ '--more--' _(and option on what to do at this prompt i.e. space ' '  or enter 'TELNET-ENTER' etc..)_ <br />
lines 376 - 369 in the conn class, set options to remove certain text from the generated text <br />
logout command? i.e. quit, exit, loggoff <br />

 <br /> <br />
#Devices to test on <br />
Cisco IOS <br />
Mikrotek <br />
Brocade <br />
