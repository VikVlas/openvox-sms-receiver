<?php
require("/www/cgi-bin/inc/head.inc");
require("/www/cgi-bin/inc/menu.inc");
include_once("/www/cgi-bin/inc/function.inc");
include_once("/www/cgi-bin/inc/wrcfg.inc");
include_once("/www/cgi-bin/inc/aql.php");
include_once("/www/cgi-bin/inc/define.inc");

?>

<!--// load jQuery and the jQuery iButton Plug-in //--> 
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script> -->
<script type="text/javascript" src="/js/jquery.ibutton.js"></script> 

<!--// load the iButton CSS stylesheet //--> 
<link type="text/css" href="/css/jquery.ibutton.css" rel="stylesheet" media="all" />


<script type="text/javascript" src="/js/functions.js"></script>
<script type="text/javascript" src="/js/check.js"></script>
<script type="text/javascript" src="/js/float_btn.js"></script>
<script type="text/javascript">
function mail_change(sw)
{
	document.getElementById('smtpserversel').disabled = !sw;
	document.getElementById('sender').disabled = !sw;
	document.getElementById('smtpserver').disabled = !sw;
	document.getElementById('smtpport').disabled = !sw;
	document.getElementById('smtpuser').disabled = !sw;
	document.getElementById('smtppwd').disabled = !sw;
	document.getElementById('smail1').disabled = !sw;
	document.getElementById('smail2').disabled = !sw;
	document.getElementById('smail3').disabled = !sw;
	document.getElementById('mail_title').disabled = !sw;
	document.getElementById('mail_content').disabled = !sw;
}

function ctl_change(sw)
{
	document.getElementById('ctl_pwd').disabled = !sw;
}

function sms_clean_sw(sw)
{
	document.getElementById('sms_inbox_maxsize').disabled = !sw;
}

function setSMTPServer(value)
{
	switch(value) {
	case "other":
		document.getElementById("sender").value = "";
		document.getElementById('smtpserver').value = "";
		document.getElementById('smtpport').value = "";
		document.getElementById('smtpuser').value = "";
		document.getElementById('smtppwd').value = "";
		document.getElementById('tls_enable').checked = false;
		break;
	case "gmail":
		document.getElementById("sender").value = "";
		document.getElementById('smtpserver').value = "smtp.gmail.com";
		document.getElementById('smtpport').value = "587";
		document.getElementById('smtpuser').value = "";
		document.getElementById('smtppwd').value = "";
		document.getElementById('tls_enable').checked = true;
		break;
	case "hotmail":
		document.getElementById("sender").value = "";
		document.getElementById('smtpserver').value = "smtp.live.com";
		document.getElementById('smtpport').value = "587";
		document.getElementById('smtpuser').value = "";
		document.getElementById('smtppwd').value = "";
		document.getElementById('tls_enable').checked = true;
		break;
	case "yahoo":
		document.getElementById("sender").value = "";
		document.getElementById('smtpserver').value = "smtp.mail.yahoo.com";
		document.getElementById('smtpport').value = "587";
		document.getElementById('smtpuser').value = "";
		document.getElementById('smtppwd').value = "";
		document.getElementById('tls_enable').checked = false;
		break;
	}
}

function check()
{
	var mail_sw = document.getElementById("mail_sw").checked;
	var ctl_sw = document.getElementById("ctl_sw").checked;
	var http_sw = document.getElementById("http_sw").checked;
	var http_api_adv_enable = document.getElementById("http_api_adv_enable").checked;
	var sms_sw = document.getElementById("sms_sw").checked;
	//var ctl_sw = document.getElementById("sms_stored").checked;
	//var ctl_sw = document.getElementById("sms_receipt").checked;

	var sender = document.getElementById("sender").value;
	var smtpserver = document.getElementById("smtpserver").value;
	var smtpport = document.getElementById("smtpport").value;
	var smtpuser = document.getElementById("smtpuser").value;
	var smtppwd = document.getElementById("smtppwd").value;
	var smail1 = document.getElementById("smail1").value;
	var smail2 = document.getElementById("smail2").value;
	var smail3 = document.getElementById("smail3").value;
	var ctl_pwd = document.getElementById("ctl_pwd").value;
	var http_timeout_total = document.getElementById("http_timeout_total").value;
	var http_debug = document.getElementById("http_debug").value;
	var http_timeout_wait = document.getElementById("http_timeout_wait").value;
	var http_timeout_gsm_send = document.getElementById("http_timeout_gsm_send").value;
	var http_timeout_socket = document.getElementById("http_timeout_socket").value;

	
	document.getElementById("chttp_debug").innerHTML = '';
	document.getElementById("chttp_timeout_total").innerHTML = '';
	document.getElementById("chttp_timeout_wait").innerHTML = '';
	document.getElementById("chttp_timeout_gsm_send").innerHTML = '';
	document.getElementById("chttp_timeout_socket").innerHTML = '';

	if(mail_sw) {
		if(!check_email(sender)) {
			document.getElementById("csender").innerHTML = con_str('<?php echo language('js check email','Please input a valid email address');?>');
			return false;
		} else {
			document.getElementById("csender").innerHTML = '';
		}

		if(!check_domain(smtpserver)) {
			document.getElementById("csmtpserver").innerHTML = con_str('<?php echo language('js check domain','Invalid domain or IP address.');?>');
			return false;
		} else {
			document.getElementById("csmtpserver").innerHTML = '';
		}

		if(smtpport != '') { //Default 25
			if(!check_networkport(smtpport)) {
				document.getElementById("csmtpport").innerHTML = con_str('<?php echo language('js check networkport','Please input valid port number (1-65535)');?>');
				return false;
			} else {
				document.getElementById("csmtpport").innerHTML = '';
			}
		}

	//	if(!check_smtpuser(smtpuser)) {
		if(!check_email(smtpuser)) {
			document.getElementById("csmtpuser").innerHTML = con_str('<?php echo language('js check smtpuser','Please input a valid STMP user name');?>');
			return false;
		} else {
			document.getElementById("csmtpuser").innerHTML = '';
		}

		if(!check_smtppwd(smtppwd)) {
			document.getElementById("csmtppwd").innerHTML = con_str('<?php echo language('js check smtppwd','Allowed character must be any of [0-9a-zA-Z`~!@#$%^*()_{}:|?-=.], 1-64 characters.');?>');
			return false;
		} else {
			document.getElementById("csmtppwd").innerHTML = '';
		}

		if(smail1 == '' && smail2 == '' && smail3 == '') {
			document.getElementById("csmail1").innerHTML = con_str('<?php echo language('js check smail','You must set a email address.');?>');
			return false;
		}

		if(smail1 != '') {
			if(!check_email(smail1)) {
				document.getElementById("csmail1").innerHTML = con_str('<?php echo language('js check email','Please input a valid email address');?>');
				return false;
			} else {
				document.getElementById("csmail1").innerHTML = '';
			}
		}

		if(smail2 != '') {
			if(!check_email(smail2)) {
				document.getElementById("csmail2").innerHTML = con_str('<?php echo language('js check email','Please input a valid email address');?>');
				return false;
			} else {
				document.getElementById("csmail2").innerHTML = '';
			}
		}

		if(smail3 != '') {
			if(!check_email(smail3)) {
				document.getElementById("csmail3").innerHTML = con_str('<?php echo language('js check email','Please input a valid email address');?>');
				return false;
			} else {
				document.getElementById("csmail3").innerHTML = '';
			}
		}
	}

	if(ctl_sw) {
		if(!check_diypwd(ctl_pwd)) {
			document.getElementById("cctl_pwd").innerHTML = con_str('<?php echo htmlentities(language('js check diypwd','Allowed character must be any of [0-9a-zA-Z`~!@$%^&*()_+{}|<>?-=[],./],4 - 32 characters.'));?>');
			return false;
		} else {
			document.getElementById("cctl_pwd").innerHTML = '';
		}
	}
	
	if(http_sw) {
		if(isNaN(http_timeout_total) || http_timeout_total <= 0) {
			document.getElementById("chttp_timeout_total").innerHTML = con_str('<?php echo htmlentities(language('Http Timeout help','Allowed character must be  greater than zero.'));?>');
			return false;
		}
		
		if(http_api_adv_enable) {
			if(isNaN(http_debug) || http_debug < 0) {
				document.getElementById("chttp_debug").innerHTML = con_str('<?php echo htmlentities(language('Http Debug help','Allowed character must be  greater than zero.'));?>');
				return false;
			}
			if(isNaN(http_timeout_wait) || http_timeout_wait <= 0) {
				document.getElementById("chttp_timeout_wait").innerHTML = con_str('<?php echo htmlentities(language('Http Timeout help','Allowed character must be  greater than zero.'));?>');
				return false;
			}
			if(isNaN(http_timeout_gsm_send) || http_timeout_gsm_send <= 0) {
				document.getElementById("chttp_timeout_gsm_send").innerHTML = con_str('<?php echo htmlentities(language('Http Timeout help','Allowed character must be  greater than zero.'));?>');
				return false;
			}
			if(isNaN(http_timeout_socket) || http_timeout_socket <= 0) {
				document.getElementById("chttp_timeout_socket").innerHTML = con_str('<?php echo htmlentities(language('Http Timeout help','Allowed character must be  greater than zero.'));?>');
				return false;
			}
		/*} else {
			document.getElementById("http_debug").value = '0';
			document.getElementById("http_timeout_wait").value = '20';
			document.getElementById("http_timeout_gsm_send").value = '10';
			document.getElementById("http_timeout_socket").value = '2';*/
		}
	}

	return true;
}

function check_sms_url(sms_url)
{
	var patt_form=new RegExp("\$\{[from]\}");
	var patt_to=new RegExp("\$\{to\}");
	var patt_mess=new RegExp("\$\{message\}");
	var patt_time=new RegExp("\$\{time\}");
	if(!patt_form.test(sms_url)) return "${from}";
	if(!patt_to.test(sms_url)) return "${to}";
	if(!patt_mess.test(sms_url)) return "${message}";
	if(!patt_time.test(sms_url)) return "${time}";
	return "true";
}

function http_change_gen(obj, showId)
{
	$("#http_api_adv_enable").iButton("toggle", false);
	$("#http_api_adv").hide();
	$("#"+showId).slideToggle();
	var div_tab_td_gsm_list = document.getElementById("div_tab_td_gsm_list").offsetHeight;
	$("#div_tab_th_gsm_list").css("height",div_tab_td_gsm_list);
}
function sms_change_gen(obj, showClass)
{
	$("."+showClass).slideToggle();
}
function sms_to_http_change(showClass)
{
	var sms_sw = document.getElementById('sms_sw').checked;
	if(sms_sw){
		$("."+showClass).show();
	} else {
		$("."+showClass).hide();
	}
}
function http_change_adv(obj, showId)
{
	$('#'+showId).slideToggle();
}
</script>

<?php
function save_to_sms_conf()
{
/* 
[send] 
attempt= 
repeat= 
verbose= 

[mail]
sw=0
sender=
smtpserver=
smtpport=
smtpuser=
smtppwd= 
tls_enable= 
smail1=
smail2=
smail3= 
mail_title= 
mail_content= 

[control]
sw=0
password=

[http_to_sms] 
enable=off|on 
use_web_server_user=on|off 
username=xxx 
password=xxx 
port=gsm-1.1,gsm-1.2,xxxx
report=json|string|no 
timeout_total=20  //s 
debug=0 
timeout_wait=20   //s 
timeout_gsm_send=10000  //ms 
timeout_socket=2  //s

*/
	$sms_conf_path = '/etc/asterisk/gw/sms.conf';
	touch($sms_conf_path);

	$aql = new aql();
	$setok = $aql->set('basedir','/etc/asterisk/gw');
	if (!$setok) {
		echo $aql->get_error();
		return;
	}

	$hlock = lock_file($sms_conf_path);

	if(!$aql->open_config_file($sms_conf_path)){
		echo $aql->get_error();
		unlock_file($hlock);
		return;
	}

	$exist_array = $aql->query("select * from sms.conf");

	if(!isset($exist_array['send'])) {
		$aql->assign_addsection('send','');
	}
	if(!isset($exist_array['mail'])) {
		$aql->assign_addsection('mail','');
	}
	if(!isset($exist_array['control'])) {
		$aql->assign_addsection('control','');
	}
	if(!isset($exist_array['http_to_sms'])) {
		$aql->assign_addsection('http_to_sms','');
	}
	if(!isset($exist_array['sms_to_http'])) {
		$aql->assign_addsection('sms_to_http','');
	}
	if(!isset($exist_array['local_store'])) {
		$aql->assign_addsection('local_store','');
	}

	if(isset($_POST['sms_local_store_enable'])) {
		$val = 'on';
	}else{
		$val = 'off';
	}
	if(isset($exist_array['local_store']['enable'])) {
		$aql->assign_editkey('local_store','enable',$val);
	} else {
		$aql->assign_append('local_store','enable',$val);
	} 

	if(isset($_POST['send_attempt'])) {
		$val = trim($_POST['send_attempt']);
		if(isset($exist_array['send']['attempt'])) {
			$aql->assign_editkey('send','attempt',$val);
		} else {
			$aql->assign_append('send','attempt',$val);
		} 
	}

	if(isset($_POST['send_repeat'])) {
		$val = trim($_POST['send_repeat']);
		if(isset($exist_array['send']['repeat'])) {
			$aql->assign_editkey('send','repeat',$val);
		} else {
			$aql->assign_append('send','repeat',$val);
		} 
	}

	if(isset($_POST['send_verbose'])) {
		$val = trim($_POST['send_verbose']);
		if(isset($exist_array['send']['verbose'])) {
			$aql->assign_editkey('send','verbose',$val);
		} else {
			$aql->assign_append('send','verbose',$val);
		} 
	}

	if(isset($_POST['mail_sw'])) {
		$val = 'on';
	} else {
		$val = 'off';
	}
	if(isset($exist_array['mail']['sw'])) {
		$aql->assign_editkey('mail','sw',$val);
	} else {
		$aql->assign_append('mail','sw',$val);
	}

	if(isset($_POST['sender'])) {
		$val = trim($_POST['sender']);
		if(isset($exist_array['mail']['sender'])) {
			$aql->assign_editkey('mail','sender',$val);
		} else {
			$aql->assign_append('mail','sender',$val);
		} 
	}

	if(isset($_POST['smtpserver'])) {
		$val = trim($_POST['smtpserver']);
		if(isset($exist_array['mail']['smtpserver'])) {
			$aql->assign_editkey('mail','smtpserver',$val);
		} else {
			$aql->assign_append('mail','smtpserver',$val);
		} 
	}

	if(isset($_POST['smtpport'])) {
		$val = trim($_POST['smtpport']);
		if(isset($exist_array['mail']['smtpport'])) {
			$aql->assign_editkey('mail','smtpport',$val);
		} else {
			$aql->assign_append('mail','smtpport',$val);
		} 
	}

	if(isset($_POST['smtpuser'])) {
		$val = trim($_POST['smtpuser']);
		if(isset($exist_array['mail']['smtpuser'])) {
			$aql->assign_editkey('mail','smtpuser',$val);
		} else {
			$aql->assign_append('mail','smtpuser',$val);
		} 
	}

	if(isset($_POST['smtppwd'])) {
		$val = trim($_POST['smtppwd']);
		if(isset($exist_array['mail']['smtppwd'])) {
			$aql->assign_editkey('mail','smtppwd',$val);
		} else {
			$aql->assign_append('mail','smtppwd',$val);
		} 
	}

	if(isset($_POST['tls_enable'])) {
		$val = 'yes';
	} else {
		$val = 'no';
	}
	if(isset($exist_array['mail']['tls_enable'])) {
		$aql->assign_editkey('mail','tls_enable',$val);
	} else {
		$aql->assign_append('mail','tls_enable',$val);
	}

	if(isset($_POST['smail1'])) {
		$val = trim($_POST['smail1']);
		if(isset($exist_array['mail']['smail1'])) {
			$aql->assign_editkey('mail','smail1',$val);
		} else {
			$aql->assign_append('mail','smail1',$val);
		} 
	}

	if(isset($_POST['smail2'])) {
		$val = trim($_POST['smail2']);
		if(isset($exist_array['mail']['smail2'])) {
			$aql->assign_editkey('mail','smail2',$val);
		} else {
			$aql->assign_append('mail','smail2',$val);
		} 
	}

	if(isset($_POST['smail3'])) {
		$val = trim($_POST['smail3']);
		if(isset($exist_array['mail']['smail3'])) {
			$aql->assign_editkey('mail','smail3',$val);
		} else {
			$aql->assign_append('mail','smail3',$val);
		} 
	}

	if(isset($_POST['mail_title'])) {
		$val = trim($_POST['mail_title']);
		if(isset($exist_array['mail']['mail_title'])) {
			$aql->assign_editkey('mail','mail_title',$val);
		} else {
			$aql->assign_append('mail','mail_title',$val);
		} 
	}

	if(isset($_POST['mail_content'])) {
		$val = trim($_POST['mail_content']);
		if(isset($exist_array['mail']['mail_content'])) {
			$aql->assign_editkey('mail','mail_content',$val);
		} else {
			$aql->assign_append('mail','mail_content',$val);
		} 
	}

	if(isset($_POST['ctl_sw'])) {
		$val = 'on';
	} else {
		$val = 'off';
	}
	if(isset($exist_array['control']['sw'])) {
		$aql->assign_editkey('control','sw',$val);
	} else {
		$aql->assign_append('control','sw',$val);
	} 

	if(isset($_POST['ctl_pwd'])) {
		$val = trim($_POST['ctl_pwd']);
		if(isset($exist_array['control']['password'])) {
			$aql->assign_editkey('control','password',$val);
		} else {
			$aql->assign_append('control','password',$val);
		} 
	}
	
	if(isset($_POST['http_sw'])) {
		$val = 'on';
	} else {
		$val = 'off';
	}
	if(isset($exist_array['http_to_sms']['enable'])) {
		$aql->assign_editkey('http_to_sms','enable',$val);
	} else {
		$aql->assign_append('http_to_sms','enable',$val);
	}
	
	if(isset($_POST['http_use_default_user'])) {
		$val = 'on';
	} else {
		$val = 'off';
	}
	if(isset($exist_array['http_to_sms']['use_default_user'])) {
		$aql->assign_editkey('http_to_sms','use_default_user',$val);
	} else {
		$aql->assign_append('http_to_sms','use_default_user',$val);
	}
	
	if(isset($_POST['http_username'])) {
		$val = trim($_POST['http_username']);
		if(isset($exist_array['http_to_sms']['username'])) {
			$aql->assign_editkey('http_to_sms','username',$val);
		} else {
			$aql->assign_append('http_to_sms','username',$val);
		} 
	}
	
	if(isset($_POST['http_password'])) {
		$val = trim($_POST['http_password']);
		if(isset($exist_array['http_to_sms']['password'])) {
			$aql->assign_editkey('http_to_sms','password',$val);
		} else {
			$aql->assign_append('http_to_sms','password',$val);
		} 
	}
	
	if(isset($_POST['http_port_select'])) {
		$val = trim($_POST['http_port_select']);
		if(isset($exist_array['http_to_sms']['port'])) {
			$aql->assign_editkey('http_to_sms','port',$val);
		} else {
			$aql->assign_append('http_to_sms','port',$val);
		} 
	}
	
	if(isset($_POST['http_report'])) {
		$val = trim($_POST['http_report']);
		if(isset($exist_array['http_to_sms']['report'])) {
			$aql->assign_editkey('http_to_sms','report',$val);
		} else {
			$aql->assign_append('http_to_sms','report',$val);
		} 
	}
	
	if(isset($_POST['http_timeout_total'])) {
		$val = trim($_POST['http_timeout_total']);
		if(isset($exist_array['http_to_sms']['timeout_total'])) {
			$aql->assign_editkey('http_to_sms','timeout_total',$val);
		} else {
			$aql->assign_append('http_to_sms','timeout_total',$val);
		} 
	}
	
	if(isset($_POST['http_debug'])) {
		$val = trim($_POST['http_debug']);
		if(isset($exist_array['http_to_sms']['debug'])) {
			$aql->assign_editkey('http_to_sms','debug',$val);
		} else {
			$aql->assign_append('http_to_sms','debug',$val);
		} 
	}
	
	if(isset($_POST['http_timeout_wait'])) {
		$val = trim($_POST['http_timeout_wait']);
		if(isset($exist_array['http_to_sms']['timeout_wait'])) {
			$aql->assign_editkey('http_to_sms','timeout_wait',$val);
		} else {
			$aql->assign_append('http_to_sms','timeout_wait',$val);
		} 
	}
	
	if(isset($_POST['http_timeout_gsm_send'])) {
		$val = trim($_POST['http_timeout_gsm_send'])*1000;
		if(isset($exist_array['http_to_sms']['timeout_gsm_send'])) {
			$aql->assign_editkey('http_to_sms','timeout_gsm_send',$val);
		} else {
			$aql->assign_append('http_to_sms','timeout_gsm_send',$val);
		} 
	}
	
	if(isset($_POST['http_timeout_socket'])) {
		$val = trim($_POST['http_timeout_socket']);
		if(isset($exist_array['http_to_sms']['timeout_socket'])) {
			$aql->assign_editkey('http_to_sms','timeout_socket',$val);
		} else {
			$aql->assign_append('http_to_sms','timeout_socket',$val);
		} 
	}
	
	if(isset($_POST['sms_sw'])) {
		$val = 'on';
	} else {
		$val = 'off';
	}
	if(isset($exist_array['sms_to_http']['sms_to_http_enable'])) {
		$aql->assign_editkey('sms_to_http','sms_to_http_enable',$val);
	} else {
		$aql->assign_append('sms_to_http','sms_to_http_enable',$val);
	}
	 
	if(isset($_POST['sms_reports_sw'])) {
		$val = 'on';
	} else {
		$val = 'off';
	}

	if(isset($exist_array['sms_to_http']['smsreports_to_http_enable'])) {
		$aql->assign_editkey('sms_to_http','smsreports_to_http_enable',$val);
	} else {
		$aql->assign_append('sms_to_http','smsreports_to_http_enable',$val);
	}
 

	
	if(isset($_POST['sms_url_host'])) {
		$val = trim($_POST['sms_url_host']);
		if(isset($exist_array['sms_to_http']['url_host'])) {
			$aql->assign_editkey('sms_to_http','url_host',$val);
		} else {
			$aql->assign_append('sms_to_http','url_host',$val);
		}
	}
	
	if(isset($_POST['sms_url_port'])) {
		$val = trim($_POST['sms_url_port']);
		if(isset($exist_array['sms_to_http']['url_port'])) {
			$aql->assign_editkey('sms_to_http','url_port',$val);
		} else {
			$aql->assign_append('sms_to_http','url_port',$val);
		}
	}
	
	if(isset($_POST['sms_url_path'])) {
		$val = trim($_POST['sms_url_path']);
		if(isset($exist_array['sms_to_http']['url_path'])) {
			$aql->assign_editkey('sms_to_http','url_path','/'.$val);
		} else {
			$aql->assign_append('sms_to_http','url_path','/'.$val);
		}
	}
	
	if(isset($_POST['sms_url_from_num'])) {
		$val = trim($_POST['sms_url_from_num']);
		if(isset($exist_array['sms_to_http']['url_from_num'])) {
			$aql->assign_editkey('sms_to_http','url_from_num',$val);
		} else {
			$aql->assign_append('sms_to_http','url_from_num',$val);
		}
	}
	
	if(isset($_POST['sms_url_to_num'])) {
		$val = trim($_POST['sms_url_to_num']);
		if(isset($exist_array['sms_to_http']['url_to_num'])) {
			$aql->assign_editkey('sms_to_http','url_to_num',$val);
		} else {
			$aql->assign_append('sms_to_http','url_to_num',$val);
		}
	}
	
	if(isset($_POST['sms_url_message'])) {
		$val = trim($_POST['sms_url_message']);
		if(isset($exist_array['sms_to_http']['url_message'])) {
			$aql->assign_editkey('sms_to_http','url_message',$val);
		} else {
			$aql->assign_append('sms_to_http','url_message',$val);
		}
	}
	
	if(isset($_POST['sms_url_time'])) {
		$val = trim($_POST['sms_url_time']);
		if(isset($exist_array['sms_to_http']['url_time'])) {
			$aql->assign_editkey('sms_to_http','url_time',$val);
		} else {
			$aql->assign_append('sms_to_http','url_time',$val);
		}
	}

	if(isset($_POST['sms_url_status'])) {
		$val = trim($_POST['sms_url_status']);
		if(isset($exist_array['sms_to_http']['url_status'])) {
			$aql->assign_editkey('sms_to_http','url_status',$val);
		} else {
			$aql->assign_append('sms_to_http','url_status',$val);
		}
	}
	
	if(isset($_POST['sms_url_user_defined'])) {
		if(trim($_POST['sms_url_user_defined']) == 'User Defined'){
			$_POST['sms_url_user_defined'] = '';
			$val = '';
		} else {
			$val = trim($_POST['sms_url_user_defined']);
		}
		if(isset($exist_array['sms_to_http']['url_user_defined'])) {
			$aql->assign_editkey('sms_to_http','url_user_defined',$val);
		} else {
			$aql->assign_append('sms_to_http','url_user_defined',$val);
		}
	}
	
	if(isset($_POST['sms_url_host']) && isset($_POST['sms_url_port'])) {
		$val = "http://".trim($_POST['sms_url_host']).":".trim($_POST['sms_url_port']).'/'.trim($_POST['sms_url_path'])."?"
			.trim($_POST['sms_url_from_num'])."=\${phonenumber}&"
			.trim($_POST['sms_url_to_num'])."=\${port}&"
			.trim($_POST['sms_url_message'])."=\${message}&"
			.trim($_POST['sms_url_time'])."=\${time}&"
			.trim($_POST['sms_url_user_defined']);
		if(isset($exist_array['sms_to_http']['url'])) {
			$aql->assign_editkey('sms_to_http','url',$val);
		} else {
			$aql->assign_append('sms_to_http','url',$val);
		}
	}

	if (!$aql->save_config_file('sms.conf')) {
		echo $aql->get_error();
		unlock_file($hlock);
		return;
	}
	unlock_file($hlock);
}

function save_to_manager_conf()
{
    $aql = new aql();
    $setok = $aql->set('basedir','/etc/asterisk');
    if(!$setok){
        echo $aql->get_error();
        return;
     }
     $manager_conf_path = '/etc/asterisk/manager.conf';
     $hlock = lock_file($manager_conf_path);
     if(!$aql->open_config_file($manager_conf_path)){
       echo $aql->get_error();
       unlock_file($hlock);
       return;
      }   
                                                                     
     $exist_array = $aql->query("select * from manager.conf");
                                                                               
                                                                                    
                                                                                   
     if(isset($_POST['http_username'])){
        $username_tmp = trim($_POST['http_username']);
     if(!isset($exist_array[$username_tmp])){
        $aql->assign_addsection($username_tmp,'');
     }   
    if(isset($_POST['http_password'])){
      $password_tmp = trim($_POST['http_password']);
    if(isset($exist_array[$username_tmp]['secret'])){
      $aql->assign_editkey($username_tmp,'secret',$password_tmp);
    } else {
      $aql->assign_append($username_tmp,'secret',$password_tmp);
    }    
    }   
     if(isset($exist_array[$username_tmp]['read'])){
    $aql->assign_editkey($username_tmp,'read','system');
    } else {
     $aql->assign_append($username_tmp,'read','system');
    }   
      if(isset($exist_array[$username_tmp]['write'])){
      $aql->assign_editkey($username_tmp,'write','system,call,log,verbose,command,agent,user,config,reporting,originate');
     } else {
       $aql->assign_append($username_tmp,'write','system,call,log,verbose,command,agent,user,config,reporting,originate');
       }
    }


      if(!$aql->save_config_file('manager.conf')){
      echo $aql->get_error();
      unlock_file($hlock);
      return;
        }
    }






function save_to_logfile_monitor_conf()
{
/*
/etc/asterisk/gw/logfile_monitor.conf
[sys_log]
autoclean_sw=on
maxsize=1MB

[ast_log]
autoclean_sw=on
maxsize=100KB

[sip_log]
autoclean_sw=on
maxsize=100KB

[at_log]
autoclean_sw=on
maxsize=100KB

[cdr_log]
autoclean_sw=on
maxsize=20MB

[sms_inbox]
autoclean_sw=on
maxsize=20MB
*/

	$conf_path = '/etc/asterisk/gw';
	$conf_file = 'logfile_monitor.conf';
	$conf_path_file = $conf_path."/".$conf_file;

	$aql = new aql();
	$setok = $aql->set('basedir',$conf_path);
	if (!$setok) {
		echo $aql->get_error();
	}

	$hlock=lock_file($conf_path_file);
	if(!file_exists($conf_path_file)) {
		fclose(fopen($conf_path_file,"w"));
	}
	if(!$aql->open_config_file($conf_path_file)){
		echo $aql->get_error();
		unlock_file($hlock);
		return;
	}
	$conf_array = $aql->query("select * from $conf_file");

	if(!isset($conf_array["sms_inbox"])) {
		$aql->assign_addsection("sms_inbox",'');
	}
	if(isset($_POST["sms_inbox_autoclean_sw"])){
		if(isset($conf_array["sms_inbox"]['autoclean_sw']))
			$aql->assign_editkey("sms_inbox",'autoclean_sw',"on");
		else
			$aql->assign_append("sms_inbox",'autoclean_sw',"on");
		if(isset($_POST["sms_inbox_maxsize"])){
			if(isset($conf_array["sms_inbox"]['maxsize']))
				$aql->assign_editkey("sms_inbox",'maxsize',$_POST["sms_inbox_maxsize"]);
			else
				$aql->assign_append("sms_inbox",'maxsize',$_POST["sms_inbox_maxsize"]);
		}
	}else{
		if(isset($conf_array["sms_inbox"]['autoclean_sw']))
			$aql->assign_editkey("sms_inbox",'autoclean_sw',"off");
		else
			$aql->assign_append("sms_inbox",'autoclean_sw',"off");
	}

	if (!$aql->save_config_file($conf_file)) {
		echo $aql->get_error();
		unlock_file($hlock);
		return;
	}
	unlock_file($hlock);
}

function save_to_extra_global_conf()
{
//extra-global.conf
/* 
[channels]
usecallerid=yes
callwaiting=yes
usecallingpres=yes
callwaitingcallerid=yes
threewaycalling=yes
transfer=yes
canpark=yes
cancallforward=yes
callreturn=yes
echocancel=yes
echocancelwhenbridged=yes
group=1
callgroup=1
pickupgroup=1
relaxdtmf=yes
 
processsms=yes
smsreport=yes
*/	
	$aql = new aql();
	$setok = $aql->set('basedir','/etc/asterisk');
	if (!$setok) {
		echo $aql->get_error();
		return;
	}
	$sms_conf_path = '/etc/asterisk/extra-global.conf';
	$hlock = lock_file($sms_conf_path);
	if(!$aql->open_config_file($sms_conf_path)){
		echo $aql->get_error();
		unlock_file($hlock);
		return;
	}
	$exist_array = $aql->query("select * from extra-global.conf");
	//print_r($exist_array);exit;
	if(isset($_POST['smsprocess'])) {
		$val = 'yes';
	} else {
		$val = 'no';
	}
	if(isset($exist_array['channels']['processsms'])) {
		$aql->assign_editkey('channels','processsms',$val);
	} else {
		$aql->assign_append('channels','processsms',$val);
	}
	
	if(isset($_POST['smsreport'])) {
		$val = 'yes';
	} else {
		$val = 'no';
	}
	if(isset($exist_array['channels']['smsreport'])) {
		$aql->assign_editkey('channels','smsreport',$val);
	} else {
		$aql->assign_append('channels','smsreport',$val);
	}
	if (!$aql->save_config_file('extra-global.conf')) {
		echo $aql->get_error();
		unlock_file($hlock);
		return;
	}	
}

function save_sms_conf_to_slave($master_ip)
{
	$sms_conf_path = '/etc/asterisk/gw/sms.conf';
	touch($sms_conf_path);
	copy($sms_conf_path,'/tmp/sms_to_slave.conf');
	$aql = new aql();
	$setok = $aql->set('basedir','/tmp');
	if (!$setok) {
		echo $aql->get_error();
		return;
	}

	$sms_slave_conf_path = '/tmp/sms_to_slave.conf';
	$hlock = lock_file($sms_slave_conf_path);

	if(!$aql->open_config_file($sms_slave_conf_path)){
		echo $aql->get_error();
		unlock_file($hlock);
		return;
	}

	$exist_array = $aql->query("select * from sms_to_slave.conf");

	if(!isset($exist_array['sms_to_http'])) {
		$aql->assign_addsection('sms_to_http','');
	}

	if(isset($_POST['sms_url_host']) && isset($_POST['sms_url_port'])) {
		if(isset($_POST['sms_url_user_defined']) && trim($_POST['sms_url_user_defined']) == 'User Defined') {
			$_POST['sms_url_user_defined'] = '';
		}
		//$webport=`/my_tools/set_config /etc/asterisk/gw/web_server.conf get option_value general port 2> /dev/null`;
		$web_info = get_web_info();
		$webport = $web_info['general']['port'];
		if(!is_numeric($webport) || $webport <= 0){
			$webport = '80';
	   	} 
		$val = "http://".trim($master_ip).':'.$webport.'/'.trim($_POST['sms_url_path']).'?'
			.trim($_POST['sms_url_from_num'])."=\${phonenumber}&"
			.trim($_POST['sms_url_to_num'])."=\${port}&"
			.trim($_POST['sms_url_message'])."=\${message}&"
			.trim($_POST['sms_url_time'])."=\${time}&"
			.trim($_POST['sms_url_user_defined']);
		if(isset($exist_array['sms_to_http']['url'])) {
			$aql->assign_editkey('sms_to_http','url',$val);
		} else {
			$aql->assign_append('sms_to_http','url',$val);
		}
	}

	if (!$aql->save_config_file('sms_to_slave.conf')) {
		echo $aql->get_error();
		unlock_file($hlock);
		return;
	}
	unlock_file($hlock);
}

if($_POST && isset($_POST['send']) && $_POST['send'] == 'Save') {
	save_to_sms_conf();
//	save_to_manager_conf();
	save_to_logfile_monitor_conf();
	save_to_extra_global_conf();
	save_routings_to_extensions();
	exec("cd /my_tools/lua/my_lua_tools/ && lua insert_port_redis.lua > /dev/null 2>&1");
	exec("cd /my_tools/lua/my_lua_tools/ && lua conf_to_redis.lua init > /dev/null 2>&1");
	
	wait_apply("exec", "/etc/init.d/logfile_monitor restart > /dev/null 2>&1 &");
	wait_apply("exec", "asterisk -rx \"extra restart\" > /dev/null 2>&1 &");
	wait_apply("exec", "asterisk -rx \"core reload\" > /dev/null 2>&1 &");
	wait_apply("exec", "/my_tools/cluster_mode > /dev/null 2>&1");
	wait_apply("exec", "/etc/init.d/lighttpd restart > /dev/null 2>&1 &");

	wait_apply("exec", "/etc/init.d/smsreports restart > /dev/null 2>&1 &");
	

	//sync slave config
	$cluster_info = get_cluster_info();
	if($cluster_info['mode'] == 'master') {
		save_sms_conf_to_slave($cluster_info['master_ip']);
		for($b=2; $b<=$__BRD_SUM__; $b++) {
			if($cluster_info[$__BRD_HEAD__.$b.'_ip'] != '') {
				$slaveip = $cluster_info[$__BRD_HEAD__.$b.'_ip'];
				set_slave_file($slaveip,"/etc/asterisk/gw/logfile_monitor.conf","/etc/asterisk/gw/logfile_monitor.conf");
				set_slave_file($slaveip,"/etc/asterisk/extra-global.conf","/etc/asterisk/extra-global.conf");
				set_slave_file($slaveip,"/tmp/sms_to_slave.conf","/etc/asterisk/gw/sms.conf");
				wait_apply("request_slave", $slaveip, "syscmd:/etc/init.d/logfile_monitor restart > /dev/null 2>&1 &");
				wait_apply("request_slave", $slaveip, "syscmd:asterisk -rx \"extra restart\" > /dev/null 2>&1 &");
				wait_apply("request_slave", $slaveip, "syscmd:asterisk -rx \"core reload\" > /dev/null 2>&1 &");
				wait_apply("request_slave", $slaveip, "syscmd:/my_tools/cluster_mode > /dev/null 2>&1");
				wait_apply("request_slave", $slaveip, "syscmd:/etc/init.d/lighttpd restart > /dev/null 2>&1 &");
			}
		}
		unlink('/tmp/sms_to_slave.conf');
	}
}
?>

<?php
$aql = new aql();
$aql->set('basedir','/etc/asterisk/gw');
$res = $aql->query("select * from sms.conf");

$sms_local_store_enable = '';
if(isset($res['local_store']['enable'])){
	if(is_true(trim($res['local_store']['enable']))){
		$sms_local_store_enable = 'checked';
	}
}

$mail_sw = '';
if(isset($res['mail']['sw'])) {
	if(is_true(trim($res['mail']['sw']))){
		$mail_sw = 'checked';
	}
}

if(isset($res['mail']['sender'])) {
	$sender=trim($res['mail']['sender']);
} else {
	$sender="";
}

if(isset($res['mail']['smtpserver'])) {
	$smtpserver=trim($res['mail']['smtpserver']);
} else {
	$smtpserver="";
}

if(isset($res['mail']['smtpport'])) {
	$smtpport=trim($res['mail']['smtpport']);
} else {
	$smtpport="";
}

if(isset($res['mail']['smtpuser'])) {
	$smtpuser=trim($res['mail']['smtpuser']);
} else {
	$smtpuser="";
}

if(isset($res['mail']['smtppwd'])) {
	$smtppwd=trim($res['mail']['smtppwd']);
} else {
	$smtppwd="";
}

if(isset($res['mail']['tls_enable']) && $res['mail']['tls_enable']=='yes') {
	$tls_enable = 'checked';
} else {
	$tls_enable = '';
}

if(isset($res['mail']['smail1'])) {
	$smail1=trim($res['mail']['smail1']);
} else {
	$smail1="";
}

if(isset($res['mail']['smail2'])) {
	$smail2=trim($res['mail']['smail2']);
} else {
	$smail2="";
}

if(isset($res['mail']['smail3'])) {
	$smail3=trim($res['mail']['smail3']);
} else {
	$smail3="";
}

if(isset($res['mail']['mail_title'])) {
	$mail_title=trim($res['mail']['mail_title']);
} else {
	$mail_title="";
}

if(isset($res['mail']['mail_content'])) {
	$mail_content=trim($res['mail']['mail_content']);
} else {
	$mail_content="";
}

$ctl_sw = '';
if(isset($res['control']['sw'])) {
	if(is_true(trim($res['control']['sw']))){
		$ctl_sw = 'checked';
	}
}

if(isset($res['control']['password'])) {
	$ctl_pwd = trim($res['control']['password']);
} else {
	$ctl_pwd="";
}

$send_attempt['-1'] = '';
$send_attempt[0] = '';
$send_attempt[1] = '';
$send_attempt[2] = '';
$send_attempt[3] = '';
$send_attempt[4] = '';
$send_attempt[5] = '';
if(isset($res['send']['attempt'])) {
	$val = trim($res['send']['attempt']);
	if($val >=-1 && $val <=5) {
		$send_attempt[$val] = 'selected';
	} else {
		$send_attempt[0] = 'selected';
	}
} else {
	$send_attempt[0] = 'selected';
}

$send_repeat[1] = '';
$send_repeat[2] = '';
$send_repeat[3] = '';
$send_repeat[4] = '';
$send_repeat[5] = '';
if(isset($res['send']['repeat'])) {
	$val = trim($res['send']['repeat']);
	if($val >=1 && $val <=5) {
		$send_repeat[$val] = 'selected';
	} else {
		$send_repeat[1] = 'selected';
	}
} else {
	$send_repeat[1] = 'selected';
}

$send_verbose[0] = '';
$send_verbose[1] = '';
$send_verbose[2] = '';
$send_verbose[3] = '';
if(isset($res['send']['verbose'])) {
	$val = trim($res['send']['verbose']);
	if($val >=0 && $val <=3) {
		$send_verbose[$val] = 'selected';
	} else {
		$send_verbose[3] = 'selected';
	}

} else {
	$send_verbose[3] = 'selected';
}


/*
$lighttpdpassword = '/etc/asterisk/gw/lighttpdpassword';
$lighttpdpassword_contents = trim(file_get_contents($lighttpdpassword));
$lighttpd_user = explode(":",$lighttpdpassword_contents);
$http_username = $lighttpd_user[0];
$http_password = $lighttpd_user[1];
 */

// default user and password
$http_username = "smsuser";
$http_password = "smspwd";

$http_sw = '';
$http_use_default_user= 'checked';
if(isset($res['http_to_sms']['enable'])) {
	if(is_true(trim($res['http_to_sms']['enable']))){
		$http_sw = 'checked';
	}
}

if(isset($res['http_to_sms']['use_default_user'])) {
	if(is_true(trim($res['http_to_sms']['use_default_user']))){
		$http_use_default_user = 'checked';
	} else {
		$http_use_default_user = '';
	}
}

if(isset($res['http_to_sms']['username'])) {
	$http_username=trim($res['http_to_sms']['username']);
}

if(isset($res['http_to_sms']['password'])) {
	$http_password=trim($res['http_to_sms']['password']);
}

if(isset($res['http_to_sms']['port'])) {
	$http_port=trim($res['http_to_sms']['port']);
} else {
	$http_port="all";
}

if(isset($res['http_to_sms']['report'])) {
	$http_report=trim($res['http_to_sms']['report']);
} else {
	$http_report="json";
}

if(isset($res['http_to_sms']['debug'])) {
	$http_debug=trim($res['http_to_sms']['debug']);
} else {
	$http_debug="0";
}

if(isset($res['http_to_sms']['timeout_total'])) {
	$http_timeout_total=trim($res['http_to_sms']['timeout_total']);
} else {
	$http_timeout_total="20";
}

if(isset($res['http_to_sms']['timeout_wait'])) {
	$http_timeout_wait=trim($res['http_to_sms']['timeout_wait']);
} else {
	$http_timeout_wait="20";
}

if(isset($res['http_to_sms']['timeout_gsm_send'])) {
	$http_timeout_gsm_send=trim($res['http_to_sms']['timeout_gsm_send'])/1000;
} else {
	$http_timeout_gsm_send="10";
}

if(isset($res['http_to_sms']['timeout_socket'])) {
	$http_timeout_socket=trim($res['http_to_sms']['timeout_socket']);
} else {
	$http_timeout_socket="2";
}


$sms_sw = '';
if(isset($res['sms_to_http']['sms_to_http_enable'])) {
	if(is_true(trim($res['sms_to_http']['sms_to_http_enable']))){
		$sms_sw = 'checked';
	}
}

$sms_reports_sw = '';
if(isset($res['sms_to_http']['smsreports_to_http_enable'])) {
	if(is_true(trim($res['sms_to_http']['smsreports_to_http_enable']))){
		$sms_reports_sw = 'checked';
	}
}	


if(isset($res['sms_to_http']['url_host'])) {
	$sms_url_host=trim($res['sms_to_http']['url_host']);
} else {
	$sms_url_host="";
}

if(isset($res['sms_to_http']['url_port'])) {
	$sms_url_port=trim($res['sms_to_http']['url_port']);
} else {
	$sms_url_port="";
}

if(isset($res['sms_to_http']['url_path'])) {
	$sms_url_path=trim($res['sms_to_http']['url_path'], "\t\n\r\0/");
} else {
	$sms_url_path="";
}

if(isset($res['sms_to_http']['url_from_num'])) {
	$sms_url_from_num=trim($res['sms_to_http']['url_from_num']);
} else {
	$sms_url_from_num="";
}

if(isset($res['sms_to_http']['url_to_num'])) {
	$sms_url_to_num=trim($res['sms_to_http']['url_to_num']);
} else {
	$sms_url_to_num="";
}

if(isset($res['sms_to_http']['url_message'])) {
	$sms_url_message=trim($res['sms_to_http']['url_message']);
} else {
	$sms_url_message="";
}

if(isset($res['sms_to_http']['url_time'])) {
	$sms_url_time=trim($res['sms_to_http']['url_time']);
} else {
	$sms_url_time="";
}

if(isset($res['sms_to_http']['url_status'])) {
	$sms_url_status=trim($res['sms_to_http']['url_status']);
} else {
	$sms_url_status="";
}

if(isset($res['sms_to_http']['url_user_defined'])) {
	$sms_url_user_defined=trim($res['sms_to_http']['url_user_defined']);
} else {
	$sms_url_user_defined="";
}

//SMS Reports to HTTP

/*
if(isset($res['sms_reports_to_http']['smsreports_url_host'])) {
	$sms_reports_url_host=trim($res['sms_reports_to_http']['smsreports_url_host']);
} else {
	$sms_reports_url_host="";
}

if(isset($res['sms_reports_to_http']['smsreports_url_port'])) {
	$sms_reports_url_port=trim($res['sms_reports_to_http']['smsreports_url_port']);
} else {
	$sms_reports_url_port="";
}

if(isset($res['sms_reports_to_http']['smsreports_url_service'])) {
	$sms_reports_url_service=trim($res['sms_reports_to_http']['smsreports_url_service'], "\t\n\r\0/");
} else {
	$sms_reports_url_service="";
}
*/
$stored_sw = 'checked';
$extra_aql = new aql();
$extra_aql->set('basedir','/etc/asterisk');
$general_res = $extra_aql->query("select * from extra-global.conf");
if(isset($general_res['channels']['processsms'])) {
	$val = trim($general_res['channels']['processsms']);
	if(strcasecmp($val,'yes') == 0) {
		$stored_sw = 'checked';
	}else{
		$stored_sw = '';
	}
}

$receipt_sw = '';
if(isset($general_res['channels']['smsreport'])) {
	$val = trim($general_res['channels']['smsreport']);
	if(strcasecmp($val,'yes') == 0) {
		$receipt_sw = 'checked';
	}else{
		$receipt_sw = '';
	}
}


/* get auto clean conf */


$sms_inbox_autoclean_sw = "";
$sms_inbox_maxsize = "";
$conf_path = '/etc/asterisk/gw';
$conf_file = 'logfile_monitor.conf';
$conf_path_file = $conf_path."/".$conf_file;

$setok = $aql->set('basedir',$conf_path);
if (!$setok) {
	echo $aql->get_error();
}

$hlock=lock_file($conf_path_file);
if(!file_exists($conf_path_file)) {
	fclose(fopen($conf_path_file,"w"));
}else{
	if(!$aql->open_config_file($conf_path_file)){
		echo $aql->get_error();
		unlock_file($hlock);
	}else{
		$res = $aql->query("select * from $conf_file where section='sms_inbox'");
		unlock_file($hlock);
		if(isset($res['sms_inbox']['autoclean_sw'])) {
			if(is_true(trim($res['sms_inbox']['autoclean_sw']))) {
				$sms_inbox_autoclean_sw = "checked";
			}
		}
		if(isset($res['sms_inbox']['maxsize'])) {
			$sms_inbox_maxsize = trim($res['sms_inbox']['maxsize']);
		}
	}
}

$http_url = "http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."/sendsms?username=xxx&password=xxx&phonenumber=xxx&message=xxx&[port=xxx&amp;][report=xxx&][timeout=xxx]";

?>

	<form enctype="multipart/form-data" action="<?php echo get_self() ?>" method="post">
		
	<div id="tab">
		<li class="tb1">&nbsp;</li>
		<li class="tbg">
			<?php echo language('General');?>
		</li>
		<li class="tb2">&nbsp;</li>
	</div>
	<div id="adv_warn">
		<li class="back_warn"></li>
		<li class="back_font">
			<?php echo language('SMS Received warning','Turn on SMS Received switch before you enable SMS Local Stored, SMS to Email or SMS to HTTP!');?>
		</li>
		<li class="">&nbsp;</li>
	</div>

	<table width="100%" class="tedit" >
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('SMS Received');?>:
					<span class="showhelp">
					<?php echo language('Enable help','ON(enabled),OFF(disabled)');?>
					</span>
				</div>
			</th>
			<td>
				<input type="checkbox" id="smsprocess" name="smsprocess" <?php echo $stored_sw ?> />
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('SMS Local Stored');?>:
					<span class="showhelp">
					<?php echo language('Enable help','ON(enabled),OFF(disabled)');?>
					</span>
				</div>
			</th>
			<td>
				<input type="checkbox" class="enable" name="sms_local_store_enable" <?php echo $sms_local_store_enable;?> />
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('SMS Status Report');?>:
					<span class="showhelp">
					<?php echo language('Enable help','ON(enabled),OFF(disabled)');?>
					</span>
				</div>
			</th>
			<td>
				<input type="checkbox" id="smsreport" name="smsreport" <?php echo $receipt_sw ?> />
			</td>
		</tr>
	</table>

	<br>

	<div id="tab">
		<li class="tb1">&nbsp;</li>
		<li class="tbg"><?php echo language('Sender Options');?></li>
		<li class="tb2">&nbsp;</li>
	</div>

	<table width="100%" class="tedit">
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Resend Failed Message');?>:
					<span class="showhelp">
					<?php echo language('Resend Failed Message help','The times that you will attempt to resend your failed message.');?>
					</span>
				</div>
			</th>
			<td >
				<select size=1 name="send_attempt">
					<option value="0" <?php echo $send_attempt[0]?> > 0 </option>
					<option value="1" <?php echo $send_attempt[1]?> > 1 </option>
					<option value="2" <?php echo $send_attempt[2]?> > 2 </option>
					<option value="3" <?php echo $send_attempt[3]?> > 3 </option>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Repeat Same Message');?>:
					<span class="showhelp">
					<?php echo language('Repeat Same Message help','The times that you will resend the same message.');?>
					</span>
				</div>
			</th>
			<td >
				<select size=1 name="send_repeat">
					<option value="1" <?php echo $send_repeat[1]?> > 1 </option>
					<option value="2" <?php echo $send_repeat[2]?> > 2 </option>
					<option value="3" <?php echo $send_repeat[3]?> > 3 </option>
					<option value="4" <?php echo $send_repeat[4]?> > 4 </option>
					<option value="5" <?php echo $send_repeat[5]?> > 5 </option>
				</select>
			</td>
		</tr>
		<tr>
		</tr>
	</table>

	<br>
	
	<div id="tab">
		<li class="tb1">&nbsp;</li>
		<li class="tbg">
			<div class="helptooltips">
				<?php echo language('SMS to Email');?>
				<span class="showhelp">
				<?php echo language('SMS to Email help','This is a tool that makes it available for your email account to transmit the inbox SMS to other email boxes.');?>
				</span>
			</div>
		</li>
		<li class="tb2">&nbsp;</li>
	</div>

	<table width="100%" class="tedit" >
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Enable');?>:
					<span class="showhelp">
					<?php echo language('Enable help','ON(enabled),OFF(disabled)');?>
					</span>
				</div>
			</th>
			<td>
				<input type="checkbox" id="mail_sw" name="mail_sw" <?php echo $mail_sw ?> onchange="mail_change(this.checked)" />
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('SMTP Server');?>:
					<span class="showhelp">
					<?php echo language('SMTP Server help');?>
					</span>
				</div>
			</th>
			<td>
				<select name="smtpserversel" id="smtpserversel" onchange="setSMTPServer(this.value)">
					<option value="other" >OTHER</option>
					<option value="gmail" >GMAIL</option>
					<option value="hotmail" >HOTMAIL</option>
					<option value="yahoo" >YAHOO</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Email Address of Sender');?>:
					<span class="showhelp">
					<?php echo language('Email Address of Sender help','To set the email address of an available email account.<br/> For example, support@openvox.cn');?>
					</span>
				</div>
			</th>
			<td >
				<input type="text" name="sender" id="sender" style="width: 250px;" value="<?php echo $sender;?>" /><span id="csender"></span>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Domain');?>:
					<span class="showhelp">
					<?php echo language('Domain help','To set outgoing mail server. <br/> e.g. mail.openvox.cn');?>
					</span>
				</div>
			</th>
			<td >
				<input type="text" name="smtpserver" id="smtpserver" style="width: 250px;" value="<?php echo $smtpserver;?>" /><span id="csmtpserver"></span>
			  </td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('SMTP Port');?>(<?php echo language('default');?> 25):
					<span class="showhelp">
					<?php echo language('SMTP Port help','To set port number of outgoing mail server. (Default is 25.)');?>
					</span>
				</div>
			</th>
			<td >
				<input type="text" name="smtpport" id="smtpport" style="width: 250px;" value="<?php echo $smtpport;?>" /><span id="csmtpport"></span>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('SMTP User Name');?>:
					<span class="showhelp">
					<?php
						$help = "The login name of your existing email account. <br/>"
							."This option might be different from your email address. <br/>"
							."Some email client doesn't need the email postfix.";
						echo language('SMTP User Name help',$help);
					?>
					</span>
				</div>
			</th>
			<td >
				<input type="text" name="smtpuser" id="smtpuser" style="width: 250px;" value="<?php echo $smtpuser;?>" /><span id="csmtpuser"></span>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('SMTP Password');?>:
					<span class="showhelp">
					<?php echo language('SMTP Password help','The password to login your existing email.');?>
					</span>
				</div>
			</th>
			<td >
				<input type="password" name="smtppwd" id="smtppwd" style="width: 250px;" value="<?php echo $smtppwd;?>" /><span id="csmtppwd"></span>
			</td>
		</tr>

		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('TLS Enable');?>:
					<span class="showhelp">
					<?php echo language('TLS Enable help');?>
					</span>
				</div>
			</th>
			<td >
				<input type="checkbox" name="tls_enable" id="tls_enable" <?php echo $tls_enable;?> />
				<?php echo language('TLS Enable help','This option allows the authentication with certificates.');?>
			</td>
		</tr>

		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Destination Email Address');?> 1:
					<span class="showhelp">
					<?php echo language('Destination Email Address 1 help','The first email address to receive the inbox message.');?>
					</span>
				</div>
			</th>
			<td >
				<input type="text" name="smail1" id="smail1" style="width: 250px;" value="<?php echo $smail1;?>" /><span id="csmail1"></span>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Destination Email Address');?> 2:
					<span class="showhelp">
					<?php echo language('Destination Email Address 2 help','The second email address to receive the inbox message.');?>
					</span>
				</div>
			</th>
			<td >
				<input type="text" name="smail2" id="smail2"  style="width: 250px;" value="<?php echo $smail2;?>" /><span id="csmail2"></span>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Destination Email Address');?> 3:
					<span class="showhelp">
					<?php echo language('Destination Email Address 3 help','The third email address to receive the inbox message.');?>
					</span>
				</div>
			</th>
			<td >
				<input type="text" name="smail3" id="smail3" style="width: 250px;" value="<?php echo $smail3;?>" /><span id="csmail3"></span>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Title');?>:
					<span class="showhelp">
					<?php echo language('Title help','
						Available variable: <br/>
						$PHONENUMBER:SMS sender number. <br/>
						$PORT:SMS from which port.<br/>
						$TIME:SMS received time.<br/>
						$MESSAGE:SMS content.<br/>');
					?>
					</span>
				</div>
			</th>
			<td>
				<input type="text" name="mail_title" id="mail_title" style="width: 500px;" value="<?php echo $mail_title;?>" /><span id="cmail_title"></span>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Content');?>:
					<span class="showhelp">
					<?php echo language('Content help','
						Available variable: <br/>
						$PHONENUMBER:SMS sender number. <br/>
						$PORT:SMS from which port.<br/>
						$TIME:SMS received time.<br/>
						$MESSAGE:SMS content.<br/>');
					?>
					</span>
				</div>
			</th>
			<td>
				<input type="text" name="mail_content" id="mail_content" style="width: 500px;" value="<?php echo $mail_content;?>" /><span id="cmail_content"></span>
			</td>
		</tr>
	</table>

	<br>

	<div id="tab">
		<li class="tb1">&nbsp;</li>
		<li class="tbg">
			<div class="helptooltips">
				<?php echo language('SMS Control');?>
				<span class="showhelp">
				<?php echo language('SMS Control help','
					Allowing endpoints to send some specified KEY WORDS and corresponding PASSWORD to operate the gateway. <br/>
					Message is case-sensitive.');
				?>
				</span>
			</div>
		</li>
		<li class="tb2">&nbsp;</li>
	</div>

	<table width="100%" class="tedit" >
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Enable');?>:
					<span class="showhelp">
					<?php echo language('Enable help','ON(enabled), OFF(disabled)');?>
					</span>
				</div>
			</th>
			<td>
				<input type="checkbox" id="ctl_sw" class="checkbox" name="ctl_sw" <?php echo $ctl_sw ?> onchange="ctl_change(this.checked)"/>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('Password');?>:
					<span class="showhelp">
					<?php echo language('Password help@sms-settings', "The password to confirm that SMS makes the gateway rebooted, shut down, <br/>
								restored configuration files and get info on this gateway.");
					?>
					</span>
				</div>
			</th>
			<td >
				<input type="text" name="ctl_pwd" id="ctl_pwd" style="width: 250px;" value="<?php echo $ctl_pwd;?>" /><span id="cctl_pwd"></span>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('SMS Formats');?>:
					<span class="showhelp">
					<?php echo language('SMS Formats help','
						For example, the message formats: <br/>
						reboot system PASSWORD: To reboot your whole gateway. <br/>
						The PASSWORD is referring to the PASSWORD you set up from option "Password" above.<br/>
						reboot asterisk PASSWORD: To restart your gateway core.<br/>
						restore configs PASSWORD: To reset the configuration files back to the default factory settings. <br/>
						get info PASSWORD: To get your gateway IP address.');
					?>
					</span>
				</div>
			</th>
			<td >
				<br>
				<?php echo language('SMS Formats contents','
					reboot system PASSWORD<br><br>
					reboot asterisk PASSWORD<br><br>
					restore config PASSWORD<br><br>
					get info PASSWORD');
				?>
				<br>
				<br>
			</td>
		</tr>
		<tr>
			<th>
				<div class="helptooltips">
					<?php echo language('SMS Inbox Auto clean');?>:
					<span class="showhelp">
					<?php echo language('SMS Inbox Auto clean help','
						switch on : when the size of the sms inbox record file reaches the max size, <br> 
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; the system will cut a half of the file. New record will be retained.<br>
						switch off : SMS record will remain, and the file size will increase gradually. <br> 
						default on, maxsize=20MB.');
					?>
					</span>
				</div>
			</th>
			<td >
				<table><tr>
					<td style="margin:0px;padding:0px;border:0">
						<input type="checkbox" id="sms_inbox_autoclean_sw" name="sms_inbox_autoclean_sw" <?php echo $sms_inbox_autoclean_sw?> 
							onchange="sms_clean_sw(this.checked)"> 
					</td>
					<td style="border:0">
						<?php echo language('maxsize');?>: 
						<select id="sms_inbox_maxsize" name="sms_inbox_maxsize" <?php if($sms_inbox_autoclean_sw != "checked")echo "disabled" ?>>
							<?php
								$value_array = array("1MB","5MB","10MB","15MB","20MB");
								foreach($value_array as $value){
									$selected = ""; 
									if($sms_inbox_maxsize == $value)
										$selected = "selected";
									echo "<option value=\"$value\" $selected>$value</option>";
								}
							?>
						</select>
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>

	<br>
	
	<div id="tab" style="height:30px;">
		<li class="tb1">&nbsp;</li>
		<li class="tbg"><?php echo language('HTTP to SMS');?></li>
		<li class="tb2">&nbsp;</li>
	</div>
	
	<div width="100%" class="div_tab"  id="div_tab">
		<div class="divc_tab_show">
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('Enable');?>:</div>
					<span class="showhelp">
					<?php echo language('Enable help', "ON(enabled),OFF(disabled)");?>
					</span>
				</div>
			</div>
			<div class="div_tab_td" id="div_tab_td">
				<input type="checkbox" id="http_sw" name="http_sw" <?php echo $http_sw ?> onchange="http_change_gen(this,'http_api_gen')" />
			</div>
		</div>
		<div class="div_tab_hide" id='http_api_gen' style="height:">
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('URL');?>:</div>
					<span class="showhelp">
					<?php 
						echo language('HTTP to SMS URL help','
							The URL for send sms. <br>
							username: the login username for send sms.<br>
							password: the login password for send sms.<br>
							phonenumber: the destination telephone number. <br>
							message: the SMS contents. <br>
							port: the gsm port for send sms. eg gsm-1.1,gsm-1.2. <br>
							report: the sending result report format. <br>
							timeout: how long to wait. <br>');
					?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<div class="div_tab_text">
					<font size="2px" color="#000" ><?php echo language($http_url);?> </font>
				</div>
			</div>
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('User Name');?>:</div>
					<span class="showhelp">
						<?php echo language('User Name help','UserName'); ?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<input id="http_username" type="text" name="http_username" value="<?php echo $http_username;?>" />
				<input type="checkbox" id="http_use_default_user" name="http_use_default_user" <?php echo $http_use_default_user ?> onchange="check_server_user();" />
				<?php echo language("Use default user and password");?>
				<span id="chttp_username"></span>
			</div>
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('Password');?>:</div>
					<span class="showhelp">
						<?php echo language('Password help','Password'); ?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<input id="http_password" type="password" name="http_password" value="<?php echo $http_password;?>" />
				<span id="chttp_password"></span>
			</div>
			<div class="div_tab_th_gsm_list" id="div_tab_th_gsm_list">
				<div class="helptooltips"  style="">
					<div class="div_tab_text"><?php echo language('Port');?>:</div>
					<span class="showhelp">
						<?php echo language('GSM Port help', 'GSM port'); ?>
					</span>
				</div>
			</div>
			<div class="div_tab_td_gsm_list" id="div_tab_td_gsm_list">
				<table cellpadding="0" cellspacing="0" class="port_table" id="port_table" style="width: 100%;">
	<?php
					echo '<tr>';
					$checked = '';
					for($i=1; $i<=$__GSM_SUM__; $i++) {
						$each_port = 'gsm-1.'.$i;
						if($http_port == 'all'){
							$checked = 'checked' ;
						} else{
							if(stristr($http_port,$each_port) != '') {
								$checked = 'checked' ;
							} else {
								$checked = '' ;
							}
						}
						echo "<td><input type=\"checkbox\" id=\"gsm_1_$i\" name=\"checkbox_name[]\" value=\"gsm-1.$i\" $checked  onclick=\"set_check_all()\" >";
						echo get_gsm_name_by_channel($i);
						echo '&nbsp;&nbsp;</td>';
					}
					echo '</tr>';
	
					$cluster_info = get_cluster_info();
					if($cluster_info['mode'] == 'master') {
						for($b=2; $b<=$__BRD_SUM__; $b++) {
							if($cluster_info[$__BRD_HEAD__.$b.'_ip'] != '') {
								echo '<tr>';
								for($i=1; $i<=$__GSM_SUM__; $i++) {
									$each_port = 'gsm-'.$b.'.'.$i;
									if($http_port == 'all'){
										$checked = 'checked' ;
									} else{
										if(stristr($http_port,$each_port) != '') {
											$checked = 'checked' ;
										} else {
											$checked = '' ;
										}
									}
									echo "<td><input type=\"checkbox\"  name=\"checkbox_name[]\" $checked value=\"gsm-$b.$i\" onclick=\"set_check_all()\" >";
									echo get_gsm_name_by_channel($i,$b);
									echo '&nbsp;&nbsp;</td>';
								}
								echo '</tr>';
							}
						}
					}
	?>
					<tr style="border:none;">
						<td style="border:none;">
							<input type="text" id="http_port_select" name="http_port_select" style="display:none" value="<?php echo $http_port; ?>" /> 
							<input type="checkbox" id="checkbox_all" onclick="select_all();"><?php echo language('All');?>
						</td>
					</tr>
				</table>
			</div>
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('Report');?>:</div>
					<span class="showhelp">
						<?php echo language('HTTP to SMS Report help', 'SMS send result Report');?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<select id="http_report" name="http_report" >
					<option  value="json" <?php if($http_report == "json") echo "selected" ?> ><?php echo language('JSON');?></option>
					<option  value="string" <?php if($http_report == "string") echo "selected" ?> ><?php echo language('String');?></option>
					<option  value="no" <?php if($http_report == "no") echo "selected" ?> ><?php echo language('No Report');?></option>
				</select>
			</div>
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('Advanced');?>:</div>
					<span class="showhelp">
					<?php echo language('Advanced help', 'Advanced');?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<input type="checkbox" id="http_api_adv_enable" name="http_api_adv_enable" onchange="http_change_adv(this,'http_api_adv')" />
			</div>
		</div>
		<div class="div_tab_hide" id='http_api_adv' style="height:">
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('Debug');?>:</div>
					<span class="showhelp">
						<?php echo language('Debug help', 'Debug'); ?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<input id="http_debug" type="text" name="http_debug" value="<?php echo $http_debug;?>" />
				<span id="chttp_debug"></span>
			</div>
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('Timeout');?>:</div>
					<span class="showhelp">
						<?php echo language('HTTP to SMS Timeout help', 'HTTP to SMS Timeout Timeout'); ?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<input id="http_timeout_total" type="text" name="http_timeout_total" value="<?php echo $http_timeout_total;?>" />
				&nbsp;<?php echo language('second');?>&nbsp;&nbsp;
				<span id="chttp_timeout_total"></span>
			</div>
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('Wait Timeout');?>:</div>
					<span class="showhelp">
						<?php echo language('Wait Timeout help', 'Wait Timeout'); ?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<input id="http_timeout_wait" type="text" name="http_timeout_wait" value="<?php echo $http_timeout_wait;?>" />
				&nbsp;<?php echo language('second');?>&nbsp;&nbsp;
				<span id="chttp_timeout_wait"></span>
			</div>
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('GSM Send Timeout');?>:</div>
					<span class="showhelp">
						<?php echo language('GSM Send Timeout help', 'GSM Send Timeout'); ?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<input id="http_timeout_gsm_send" type="text" name="http_timeout_gsm_send" value="<?php echo $http_timeout_gsm_send;?>" />
				&nbsp;<?php echo language('second');?>&nbsp;&nbsp;
				<span id="chttp_timeout_gsm_send"></span>
			</div>
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('Socket Timeout');?>:</div>
					<span class="showhelp">
						<?php echo language('Socket Timeout help', 'Socket Timeout'); ?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<input id="http_timeout_socket" type="text" name="http_timeout_socket" value="<?php echo $http_timeout_socket;?>" />
				&nbsp;<?php echo language('second');?>&nbsp;&nbsp;
				<span id="chttp_timeout_socket"></span>
			</div>
		</div>
	</div>
	
	<br>

	
	<div id="tab" style="height:30px;">
		<li class="tb1">&nbsp;</li>
		<li class="tbg"><?php echo language('SMS to HTTP');?></li>
		<li class="tb2">&nbsp;</li>
	</div>
	
	<div width="100%" class="div_tab" id="div_tab">
		<div class="divc_tab_show">
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('Enable');?>:</div>
					<span class="showhelp">
					<?php echo language('Enable help', "ON(enabled),OFF(disabled)");?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
				<input type="checkbox" id="sms_sw" name="sms_sw" <?php echo $sms_sw ?> onchange="sms_to_http_change('div_tab_sms_hide')" />
			</div>
		</div>
		<div class="div_tab_sms_hide" id="sms_reports_gen" style="height:">
			<div class="div_tab_th">
				<div class="helptooltips">
					<div class="div_tab_text"><?php echo language('Enable SMS Reports to HTTP');?>:</div>
					<span class="showhelp">
					<?php echo language('Enable help', "ON(enabled),OFF(disabled)");?>
					</span>
				</div>
			</div>
		
			<div class="div_tab_td"  id="div_tab_td">
				<input type="checkbox" id="sms_reports_sw" name="sms_reports_sw" <?php echo $sms_reports_sw ?> />
			</div>
		</div>
		<div class="div_tab_sms_hide" id="sms_api_gen" style="height:">
			<div class="div_tab_th" style="">
				<div class="helptooltips">
					<div class="div_tab_text" style=""><?php echo language('URL');?>:</div>
					<span class="showhelp">
						<?php echo language('SMS to HTTP URL help', 'The SMS receive HTTP URL'); ?>
					</span>
				</div>
			</div>
			<div class="div_tab_td"  id="div_tab_td">
					http://&nbsp;
				<input id="sms_url_host" type="text" name="sms_url_host" value="<?php echo $sms_url_host; ?>" style="width:100px;" 
					onfocus="_onfocus(this,'host')" onblur="_onblur(this,'host')" />
					&nbsp;:&nbsp;
				<input id="sms_url_port" type="text" name="sms_url_port" value="<?php echo $sms_url_port; ?>" style="width:20px;" 
					onfocus="_onfocus(this,'port')" onblur="_onblur(this,'port')" />
					&nbsp;/&nbsp;
				<input id="sms_url_path" type="text" name="sms_url_path" value="<?php echo $sms_url_path; ?>" style="width:100px;" 
					onfocus="_onfocus(this,'path')" onblur="_onblur(this,'path')" />
					&nbsp;?&nbsp;
				<input id="sms_url_from_num" type="text" name="sms_url_from_num" value="<?php echo $sms_url_from_num; ?>" style="width:40px;" 
					onfocus="_onfocus(this,'key')" onblur="_onblur(this,'key')" />
					=phonenumber&nbsp;&amp;
				<input id="sms_url_to_num" type="text" name="sms_url_to_num" value="<?php echo $sms_url_to_num; ?>" style="width:40px;" 
					onfocus="_onfocus(this,'key')" onblur="_onblur(this,'key')" />
					=port&nbsp;&amp;
				<input id="sms_url_message" type="text" name="sms_url_message" value="<?php echo $sms_url_message; ?>" style="width:40px;" 
					onfocus="_onfocus(this,'key')" onblur="_onblur(this,'key')" />
					=message&nbsp;&amp;
				<input id="sms_url_time" type="text" name="sms_url_time" value="<?php echo $sms_url_time; ?>" style="width:40px;" 
					onfocus="_onfocus(this,'key')" onblur="_onblur(this,'key')" />
					=time&nbsp;&amp;
				<input id="sms_url_status" type="text" name="sms_url_status" value="<?php echo $sms_url_status; ?>" style="width:40px;" 
					onfocus="_onfocus(this,'key')" onblur="_onblur(this,'key')" />
					=status&nbsp;&amp;
				<input id="sms_url_user_defined" type="text" name="sms_url_user_defined" value="<?php echo $sms_url_user_defined; ?>" style="width:40px;" 
					onfocus="_onfocus(this,'User Defined')" onblur="_onblur(this,'User Defined')" />
				<span id="csms_url"></span>
			</div>
		</div>
	</div>
	
	<br>

	<input type="hidden" name="send" id="send" value="" />
	<input type="submit" class="float_btn gen_short_btn"  value="<?php echo language('Save');?>" onclick="document.getElementById('send').value='Save';return check();" />
	<table id="float_btn2" style="border:none;" class="float_btn2">
		<tr id="float_btn_tr2" class="float_btn_tr2">
			<td width="20px">
				<input type="submit" id="float_button_1" class="float_short_button" value="<?php echo language('Save');?>" onclick="document.getElementById('send').value='Save';return check();" />
			</td>
		</tr>
	</table>
	</form>

<script type="text/javascript">
function _onfocus(obj,str)
{
	if (obj.value == str) {
		obj.value =''
		obj.style.color = '#000000';
	}
}

function _onblur(obj,str)
{
	if (trim(obj.value) =='') {
		obj.value = str;
		obj.style.color = '#aaaaaa';
	}
}

function check_url_help()
{
	var sms_url_host = document.getElementById("sms_url_host");
	var sms_url_port = document.getElementById("sms_url_port");
	var sms_url_path = document.getElementById("sms_url_path");
	var sms_url_from_num = document.getElementById("sms_url_from_num");
	var sms_url_to_num = document.getElementById("sms_url_to_num");
	var sms_url_message = document.getElementById("sms_url_message");
	var sms_url_time = document.getElementById("sms_url_time");
	var sms_url_user_defined = document.getElementById("sms_url_user_defined");
	if(trim(sms_url_host.value) == ''){
		sms_url_host.value = 'host';
		sms_url_host.style.color = '#aaaaaa';
	}
	if(trim(sms_url_port.value) == ''){
		sms_url_port.value = 'port';
		sms_url_port.style.color = '#aaaaaa';
	}
	if(trim(sms_url_path.value) == ''){
		sms_url_path.value = 'path';
		sms_url_path.style.color = '#aaaaaa';
	}
	if(trim(sms_url_from_num.value) == ''){
		sms_url_from_num.value = 'key';
		sms_url_from_num.style.color = '#aaaaaa';
	}
	if(trim(sms_url_to_num.value) == ''){
		sms_url_to_num.value = 'key';
		sms_url_to_num.style.color = '#aaaaaa';
	}
	if(trim(sms_url_message.value) == ''){
		sms_url_message.value = 'key';
		sms_url_message.style.color = '#aaaaaa';
	}
	if(trim(sms_url_time.value) == ''){
		sms_url_time.value = 'key';
		sms_url_time.style.color = '#aaaaaa';
	}
	if(trim(sms_url_user_defined.value) == ''){
		sms_url_user_defined.value = 'User Defined';
		sms_url_user_defined.style.color = '#aaaaaa';
	}
}

function onload_func()
{
<?php
	if($ctl_sw != '') {
		echo "ctl_change(true);\n";
	} else {
		echo "ctl_change(false);\n";
	}

	if($mail_sw != '') {
		echo "mail_change(true);\n";
	} else {
		echo "mail_change(false);\n";
	}
?>

};

function check_server_user()
{
	var server_user_sw = document.getElementById('http_use_default_user').checked;
	if(!server_user_sw){
		$('#http_username').removeAttr("disabled");
		$('#http_password').removeAttr("disabled");
	} else {
		$('#http_username').attr("disabled","true");
		$('#http_password').attr("disabled","true");
	}
};

var select_all_flag = false;
function select_all()
{
	var inputs = document.getElementsByTagName("input");     
	for(var i=0;i<inputs.length;i++){
		if(inputs[i].getAttribute("name") == "checkbox_name[]"){     
			if(select_all_flag == false)
				inputs[i].checked = true;
			else
				inputs[i].checked = false;
		}
	}
	if(select_all_flag == false)
		select_all_flag = true;
	else
		select_all_flag = false;	
	set_check_all();
};
function set_check_all_value(check_obj)
{
	var target_obj = document.getElementById("http_port_select"); 
	if(target_obj.value == ''){
		target_obj.value = check_obj.value;
	} else {
		target_obj.value += "," + check_obj.value; 
	}
};
function remove_check_all_value(check_obj)
{
	var target_obj = document.getElementById("http_port_select"); 
	if(target_obj.value.indexOf("," + check_obj.value + ",") != -1){ 
		target_obj.value = target_obj.value.replace("," + check_obj.value,''); 
	}else if(target_obj.value.indexOf("," + check_obj.value) != -1){ 
		 target_obj.value = target_obj.value.replace("," + check_obj.value,''); 
	}else if(target_obj.value.indexOf(check_obj.value + ",") != -1){ 
		target_obj.value = target_obj.value.replace(check_obj.value + ",",''); 
	}else if(target_obj.value.indexOf(check_obj.value) != -1){ 
		target_obj.value = target_obj.value.replace(check_obj.value,''); 
	}
};
function set_check_all()
{
	var target_obj = document.getElementById("http_port_select"); 
	target_obj.value = '';
	var inputs = document.getElementsByTagName("input");
	for(var i=0;i<inputs.length;i++){
		if( (inputs[i].getAttribute("type")=="checkbox" || inputs[i].getAttribute("type")=="Checkbox") && inputs[i].getAttribute("name")=="checkbox_name[]"){
			if(inputs[i].checked){
				set_check_all_value(inputs[i]);
			} else {
				remove_check_all_value(inputs[i]);
			}
		}
	}
};
function http_sms(){
	if($('#http_sw').attr("checked")=="checked")$("#http_api_gen").show();
	if($('#sms_sw').attr("checked")== "checked") {
		$("#sms_api_gen").show();
		$("#sms_reports_gen").show();
	} else {
		$("#sms_api_gen").hide();
		$("#sms_reports_gen").hide();
	}
	//if($('#sms_reports_sw').attr("checked")=="checked")$("#sms_report_api").show();
	var div_tab_td_gsm_list = document.getElementById("div_tab_td_gsm_list").offsetHeight;
	$("#div_tab_th_gsm_list").css("height",div_tab_td_gsm_list);
};
$(document).ready(function (){ 
	$("#mail_sw").iButton();
	$("#ctl_sw").iButton();
	$("#sms_inbox_autoclean_sw").iButton();
	$("#smsreport").iButton();
	$("#smsprocess").iButton();
	$("#http_sw").iButton();
	$("#http_api_adv_enable").iButton();
	$("#sms_sw").iButton();
	$("#sms_reports_sw").iButton();
	$(".enable").iButton();
	onload_func();
	http_sms();
	check_server_user();
	check_url_help();
});
</script>

<?php require("/www/cgi-bin/inc/boot.inc");?>
<div id="float_btn1" class="float_btn1 sec_float_btn1">
</div>
<div  class="float_close" onclick="close_btn()">
</div>
