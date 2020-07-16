<?php
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА КЛИЕНТОВ</p>";

/*
if( $isFull )
{
	#очистка таблицы
	MySQLQuery::instance()->simpleQuery("TRUNCATE TABLE " . $clients_table);
}
*/

$csv = file(PARSE_DIR.$value);

#запись остатков в базу
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись клиентов в базу</p>";
$iteration = 0;
foreach($csv as $key => $value)
{
	$iteration++;
	$value = explode(";", $value);
	
	$value[ 0 ] = removeBOM( $value[ 0 ] );

	MySQLQuery::instance()->Query
	(
		"INSERT INTO `".$clients_table."`
			(id, code_apt, name, address, id_cities, route, worktime, category)
			VALUES('".$value[0]."', '".$value[0]."', '".$value[1]."','".$value[2]."', '".$value[3]."','".$value[4]."','".$value[5]."', 22)
			ON DUPLICATE KEY UPDATE id='".$value[0]."', code_apt='".$value[0]."', name='".$value[1]."', address='".$value[2]."', id_cities='".$value[3]."', route='".$value[4]."', worktime='".$value[5]."', category='22'
		"
	);
}

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Clients.csv'");
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА КЛИЕНТОВ</p>";
?>