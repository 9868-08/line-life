<?php
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА изображений</p>";

$csv = file(PARSE_DIR."Image.csv");

#запись остатков
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись изображений в базу</p>";

foreach($csv as $key => $value)
{
	$value = explode(";", $value);

	if( $value[1] )
		MySQLQuery::instance()->simpleQuery("UPDATE `md_market` SET `mainphoto` = '".trim( $value[2] )."' WHERE `code` = '".$value[1]."'");
}

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Image.csv'");
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА изображений</p>";
?>