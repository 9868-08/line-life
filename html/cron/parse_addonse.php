<?php
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА</p>";

$csv = file(PARSE_DIR.$value);

#запись остатков в базу
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись в базу</p>";

foreach( $csv as $key => $value )
{
	$value = explode( ";", $value );

	$value[ 0 ] = trim( $value[ 0 ] );
	
	if( $value[ 0 ] )
	{
		$addonse_array[ $value[ 0 ] ][] = trim( $value[ 1 ] );
	}
}

foreach( $addonse_array as $key2 => $value2 )
{
	$sameItems = implode( "|", $value2 );
	$query = "UPDATE `md_market` SET `sameItems` = '".$sameItems."' WHERE `code` = '".$key2."'";
	MySQLQuery::instance()->simpleQuery ( $query );
}

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'AddOns.csv'");
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА</p>";
?>