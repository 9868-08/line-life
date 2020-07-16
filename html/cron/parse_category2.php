<?php
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА категорий</p>";

if( $isFull )
{
	#очистка таблиц категорий
	MySQLQuery::instance()->simpleQuery("TRUNCATE TABLE `md_cat1lvl`");
	MySQLQuery::instance()->simpleQuery("TRUNCATE TABLE `md_cat2lvl`");
	MySQLQuery::instance()->simpleQuery("TRUNCATE TABLE `md_category`");
} else {
	// Находим категории 1-го уровня
	$result= MySQLQuery::instance()->Query("SELECT * FROM `md_cat1lvl`");
	foreach( $result as $key => $value )
		$categories_arr_1[ $value[ "cat_code_1lvl" ] ] = $value[ "cat_name_1lvl" ];

	// Находим категории 2-го уровня
	$result= MySQLQuery::instance()->Query("SELECT * FROM `md_cat2lvl`");
	foreach( $result as $key => $value )
		$categories_arr_2[ $value[ "cat_code_2vl" ] ] = $value[ "cat_name_2lvl" ];

	// Находим категории 3-го уровня
	$result= MySQLQuery::instance()->Query("SELECT * FROM `md_category`");
	foreach( $result as $key => $value )
		$categories_arr_3[ $value[ "cat_code" ] ] = $value[ "cat_name" ];
}

/****************************************************************************************
************************************** Первый уровень ***********************************
*****************************************************************************************/
$csv = file(PARSE_DIR.'Cat_1lvl.csv');
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>Запись категорий в базу первого уровня</p>";
$fields = array();

foreach($csv as $key => $value)
{
	$insert_id = "";
	$value = str_replace("\n", "", $value);
	$value = explode(";", $value);
	$value[ 0 ] = removeBOM( $value[ 0 ] );
	
	$fields["cat_code_1lvl"] = $value[0];
	$fields["cat_name_1lvl"] = $value[1];
	$fields["cat_client"] = $value[2];

	if( $value[0] )
	{
		if ( $categories_arr_1[ $value[ 0 ] ] )
		{
			MySQLQuery::instance()->simpleQuery(
				"UPDATE `md_cat1lvl`
				SET
					`cat_name_1lvl` = '".$fields["cat_name_1lvl"]."',
					`cat_client` = '".$fields["cat_client"]."'
				WHERE `cat_code_1lvl` = '".$fields["cat_code_1lvl"]."'"
			);
		} else {
			MySQLQuery::instance()->insert("md_cat1lvl", $fields);
		}
	}
}

/****************************************************************************************
************************************** Второй уровень ***********************************
*****************************************************************************************/
$csv = file(PARSE_DIR.'Cat_2lvl.csv');
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>Запись категорий в базу второго уровня</p>";
$fields = array();

foreach($csv as $key => $value)
{
	$insert_id = "";
	$value = str_replace("\n", "", $value);
	$value = explode(";", $value);
	$value[ 0 ] = removeBOM( $value[ 0 ] );
	
	$fields["cat_code_2vl"] = $value[0];
	$fields["cat_name_2lvl"] = $value[1];
	$fields["cat_parent_1lvl"] = $value[2];
	$fields["cat_client"] = $value[3];

	if( $value[0] )
	{
		if ( $categories_arr_2[ $value[ 0 ] ] )
		{
			MySQLQuery::instance()->simpleQuery(
				"UPDATE `md_cat2lvl`
				SET
					`cat_name_2lvl` = '".$fields["cat_name_2lvl"]."',
					`cat_parent_1lvl` = '".$fields["cat_parent_1lvl"]."',
					`cat_client` = '".$fields["cat_client"]."'
				WHERE `cat_code_2vl` = '".$fields["cat_code_2vl"]."'"
			);

		} else {
			MySQLQuery::instance()->insert("md_cat2lvl", $fields);
		}
	}
}

/****************************************************************************************
************************************** Третий уровень ***********************************
*****************************************************************************************/
$csv = file(PARSE_DIR.'Category.csv');
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>Запись категорий в базу третьего уровня</p>";
$fields = array();

foreach($csv as $key => $value)
{
	$insert_id = "";
	$value = str_replace("\n", "", $value);
	$value = explode(";", $value);
	$value[ 0 ] = removeBOM( $value[ 0 ] );
	
	$fields["cat_code"] = $value[0];
	$fields["cat_name"] = $value[1];
	$fields["cat_parent_2lvl"] = $value[2];
	$fields["cat_client"] = $value[3];

	if( $value[0] )
	{
		if ( $categories_arr_3[ $value[ 0 ] ] )
		{
			MySQLQuery::instance()->simpleQuery(
				"UPDATE `md_category`
				SET
					`cat_name` = '".$fields["cat_name"]."',
					`cat_parent_2lvl` = '".$fields["cat_parent_2lvl"]."',
					`cat_client` = '".$fields["cat_client"]."'
				WHERE `cat_code` = '".$fields["cat_code"]."'"
			);

		} else {
			MySQLQuery::instance()->insert("md_category", $fields);
		}
	}
}

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Cat_1lvl.csv'");
$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Cat_2lvl.csv'");
$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Category.csv'");
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА категорий</p>";
?>