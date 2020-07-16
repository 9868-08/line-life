<?php
// require_once "/var/www/develop.apteka34plusru/data/www/develop.apteka34plus.ru/autoLoad.php";
require_once "/var/www/apteka03plus.ru/data/www/apteka03plus.ru/autoLoad.php";

//Очищаем поле
MySQLQuery::instance()->query("UPDATE `md_market` SET `product_day` = ''");
$curMonth = date('n');
$allDay = date('t'); //количество дней в месяце
$curYear  = date('Y');
$today = date("d-m-Y");

$monthMassive = ["1"=>"Январь", "2"=>"Февраль", "3"=>"Март", "4"=>"Апрель", "5"=>"Май", "6"=>"Июнь", "7"=>"Июль", "8"=>"Август", "9"=>"Сентябрь", "10"=>"Октябрь", "11"=>"Ноябрь", "12"=>"Декабрь"];
$month = $monthMassive[$curMonth];
$firstDayMonth = mktime(0, 0, 0, $curMonth, 1, $curYear);
$LastDayMonth = mktime(0, 0, 0, $curMonth, $allDay, $curYear);
//Вытаскиваем данные в интервале текущего года и текущего месяца
$sql = "SELECT `id_product`,`sale_product`, `data_product` FROM `md_product_day` WHERE `day_product` > $firstDayMonth AND `day_product` < $LastDayMonth AND `month_product` = '$month'";
$query = MySQLQuery::instance()->query($sql)[0];
if($query){
  foreach($query as $key => $value){
    $query[$key] = unserialize($value);
  }
  foreach($query["data_product"] as $key => $value){
    if($value == $today){
        $day = $key;
    }
  }

  $code = $query["id_product"][$day];
  if($code == ""){
    MySQLQuery::instance()->query("UPDATE `md_market` SET `product_day` = '' WHERE `code` = $code");
    exit();
  }
  $productday["id_product"] = $query["id_product"][$day];
  $productday["sale_product"] = $query["sale_product"][$day];

  $sqlDaw = "SELECT `code` FROM `md_price` WHERE `code` = '".$productday["id_product"]."' AND `rest` != 0";
  $queryDaw = MySQLQuery::instance()->query($sqlDaw);

  $flag = false;
  while(!$queryDaw){
      $productday["id_product"] = $query["id_product"]["reserv"][0];
      $productday["sale_product"] = $query["sale_product"]["reserv"][0];
      $query["id_product"][$day] = $productday["id_product"];
      $query["sale_product"][$day] = $productday["sale_product"];
      array_shift($query["id_product"]["reserv"]);
      array_shift($query["sale_product"]["reserv"]);
      $sqlDaw = "SELECT `code` FROM `md_price` WHERE `code` = ".$productday["id_product"]." AND `rest` != 0";
      $queryDaw = MySQLQuery::instance()->query($sqlDaw);
      $code = $productday["id_product"];
      $flag = true;
  }

  if($flag){
    $id = serialize($query["id_product"]);
    $sale = serialize($query["sale_product"]);
    $data = serialize($query["data_product"]);
    $edit = "UPDATE `md_product_day` SET `id_product`='".$id."', `sale_product`='".$sale."', `data_product`='".$data."' WHERE `day_product` > $firstDayMonth AND `day_product` < $LastDayMonth AND `month_product` = '$month'";
    MySQLQuery::instance()->query($edit);
  }

  $productday = serialize($productday);
  MySQLQuery::instance()->query("UPDATE `md_market` SET `product_day` = '$productday' WHERE `code` = $code");

}

?>
