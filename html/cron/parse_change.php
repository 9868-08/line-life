<?php
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА предложений</p>";

$csv = file(PARSE_DIR.$value);

#запись остатков в базу
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись предложений в базу</p>";

foreach($csv as $key => $value)
{
	$value = explode( ";", $value );

	$value[ 0 ] = trim( $value[ 0 ] );
	
	if( $value[ 0 ] )
	{
		$analogsItems[ $value[ 0 ] ][] = trim( $value[ 1 ] );
	}
}

foreach( $analogsItems as $key2 => $value2 )
{
	$analogsItems = implode( "|", $value2 );
	$query = "UPDATE `md_market` SET `analogsItems` = '".$analogsItems."' WHERE `code` = '".$key2."'";
	MySQLQuery::instance()->simpleQuery ( $query );
}

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Change.csv'");
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА предложений</p>";
?>