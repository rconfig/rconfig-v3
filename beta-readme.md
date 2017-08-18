# rConfig 3.8beta readme

### Introduction
The rConfig community has been asking for a long time for multi-vendor support. It's here. I've created a template based connection feature that allows you to specific different 
vendors connection profiles. From upper and lower case username prompts, to connections timeouts, paging commands and so on... you can now get very granular on how you connect 
to various vendors network devices, Linux servers and other SSH/ Telnet based devices. 

The other major feature in this beta release is encrypted device passwords. Using a simple key and Initialization vector, stored securely, all of your precious network credentials
will be stored in the database in a highly encrypted manner and decrypted just before rConfig connects to your devices. 

### About the beta
Its very important that I release a very stable version of rConfig. That said, I need two things from beta participants; 
* Expect some broken things, but be very specific when your reporting errors. Be prepared to be part of the solution and resolution
* Stay engaged i.e. install new fixes, be prepared to re-install a few times, run and re-run upgrades from 3.7.5 etc.. and then feedback

If you can do this, you'll play a key part in making the new release very successfully. On that last point, right now I've dubbed this beta release 3.8beta. It may well be the case
that when eventually ready to release, I may move this to a full blown V4. But remains to be seen. 

### Getting started with the beta
First things first... __ Do not install the beta for production use, and DO NOT upgrade your existing installation of rConfig with this beta version__. Be patient!!!

#### Installation
The beta installations is going to be very similar to how you would install rConfig today. Except we need to tweak a little after our initial script launch do we get 
the beta version installed.

Upgrades will be identical to existing process. Except you cannot download the file from the rconfig.com until i post it, and will notify its exact URL on the private slack channel.


You need to manually set your keys for password encryption in the config/config.inc.php file. Fi you do not set a key, the passwords for new devices will be saved in plan text in the DB

#### Build Notes
Please review 3.8updates.md in the root directory of rConfig3.8 beta... I was using this as a running task list for the duration of the beta build. Any items prefixed with a dash '-' are completed items.
I will now move over to the github issues board to log and track issues for the beta duration - slack, integration with github is inplace and you should get notifications of
commits, issues etc...

Please read all of the 3.8updates.md file as there are some notes at the end as to some unfinished work.

#### Testing



#### Feedback and bugs
Slack, github issues board
Time delay
Specifics


#### Templates






