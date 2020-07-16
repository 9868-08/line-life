<?php
error_reporting( 1 );
ini_set( "memory_limit", "16384M" );
ini_set( "max_input_time", 0 );
// ini_set( "max_execution_time", "64000" );
header( "Content-Type: text/html; charset=utf-8" );

$actarea_table = 'md_actarea';
$cities_table = 'md_cities';
$clients_table = 'md_pharmacy';
$price_table = 'md_price';
$products_table = 'md_market';
$category_table = 'el_modules_categories';
$cat1lvl_table = 'md_cat1lvl';
$cat2lvl_table = 'md_cat2lvl';
$images_table = 'md_images';
$change_table = 'md_change';
$addonse_table = 'md_addonse';
$nosology_table = 'md_nosology';

require_once "/var/www/apteka03plus.ru/data/www/apteka03plus.ru/autoLoad.php";
require_once ( ROOT_DIR . "config.php" );

define('SHOW_LOG', 1);
define('PARSE_DIR', FILES_DIR."__user__/parse/");

#перебор файлов для парсера
$files = scandir( PARSE_DIR );

foreach ($files as $key => $value)
{
	if($value != "." && $value != "..")
	{
		if($value == "ActArea.csv"){
			#работа с парсером областей
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_actarea.php';
		}

		if($value == "Cities.csv"){
			#работа с парсером городов
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_cities.php';
		}

		if($value == "Clients.csv"){
			#работа с парсером остатков
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_clients.php';
		}

		// if($value == "Clientcat_1lvl.csv"){
		// 	#работа с парсером остатков
		// 	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
		// 	$file = PARSE_DIR.$value;
		// 	$stat = stat($file);
		// 	if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
		// 		require_once 'parse_category.php';
		// }
    //
		// if($value == "Cat_1lvl.csv"){
		// 	#работа с парсером остатков
		// 	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
		// 	$file = PARSE_DIR.$value;
		// 	$stat = stat($file);
		// 	if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
		// 		require_once 'parse_category2.php';
		// }

		if($value == "Doprod.csv"){
			#работа с парсером остатков
			$start = microtime(true);
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_doprod.php';
			$parse["parse_doprod"] = round(microtime(true) - $start, 4);
			$start = microtime(true);
		}

		if($value == "ControlDoprod.csv"){
			#работа с парсером остатков
			$start = microtime(true);
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_doprod_control.php';
			$parse["parse_product_category"] = round(microtime(true) - $start, 4);
			$start = microtime(true);
		}

		if($value == "Price.csv"){
			#работа с парсером остатков
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_price.php';
		}

		if($value == "TovCat.csv"){
			#работа с парсером остатков
			$start = microtime(true);
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_nosology.php';
			$parse["parse_nosology"] = round(microtime(true) - $start, 4);
			$start = microtime(true);
		}

		if($value == "Products.csv"){
			#работа с парсером остатков
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_products.php';
		}

		if($value == "Image.csv"){
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_image.php';
		}

		if($value == "Change.csv"){
			#работа с парсером остатков
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_change.php';
		}

		if($value == "AddOns.csv"){
			#работа с парсером остатков
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_addonse.php';
		}

		if($value == "ClientCat.csv"){
			#работа с парсером остатков
			#добаление категорий в базу данных
			$start = microtime(true);
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_category3.php';
			$parse["parse_category3"] = round(microtime(true) - $start, 4);
			$start = microtime(true);
		}

		if($value == "Products_ClientCat.csv"){
			#работа с парсером остатков
			#добавление категорик к товарам в md_market
			$start = microtime(true);
			$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = '$value'");
			$file = PARSE_DIR.$value;
			$stat = stat($file);
			if($stat["mtime"] != $lastDate[0]["date"] && $stat["size"] != 0)
				require_once 'parse_product_category.php';
			$parse["parse_product_category"] = round(microtime(true) - $start, 4);
			$start = microtime(true);
		}
	}
}

function removeBOM( $str = "" )
{
    if( substr( $str, 0, 3 ) == pack( 'CCC', 0xef, 0xbb, 0xbf ) )
        $str = substr( $str, 3 );

    return $str;

}
?>
