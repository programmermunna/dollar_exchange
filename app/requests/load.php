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
if($a == "receive_list") {
    $id = protect($_GET['id']);
    $query = $db->query("SELECT * FROM ce_gateways_directions WHERE gateway_id='$id'");
    $receive_list = '';
    if($query->num_rows>0) {
        $row = $query->fetch_assoc();
        $directions = explode(",",$row['directions']);
        foreach($directions as $k=>$v) {
            $receive_list .= '<option value="'.$v.'">'.gatewayinfo($v,"name").' '.gatewayinfo($v,"currency").'</option>';
        }
        $data['status'] = 'success';
        $data['content'] = $receive_list;
    } else {
        $data['status'] = 'error';
        $data['msg'] = 'Error loading gateway directions.';
    }
} elseif($a == "rate") {
    $from = protect($_GET['from']);
    $to = protect($_GET['to']);
    $rate = get_rates($from,$to);
    if($rate['status'] == "success") {  
        $data['status'] = 'success';
        $data['rate_from'] = $rate['rate_from'];
        $data['rate_to'] = $rate['rate_to'];
        $data['currency_from'] = $rate['currency_from'];
        $data['currency_to'] = $rate['currency_to'];
        $data['reserve'] = gatewayinfo($to,"reserve");
        $data['sic1'] = gatewayinfo($from,"is_crypto");
        $data['sic2'] = gatewayinfo($to,"is_crypto");
    } else {
        $data['status'] = 'error';
        $data['msg'] = 'Error loading exchange rate.';
    }
} elseif($a == "img") {
    $id = protect($_GET['id']);
    $icon = gticon($id); 
    if($icon) {
        $data['status'] = 'success';
        $data['content'] = $icon;
    } else {
        $data['status'] = 'error';
        $data['msg'] = 'Error loading gateway icon';
    }
} else {
    $data['status'] = 'error';
    $data['msg'] = 'Error getting information';
}
echo json_encode($data);
?>