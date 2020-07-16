<?php

error_reporting( 255 );
ini_set( "memory_limit", "16384M" );
ini_set( "max_execution_time", "64000" );
header( "Content-Type: text/html; charset=utf-8" );

require_once "/var/www/apteka03plus.ru/data/www/apteka03plus.ru/autoLoad.php";
require_once ( ROOT_DIR . "config.php" );
require_once ( 'pclzip.lib.php' );
require_once( SYS_DIR . 'phpmailer/class.phpmailer.php');
require_once( ELIB_DIR . 'cron/CronManager.php');

$cronManager = new CronManager(MySQLQuery::instance(), new PHPMailer);

$cronManager->startCron();
// $cronManager->test();

?>
