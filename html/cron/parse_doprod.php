<?php
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА допродаж</p>";

$csv = file(PARSE_DIR.$value);

#запись остатков в базу
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись допродаж в базу</p>";

foreach($csv as $key => $value)
{
	$value = explode( ";", $value );

	$value[ 0 ] = trim( removeBOM( $value[ 0 ] ) );

	if( $value[ 0 ] )
	{
		$mayNeed[ $value[ 0 ] ][] = trim( $value[ 1 ] );
	}
}

foreach( $mayNeed as $key2 => $value2 )
{
	$mayNeed = implode( "|", $value2 );
	$query = "UPDATE `md_market` SET `mayNeed` = '".$mayNeed."' WHERE `code` = '".$key2."'";
	MySQLQuery::instance()->simpleQuery ( $query );
}

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Doprod.csv'");
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА допродаж</p>";
?>
