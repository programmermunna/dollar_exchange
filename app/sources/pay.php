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
if($row['status']>2) {
    $redirect = $settings['url']."order/".$row['order_hash'];
    header("Location: $redirect");   
}
$timenow = time();
$created = $row['created']+1800;
if($timenow > $created) {
    $update = $db->query("UPDATE ce_orders SET status='6',expired='$timenow' WHERE  id='$row[id]'");
    $redirect = $settings['url']."order/".$row['order_hash'];
    header("Location: $redirect");
}
$tpl = new Template("app/templates/".$settings['default_template']."/pay.html",$lang);
$tpl->set("url",$settings['url']);
$tpl->set("name",$settings['name']);
$tpl->set("order_id",$row['id']);
$pay_form = '';
if(gatewayinfo($row['gateway_send'],"manual_payment") == "1" or gatewayinfo($row['gateway_send'],"external_gateway") == "1") {
    $gtpl = new Template("app/templates/".$settings['default_template']."/pay/Manual.html",$lang);
    $gtpl->set("url",$settings['url']);
    $gtpl->set("name",$settings['name']); 
    $results = '';
    $gtpl->set("gateway_send",gatewayinfo($row['gateway_send'],"name"));
    $gtpl->set("gateway_send_currency",gatewayinfo($row['gateway_send'],"currency"));
    $gtpl->set("gateway_send_icon",gticon($row['gateway_send']));
    $gtpl->set("amount_send",$row['amount_send']);
    $gtpl->set("gateway_receive",gatewayinfo($row['gateway_receive'],"name"));
    $gtpl->set("gateway_receive_currency",gatewayinfo($row['gateway_receive'],"currency"));
    $gtpl->set("gateway_receive_icon",gticon($row['gateway_receive']));
    $gtpl->set("amount_receive",$row['amount_receive']);
    $gtpl->set("PaymentDetails",getManualForm($row['id']));
    $CE_Form = protect($_POST['ce_submit']);
    if($CE_Form == "transactionid" && empty($row['transaction_send'])) {
        $transaction_send = protect($_POST['transaction_send']);
        if(empty($transaction_send)) { 
            $results = error($lang['error_43']);
        } else {
            $time = time();
            $update = $db->query("UPDATE ce_orders SET status='3',transaction_send='$transaction_send',updated='$time' WHERE id='$row[id]'");
        }
    }
    $CE_Form = protect($_POST['ce_upload']);
    if($CE_Form == "file") {
        $extensions = array('jpg','jpeg','png'); 
        $fileextension = end(explode('.',$_FILES['uploadFile']['name'])); 
        $fileextension = strtolower($fileextension); 
        $filesnum = $db->query("SELECT * FROM ce_orders_attachments WHERE oid='$row[id]'");
        $filesnumv = $filesnum->num_rows;
        $filesmax = gatewayinfo($row['gateway_send'],"max_attachments");
        if($filesnumv > $filesmax) {
            $error = str_ireplace("%num%",$filesmax,$lang['error_44']);
            $results = error($error);
        } elseif(empty($_FILES['uploadFile']['name'])) {
            $results = error($lang['error_45']);
        } elseif(!in_array($fileextension,$extensions)) { 
            $results = error($lang['error_46']);
        } else {
            $filename = $_FILES['uploadFile']['name'];
            $filesize = $_FILES['uploadFile']['size'];
            $uploadDir = md5($settings['name']);
            $uploadDir = croptext($uploadDir,10);
            $cuploadDir = './'.$uploadDir;
            if(!is_dir($cuploadDir)) {
                mkdir($cuploadDir,0777);
            }
            $orderDir = md5($row['id']);
            $orderDir = croptext($orderDir,10);
            $corderDir = './'.$uploadDir.'/'.$orderDir;
            if(!is_dir($corderDir)) {
                mkdir($corderDir,0777);
            }
            $upload_dir = './'.$uploadDir.'/'.$orderDir.'/';
            $filepath = time().'_file.'.$fileextension;
            $uploading = $upload_dir.$filepath;
            $filepath_toDB = $uploadDir.'/'.$orderDir.'/'.$filepath;
            @move_uploaded_file($_FILES['uploadFile']['tmp_name'],$uploading);
            $time = time();
            $insert = $db->query("INSERT ce_orders_attachments (oid,filename,filesize,filepath,uploaded) VALUES ('$row[id]','$filename','$filesize','$filepath_toDB','$time')");
        }
							
    }
    $transaction_form = '';
    if(orderinfo($row['id'],"transaction_send")) {
        $transtpl = new Template("app/templates/".$settings['default_template']."/pay/Transaction_Success.html",$lang);
        $transtpl->set("url",$settings['url']);
        $transtpl->set("name",$settings['name']);
        $transtpl->set("transaction_send",orderinfo($row['id'],"transaction_send"));
        $transaction_form = $transtpl->output();
    } else {
        $transtpl = new Template("app/templates/".$settings['default_template']."/pay/Submit_Transaction.html",$lang);
        $transtpl->set("url",$settings['url']);
        $transtpl->set("name",$settings['name']);
        $transtpl->set("gateway_send",gatewayinfo($row['gateway_send'],"name"));
        $transaction_form = $transtpl->output();     
    }
    $attachment_form = '';
    if(gatewayinfo($row['gateway_send'],"allow_attachments") == "1") {
        $atttpl = new Template("app/templates/".$settings['default_template']."/pay/Upload_Form.html",$lang);
        $atttpl->set("url",$settings['url']);
        $atttpl->set("name",$settings['name']);
        $attachments = '';
        $AttQuery = $db->query("SELECT * FROM ce_orders_attachments WHERE oid='$row[id]'");
        if($AttQuery->num_rows>0) {
            $attachments .= '<br><br><br>';
            while($att = $AttQuery->fetch_assoc()) {
                $at2tpl = new Template("app/templates/".$settings['default_template']."/pay/Attachment.html",$lang);
                $at2tpl->set("filename",$att['filename']);
                $at2tpl->set("filesize",formatBytes($att['filesize']));
                $attachments .= $at2tpl->output(); 
            }
        }
        $atttpl->set("attachments",$attachments);
        $attachment_form = $atttpl->output();
    }
    $gtpl->set("transaction_form",$transaction_form);
    $gtpl->set("attachment_form",$attachment_form);
    $gtpl->set("results",$results);
    $pay_form = $gtpl->output();
} elseif(gatewayinfo($row['gateway_send'],"is_crypto") == "1") {
    $merchant_source = gatewayinfo($row['gateway_send'],"merchant_source");
    if($merchant_source == "block.io") {
        $gtpl = new Template("app/templates/".$settings['default_template']."/pay/Blockio.html",$lang);
        $gtpl->set("url",$settings['url']);
        $gtpl->set("name",$settings['name']);
        $gtpl->set("gateway",gatewayinfo($row['gateway_send'],"name"));
        $gtpl->set("gateway_icon",gticon($row['gateway_send']));
        $gtpl->set("amount",$row['amount_send']);
        $gtpl->set("currency",$row['currency_from']);  
        $blockio_api_key = gatewayinfo($row['gateway_send'],"g_field_1");
        $blockio_secret = gatewayinfo($row['gateway_send'],"g_field_2");	
        $option = gatewayinfo($row['gateway_send'],"g_field_3");
        if($option == "1") {
            // use one address for each exchange
            $address = gatewayinfo($row['gateway_send'],"g_field_4");
        } elseif($option == "2") {
            // use new address for each exchange
            $apiKey = $blockio_api_key;
            $pin = $blockio_secret;
            $version = 2; // the API version
            $block_io = new BlockIo($apiKey, $pin, $version);
            if($row['u_field_10']) {
                $address = $row['u_field_10'];
            } else {
                $new_address = $block_io->get_new_address();
                if($new_address->status == "success") { 
                    $address = $new_address->data->address;
                    $time = time();
                    $update = $db->query("UPDATE ce_orders SET u_field_10='$address' WHERE id='$row[id]'");
                    //$insert = $db->query("INSERT bit_edata (exchange_id,type,value,num,time) VALUES ('$row[exchange_id]','new_address','$address','0','$time')");
                } else {
                    $address = 'Cant generate new address, please contact with administrator.';
                }
            }
        } else {
            $address = 'no address';
        } 
        $gtpl->set("address",$address);
        $gtpl->set("order_id",$row['id']);
        $pay_form = $gtpl->output();
    } elseif($merchant_source == "blockchain.com") {
        $gtpl = new Template("app/templates/".$settings['default_template']."/pay/Blockchain.html",$lang);
        $gtpl->set("url",$settings['url']);
        $gtpl->set("name",$settings['name']);
        $gtpl->set("gateway",gatewayinfo($row['gateway_send'],"name"));
        $gtpl->set("gateway_icon",gticon($row['gateway_send']));
        $gtpl->set("amount",$row['amount_send']);
        $gtpl->set("currency",$row['currency_from']);
        if($row['u_field_10']) {
            $address = $row['u_field_10'];   
        } else {
            $secret = gatewayinfo($row['gateway_send'],"g_field_3");
            $my_xpub = gatewayinfo($row['gateway_send'],"g_field_1");
            $my_api_key = gatewayinfo($row['gateway_send'],"g_field_2");
            $my_callback_url = $settings['url']."ce_callbacks/Blockchain_IPN.php?order_id=".$id."&secret=".$secret;
            $root_url = 'https://api.blockchain.info/v2/receive';
            $parameters = 'xpub=' .$my_xpub. '&callback=' .urlencode($my_callback_url). '&key=' .$my_api_key;
            $response = file_get_contents($root_url . '?' . $parameters);
            $object = json_decode($response);
            $address = $object->address;
            $update = $db->query("UPDATE ce_orders SET u_field_10='$address' WHERE id='$row[id]'");
        }
        $gtpl->set("msg",$msg);
        $gtpl->set("qrcode",$qrcode);
        $gtpl->set("address",$address);
        $gtpl->set("order_id",$row['id']);
        $pay_form = $gtpl->output();
    } elseif($merchant_source == "coinpayments.net") {
        $gtpl = new Template("app/templates/".$settings['default_template']."/pay/CoinPayments.html",$lang);
        $gtpl->set("url",$settings['url']);
        $gtpl->set("name",$settings['name']);
        $gtpl->set("gateway",gatewayinfo($row['gateway_send'],"name"));
        $gtpl->set("gateway_icon",gticon($row['gateway_send']));
        $gtpl->set("amount",$row['amount_send']);
        $gtpl->set("currency",$row['currency_from']);
        $cp = CP_CreateOrder($row['gateway_send'],$row['id']);
        $msg = '';
        $qrcode = '';
        $address = '';
        if($cp['status'] == "success") {
            $qrcode = $cp['qrcode'];
            $address = $cp['address'];
        } else {
            $msg = $cp['msg'];
        }
        $gtpl->set("msg",$msg);
        $gtpl->set("qrcode",$qrcode);
        $gtpl->set("address",$address);
        $gtpl->set("order_id",$row['id']);
        $pay_form = $gtpl->output();
    } elseif($merchant_source == "gourl.io") {
        
    } else {
        $pay_form = 'Unsupported gateway. Please contact with administrator.';
    }
} elseif(gatewayinfo($row['gateway_send'],"merchant_source") == "stripe" or gatewayinfo($row['gateway_send'],"merchant_source") == "2checkout") {
    $gtpl = new Template("app/templates/".$settings['default_template']."/pay/CreditCard.html",$lang);
    $gtpl->set("url",$settings['url']);
    $gtpl->set("name",$settings['name']);
    $gtpl->set("gateway",gatewayinfo($row['gateway_send'],"name"));
    $gtpl->set("gateway_icon",gticon($row['gateway_send']));
    $gtpl->set("p_form",ce_PaymentForm($row['id']));
    $pay_form = $gtpl->output(); 
} else {
    $gtpl = new Template("app/templates/".$settings['default_template']."/pay/Merchant.html",$lang);
    $gtpl->set("url",$settings['url']);
    $gtpl->set("name",$settings['name']);
    $gtpl->set("gateway",gatewayinfo($row['gateway_send'],"name"));
    $gtpl->set("gateway_icon",gticon($row['gateway_send']));
    $gtpl->set("p_form",ce_PaymentForm($row['id']));
    $pay_form = $gtpl->output();
}
$tpl->set("pay_form",$pay_form);
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