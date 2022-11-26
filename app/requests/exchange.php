<?php
header('Content-Type: application/json');
define('CryptExchanger_INSTALLED',TRUE);
ob_start();
session_start();
error_reporting(0);
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");
include(getLanguage($settings['url'],null,2));
$data = array();
$a = protect($_GET['a']);
if($a == "prepare") {
    $gateway_send = protect($_POST['ce_gateway_send']);
    $gateway_receive = protect($_POST['ce_gateway_receive']);
    $amount_send = protect($_POST['ce_amount_send']);
    $amount_receive = protect($_POST['ce_amount_receive2']);
    $rate_from = protect($_POST['ce_rate_from']);
    $rate_to = protect($_POST['ce_rate_to']);
    $currency_from = protect($_POST['ce_currency_from']);
    $currency_to = protect($_POST['ce_currency_to']);
    $reserve = protect($_POST['ce_reserve']);
    $sic1 = protect($_POST['ce_sic1']);
    $sic2 = protect($_POST['ce_sic2']);
    if(empty($gateway_send) or empty($gateway_receive) or empty($amount_send) or empty($amount_receive) or empty($rate_from) or empty($rate_to) or empty($currency_from) or empty($currency_to)) {
        $data['status'] = 'error';
        $data['msg'] = $lang['error_53'];
    } elseif(!is_numeric($amount_send) or !is_numeric($amount_receive) or !is_numeric($rate_from) or !is_numeric($rate_to)) {
        $data['status'] = 'error';
        $data['msg'] = $lang['error_54'];
    } elseif(gatewayinfo($gateway_send,"min_amount") > $amount_send) {
        $data['status'] = 'error';
        $amount_error = gatewayinfo($gateway_send,"min_amount").' '.gatewayinfo($gateway_send,"currency");
        $error = str_ireplace("%amount%",$amount_error,$lang['error_55']);
        $data['msg'] = $error;
    } elseif(gatewayinfo($gateway_send,"max_amount") < $amount_send) {
        $data['status'] = 'error';
        $amount_error = gatewayinfo($gateway_send,"max_amount").' '.gatewayinfo($gateway_send,"currency");
        $error = str_ireplace("%amount%",$amount_error,$lang['error_56']);
        $data['msg'] = $error;
    } elseif(gatewayinfo($gateway_receive,"reserve") < $amount_receive) {
        $data['status'] = 'error';
        $gateway_name = str_ireplace(" ","_",gatewayinfo($gateway_receive,'name'));
        $link = $settings['url']."reserve_request/".$gateway_receive."/".$gateway_name;
        $data['msg'] = $lang[error_57].' <a href="'.$link.'">'.$lang[error_57_1].'</a> '.$lang[error_57_2];
    } else {
        $gtname_send = str_ireplace(" ","-",gatewayinfo($gateway_send,"name"));
        $gtname_send = $gtname_send.'_'.gatewayinfo($gateway_send,"currency");
        $gtname_receive = str_ireplace(" ","-",gatewayinfo($gateway_receive,"name"));
        $gtname_receive = $gtname_receive.'_'.gatewayinfo($gateway_receive,"currency");
        $_SESSION['ce_ex_amount'] = $amount_send;
        $redirect = $settings['url']."exchange/".$gateway_send."_".$gateway_receive."/".$gtname_send."-to-".$gtname_receive;
        $data['status'] = 'success'; 
        $data['redirect'] = $redirect;
    }
} else {
    $data['status'] = 'error';
    $data['msg'] = 'Error getting information';
}
echo json_encode($data);
?>