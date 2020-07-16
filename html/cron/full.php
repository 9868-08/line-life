<?php
error_reporting( 255 );
ini_set( "memory_limit", "16384M" );
ini_set( "max_execution_time", "64000" );

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

$_SERVER[ "SERVER_NAME" ] = "5.45.120.160";
$server_name	= $_SERVER[ "SERVER_NAME" ];

require_once "/var/www/apteka03plus.ru/data/www/apteka03plus.ru/autoLoad.php";
require_once ( ROOT_DIR . "config.php" );
require_once ( 'pclzip.lib.php' );

define('SHOW_LOG', 1);
define('PARSE_DIR', FILES_DIR."__user__/parse/");

$filepath = ROOT_DIR . "parse_data/";
$filepath_to = ROOT_DIR . "files/__user__/parse/";
$isParse = false;

foreach( glob( $filepath . '*FULL.zip' ) as $file )
{
	$basename = basename( $file, ".zip" );
	$archive = new PclZip( $file );
	$result = $archive->extract( PCLZIP_OPT_PATH, $filepath_to );

	if( $result == 0 )
	{
		echo $archive->errorInfo( true );
		exit();
	} else {
		$isParse = true;
		$isFull = true;

		rename( $file, $filepath . $basename . ".zip.bak" );

		echo "<p style = 'font-size:12px; color:orange;'>Успешно обработали архив - ".$basename.".zip</p>";

		//unlink( $file );
	}
}

if( $isParse )
{
	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = 'Clientcat_1lvl.csv'");
	$file = PARSE_DIR.$value;
	$stat = stat($file);
	$fields = ""; $value = "Clientcat_1lvl.csv";
	require_once 'parse_category.php';

	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = 'Cat_1lvl.csv'");
	$file = PARSE_DIR.$value;
	$stat = stat($file);
	$fields = ""; $value = "Cat_1lvl.csv";
	require_once 'parse_category2.php';

	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = 'Products.csv'");
	$file = PARSE_DIR.$value;
	$stat = stat($file);
	$fields = ""; $value = "Products.csv";
	require_once 'parse_products.php';

	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = 'Image.csv'");
	$file = PARSE_DIR.$value;
	$stat = stat($file);
	$fields = ""; $value = "Image.csv";
	require_once 'parse_image.php';

	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = 'Change.csv'");
	$file = PARSE_DIR.$value;
	$stat = stat($file);
	$fields = ""; $value = "Change.csv";
	require_once 'parse_change.php';

	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = 'AddOns.csv'");
	$file = PARSE_DIR.$value;
	$stat = stat($file);
	$fields = ""; $value = "AddOns.csv";
	require_once 'parse_addonse.php';

	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = 'Price.csv'");
	$file = PARSE_DIR.$value;
	$stat = stat($file);
	$fields = ""; $value = "Price.csv";
	require_once 'parse_price.php';

	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = 'ActArea.csv'");
	$file = PARSE_DIR.$value;
	$stat = stat($file);
	$fields = ""; $value = "ActArea.csv";
	require_once 'parse_actarea.php';

	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = 'Cities.csv'");
	$file = PARSE_DIR.$value;
	$stat = stat($file);
	$fields = ""; $value = "Cities.csv";
	require_once 'parse_cities.php';

	$lastDate = MySQLQuery::instance()->query("select * from el_parse where name = 'Clients.csv'");
	$file = PARSE_DIR.$value;
	$stat = stat($file);
	$fields = ""; $value = "Clients.csv";
	require_once 'parse_clients.php';
}

function removeBOM( $str = "" )
{
    if( substr( $str, 0, 3 ) == pack( 'CCC', 0xef, 0xbb, 0xbf ) )
        $str = substr( $str, 3 );

    return $str;
}
?>
