<?php
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА ГОРОДОВ</p>";

/*
if( $isFull )
{
	#очистка таблицы областей
	MySQLQuery::instance()->simpleQuery("TRUNCATE TABLE " . $cities_table);
}
*/

$csv = file(PARSE_DIR.$value);

#запись остатков в базу
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись городов в базу</p>";
$iteration = 0;
foreach($csv as $key => $value){
	$iteration++;
	$value = explode(";", $value);

	$fields["area_id"] = removeBOM( $value[ 0 ] );
	$fields["id"] = $value[ 1 ];
	$fields["region_id"] = $value[ 1 ];
	$fields["name"] = trim( $value[ 2 ] );
	
	MySQLQuery::instance()->insert($cities_table, $fields);
}

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Cities.csv'");
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА ГОРОДОВ</p>";
?>