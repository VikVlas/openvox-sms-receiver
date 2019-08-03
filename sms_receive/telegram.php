<?php

if(!defined('TELEGRAM_TOKEN')) define('TELEGRAM_TOKEN', '123456789:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'); 

if(!defined('TELEGRAM_CHATID')) define('TELEGRAM_CHATID', '-11111111,-22222222222');

//if($argv[1] != '') {
//	echo($argv[1]);
//	message_to_telegram($argv[1], $argv[2]);
//}	

function message_to_telegram($text, $chat_id = TELEGRAM_CHATID, $token = TELEGRAM_TOKEN) {
	$chatIdArray = explode(",", $chat_id);
	$ch = curl_init();
	foreach($chatIdArray as $chat_id) {
		curl_setopt_array($ch,  array(
			CURLOPT_URL => 'https://api.telegram.org/bot' . $token . '/sendMessage',
			CURLOPT_POST => TRUE,
			CURLOPT_CAINFO => '/etc/ssl/server.pem',
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_SSL_VERIFYHOST => FALSE,
			CURLOPT_POSTFIELDS => array(
				'chat_id' => $chat_id,
				'text' => $text,
			),
		)
		);
		$ret = json_decode(curl_exec($ch));
		if (!curl_errno($ch)) {
//          $info = curl_getinfo($ch);
//          print_r($info);
//          print_r($ret);
        } else {
			echo(curl_error($ch));
		}
	}
	curl_close($ch);
	return $ret;
}
?>
