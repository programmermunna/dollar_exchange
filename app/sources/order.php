<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$id = protect($_GET['id']);
$query = $db->query("SELECT * FROM ce_orders WHERE order_hash='$id'");
if($query->num_rows==0) {
    header("Location: $settings[url]");
}
$row = $query->fetch_assoc();
$tpl = new Template("app/templates/".$settings['default_template']."/order.html",$lang);
$tpl->set("url",$settings['url']);
$tpl->set("name",$settings['name']);
$tpl->set("order_id",$row['id']);
$_SESSION['ce_order_hash'] = $row['order_hash'];
$_SESSION['ce_order_id'] = $row['id'];
if(gatewayinfo($row['gateway_send'],"merchant_source") == "stripe" or gatewayinfo($row['gateway_send'],"merchant_source") == "2checkout") {
    $tpl->set("gateway_send","VISA/MasterCard");
} else {
    $tpl->set("gateway_send",gatewayinfo($row['gateway_send'],"name"));
}
$tpl->set("gateway_send_currency",gatewayinfo($row['gateway_send'],"currency"));
$tpl->set("gateway_send_icon",gticon($row['gateway_send']));
$tpl->set("amount_send",$row['amount_send']);
if(gatewayinfo($row['gateway_receive'],"merchant_source") == "stripe" or gatewayinfo($row['gateway_receive'],"merchant_source") == "2checkout") {
    $tpl->set("gateway_receive","VISA/MasterCard");
} else {
    $tpl->set("gateway_receive",gatewayinfo($row['gateway_receive'],"name"));
}
$tpl->set("gateway_receive_currency",gatewayinfo($row['gateway_receive'],"currency"));
$tpl->set("gateway_receive_icon",gticon($row['gateway_receive']));
$tpl->set("amount_receive",$row['amount_receive']);
$ce_form = protect($_POST['ce_form']);
if($ce_form == "pay") {
    $redirect = $settings['url']."pay/".$row['order_hash'];
    header("Location: $redirect");
} 
if($ce_form == "cancel") {
    $time = time();
    $update = $db->query("UPDATE ce_orders SET status='5',updated='$time' WHERE id='$row[id]'");
    $redirect = $settings['url']."order/".$row['order_hash']; 
    header("Location: $redirect");
}
$pay_btn = '';
$cancel_btn = '';
if($row['status'] == "1") {
    $pbtpl = new Template("app/templates/".$settings['default_template']."/rows/pay_btn.html",$lang);
    $pay_btn = $pbtpl->output();
    $cbtpl = new Template("app/templates/".$settings['default_template']."/rows/cancel_btn.html",$lang);
    $cancel_btn = $cbtpl->output();
}
$tpl->set("pay_btn",$pay_btn);
$tpl->set("cancel_btn",$cancel_btn);
$status = ce_decodeStatus($row['status']);
$tpl->set("status_class",$status['style']);
$tpl->set("status_text",$status['text']);
$tpl->set("u_email",$row['u_field_1']);
$tpl->set("rate_from",$row['rate_from']);
$tpl->set("rate_to",$row['rate_to']);
$tpl->set("created_date",date("d/n/Y h:ia",$row['created']));
$order_rows = '';
if(gatewayinfo($row['gateway_receive'],"manual_payment") == "1"  or gatewayinfo($row['gateway_receive'],"external_gateway") == "1") {
    $CheckFields = $db->query("SELECT * FROM ce_gateways_fields WHERE gateway_id='$row[gateway_receive]' ORDER BY id");
    if($CheckFields->num_rows>0) {
        $i=2;
        while($cf = $CheckFields->fetch_assoc()) {
            $ortpl = new Template("app/templates/".$settings['default_template']."/rows/order_row.html",$lang);
            $ortpl->set("field_name",$cf['field_name']);
            $u_field = 'u_field_'.$i;
            $ortpl->set("field_value",$row[$u_field]);
            $order_rows .= $ortpl->output();
            $i++;
        }
    } else {
        if(gatewayinfo($row['gateway_receive'],"is_crypto") == "1") {
            $ortpl = new Template("app/templates/".$settings['default_template']."/rows/order_row.html",$lang);
            $field_name = 'To '.gatewayinfo($row[gateway_receive],"name").' address';
            $ortpl->set("field_name",$field_name);
            $ortpl->set("field_value",$row['u_field_2']);
            $order_rows = $ortpl->output();
        } else {
            $ortpl = new Template("app/templates/".$settings['default_template']."/rows/order_row.html",$lang);
            $field_name = 'To '.gatewayinfo($row[gateway_receive],"name").' account';
            $ortpl->set("field_name",$field_name);
            $ortpl->set("field_value",$row['u_field_2']);
            $order_rows = $ortpl->output();
        }   
    }
} else {
    if(gatewayinfo($row['gateway_receive'],"is_crypto") == "1") {
        $ortpl = new Template("app/templates/".$settings['default_template']."/rows/order_row.html",$lang);
        $field_name = 'To '.gatewayinfo($row[gateway_receive],"name").' address';
        $ortpl->set("field_name",$field_name);
        $ortpl->set("field_value",$row['u_field_2']);
        $order_rows = $ortpl->output();
    } else {
        $ortpl = new Template("app/templates/".$settings['default_template']."/rows/order_row.html",$lang);
        $field_name = 'To '.gatewayinfo($row[gateway_receive],"name").' account';
        $ortpl->set("field_name",$field_name);
        $ortpl->set("field_value",$row['u_field_2']);
        $order_rows = $ortpl->output();
    }
}
$tpl->set("order_rows",$order_rows);
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
echo $tpl->output();
?>