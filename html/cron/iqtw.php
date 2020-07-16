<?php
error_reporting( 255 );
ini_set( "memory_limit", "16384M" );
ini_set( "max_execution_time", "64000" );
header( "Content-Type: text/html; charset=utf-8" );

require_once "/var/www/apteka03plus.ru/data/www/apteka03plus.ru/autoLoad.php";
require_once ( ROOT_DIR . "config.php" );

$codeAptInBase = [5749,5835,6017,6616,6739,6747,6754,6755,6757,7144,7186,7433,7439,7493,7532,7536,7618,7706,7748,7764,7816,7908];
$int = 0;
foreach ($codeAptInBase as $key => $value) {
  # code...
  $query ="SELECT COUNT(*) FROM `md_price` WHERE `code_apt` = {$value}";
  $result= MySQLQuery::instance()->query($query);
  $int += (int)$result[0]["COUNT(*)"];
  echo $value ." - ";
  var_dump($result);
  echo "<br/>";
}

echo $int;
echo "<br/>";

$query2 ="SELECT * FROM `md_price`";
$result2= MySQLQuery::instance()->query($query2);

echo "<br/>";
var_dump(count($result2));

$query3 ="SELECT DISTINCT(`code`) FROM `md_price`";
$result3= MySQLQuery::instance()->query($query3);

echo "<br/>";
var_dump(count($result3));

$query4 ="SELECT (`code`) FROM `md_price` WHERE `rest` > 0";
$result4= MySQLQuery::instance()->query($query4);

echo "<br/>";
var_dump(count($result4));




 ?>
