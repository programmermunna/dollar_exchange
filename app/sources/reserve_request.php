<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$gateway = protect($_GET['id']);
if(empty(gatewayinfo($gateway,"name"))) {
    header("Location: $settings[url]");
}
$tpl = new Template("app/templates/".$settings['default_template']."/reserve_request.html",$lang);
$tpl->set("url",$settings['url']);
$tpl->set("name",$settings['name']);
$tpl->set("gateway_name",gatewayinfo($gateway,"name"));
$tpl->set("gateway_currency",gatewayinfo($gateway,"currency"));
$tpl->set("gateway_icon",gticon($gateway));
$results = '';
$CEAction = protect($_POST['ce_btn']);
if(isset($CEAction) && $CEAction == "request") {
    $email = protect($_POST['email']);
    $amount = protect($_POST['amount']);
    if(empty($email)) {
        $results = error($lang['error_39']);
    } elseif(!isValidEmail($email)) {
        $results = error($lang['error_23']);
    } elseif(empty($amount)) {
        $results = error($lang['error_51']);
    } elseif(!is_numeric($amount)) {
        $results = error($lang['error_52']);
    } else {
        $time = time();
        $insert = $db->query("INSERT ce_reserve_requests (gateway_id,email,amount,requested_on,updated_on,updated_by) VALUES ('$gateway','$email','$amount','$time','0','0')");
        $results = success($lang['success_15']);
    }
}
$tpl->set("results",$results);
echo $tpl->output();
?> 