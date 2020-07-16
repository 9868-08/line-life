<?php
error_reporting( 255 );
ini_set( "memory_limit", "16384M" );
ini_set( "max_execution_time", "64000" );
header( "Content-Type: text/html; charset=utf-8" );

require_once "/var/www/apteka03plus.ru/data/www/apteka03plus.ru/autoLoad.php";
require_once ( ROOT_DIR . "config.php" );
require_once ( 'pclzip.lib.php' );
require_once( SYS_DIR . 'phpmailer/class.phpmailer.php');


$server_name	= $_SERVER[ "SERVER_NAME" ];
if ($server_name == "apteka03plus.ru"){
	$domainForMail = "apteka03plus.ru";
}else{
	$domainForMail = "аптека03плюс.рф";
}
$filepath = ROOT_DIR . "parse_data/";
$filepath_to = ROOT_DIR . "files/__user__/parse/";

foreach( glob( $filepath . '*.zip' ) as $file )
{
	$basename = basename( $file, ".zip" );

	if( !strpos( $basename, "FULL" ) )
	{
		if(filesize($file) > 0){
			$archive = new PclZip( $file );
			$result = $archive->extract( PCLZIP_OPT_PATH, $filepath_to );

			if( $result == 0 )
			{
				echo $archive->errorInfo( true );
				exit();
			} else {
				rename( $file, $filepath . $basename . ".zip.bak" );

				echo "<p style = 'font-size:12px; color:orange;'>Успешно обработали архив - ".$basename.".zip</p>";

				//unlink( $file );
				exit();
			}
		}else{
			echo "<p style = 'font-size:12px; color:orange;'>Удаление битового архива - ".$basename.".zip</p>";
			unlink( $file );
			MySQLQuery::instance()->simpleQuery(
				"UPDATE `md_monitoring` SET `archive` = '$basename'"
				);
		}
	}
}

function _cancelOrder( $orderId )
{
	ini_set( "soap.wsdl_cache_enabled", "0" );

	$client = new SoapClient( "http://212.32.222.229:8085/sklad/ws/WSforsites?wsdl" );

	$result = $client->CancelOrder( array( 'OrderID' => $orderId ) );

	return $result->return;
}

function _sendSms( $phoneNumber, $message )
{
	$smsSender = SMSSender::instance();
	$smsSender->sendSms($phoneNumber,$message);


	// $phoneNumber = intval($phoneNumber);
	// if($phoneNumber > 10000000000){
	// 	$phoneNumber = $phoneNumber - 10000000000;
	// }
	// $message	= $message;
	// $smsQuery["from"] = "Apteka_plus";
	// $smsQuery["to"] = $phoneNumber;
	// $smsQuery["message"] = $message;
  //
	// $smsQueryJson =  json_encode($smsQuery);
	// $service_url = 'https://a2p-api.megalabs.ru/sms/v1/sms';
	// $curl = curl_init($service_url);
	// curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	// curl_setopt($curl, CURLOPT_USERPWD, "VLG_vdly:ZWpc7L43"); //Your credentials goes here
	// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	// curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	// curl_setopt($curl, CURLOPT_POST, true);
	// curl_setopt($curl, CURLOPT_POSTFIELDS, $smsQueryJson);
	// curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	// 	'Content-Type: application/json'));
  //
	// curl_exec($curl);
	// curl_close($curl);
}

// ************************************************************************************************************************************ //
// Получение статустов ордеров
// ************************************************************************************************************************************ //

$time	= time();
$query	= "SELECT * FROM `md_zakaz` WHERE `stadia` IN ( '0', '1')";
$orders	= MySQLQuery::instance()->query( $query );

if( $orders )
{
	// Список статусов
	$query2 = "SELECT * FROM `sp_orders_status`";
	$status = MySQLQuery::instance()->query( $query2 );
	if( $status )
	{
		foreach( $status as $k => $v ) {
			$status_list[ $v[ "id" ] ] = $v[ "name" ];
		}
	}

	// Список аптек
	$query3 = "SELECT * FROM `md_pharmacy`";
	$pharmacy = MySQLQuery::instance()->query( $query3 );
	if( $pharmacy )
	{
		foreach( $pharmacy as $k => $v ) {
			$pharmacy_list[ $v[ "id" ] ] = array( "name" => $v[ "name" ], "route" => $v[ "route" ] );
		}
	}

	ini_set( "soap.wsdl_cache_enabled", "0" );

	$client = new SoapClient( "http://212.32.222.229:8085/sklad/ws/WSforsites?wsdl" );

	foreach ( $orders as $value )
	{
		/***************************************************/
		// Автоматическая отмена заказа через 24 часа + часы
		/***************************************************/

		$timeNextDay	= strtotime( $value[ "date" ] );
		$timeHours		= 24 - date('H', $timeNextDay ) + 1;
		$timeNextDay	= $timeNextDay + 86400 + $timeHours*3600;
		//$timeNextDay	= 360;
		$stadia			= $time > $timeNextDay ? 3 : "";
		$phone			= $value[ "phone" ];


		$date = new DateTime($value["date"]);
					$format = $date->format('d.m');
					$nextDay = $date->getTimestamp() + 86400;
					$date2 = new DateTime();
					$date2->setTimestamp($nextDay);
					$format2 = $date2->format('d.m');
					$timeWork = str_replace("Ежедневно","",$pharmacy_list[ $value[ "code_apt" ] ][ "route" ]);

					$address		= "по адресу " . $pharmacy_list[ $value[ "code_apt" ] ][ "name" ] . " c " . $format . " по " . $format2 . " c " . $timeWork;

		if( $stadia && $value[ "stadia" ] != 3 )
		{
			$result2 = _cancelOrder( $value[ "id" ] );

			if( $result2 == 1 )
			{
				$text	= "Заказ №".$value[ "id" ]." отменен. Истек срок бронирования.";

				_sendSms( $phone, $text );
				echo "<p>Заказ №".$value[ "id" ]." отменен. Истек срок бронирования.</p>";
				MySQLQuery::instance()->simpleQuery( "UPDATE `md_zakaz` SET `stadia` = ".$stadia.", `smssend` = 1 WHERE `id` = '".$value[ "id" ]."'" );

				//Возвращаем зарезервированный товары
				$refreshCount = MySQLQuery::instance()->select("md_zakaz_item", array( "id_tovar" => "id_tovar", "count" => "count", "code_apt" => "code_apt" ), array( "id_zakaz" => $value[ "id" ] ));
				foreach ($refreshCount as $value) {
					$nowCount = MySQLQuery::instance()->select("md_price", array( "reserved" => "reserved"), array("code" => $value[ "id_tovar" ], "code_apt" => $value[ "code_apt" ]));
					$nowCount = $nowCount[0];
					$totalReserved = $nowCount[ "reserved" ] - $value[ "count" ];
					$totalReserved = $totalReserved <= 0 ? 0 : $totalReserved;
					MySQLQuery::instance()->update("md_price", array( "reserved" =>  $totalReserved), array("code" => $value[ "id_tovar" ], "code_apt" => $value[ "code_apt" ]));
				}
				//вернули

				if( $text )
				{
					// mailer
					$mail = new PHPMailer;
					$mail->isSendmail();
					$mail->CharSet = 'utf-8';
					$mail->setFrom('no-reply@'.$server_name, $domainForMail);
					$mail->addReplyTo('no-reply@' . $server_name, '');
					$mail->Subject = 'Поменялся статус вашего заказа на сайте ' . $domainForMail;
					$mail->addAddress( $value[ "email" ], '' );
					$mail->msgHTML( $text );
					if ($value[ "email" ] != '' && isset($value[ "email" ])){
						$mail->send();
					}
				}
			}
		}
		/************************************************/
		else {
			$result = $client->GetDataOrder( array( 'OrdersIDs' => $value[ "id" ] ) );

			$stadia = $result->return->Order->Status;

			if( $value[ "stadia" ] != $stadia )
			{
				$text = "";

				MySQLQuery::instance()->simpleQuery( "UPDATE `md_zakaz` SET `stadia` = ".$stadia." WHERE `id` = '".$value[ "id" ]."'" );

				switch( $stadia )
				{
					case 1:
						$array	= $result->return->Order->OrderRows;

						if( $array )
						{
							$OrderPartially = false;

							if(count($array) > 1){
															foreach ( $array as $k => $v )
															{
																if( $v->Rezerv < $v->Zakaz ) {
																	$OrderPartially	= true;
																	MySQLQuery::instance()->simpleQuery( "UPDATE `md_zakaz_item` SET `rezerv` = ". $v->Rezerv ." WHERE `id_zakaz` = '".$value[ "id" ]."' AND `id_tovar` = '".$v->Product."'" );
																}
																else
																	$OrderPartially	= $OrderPartially;
															}
														}else{
															if( $array->Rezerv < $array->Zakaz ) {
																$OrderPartially	= true;
																MySQLQuery::instance()->simpleQuery( "UPDATE `md_zakaz_item` SET `rezerv` = ". $array->Rezerv ." WHERE `id_zakaz` = '".$value[ "id" ]."' AND `id_tovar` = '".$array->Product."'" );
															}
															else
																$OrderPartially	= $OrderPartially;
														}

							#если заказ частично собран
							if( $OrderPartially )
							{
								$text	= "Ваш заказ №".$value[ "id" ] . " собран не полностью. Подробности уточняйте на сайте ".$domainForMail. " Забрать заказ можно в аптеке ".$address;
							} else {
								$text	= "Заберите заказ №".$value[ "id" ] . " " . $address;
							}

							echo "<p> 1 " . $value["id"] . " |||| " .  $value[ "smssend" ] . "</p>";

							if( $value[ "smssend" ] == 0 )
							{
								_sendSms( $phone, $text );

								echo "<p> 2 " . $value["id"] . " |||| " .  $value[ "smssend" ] . "</p>";

								MySQLQuery::instance()->simpleQuery( "UPDATE `md_zakaz` SET `smssend` = '1' WHERE `id` = '".$value[ "id" ]."'" );
							}
						}
					break;
					case 2:
						if( $value[ "smssend" ] == 0 )
						{
							$text	= "Заказ №".$value[ "id" ] . " выкуплен.";

							//Возвращаем зарезервированный товары
							$refreshCount = MySQLQuery::instance()->select("md_zakaz_item", array( "id_tovar" => "id_tovar", "count" => "count", "code_apt" => "code_apt" ), array( "id_zakaz" => $value[ "id" ] ));
							foreach ($refreshCount as $value) {
								$nowCount = MySQLQuery::instance()->select("md_price", array( "reserved" => "reserved", "rest" => "rest"), array("code" => $value[ "id_tovar" ], "code_apt" => $value[ "code_apt" ]));
								$nowCount = $nowCount[0];
								$totalReserved = $nowCount[ "reserved" ] - $value[ "count" ];
								$totalRest = $nowCount[ "rest" ] - $value[ "count" ];
								$totalReserved = $totalReserved <= 0 ? 0 : $totalReserved;
								$totalRest = $totalRest <= 0 ? 0 : $totalRest;
								MySQLQuery::instance()->update("md_price", array( "reserved" =>  $totalReserved, "rest" => $totalRest), array("code" => $value[ "id_tovar" ], "code_apt" => $value[ "code_apt" ]));
							}
							//вернули

							_sendSms( $phone, $text );

							MySQLQuery::instance()->simpleQuery( "UPDATE `md_zakaz` SET `smssend` = '1' WHERE `id` = '".$value[ "id" ]."'" );
						}
					break;
					case 3:
						$text	= "Заказ №".$value[ "id" ]." отменен. Данного товара нет в наличии.";

						//Возвращаем зарезервированный товары
						$refreshCount = MySQLQuery::instance()->select("md_zakaz_item", array( "id_tovar" => "id_tovar", "count" => "count", "code_apt" => "code_apt" ), array( "id_zakaz" => $value[ "id" ] ));
						foreach ($refreshCount as $value) {
							$nowCount = MySQLQuery::instance()->select("md_price", array( "reserved" => "reserved"), array("code" => $value[ "id_tovar" ], "code_apt" => $value[ "code_apt" ]));
							$nowCount = $nowCount[0];
							$totalReserved = $nowCount[ "reserved" ] - $value[ "count" ];
							$totalReserved = $totalReserved < 0 ? 0 : $totalReserved;
							MySQLQuery::instance()->update("md_price", array( "reserved" =>  $totalReserved), array("code" => $value[ "id_tovar" ], "code_apt" => $value[ "code_apt" ]));
						}
						//вернули

						_sendSms( $phone, $text );

						MySQLQuery::instance()->simpleQuery( "UPDATE `md_zakaz` SET `smssend` = '1' WHERE `id` = '".$value[ "id" ]."'" );
					break;
				}

				if( $text )
				{
					// mailer
					$mail = new PHPMailer;
					$mail->isSendmail();
					$mail->CharSet = 'utf-8';
					$mail->setFrom('no-reply@apteka03plus.ru', 'apteka03plus.ru');
					$mail->addReplyTo('no-reply@apteka03plus.ru', '');
					$mail->Subject = 'Поменялся статус вашего заказа на сайте ' . $domainForMail;
					$mail->addAddress( $value[ "email" ], '' );
					$mail->msgHTML( $text );
					if ($value[ "email" ] != '' && isset($value[ "email" ])){
						$mail->send();
					}
				}
			}
		}
	}
}
?>
