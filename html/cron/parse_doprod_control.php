<?php
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА контролируемых допродаж</p>";

$csv = file(PARSE_DIR.$value);

#запись остатков в базу
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись контролируемых допродаж в базу</p>";

foreach($csv as $key => $value)
{
	$value = explode( ";", $value );

	$value[ 0 ] = trim( removeBOM( $value[ 0 ] ) );

	if( $value[ 0 ] )
	{
		$forgetToBuy[ $value[ 0 ] ][] = trim( $value[ 1 ] );
	}
}

foreach( $forgetToBuy as $key2 => $value2 )
{
	$forgetToBuy = implode( "|", $value2 );
  $query = "UPDATE `md_market` SET `forgetToBuy` = '".$forgetToBuy."' WHERE `code` = '".$key2."'";
	$queryForLog = $queryForLog . "UPDATE `md_market` SET `forgetToBuy` = '".$forgetToBuy."' WHERE `code` = '".$key2."'" . "\n";
	MySQLQuery::instance()->simpleQuery ( $query );
}
$fileToLog = "/var/www/apteka34plus.ru/data/www/logDEVdoprod.txt";
$currentFileToLog = file_get_contents($fileToLog);
$currentFileToLog .= $queryForLog;
file_put_contents($fileToLog, $currentFileToLog);

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'ControlDoprod.csv'");
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА контролируемых допродаж</p>";
?>
