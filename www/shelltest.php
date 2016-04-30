<?php

if(shell_exec('echo EXEC') == 'EXEC'){
    echo 'exec works';
}
echo shell_exec('echo EXEC');


$output = shell_exec('ls -ahl /home/rconfig/config/');
$output = explode("\n", $output);

echo '<pre>';
var_dump($output);

echo shell_exec('sudo -u apache chown -R apache /home/rconfig/');
unset($output);

echo '---------------------------------------------------------';

$output = shell_exec('ls -ahl /home/rconfig/config/');
$output = explode("\n", $output);

echo '<pre>';
var_dump($output);
die();