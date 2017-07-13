<?php

if (php_sapi_name() != 'cli') {
    // only set this when not run from CLI 
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != '') {
        $config_basedir = "https://".$_SERVER['HTTP_HOST']."/";
    } else {
        $config_basedir = "http://".$_SERVER['HTTP_HOST']."/";
    }
}
$config_web_basedir = "/var/www/html/";
$config_sitename = "Office365 rConfig";
$config_author = "Stephen Stack";
$config_version = "1.0.0";
$config_app_basedir = $config_web_basedir."o365.rconfig.com/";
$config_log_basedir = $config_app_basedir."logs/";
$config_data_basedir = $config_app_basedir."data/";
$config_backup_basedir = $config_app_basedir."backups/";
$config_syslogBackup_basedir = $config_app_basedir."syslogs/";
$config_webLogs_basedir = $config_app_basedir."logs/wwwlogs/";
$config_phpLogs_basedir = $config_app_basedir."logs/phpLog/";
$config_reports_basedir = $config_app_basedir."reports/";
$config_temp_dir = $config_app_basedir."tmp/";
$config_page = basename($_SERVER['SCRIPT_NAME']);
$logdir = '../logs/';

/* DEFINES Below*/
define('WEB_DIR', '/home/rconfig/www');

/* DATABASE DEFINES */
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'o365IpAddress');
define('DB_USER', 'root');
define('DB_PASSWORD', 'Paj3r0Sp0rt');

/**
 * Database Table Constants - these constants
 * hold the names of all the database tables used
 * in the script.
 */
define("TBL_USERS", "users");
define("TBL_ACTIVE_USERS",  "active_users");
define("TBL_ACTIVE_GUESTS", "active_guests");
define("TBL_BANNED_USERS",  "banned_users");

/**
 * Special Names and Level Constants - the admin
 * page will only be accessible to the user with
 * the admin name and also to those users at the
 * admin user level. Feel free to change the names
 * and level constants as you see fit, you may
 * also add additional level specifications.
 * Levels must be digits between 0-9.
 */
define("ADMIN_NAME", "admin");
define("GUEST_NAME", "Guest");
define("ADMIN_LEVEL", 9);
define("USER_LEVEL",  1);
define("GUEST_LEVEL", 0);

/**
 * This boolean constant controls whether or
 * not the script keeps track of active users
 * and active guests who are visiting the site.
 */
define("TRACK_VISITORS", true);

/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user and guest
 * are still considered active visitors.
 */
define("USER_TIMEOUT", 10);
define("GUEST_TIMEOUT", 5);

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
define("COOKIE_PATH", "/");  //Available in whole domain

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME", "Administrator");
define("EMAIL_FROM_ADDR", "admin@rconfig.com");
define("EMAIL_WELCOME", true);

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */