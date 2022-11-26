<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$from = protect($_GET['from']);
$to = protect($_GET['to']);
if(empty($from) or empty($to)) {
    header("Location: $settings[url]");
}
if(empty(gatewayinfo($from,"name")) or empty(gatewayinfo($to,"name"))) {
    header("Location: $settings[url]");
}
$tpl = new Template("app/templates/".$settings['default_template']."/exchange.html",$lang);
$tpl->set("url",$settings['url']);
$tpl->set("name",$settings['name']);
$results = '';
$rate = get_rates($from,$to);
$amount_send='0';
$amount_receive='0';
if($_SESSION['ce_ex_amount']) {
    $amount_send = $_SESSION['ce_ex_amount'];
    $amount_receive = ce_ExCalculator($from,$to,$amount_send);
}
if($amount_send == "0") {
    $amount_send = $rate['rate_from'];
    $amount_receive = $rate['rate_to'];
}
$gateway_send = $from;
$gateway_receive = $to;
$gtname_send = str_ireplace(" ","-",gatewayinfo($gateway_send,"name"));
$gtname_send = $gtname_send.'_'.gatewayinfo($gateway_send,"currency");
$gtname_receive = str_ireplace(" ","-",gatewayinfo($gateway_receive,"name"));
$gtname_receive = $gtname_receive.'_'.gatewayinfo($gateway_receive,"currency");
$_SESSION['ce_redirect'] = $settings['url']."exchange/".$gateway_send."_".$gateway_receive."/".$gtname_send."-to-".$gtname_receive;
$tpl->set("amount_send",$amount_send);
$tpl->set("amount_receive",$amount_receive);
$tpl->set("gateway_send_id",$from);
if(gatewayinfo($from,"merchant_source") == "stripe" or gatewayinfo($from,"merchant_source") == "2checkout") {
    $tpl->set("gateway_send","VISA/MasterCard");
} else {
    $tpl->set("gateway_send",gatewayinfo($from,"name"));
}
$tpl->set("gateway_send_currency",gatewayinfo($from,"currency"));
$tpl->set("gateway_send_icon",gticon($from));
if(gatewayinfo($to,"merchant_source") == "stripe" or gatewayinfo($to,"merchant_source") == "2checkout") {
    $tpl->set("gateway_receive","VISA/MasterCard");
} else {
    $tpl->set("gateway_receive",gatewayinfo($to,"name"));
}
$tpl->set("gateway_receive_id",$to);
$tpl->set("gateway_receive_currency",gatewayinfo($to,"currency"));
$tpl->set("gateway_receive_icon",gticon($to));
$tpl->set("min_amount",gatewayinfo($from,"min_amount"));
$tpl->set("max_amount",gatewayinfo($from,"max_amount"));
$tpl->set("reserve",gatewayinfo($to,"reserve"));
$tpl->set("rate_from",$rate['rate_from']);
$tpl->set("rate_to",$rate['rate_to']);
$tpl->set("currency_from",$rate['currency_from']);
$tpl->set("currency_to",$rate['currency_to']);
$tpl->set("sic1",gatewayinfo($from,"is_crypto"));
$tpl->set("sic2",gatewayinfo($to,"is_crypto"));
$ufields = '';
if(gatewayinfo($to,"manual_payment") == "1") {
    $CheckFields = $db->query("SELECT * FROM ce_gateways_fields WHERE gateway_id='$to' ORDER BY id");
    if($CheckFields->num_rows>0) {
        $i=2;
        while($cf = $CheckFields->fetch_assoc()) {
            $finame = 'u_field_'.$i;
            $ftpl = new Template("app/templates/".$settings['default_template']."/rows/ufield.html",$lang);
            $ftpl->set("f_title",$cf['field_name']);
            $ftpl->set("f_name",$finame);
            $ufields .= $ftpl->output();
            $i++;
        }
    } else {
        $finame = 'u_field_2';
        if(gatewayinfo($to,"is_crypto")) {
            $fititle = 'Your '.gatewayinfo($to,"name").' address';
        } else {
            $fititle = 'Your '.gatewayinfo($to,"name").' account';
        }
        $ftpl = new Template("app/templates/".$settings['default_template']."/rows/ufield.html",$lang);
        $ftpl->set("f_title",$fititle);
        $ftpl->set("f_name",$finame);
        $ufields .= $ftpl->output();
    }
} elseif(gatewayinfo($to,"external_gateway") == "1") {
    $CheckFields = $db->query("SELECT * FROM ce_gateways_fields WHERE gateway_id='$to' ORDER BY id");
    if($CheckFields->num_rows>0) {
        $i=2;
        while($cf = $CheckFields->fetch_assoc()) {
            $finame = 'u_field_'.$i;
            $ftpl = new Template("app/templates/".$settings['default_template']."/rows/ufield.html",$lang);
            $ftpl->set("f_title",$cf['name']);
            $ftpl->set("f_name",$finame);
            $ufields .= $ftpl->output();
            $i++;
        }
    } else {
        $finame = 'u_field_2';
        if(gatewayinfo($to,"is_crypto")) {
            $lang = str_ireplace("%gateway%",gatewayinfo($to,"name"),$lang['your_address']);
            $fititle = $lang;
        } else {
            $lang = str_ireplace("%gateway%",gatewayinfo($to,"name"),$lang['your_account']);
        }
        $ftpl = new Template("app/templates/".$settings['default_template']."/rows/ufield.html",$lang);
        $ftpl->set("f_title",$fititle);
        $ftpl->set("f_name",$finame);
        $ufields .= $ftpl->output();
    }
} else {
    $CheckFields = $db->query("SELECT * FROM ce_gateways_fields WHERE gateway_id='$to' ORDER BY id");
    if($CheckFields->num_rows>0) {
        $i=2;
        while($cf = $CheckFields->fetch_assoc()) {
            $finame = 'u_field_'.$i;
            $ftpl = new Template("app/templates/".$settings['default_template']."/rows/ufield.html",$lang);
            $ftpl->set("f_title",$cf['name']);
            $ftpl->set("f_name",$finame);
            $ufields .= $ftpl->output();
            $i++;
        }
    } else {
        $finame = 'u_field_2';
        if(gatewayinfo($to,"is_crypto")) {
            $fititle = 'Your '.gatewayinfo($to,"name").' address';
        } else {
            $fititle = 'Your '.gatewayinfo($to,"name").' account';
        }
        $ftpl = new Template("app/templates/".$settings['default_template']."/rows/ufield.html",$lang);
        $ftpl->set("f_title",$fititle);
        $ftpl->set("f_name",$finame);
        $ufields .= $ftpl->output();
    }
}
$tpl->set("ufields",$ufields);
$CEAction = protect($_POST['ce_btn']);
if(isset($CEAction) && $CEAction == "exchange") {
    $gateway_send = $from;
    $gateway_receive = $to;
    $amount_send = protect($_POST['ce_amount_send']);
    $amount_receive = ce_ExCalculator($from,$to,$amount_send);
    $rate_from = $rate['rate_from'];
    $rate_to = $rate['rate_to'];
    $currency_from = $rate['currency_from'];
    $currency_to = $rate['currency_to'];
    if(checkSession()) { $uid = $_SESSION['ce_uid']; } else { $uid = '0'; }
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = time(); 
    $u_field_1 = protect($_POST['u_field_1']);
    $u_field_2 = protect($_POST['u_field_2']);
    $u_field_3 = protect($_POST['u_field_3']);
    $u_field_4 = protect($_POST['u_field_4']);
    $u_field_5 = protect($_POST['u_field_5']);
    $u_field_6 = protect($_POST['u_field_6']);
    $u_field_7 = protect($_POST['u_field_7']);
    $u_field_8 = protect($_POST['u_field_8']);
    $u_field_9 = protect($_POST['u_field_9']);
    $u_field_10 = protect($_POST['u_field_10']);
    $recaptcha_response = protect($_POST['g-recaptcha-response']);
    if(empty($gateway_send) or empty($gateway_receive) or empty($amount_send) or empty($amount_receive) or empty($rate_from) or empty($rate_to) or empty($currency_from) or empty($currency_to) or empty($u_field_1) or empty($u_field_2)) {
        $results = error($lang['error_1']);
    } elseif(!is_numeric($amount_send)) {
        $results = error($lang['error_24']);
    } elseif(!isValidEmail($u_field_1)) {
        $results = error($lang['error_23']);
    } elseif(gatewayinfo($from,"require_login") == "1" && !checkSession()) {
        $results = error($lang['error_25']);
    } elseif(gatewayinfo($from,"require_email_verify") == "1" && idinfo($_SESSION['ce_uid'],"email_verified") == "0") {
        $results = error($lang['error_26']);
    } elseif(gatewayinfo($from,"require_document_verify") == "1" && idinfo($_SESSION['ce_uid'],"document_verified") == "0") {
        $results = error($lang['error_28']);
    } elseif(gatewayinfo($from,"min_amount") > $amount_send) {
        $min_amount = gatewayinfo($from,"min_amount");
        $error = str_ireplace("%amount%",$min_amount,$lang['error_29']);
        $error = str_ireplace("%currency%",$currency_from,$error);
        $results = error($error);
    } elseif(gatewayinfo($from,"max_amount") < $amount_send) {
        $max_amount = gatewayinfo($from,"max_amount");
        $error = str_ireplace("%amount%",$max_amount,$lang['error_30']);
        $error = str_ireplace("%currency%",$currency_from,$error);
        $results = error($error);
    } elseif(gatewayinfo($to,"reserve") < $amount_receive) {
        $reserve = gatewayinfo($to,"reserve");
        $error = str_ireplace("%amount%",$reserve,$lang['error_31']);
        $error = str_ireplace("%currency%",$currency_to,$error);
        $results = error($error);
    } elseif(isset($_POST['p1']) && $_POST['p1'] !== "ok") {
        $results = error($lang['error_32']);
    } elseif($settings['enable_recaptcha'] == "1" && !VerifyGoogleRecaptcha($recaptcha_response)) {
        $results = error($lang['error_58']);  
    } else {
        $order_hash = randomHash(50);
        $refid = 0;
        if(isset($_SESSION['ce_refid'])) {
            $refid = $_SESSION['ce_refid'];
        }
        $create = $db->query("INSERT ce_orders (uid,gateway_send,gateway_receive,amount_send,amount_receive,rate_from,rate_to,currency_from,currency_to,u_field_1,u_field_2,u_field_3,u_field_4,u_field_5,u_field_6,u_field_7,u_field_8,u_field_9,u_field_10,ip,status,created,updated,expired,order_hash,refereer,refereer_comission,refereer_comission_currency,refereer_set) VALUES ('$uid','$gateway_send','$gateway_receive','$amount_send','$amount_receive','$rate_from','$rate_to','$currency_from','$currency_to','$u_field_1','$u_field_2','$u_field_3','$u_field_4','$u_field_5','$u_field_6','$u_field_7','$u_field_8','$u_field_9','$u_field_10','$ip','1','$time','0','0','$order_hash','$refid','0','','0')");
        $GetLast = $db->query("SELECT * FROM ce_orders WHERE u_field_1='$u_field_1' ORDER BY id DESC LIMIT 1");
        $get = $GetLast->fetch_assoc();
        CE_Send_CreateOrder($u_field_1,$get['id']);
        CE_Send_NewOrderToAdmin($get['id']);
        $redirect = $settings['url']."order/".$order_hash;
        header("Location: $redirect");
    }
}
$exchange_rules = '';
$CheckRules = $db->query("SELECT * FROM ce_gateways_rules WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
if($CheckRules->num_rows>0) {
    $r = $CheckRules->fetch_assoc();
    $rtpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_rules.html",$lang);
    $gt_send = gatewayinfo($gateway_send,"name")." ".gatewayinfo($gateway_send,"currency");
    $gt_receive = gatewayinfo($gateway_receive,"name")." ".gatewayinfo($gateway_receive,"currency");
    $rtpl->set("gateway_send",$gt_send);
    $rtpl->set("gateway_receive",$gt_receive);
    $rtpl->set("rules",$r['exchange_rules']);
    $exchange_rules= $rtpl->output();
}
$tpl->set("exchange_rules",$exchange_rules);
$UserMenu = '';
if(checkSession()) {
    $umtpl = new Template("app/templates/".$settings['default_template']."/rows/e_user_logged.html",$lang);
    $umtpl->set("url",$settings['url']);
    $UserMenu= $umtpl->output();
} else {
    $umtpl = new Template("app/templates/".$settings['default_template']."/rows/e_user_notlogged.html",$lang);
    $umtpl->set("url",$settings['url']);
    $UserMenu= $umtpl->output();
}
$tpl->set("UserMenu",$UserMenu);
$tpl->set("results",$results);
$au_rate_int = 60 * $settings['au_rate_int'];
$au_rate_int = $au_rate_int * 1000;
$autoupdateinterval = $au_rate_int;
$tpl->set("autoupdateinterval",$autoupdateinterval);
$gateway_name = str_ireplace(" ","_",gatewayinfo($gateway_receive,'name'));
$request_link = $settings['url']."reserve_request/".$gateway_receive."/".$gateway_name;
$tpl->set("request_link",$request_link);
$insert_recaptcha = '';
if($settings['enable_recaptcha'] == "1") {
    $captcha_tpl = new Template("app/templates/".$settings['default_template']."/rows/recaptcha.html",$lang);
    $captcha_tpl->set("recaptcha_publickey",$settings['recaptcha_publickey']);
    $insert_recaptcha = $captcha_tpl->output();   
}
$tpl->set("insert_recaptcha",$insert_recaptcha);
echo $tpl->output();
?>