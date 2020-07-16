<?php 
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА категорий</p>";

if( $isFull )
{
	#очистка таблицы категорий
	MySQLQuery::instance()->simpleQuery("DELETE FROM `el_modules_categories` WHERE `module` = '71' AND `level` != '0'");
} else {
	$result= MySQLQuery::instance()->Query("SELECT * FROM `el_modules_categories` WHERE `module`='71' AND `level`='1'");
	foreach( $result as $key => $value )
		$categories_arr[ $value[ "code" ] ] = $value[ "id" ];
}

$csv = file(PARSE_DIR.'Clientcat_1lvl.csv');

#запись категорий первого уровня
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись категорий в базу</p>";
$iteration = 0;
$fields = [];

foreach($csv as $key => $value)
{
	$insert_id = "";
	$value = str_replace("\n", "", $value);
	$iteration++;
	$value = explode(";", $value);
	$value[ 0 ] = removeBOM( $value[ 0 ] );
	
	$fields["module"] = 71;
	$fields["code"] = $value[0];
	$fields["name"] = $value[1];
	$fields["parent"] = 3;
	$fields["level"] = 1;

	if ( $categories_arr[ $value[ 0 ] ] )
	{
		MySQLQuery::instance()->simpleQuery(
			"UPDATE `el_modules_categories`
			SET
				`name` = ".$fields["name"]."
			WHERE `code` = '".$fields["code"]."'"
		);

	} else {
		$insert_id = MySQLQuery::instance()->insert($category_table, $fields);
		$insert_id = $insert_id ? $insert_id : mysql_insert_id();

		$categories_arr[ $fields["code"] ] = $insert_id;
	}
}

/****************************************************************************************
**************************************** Уровень два *************************************
*****************************************************************************************/

$result= MySQLQuery::instance()->Query("SELECT * FROM `el_modules_categories` WHERE `module`='71' AND `level`='2'");
foreach( $result as $key => $value )
	$categories_arr2[ $value[ "code" ] ] = $value[ "id" ];

$csv = file(PARSE_DIR.'Clientcat_2lvl.csv');

#запись категорий второго уровня
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись категорий в базу</p>";
$iteration = 0;
$fields = [];
foreach($csv as $key => $value){
	$value = str_replace("\n", "", $value);
	$iteration++;
	$value = explode(";", $value);
	$value[ 0 ] = removeBOM( $value[ 0 ] );
	$value[ 2 ] = trim ( $value[ 2 ] );

	$fields["module"] = 71;
	$fields["code"] = $value[0];
	$fields["name"] = $value[1];
	$fields["parent"] = $categories_arr[ $value[ 2 ] ];
	$fields["level"] = 2;

	// Если есть родительская категория
	if ( $categories_arr[ $value[ 2 ] ] )
	{
		if ( $categories_arr2[ $value[ 0 ] ] )
		{
			MySQLQuery::instance()->simpleQuery(
				"UPDATE `el_modules_categories`
				SET
					`name` = ".$fields["name"].",
					`parent` = ".$fields["parent"]."
				WHERE `code` = '".$fields["code"]."'"
			);

		} else {
			MySQLQuery::instance()->insert($category_table, $fields);
		}
	}
}

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Clientcat_1lvl.csv'");
$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Clientcat_2lvl.csv'");
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА категорий</p>";
?>