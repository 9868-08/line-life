<?php
ini_set( "max_execution_time", "64000" );
$start = microtime(true);
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА ЦЕН</p>";

if( $isFull )
{
	#очистка таблицы областей
	MySQLQuery::instance()->simpleQuery("TRUNCATE TABLE `".$price_table."`");
}

$csv = file( PARSE_DIR.$value );

#запись остатков в базу
if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись цен в базу</p>";
$iteration = 0;

$codeAptInBase = MySQLQuery::instance()->query( "SELECT code_apt FROM `".$clients_table."`" );

$countCsv = count($csv);
function testGenerator($el){
	$countEL = count($el);
	if($countEL > 0){
		for($i = 0; $i < $countEL; $i++) {
			yield $el[$i];
		}
	}
}
$iterationForeach = 0;
foreach (testGenerator($csv) as $valueCsv) {
	$itemPrice = explode(";", $valueCsv );
	$itemPrice[ 0 ] = removeBOM( $itemPrice[ 0 ] );
	$itemPrice[ 2 ] = str_replace(",",".",$itemPrice[ 2 ]);#делаем реплейс да бы записывать число с дробной частью
	$itemPrice[ 4 ] = str_replace(",",".",$itemPrice[ 4 ]);#делаем реплейс да бы записывать число с дробной частью

	$arrayNewPrice[$itemPrice[1]][$itemPrice[0]] = $itemPrice;
	$iterationForeach++;
}


foreach ($arrayNewPrice as $keyNewPrice => $valueNewPrice) {
	foreach ($codeAptInBase as $keyAptInBase => $valueAptInBase) {
		if(isset($valueNewPrice[$valueAptInBase["code_apt"]]) && $valueNewPrice[$valueAptInBase["code_apt"]]){
			$result= MySQLQuery::instance()->query( "SELECT * FROM `".$price_table."` WHERE `code_apt` = '{$valueAptInBase["code_apt"]}' AND `code` = '{$keyNewPrice}'" );
				if( $result )
				{
					MySQLQuery::instance()->simpleQuery(
						"UPDATE `".$price_table."`
						SET
							`code_apt` = ".$valueNewPrice[$valueAptInBase["code_apt"]][ 0 ].",
							`code` = ".$valueNewPrice[$valueAptInBase["code_apt"]][ 1 ].",
							`cost` = ".$valueNewPrice[$valueAptInBase["code_apt"]][ 2 ].",
							`rest` = ".$valueNewPrice[$valueAptInBase["code_apt"]][ 3 ].",
							`price` = ".$valueNewPrice[$valueAptInBase["code_apt"]][ 4 ].",
							`check_out` = 1
						WHERE `code_apt` = '".$valueNewPrice[$valueAptInBase["code_apt"]][ 0 ]."' AND `code` = '".$valueNewPrice[$valueAptInBase["code_apt"]][ 1 ]."'"
					);
				} else {
					$fields["code_apt"] = $valueNewPrice[$valueAptInBase["code_apt"]][ 0 ];
					$fields["code"] = $valueNewPrice[$valueAptInBase["code_apt"]][ 1 ];
					$fields["cost"] = $valueNewPrice[$valueAptInBase["code_apt"]][ 2 ];
					$fields["rest"] = $valueNewPrice[$valueAptInBase["code_apt"]][ 3 ];
					$fields["price"] = $valueNewPrice[$valueAptInBase["code_apt"]][ 4 ];
					$fields["check_out"] = 1;

					MySQLQuery::instance()->insert( $price_table, $fields );
				}
		}else{
			$result= MySQLQuery::instance()->query( "SELECT * FROM `".$price_table."` WHERE `code_apt` = '".$valueAptInBase["code_apt"]."' AND `code` = '".$keyNewPrice."'" );
				if( $result )
				{
					MySQLQuery::instance()->simpleQuery(
						"UPDATE `".$price_table."`
						SET
							`code_apt` = ".$valueAptInBase["code_apt"].",
							`code` = ".$keyNewPrice.",
							`cost` = 0,
							`rest` = 0,
							`price` = 0,
							`check_out` = 1
						WHERE `code_apt` = '".$valueAptInBase["code_apt"]."' AND `code` = '".$keyNewPrice."'"
					);
				} else {
					$fields["code_apt"] = $valueAptInBase["code_apt"];
					$fields["code"] = $keyNewPrice;
					$fields["cost"] = 0;
					$fields["rest"] = 0;
					$fields["price"] = 0;
					$fields["check_out"] = 1;

					MySQLQuery::instance()->insert( $price_table, $fields );
				}
		}
	}
}

MySQLQuery::instance()->simpleQuery( "DELETE FROM `".$price_table."` WHERE `check_out` = 0");

MySQLQuery::instance()->simpleQuery( "UPDATE `".$price_table."` SET `check_out` = 0");

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Price.csv'");

if(SHOW_LOG)echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА ЦЕН</p>";$time = microtime(true) - $start;
$time = microtime(true) - $start;
$textLog = $time;
$memSize = memory_get_usage();
$fileToLog = "/var/www/apteka03plus.ru/data/www/log66price.txt";
$currentFileToLog = file_get_contents($fileToLog);
$currentFileToLog .= "Время -  {$textLog}| Строк в файле - {$countCsv}|Обработано строк - {$iterationForeach}|Память - {$memSize}\n";
file_put_contents($fileToLog, $currentFileToLog);
?>
