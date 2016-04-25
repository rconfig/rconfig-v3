<?php
$host = gethostname();
$ip = $_SERVER['SERVER_ADDR'];
include("../classes/nav.class.php");
$nav = new nav();
echo $nav->renderTopNav($config_page, $host, $ip);
echo $nav->renderSubNav($config_page);