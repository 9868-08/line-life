<?php
if (SHOW_LOG) {
    echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА ТОВАРОВ</p>";
}
if ($isFull) {
    #очистка таблицы
    MySQLQuery::instance()->simpleQuery("DELETE FROM `".$products_table."` WHERE `mainpage`='0' OR `popular`='0'");
} else {
  //#список продуктов
  $result= MySQLQuery::instance()->Query("SELECT * FROM `".$products_table."`");
  foreach ($result as $key => $value) {
      $products_arr[ $value[ "code" ] ] = $value[ "code" ];
  }
}


//#cписок нозологий
$nosologyTable= MySQLQuery::instance()->Query("SELECT `id`,`code`,`name` FROM `".$nosology_table."`");
foreach ($nosologyTable as $key => $value) {
    $nosologyMassive[ $value[ "code" ] ] = $value[ "name" ];
}

$csv = file(PARSE_DIR.'Products.csv');

if (SHOW_LOG) {
    echo "<p style = 'font-size:12px; color:orange; padding:20px'>Запись товаров в базу</p>";
}
if (substr($csv[0][0], 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
    $csv[0][0] = substr($value[0], 3);
}
$csv[0][0] = removeBOM( $csv[0][0] );

foreach ($csv as $key => $value) {
    $value = 				explode(";", $value);
    $code = 				trim((int)$value[0]);
    $name = 				mysql_escape_string(htmlspecialchars(trim($value[1])));
    $brand = 				mysql_escape_string(htmlspecialchars(trim($value[2])));
    $minpack = 			trim((int)$value[3]);
    $desc = 				mysql_escape_string(htmlspecialchars(trim($value[4])));
    $format = 			mysql_escape_string(htmlspecialchars(trim($value[5])));
    $nosology = 		trim((int)$value[9]);
    $flag_nosology = 0;
    $nosology_sql = "";
    if (strlen($nosology) > 2) {  //если нозология существует
        $nosology_name = mysql_escape_string($nosologyMassive[ $nosology ]);
        $nosology_sql = 	"`nosology` = ".$nosology.",
			`nosology_name` = '".$nosology_name."',";
        $flag_nosology = 1;
    }
    if ($products_arr[ $code ] && $code && !$isFull) {
        $update_query = "UPDATE `".$products_table."`
		SET
			`name` = '".$name."',
			`brand` = '".$brand."',
			`minpack` = ".$minpack.",
			`desc` = '".$desc."',
			".$nosology_sql."
			`format` = '".$format."'
		WHERE `code` = '".$code."'
		";
          MySQLQuery::instance()->simpleQuery($update_query);
    } else {
        if ($flag_nosology == 1) {  //если нозология существует
            $insert_query = "INSERT INTO `".$products_table."`
						(`id`,`code`,`artikul`,`name`,`brand`,`minpack`,`desc`,`format`,`nosology`,`nosology_name`) VALUES
						(".$code.", ".$code.", ".$code.", '".$name."', '".$brand."', ".$minpack.", '".$desc."','".$format."',".$nosology.",'".$nosology_name."')";
        } else {
            $insert_query = "INSERT INTO `".$products_table."`
      			(`id`,`code`,`artikul`,`name`,`brand`,`minpack`,`desc`,`format`) VALUES
      			(".$code.", ".$code.", ".$code.", '".$name."', '".$brand."', ".$minpack.", '".$desc."','".$format."')";
              }
          MySQLQuery::instance()->simpleQuery($insert_query);

    }
}

$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Products.csv'");
if (SHOW_LOG) {
    echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА ТОВАРОВ</p>";
}
