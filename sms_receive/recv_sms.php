#!/bin/php
<?php 
require_once("/www/cgi-bin/php/system_fc.php");
include_once("/www/cgi-bin/inc/function.inc");
?>
<?php
require_once("/www/cgi-bin/inc/redis.inc");
include_once("/www/cgi-bin/inc/smsinboxdb.php"); 
include_once("/www/cgi-bin/inc/smsoutboxdb.php"); 

$redis_client = new Predis\Client();
while(true) {
	$blpop_sms_out = $redis_client->blpop("app.asterisk.smssend.list", 5);
	$blpop_str=$redis_client->blpop("app.asterisk.smsreceive.list",5);

	if ($blpop_sms_out || $blpop_str) {
		$sms_info = get_sms_info();
	}

	if ($blpop_sms_out) {
		if (isset($blpop_sms_out)) {
			$sms_out = $blpop_sms_out[1];
			unset($blpop_sms_out);
		}
		if ($sms_out <> "") {
			$sms_out = str_replace("\r\n", " ", $sms_out);
			$sms_out = str_replace("\r", " ", $sms_out);
			$sms_out = str_replace("\n", " ", $sms_out);
			$sms_out_decoded = json_decode($sms_out, true);
			$sms_out_port = $sms_out_decoded["port"];
			$sms_out_number = $sms_out_decoded["number"];
			$sms_out_date = $sms_out_decoded["date"];
			$sms_out_content = $sms_out_decoded["content"];
			$sms_out_status = $sms_out_decoded["status"];
			if ($sms_out <> "" && is_true($sms_info['local_store']['enable'])) {
				$db_out = new SMSOUTBOXDB(); 
				$db_out->insert_sms("$sms_out_port", "$sms_out_number", "$sms_out_date", "$sms_out_content", "$sms_out_status");
				$db_out->close();
			}
		}
	}

	if (!$blpop_str) 
	{	
		continue;
	}


	if (isset($blpop_str))
	{	
		$pop_str=$blpop_str[1];
		unset($blpop_str);
	}
	
	if ($pop_str<>"")
	{
		//add by liubin:
		//replace "\r,\n,\r\n" to " ",and make sure the fun:json_decode() run fine.
		if (strstr($pop_str,"\r\n")) {
			//echo $pop_str;
			$pop_str = str_replace("\r\n","\\n",$pop_str);
		}
		if (strstr($pop_str,"\r")) {
			//echo $pop_str;
			$pop_str = str_replace("\r"," ",$pop_str);
		}
		if (strstr($pop_str,"\n")) {
			//echo $pop_str;
			$pop_str = str_replace("\n","\\n",$pop_str);
		}
	

		$pop_array=json_decode($pop_str,true);
				
		$PORT=$pop_array["port"];
		$PHONENUMBER=$pop_array["src"];
		$TIME=$pop_array["time"];
		$MESSAGE=$pop_array["text"];
		echo $PORT;
	}
//	preg_match_all('/\d+/',$PORT,$arr);
//	$PORT = "Board-".$arr[0][0]."-gsm-".$arr[0][1];
//	echo "$PORT";
//	echo "\n";
	if(file_exists("/etc/asterisk/gw/custom_sms")) {
		@system("/etc/asterisk/gw/custom_sms \"$PORT\" \"$PHONENUMBER\" \"$TIME\" \"$MESSAGE\" > /dev/null 2>&1 &");
	}
	/* Save to database */

	
	if(is_true($sms_info['local_store']['enable'])){ 
		$db = new SMSINBOXDB(); 
		$db->insert_sms("$PORT","$PHONENUMBER","$TIME","$MESSAGE");
		$db->close();
	}
	/* SMS to Email */
	$sw = $sms_info['mail']['sw'];
	if($sw == 'on') {
		$smail1 = trim($sms_info['mail']['smail1']);
		$smail2 = trim($sms_info['mail']['smail2']);
		$smail3 = trim($sms_info['mail']['smail3']);
		$sender = trim($sms_info['mail']['sender']);
		$smtp_server = trim($sms_info['mail']['smtpserver']);
		$smtp_port = trim($sms_info['mail']['smtpport']);
		if($smtp_port == "") $smtp_port=25;
		$smtp_user = trim($sms_info['mail']['smtpuser']);
		$smtp_pwd = trim($sms_info['mail']['smtppwd']);
		$tls_enable = trim($sms_info['mail']['tls_enable']);
		$title = trim($sms_info['mail']['mail_title']);
		$content = trim($sms_info['mail']['mail_content']);
		
		$EMAIL_PORT = get_gsm_name_by_channel($PORT);

		if($title == '') {
			$title="$PHONENUMBER send sms to port $EMAIL_PORT in $TIME";
		} else {
			$title = str_replace("\$PHONENUMBER","$PHONENUMBER",$title);
			$title = str_replace("\$PORT","$EMAIL_PORT",$title);
			$title = str_replace("\$TIME","$TIME",$title);
			$title = str_replace("\$MESSAGE","$MESSAGE",$title);
		}

		if($content == '') {
			$content = "$MESSAGE";
		} else {
			$content = str_replace("\$PHONENUMBER","$PHONENUMBER",$content);
			$content = str_replace("\$PORT","$EMAIL_PORT",$content);
			$content = str_replace("\$TIME","$TIME",$content);
			$content = str_replace("\$MESSAGE","$MESSAGE",$content);
		}

		if( ($smail1 != "" || $smail2 != "" || $smail3 != "") && 
			$sender != "" && 
			$smtp_server != "" &&
			$smtp_user != "" &&
			$smtp_pwd != ""
		) {
			require_once('/my_tools/PHPMailer_5.2.2/class.phpmailer.php');
			$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
			try {
				$mail->IsSMTP(); // telling the class to use SMTP
				$mail->SMTPAuth   = true;                  // enable SMTP authentication

				if($tls_enable == 'yes') {
					#If you want to use TLS, try adding:
					$mail->SMTPSecure = 'tls';
				}

				#If you want to use SSL, try adding:
				#$mail->SMTPSecure = 'ssl';

				$mail->CharSet	  = 'utf-8';
				$mail->Host       = $smtp_server; // SMTP server
				$mail->SetFrom($sender);

				//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
				$mail->Port       = $smtp_port;                    // set the SMTP port for the GMAIL server
	
				$mail->Username   = $smtp_user; // SMTP account username
				$mail->Password   = $smtp_pwd;        // SMTP account password
				if($smail1 != "")
					$mail->AddAddress($smail1);

				if($smail2 != "")
					$mail->AddAddress($smail2);

				if($smail3 != "")
					$mail->AddAddress($smail3);

				$mail->Subject = $title;
				$mail->Body = $content; 
				$mail->Send();
			//  echo "Message Sent OK</p>\n";
			} catch (phpmailerException $e) {
				echo $e->errorMessage(); //Pretty error messages from PHPMailer
			} catch (Exception $e) {
				echo $e->getMessage(); //Boring error messages from anything else!
			}
		}
	}
	//-----------------------fanchao 2015-6-24 ---------------------------//
	/* SMS Commands */
	$sw = $sms_info['control']['sw'];
	$send_uuid_list = array();
	if($sw == 'on') {
		$password = trim($sms_info['control']['password']);
		if( $MESSAGE == "reboot system $password") {
			system_reboot();
		} else if( $MESSAGE == "reboot asterisk $password") {
			ast_reboot();
		} else if( $MESSAGE == "restore config $password") {
			res_def_cfg_file();
		} else if( $MESSAGE == "get info $password") {
			$PORT_VALUE = '';
			$SPAN=$PORT;
			if(strpos($PORT,'gsm')){
				preg_match_all('/\d+/',$SPAN,$arr);
				$PORT_VALUE = 'gsm'.$arr[0][0].'.'.$arr[0][1];
			} else {
				$PORT_VALUE = 'gsm1.'.$SPAN;
			}
			$SMSSRC=$PHONENUMBER;
			exec("/my_tools/add_syslog \"Send SMS to $SMSSRC by $SPAN (get ip)\" 2>&1");
			$str = "";
			if($cluster_info['mode'] == "master" || $cluster_info['mode'] == "stand_alone")
			{
				$filename = "/etc/cfg/gw/network/lan.conf";
				$handle = fopen($filename, "r");
				$content = fread($handle, filesize($filename));
				preg_match("/\d*\.\d*\.\d*\.\d*/",$content,$matches);
				if (isset($matches[0])) {
					$str = "IP : ".$matches[0];
				} else {
					$slot_num = get_slotnum();
					$default_ip = "172.16.99.".$slot_num;
					$str = "IP : ".$default_ip;
				}
			}
			echo $str;
			$STR=$str;	
			//exec("asterisk -rx \"gsm send sms $SPAN $SMSSRC \\\"$STR\\\"\" > /dev/null 2>&1");
			$uuid = create_uuid();
			$send_uuid_list[] = $uuid;
			$push_array = array();
			$push_array['uuid'] = $uuid;
			$push_array['type'] = 'sms';
			$push_array['port'] = $PORT_VALUE;
			$push_array['content'] = $STR;
			$push_array['value'] = $SMSSRC;
			$push_array['switch'] = "0";
			$push_array['retry'] = "1";
			$push_array['exten'] = "1";
			//print_r($push_array);
			$push_str = json_encode($push_array);
			$redis_client->rpush("app.asterisk.php.sendlist",$push_str);
			
		}
	}
	unset($sms_info);
}
?>

