<?php
require_once '/home/rconfig/vendor/autoload.php';

$loader = new \Composer\Autoload\ClassLoader();
$loader->addPsr4('phpseclib\\', '/home/rconfig/vendor/autoload.php');
$loader->register();


use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

define('NET_SSH2_LOGGING', SSH2::LOG_REALTIME);

//$key = new RSA();
//$key->loadKey(file_get_contents('private-key.txt'));

// Domain can be an IP too
$ssh = new SSH2('localhost');
if (!$ssh->login('root', 'Paj3r0Sp0rt')) {
    exit('Login Failed');
}

echo $ssh->exec('cat /etc/php.ini');
echo $ssh->exec('ls -ahl');
echo $ssh->getLog();