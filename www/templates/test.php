<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);


include('/home/rconfig/classes/spyc.class.php');

//$yaml = file_get_contents('test.yml');
//var_dump($yaml);
//$data = yaml_parse($yaml);
echo '<pre>';
$data = Spyc::YAMLLoad('test.yml');

var_dump($data);