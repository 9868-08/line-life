<?php
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА ОБЛАСТЕЙ</p>";

/*
if( $isFull )
{
	#очистка таблицы областей
	MySQLQuery::instance()->simpleQuery("TRUNCATE TABLE " . $actarea_table);
}
*/

$csv = file(PARSE_DIR.$value);

#запись остатков в базу
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись областей в базу</p>";
$iteration = 0;
foreach($csv as $key => $value){
	$value = str_replace("\n", "", $value);
	$iteration++;
	$value = explode(";", $value);

	$fields["id"] = removeBOM( $value[ 0 ] );
	$fields["name"] = trim( $value[ 1 ] );

	MySQLQuery::instance()->insert($actarea_table, $fields);
}

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'ActArea.csv'");
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА ОБЛАСТЕЙ</p>";
?>