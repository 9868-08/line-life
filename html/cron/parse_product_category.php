<?php
$res[] = "Начало работы парсера категорий";
if (SHOW_LOG) {
    echo "<p style = 'font-size:12px; color:orange;'>НАЧАЛО РАБОТЫ ПАРСЕРА категорий</p>";
}
    //Поля удаляются и добавляются, обновления поля НЕТУ
    #очистка поля категорий в md_market
    MySQLQuery::instance()->simpleQuery(
    "UPDATE `".$products_table."`
  	SET
  		`category` = 0,
  		`category2` = 0"
    );
    //#список категорий
    $sql_category = MySQLQuery::instance()->query("SELECT `id`, `code` FROM `el_modules_categories` WHERE `code` != 0");
    foreach ($sql_category as $key => $value) {
        $category[ $value["code"] ] = $value["id"];
    }
    //#список товаров
    $sql_codeMarket = MySQLQuery::instance()->query("SELECT `code` FROM `md_market` WHERE `code` != 0");
    foreach($sql_codeMarket as $key => $value){
        $codeMarket[ $value["code"] ] = 1;
    }

$csv = file(PARSE_DIR.'Products_ClientCat.csv');
if (SHOW_LOG) {
    echo "<p style = 'font-size:12px; color:orange; padding:20px'>запись категорий в базу</p>";
    $res[] = "Запись категорий в базу категорий";
}
foreach ($csv as $key => $value) {
    $value = str_replace("\n", "", $value);
    $value = explode(";", $value);
    $value[ 0 ] = removeBOM($value[ 0 ]);
    $code = trim((int)$value[0]);
    $cat = $category[ trim((int)$value[1]) ];
    if($cat){
    //Повторный товар
    if ($massive[$code]["flag"] == 1) {
        $massive[$code]["category2"][] = $cat;
    }
    //Первая запись товара
    else {
        $massive[$code]["category"] = $cat;
        $massive[$code]["flag"] = 1;
    }
  }
}
// echo "<pre>";
// print_r($massive);
// echo "</pre>";
  foreach ($massive as $key => $value) {
    if($codeMarket[$key]){
      $m_category = $value["category"];
      $m_category2 = $value["category2"];
      if ($m_category2) {
          $m_category2 = implode(",", $m_category2);
          $insert_cat = "UPDATE `md_market`
          SET
          `category` = ".$m_category.",
          `category2` = '".$m_category2."'
          WHERE `code` = ".$key."
          ";
           MySQLQuery::instance()->simpleQuery( $insert_cat );

      } else {
          $insert_cat = "UPDATE `md_market`
          SET
          `category` = ".$m_category."
          WHERE `code` = ".$key."
          ";
          MySQLQuery::instance()->simpleQuery( $insert_cat );
      }
    }
  }
$itemId = MySQLQuery::instance()->simpleQuery("UPDATE `el_parse` SET `date` = ".$stat["mtime"]." WHERE `name` = 'Products_ClientCat.csv'");
if (SHOW_LOG) {
    echo "<p style = 'font-size:12px; color:orange;'>КОНЕЦ РАБОТЫ ПАРСЕРА категорий</p>";
      $res[] = "Конец работы парсера категорий";
}
