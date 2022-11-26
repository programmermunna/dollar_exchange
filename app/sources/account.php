<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if(!checkSession()) {
    $redirect = $settings['url']."login";
	header("Location: $redirect");
}

$b = protect($_GET['b']);
$tpl = new Template("app/templates/".$settings['default_template']."/account/header.html",$lang);
$tpl->set("url",$settings['url']);
$new_tickets = '';
$GetTickets = $db->query("SELECT * FROM ce_tickets WHERE uid='$_SESSION[ce_uid]' and status='8'");
if($GetTickets->num_rows>0) {
    $nttpl = new Template("app/templates/".$settings['default_template']."/rows/new_tickets_num.html",$lang);
    $nttpl->set("num",$GetTickets->num_rows);
    $new_tickets = $nttpl->output();
}
$tpl->set("new_tickets",$new_tickets);
echo $tpl->output();
if($b == "dashboard") {
    $tpl = new Template("app/templates/".$settings['default_template']."/account/dashboard.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $MyQuery = $db->query("SELECT * FROM ce_orders WHERE uid='$_SESSION[ce_uid]'");
    $my_exchanges_num = (int)$MyQuery->num_rows;
    $tpl->set("my_exchanges_num",$my_exchanges_num);
    $MyQuery = $db->query("SELECT * FROM ce_orders WHERE refereer='$_SESSION[ce_uid]' and status='4'");
    $my_referrals_num = (int)$MyQuery->num_rows;
    $tpl->set("my_referrals_num",$my_referrals_num);
    $discount_level = (int)idinfo($_SESSION['ce_uid'],"discount_level");
    $tpl->set("discount_level",$discount_level);
    $exchanges_list = '';
    $E_Query = $db->query("SELECT * FROM ce_orders WHERE uid='$_SESSION[ce_uid]' ORDER BY id DESC LIMIT 10");
    if($E_Query->num_rows>0) {
        while($e_row = $E_Query->fetch_assoc()) {
            $etpl = new Template("app/templates/".$settings['default_template']."/rows/account_exchange_row.html",$lang);
            $etpl->set("url",$settings['url']);
            $etpl->set("order_id",$e_row['id']);
            $etpl->set("gateway_send_name",gatewayinfo($e_row['gateway_send'],"name"));
            $etpl->set("gateway_send_currency",gatewayinfo($e_row['gateway_send'],"currency"));
            $etpl->set("gateway_send_icon",gticon($e_row['gateway_send']));
            $etpl->set("amount_send",$e_row['amount_send']);
            $status = ce_decodeStatus($e_row['status']);
            $etpl->set("status_class",$status['style']);
            $etpl->set("status_text",$status['text']);
            $etpl->set("gateway_receive_name",gatewayinfo($e_row['gateway_receive'],"name"));
            $etpl->set("gateway_receive_currency",gatewayinfo($e_row['gateway_receive'],"currency"));
            $etpl->set("gateway_receive_icon",gticon($e_row['gateway_receive']));
            $etpl->set("amount_receive",$e_row['amount_receive']);
            $etpl->set("date",date("d/m/Y H:i",$e_row['created']));
            $exchange_rate = $e_row['rate_from']." ".gatewayinfo($e_row['gateway_send'],"currency")." - ".$e_row['rate_to']." ".gatewayinfo($e_row['gateway_receive'],"currency");
            $etpl->set("exchange_rate",$exchange_rate);
            $etpl->set("email",$e_row['u_field_1']);
            $order_rows = '';
            if(gatewayinfo($e_row['gateway_receive'],"manual_payment") == "1"  or gatewayinfo($e_row['gateway_receive'],"external_gateway") == "1") {
                $CheckFields = $db->query("SELECT * FROM ce_gateways_fields WHERE gateway_id='$e_row[gateway_receive]' ORDER BY id");
                if($CheckFields->num_rows>0) {
                    $i=2;
                    while($cf = $CheckFields->fetch_assoc()) {
                        $ortpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_row.html",$lang);
                        $ortpl->set("field_name",$cf['field_name']);
                        $u_field = 'u_field_'.$i;
                        $ortpl->set("field_value",$e_row[$u_field]);
                        $order_rows .= $ortpl->output();
                        $i++;
                    }
                } else {
                    if(gatewayinfo($e_row['gateway_receive'],"is_crypto") == "1") {
                        $ortpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_row.html",$lang);
                        $field_name = 'To '.gatewayinfo($e_row[gateway_receive],"name").' address';
                        $ortpl->set("field_name",$field_name);
                        $ortpl->set("field_value",$e_row['u_field_2']);
                        $order_rows = $ortpl->output();
                    } else {
                        $ortpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_row.html",$lang);
                        $field_name = 'To '.gatewayinfo($e_row[gateway_receive],"name").' account';
                        $ortpl->set("field_name",$field_name);
                        $ortpl->set("field_value",$e_row['u_field_2']);
                        $order_rows = $ortpl->output();
                    }   
                }
            } else {
                if(gatewayinfo($row['gateway_receive'],"is_crypto") == "1") {
                    $ortpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_row.html",$lang);
                    $field_name = 'To '.gatewayinfo($e_row[gateway_receive],"name").' address';
                    $ortpl->set("field_name",$field_name);
                    $ortpl->set("field_value",$e_row['u_field_2']);
                    $order_rows = $ortpl->output();
                } else {
                    $ortpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_row.html",$lang);
                    $field_name = 'To '.gatewayinfo($e_row[gateway_receive],"name").' account';
                    $ortpl->set("field_name",$field_name);
                    $ortpl->set("field_value",$e_row['u_field_2']);
                    $order_rows = $ortpl->output();
                }
            }
            $etpl->set("order_rows",$order_rows);
            $exchanges_list .= $etpl->output();
        }
    }
    $tpl->set("exchanges_list",$exchanges_list);
    echo $tpl->output();
} elseif($b == "exchanges") {
    $tpl = new Template("app/templates/".$settings['default_template']."/account/exchanges.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $exchanges_list = '';
    $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
	$limit = 10;
	$startpoint = ($page * $limit) - $limit;
	if($page == 1) {
		$i = 1;
	} else {
		$i = $page * $limit;
    }
    $CE_Form = protect($_POST['ce_filter']);
    if($CE_Form == "exchanges") {
        $filter = array();
        $status = protect($_POST['status']);
        if($status !== "ALL") {
            $filter[] = "status='$status'";
            $filter2[] = "status='$status'";  
        }
        $date_from = protect($_POST['date_from']);
        if(!empty($date_from)) {
            $date_from = strtotime($date_from);
            $filter[] = "created > $date_from";
            $filter2[] = "created > $date_from";
        }
        $date_to = protect($_POST['date_to']);
        if(!empty($date_to)) {  
            $date_to = strtotime($date_to);
            $filter[] = "created < $date_to";
            $filter2[] = "created < $date_to";
        }
        $amount = protect($_POST['amount']);
        $amount = (int)$amount;
        if($amount >0) {
            $filter[] = "amount_send='$amount'";
            $filter2[] = "amount_receive='$amount'";
        }
        $filters = implode(" and ",$filter);
        $filters2 = implode(" and ",$filter2);
        $statement = "ce_orders WHERE uid='$_SESSION[ce_uid]' and $filters or uid='$_SESSION[ce_uid]' and $filters2";
    } else {
        $statement = "ce_orders WHERE uid='$_SESSION[ce_uid]'";
    }
	$E_Query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
	if($E_Query->num_rows>0) {
        while($e_row = $E_Query->fetch_assoc()) {
            $etpl = new Template("app/templates/".$settings['default_template']."/rows/account_exchange_row.html",$lang);
            $etpl->set("url",$settings['url']);
            $etpl->set("order_id",$e_row['id']);
            $etpl->set("gateway_send_name",gatewayinfo($e_row['gateway_send'],"name"));
            $etpl->set("gateway_send_currency",gatewayinfo($e_row['gateway_send'],"currency"));
            $etpl->set("gateway_send_icon",gticon($e_row['gateway_send']));
            $etpl->set("amount_send",$e_row['amount_send']);
            $status = ce_decodeStatus($e_row['status']);
            $etpl->set("status_class",$status['style']);
            $etpl->set("status_text",$status['text']);
            $etpl->set("gateway_receive_name",gatewayinfo($e_row['gateway_receive'],"name"));
            $etpl->set("gateway_receive_currency",gatewayinfo($e_row['gateway_receive'],"currency"));
            $etpl->set("gateway_receive_icon",gticon($e_row['gateway_receive']));
            $etpl->set("amount_receive",$e_row['amount_receive']);
            $etpl->set("date",date("d/m/Y H:i",$e_row['created']));
            $exchange_rate = $e_row['rate_from']." ".gatewayinfo($e_row['gateway_send'],"currency")." - ".$e_row['rate_to']." ".gatewayinfo($e_row['gateway_receive'],"currency");
            $etpl->set("exchange_rate",$exchange_rate);
            $etpl->set("email",$e_row['u_field_1']);
            $order_rows = '';
            if(gatewayinfo($e_row['gateway_receive'],"manual_payment") == "1"  or gatewayinfo($e_row['gateway_receive'],"external_gateway") == "1") {
                $CheckFields = $db->query("SELECT * FROM ce_gateways_fields WHERE gateway_id='$e_row[gateway_receive]' ORDER BY id");
                if($CheckFields->num_rows>0) {
                    $i=2;
                    while($cf = $CheckFields->fetch_assoc()) {
                        $ortpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_row.html",$lang);
                        $ortpl->set("field_name",$cf['field_name']);
                        $u_field = 'u_field_'.$i;
                        $ortpl->set("field_value",$e_row[$u_field]);
                        $order_rows .= $ortpl->output();
                        $i++;
                    }
                } else {
                    if(gatewayinfo($e_row['gateway_receive'],"is_crypto") == "1") {
                        $ortpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_row.html",$lang);
                        $field_name = 'To '.gatewayinfo($e_row[gateway_receive],"name").' address';
                        $ortpl->set("field_name",$field_name);
                        $ortpl->set("field_value",$e_row['u_field_2']);
                        $order_rows = $ortpl->output();
                    } else {
                        $ortpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_row.html",$lang);
                        $field_name = 'To '.gatewayinfo($e_row[gateway_receive],"name").' account';
                        $ortpl->set("field_name",$field_name);
                        $ortpl->set("field_value",$e_row['u_field_2']);
                        $order_rows = $ortpl->output();
                    }   
                }
            } else {
                if(gatewayinfo($row['gateway_receive'],"is_crypto") == "1") {
                    $ortpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_row.html",$lang);
                    $field_name = 'To '.gatewayinfo($e_row[gateway_receive],"name").' address';
                    $ortpl->set("field_name",$field_name);
                    $ortpl->set("field_value",$e_row['u_field_2']);
                    $order_rows = $ortpl->output();
                } else {
                    $ortpl = new Template("app/templates/".$settings['default_template']."/rows/exchange_row.html",$lang);
                    $field_name = 'To '.gatewayinfo($e_row[gateway_receive],"name").' account';
                    $ortpl->set("field_name",$field_name);
                    $ortpl->set("field_value",$e_row['u_field_2']);
                    $order_rows = $ortpl->output();
                }
            }
            $etpl->set("order_rows",$order_rows);
            $exchanges_list .= $etpl->output();
        }
    }
    $tpl->set("exchanges_list",$exchanges_list);
    $ver = $settings['url']."account/exchanges";
	if(web_pagination($statement,$ver,$limit,$page)) {
		$pages = web_pagination($statement,$ver,$limit,$page);
	} else {
		$pages = '';
	}
	$tpl->set("pages",$pages);
    echo $tpl->output();

} elseif($b == "new") {
    $c = protect($_GET['c']);
    if($c == "review") {
        $tpl = new Template("app/templates/".$settings['default_template']."/account/new_review.html",$lang);
        $results = '';
        $CEAction = protect($_POST['ce_btn']);
        if(isset($CEAction) && $CEAction == "submit_review") {
            $display_name = protect($_POST['display_name']);
            $order_id = protect($_POST['order_id']);
            $type = protect($_POST['type']);
            $comment = protect($_POST['comment']);
            $check1 = $db->query("SELECT * FROM ce_orders WHERE id='$order_id' and uid='$_SESSION[ce_uid]'");
            $check2 = $db->query("SELECT * FROM ce_users_reviews WHERE uid='$_SESSION[ce_uid]' and order_id='$order_id'");
            if(empty($display_name) or empty($order_id) or empty($type) or empty($comment)) { 
                $results = error($lang['error_1']);
            } elseif(!isValidUsername($display_name)) {
                $results = error($lang['error_2']);
            } elseif($check1->num_rows==0) {
                $results = error($lang['error_3']);
            } elseif($check2->num_rows==1) {
                $results = error($lang['error_4']);
            } else {
                $time = time();
                $insert = $db->query("INSERT ce_users_reviews (uid,display_name,order_id,type,comment,posted,status) VALUES ('$_SESSION[ce_uid]','$display_name','$order_id','$type','$comment','$time','2')");
                $results = success($lang['success_1']);
            }
        }
        $tpl->set("results",$results);
        $display_name = idinfo($_SESSION['ce_uid'],"first_name")."_".idinfo($_SESSION['ce_uid'],"last_name");
        $tpl->set("display_name",$display_name);
        $orders_list = '';
        $OrdersQuery = $db->query("SELECT * FROM ce_orders WHERE uid='$_SESSION[ce_uid]' and status='4' ORDER BY id DESC");
        if($OrdersQuery->num_rows>0) {
            while($or = $OrdersQuery->fetch_assoc()) {
                $checkr = $db->query("SELECT * FROM ce_users_reviews WHERE uid='$_SESSION[ce_uid]' and order_id='$or[id]'");
                if($checkr->num_rows==0) {
                    $odetails = array();
                    $odetails['amount_send'] = $or['amount_send'];
                    $odetails['amount_receive'] = $or['amount_receive'];
                    $odetails['gateway_send_name'] = gatewayinfo($or['gateway_send'],"name");
                    $odetails['gateway_receive_name'] = gatewayinfo($or['gateway_receive'],"name");
                    $odetails['gateway_send_currency'] = gatewayinfo($or['gateway_send'],"currency");
                    $odetails['gateway_receive_currency'] = gatewayinfo($or['gateway_receive'],"currency");
                    $orders_list .= '<option value="'.$or[id].'">'.$lang[order].' #'.$or[id].' - '.$lang[exchange].' ('.$odetails[gateway_send_name].') '.$odetails[amount_send].' '.$odetails[gateway_send_currency].' '.$lang[to].' ('.$odetails[gateway_receive_name].') '.$odetails[amount_receive].' '.$odetails[gateway_receive_currency].'</option>';
                }
            }
        }
        $tpl->set("orders_list",$orders_list);
        echo $tpl->output();
    } elseif($c == "ticket") {
        $tpl = new Template("app/templates/".$settings['default_template']."/account/new_ticket.html",$lang);
        $results = '';
        $CEAction = protect($_POST['ce_btn']);
        if(isset($CEAction) && $CEAction == "submit_ticket") {
            $title = protect($_POST['title']);
            $order_id = protect($_POST['order_id']);
            $message = addslashes($_POST['message']);
            $check1 = $db->query("SELECT * FROM ce_tickets WHERE uid='$_SESSION[ce_uid]' and order_id='$order_id'");
            if(empty($title) or empty($order_id) or empty($message)) {
                $results = error($lang['error_1']);
            } elseif($check1->num_rows>0) {
                $error = str_ireplace("%num%",$order_id,$lang['error_5']);
                $results = error($error);
            } else {
                $time = time();
                $hash = randomHash(30);
                $insert = $db->query("INSERT ce_tickets (uid,title,hash,content,order_id,created,updated,status,served_by) VALUES ('$_SESSION[ce_uid]','$title','$hash','$message','$order_id','$time','0','9','0')");
                $GetQuery = $db->query("SELECT * FROM ce_tickets WHERE uid='$_SESSION[ce_uid]' and order_id='$order_id'");
                $get = $GetQuery->fetch_assoc();
                $insert = $db->query("INSERT ce_tickets_messages (tid,message,author,created) VALUES ('$get[id]','$message','$_SESSION[ce_uid]','$time')");
                $results = success($lang['success_2']);
            }
        }
        $orders_list = '';
        $OrdersQuery = $db->query("SELECT * FROM ce_orders WHERE uid='$_SESSION[ce_uid]' ORDER BY id DESC");
        if($OrdersQuery->num_rows>0) {
            while($or = $OrdersQuery->fetch_assoc()) {
                $checkr = $db->query("SELECT * FROM ce_tickets WHERE uid='$_SESSION[ce_uid]' and order_id='$or[id]'");
                if($checkr->num_rows==0) {
                    $odetails = array();
                    $odetails['amount_send'] = $or['amount_send'];
                    $odetails['amount_receive'] = $or['amount_receive'];
                    $odetails['gateway_send_name'] = gatewayinfo($or['gateway_send'],"name");
                    $odetails['gateway_receive_name'] = gatewayinfo($or['gateway_receive'],"name");
                    $odetails['gateway_send_currency'] = gatewayinfo($or['gateway_send'],"currency");
                    $odetails['gateway_receive_currency'] = gatewayinfo($or['gateway_receive'],"currency");
                    $orders_list .= '<option value="'.$or[id].'">'.$lang[order].' #'.$or[id].' - '.$lang[exchange].' ('.$odetails[gateway_send_name].') '.$odetails[amount_send].' '.$odetails[gateway_send_currency].' '.$lang[to].' ('.$odetails[gateway_receive_name].') '.$odetails[amount_receive].' '.$odetails[gateway_receive_currency].'</option>';
                }
            }
        }
        $tpl->set("results",$results);
        $tpl->set("orders_list",$orders_list);
        echo $tpl->output();
    } elseif($c == "withdrawal") {
        $tpl = new Template("app/templates/".$settings['default_template']."/account/new_withdrawal.html",$lang);
        $results = '';
        $gateways_list = '';
        $CEAction = protect($_POST['ce_btn']);
        if(isset($CEAction) && $CEAction == "withdrawal") {
            $gateway = protect($_POST['gateway']);
            $account = protect($_POST['account']);
            $amount = protect($_POST['amount']);
            $balance = '0';
            $BalanceQuery = $db->query("SELECT * FROM ce_users_earnings WHERE uid='$_SESSION[ce_uid]'");
            if($BalanceQuery->num_rows>0) {
                $bal = $BalanceQuery->fetch_assoc();
                $balance = $bal['amount'];
            }
            if(empty($gateway) or empty($account) or empty($amount)) {
                $results = error($lang['error_1']);
            } elseif(!gatewayinfo($gateway,"id")) {
                $results = error($lang['error_6']);
            } elseif(!is_numeric($amount)) {
                $results = error($lang['error_7']);
            } elseif($amount < $settings['referral_min_withdrawal']) {
                $error = str_ireplace("%amount%",$settings['referral_min_withdrawal'],$lang['error_8']);
                $results = error($error);
            } elseif($amount > $balance) {
                $error = str_ireplace("%balance%",$balance,$lang['error_9']);
                $results = error($error);
            } else {
                $time = time();
                $currency = 'USD'; 
                $update = $db->query("UPDATE ce_users_earnings SET amount=amount-$amount WHERE uid='$_SESSION[ce_uid]' and currency='USD'");
                $insert = $db->query("INSERT ce_users_withdrawals (uid,gateway,account,amount,currency,requested_on,processed_on,status) VALUES ('$_SESSION[ce_uid]','$gateway','$account','$amount','$currency','$time','','1')");
                $results = success($lang['success_3']);
            }
        }
        $balance = '0 USD';
        $BalanceQuery = $db->query("SELECT * FROM ce_users_earnings WHERE uid='$_SESSION[ce_uid]'");
        if($BalanceQuery->num_rows>0) {
            $bal = $BalanceQuery->fetch_assoc();
            $balance = $bal['amount']." ".$bal['currency'];
        }
        $GatewaysQuery = $db->query("SELECT * FROM ce_gateways ORDER BY id");
        if($GatewaysQuery->num_rows>0) {
            while($g = $GatewaysQuery->fetch_assoc()) {
                $gateways_list .= '<option value="'.$g[id].'">'.$g[name].' '.$g[currency].'</option>';
            }
        }
        $tpl->set("gateways_list",$gateways_list);
        $min_amount = $settings['referral_min_withdrawal']." USD";
        $tpl->set("min_amount",$min_amount);
        $tpl->set("balance",$balance);
        $tpl->set("results",$results);
        echo $tpl->output();
    } else {
        $redirect = $settings['url']."account/dashboard";
        header("Location: $redirect");
    }
} elseif($b == "view") { 
    $c = protect($_GET['c']);
    if($c == "ticket") {
        $hash = protect($_GET['hash']);
        $Query = $db->query("SELECT * FROM ce_tickets WHERE uid='$_SESSION[ce_uid]' and hash='$hash'");
        if($Query->num_rows>0) {
            $row = $Query->fetch_assoc();
            $results = '';
            $CEAction = protect($_POST['ce_btn']);
            if(isset($CEAction) && $CEAction == "send_message") {
                $message = addslashes($_POST['message']);
                if(empty($message)) {
                    $results = error($lang['error_10']);
                } else {
                    $time = time();
                    $insert = $db->query("INSERT ce_tickets_messages (tid,message,author,created) VALUES ('$row[id]','$message','$_SESSION[ce_uid]','$time')");
                    $update = $db->query("UPDATE ce_tickets SET status='9',updated='$time' WHERE id='$row[id]'");
                    $results = success($lang['success_4']);
                }
            }
            if(isset($CEAction) && $CEAction == "mark_solved") {
                $update = $db->query("UPDATE ce_tickets SET status='2' WHERE id='$row[id]'");
                $redirect = $settings['url']."account/view/ticket/".$row['hash'];
                header("Location: $redirect");
            }
            if(isset($CEAction) && $CEAction == "mark_closed") {
                $update = $db->query("UPDATE ce_tickets SET status='1' WHERE id='$row[id]'");
                $redirect = $settings['url']."account/view/ticket/".$row['hash'];
                header("Location: $redirect");
            }
            $messages = '';
            $MessagesQuery = $db->query("SELECT * FROM ce_tickets_messages WHERE tid='$row[id]' ORDER BY id DESC");
            if($MessagesQuery->num_rows>0) {
                while($msg = $MessagesQuery->fetch_assoc()) {
                    if($msg['author']>0) {
                        $mtpl = new Template("app/templates/".$settings['default_template']."/rows/ticket_user_row.html",$lang);
                        $mtpl->set("message",$msg['message']);
                        $mtpl->set("date",date("d/m/Y H:i:s",$msg['created']));
                        $messages .= $mtpl->output();
                    } else {
                        $mtpl = new Template("app/templates/".$settings['default_template']."/rows/ticket_admin_row.html",$lang);
                        $mtpl->set("message",$msg['message']);
                        $mtpl->set("date",date("d/m/Y H:i:s",$msg['created']));
                        $messages .= $mtpl->output();
                    }
                }
            }
            $tpl = new Template("app/templates/".$settings['default_template']."/account/ticket.html",$lang);
            $tpl->set("title",$row['title']);
            $tpl->set("results",$results);
            $tpl->set("messages",$messages);
            $message_form = '';
            $ticket_controls = '';
            if($row['status']>2) {
                $ftpl = new Template("app/templates/".$settings['default_template']."/rows/message_form.html",$lang);
                $message_form = $ftpl->output();
                $ttpl = new Template("app/templates/".$settings['default_template']."/rows/ticket_controls.html",$lang);
                $ticket_controls = $ttpl->output();
            }
            $tpl->set("ticket_controls",$ticket_controls);
            $tpl->set("message_form",$message_form);
            echo $tpl->output();
        } else {    
            $redirect = $settings['url']."account/tickets";
            header("Location: $redirect");
        }
    } else {
        $redirect = $settings['url']."account/dashboard";
        header("Location: $redirect");
    }
} elseif($b == "tickets") {
    $tpl = new Template("app/templates/".$settings['default_template']."/account/tickets.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $tickets_list = '';
    $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
	$limit = 20;
	$startpoint = ($page * $limit) - $limit;
	if($page == 1) {
		$i = 1;
	} else {
		$i = $page * $limit;
    }
    $statement = "ce_tickets WHERE uid='$_SESSION[ce_uid]'";
	$E_Query = $db->query("SELECT * FROM {$statement} ORDER BY status DESC, id DESC LIMIT {$startpoint} , {$limit}");
	if($E_Query->num_rows>0) {
        while($e_row = $E_Query->fetch_assoc()) {
            $etpl = new Template("app/templates/".$settings['default_template']."/rows/account_ticket_row.html",$lang);
            $etpl->set("title",$e_row['title']);
            $etpl->set("hash",$e_row['hash']);
            $etpl->set("url",$settings['url']);
            if($e_row['updated']>0) {
                $date = date("d/m/Y H:i",$e_row['updated']);
            } else {
                $date = date("d/m/Y H:i",$e_row['created']);
            }
            if($e_row['status'] == "9") {
                $status = '<span class="badge badge-info">'.$lang[status_ticket_1].'</span>';
                $class = 'table-info';
            } elseif($e_row['status'] == "8") {
                $status = '<span class="badge badge-success">'.$lang[status_ticket_2].'</span>';
                $class = 'table-danger';
            } elseif($e_row['status'] == "2") {
                $status = '<span class="badge badge-success">'.$lang[status_ticket_3].'</span>';
                $class = '';
            } elseif($e_row['status'] == "1") {
                $status = '<span class="badge badge-danger">'.$lang[status_ticket_4].'</span>';
                $class = '';
            } else {
                $status = '<span class="badge badge-default">'.$lang[status_unknown].'</span>';
                $class = '';
            }
            $etpl->set("class",$class);
            $etpl->set("date",$date);
            $etpl->set("status",$status);
            $tickets_list .= $etpl->output();
        }
    }
    $ver = $settings['url']."account/tickets";
	if(web_pagination($statement,$ver,$limit,$page)) {
		$pages = web_pagination($statement,$ver,$limit,$page);
	} else {
		$pages = '';
	}
	$tpl->set("pages",$pages);
    $tpl->set("tickets_list",$tickets_list);
    echo $tpl->output();
} elseif($b == "reviews") {
    $tpl = new Template("app/templates/".$settings['default_template']."/account/reviews.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $reviews_list = '';
    $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
	$limit = 20;
	$startpoint = ($page * $limit) - $limit;
	if($page == 1) {
		$i = 1;
	} else {
		$i = $page * $limit;
    }
    $statement = "ce_users_reviews WHERE uid='$_SESSION[ce_uid]'";
	$E_Query = $db->query("SELECT * FROM {$statement} ORDER BY id DESC LIMIT {$startpoint} , {$limit}");
	if($E_Query->num_rows>0) {
        while($e_row = $E_Query->fetch_assoc()) {
            $etpl = new Template("app/templates/".$settings['default_template']."/rows/account_review_row.html",$lang);
            $etpl->set("comment",$e_row['comment']);
            if($e_row['type'] == "1") {
                $type = '<span class="text text-success"><i class="fa fa-smile"></i> '.$lang[review_positive].'</span>';
            } elseif($e_row['type'] == "2") {
                $type = '<span class="text text-danger"><i class="fa fa-frown-o"></i> '.$lang[review_negative].'</span>';
            } elseif($e_row['type'] == "3") {
                $type = '<span class="text text-warning"><i class="fa fa-meh-o"></i> '.$lang[review_neutral].'</span>';
            } else { }
            $etpl->set("type",$type);
            $etpl->set("date",date("d/m/Y H:i",$e_row['posted']));
            if($e_row['status'] == "1") {
                $status = '<span class="badge badge-success">'.$lang[status_review_1].'</span>';
            } elseif($e_row['status'] == "2") {
                $status = '<span class="badge badge-warning">'.$lang[status_review_2].'</span>';
            } elseif($e_row['status'] == "3") {
                $status = '<span class="badge badge-danger">'.$lang[status_review_3].'</span>';
            } else {
                $status = '<span class="badge badge-default">'.$lang[status_unknown].'</span>';
            }
            $etpl->set("status",$status);
            $reviews_list .= $etpl->output();
        }
    }
    $ver = $settings['url']."account/reviews";
	if(web_pagination($statement,$ver,$limit,$page)) {
		$pages = web_pagination($statement,$ver,$limit,$page);
	} else {
		$pages = '';
	}
	$tpl->set("pages",$pages);
    $tpl->set("reviews_list",$reviews_list);
    echo $tpl->output();
} elseif($b == "referrals") {
    $tpl = new Template("app/templates/".$settings['default_template']."/account/referrals.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $tpl->set("refid",$_SESSION['ce_uid']);
    $tpl->set("referral_comission",$settings['referral_comission']);
    $total_referrals = '0';
    $MyQuery = $db->query("SELECT * FROM ce_orders WHERE refereer='$_SESSION[ce_uid]' and status='4'");
    $total_referrals = (int)$MyQuery->num_rows;
    $balance = '0 USD';
    $BalanceQuery = $db->query("SELECT * FROM ce_users_earnings WHERE uid='$_SESSION[ce_uid]'");
    if($BalanceQuery->num_rows>0) {
        $bal = $BalanceQuery->fetch_assoc();
        $balance = $bal['amount']." ".$bal['currency'];
    }
    $withdrawals_list = '';
    $tpl->set("total_referrals",$total_referrals);
    $tpl->set("balance",$balance);
    $WithdrawalsQuery = $db->query("SELECT * FROM ce_users_withdrawals WHERE uid='$_SESSION[ce_uid]' ORDER BY id DESC");
    if($WithdrawalsQuery->num_rows>0) {
        while($w = $WithdrawalsQuery->fetch_assoc()) {
            $wtpl = new Template("app/templates/".$settings['default_template']."/rows/account_withdrawal_row.html",$lang);
            $wtpl->set("account",$w['account']);
            $gateway = gatewayinfo($w['gateway'],"name")." ".gatewayinfo($w['gateway'],"currency");
            $wtpl->set("gateway",$gateway);
            $wtpl->set("amount",$w['amount']);
            $wtpl->set("currency",$w['currency']);
            if($w['status'] == "1") {
                $status = '<span class="badge badge-warning">'.$lang[status_referral_1].'</span>';
            } elseif($w['status'] == "2") {
                $status = '<span class="badge badge-success">'.$lang[status_referral_2].'</span>';
            } elseif($w['status'] == "3") {
                $status = '<span class="badge badge-danger">'.$lang[status_referral_3].'</span>';
            } else {
                $status = '<span class="badge badge-default">'.$lang[status_unknown].'</span>';
            }
            $wtpl->set("status",$status);
            $wtpl->set("date",date("d/m/Y H:i",$w['requested_on']));
            $withdrawals_list .= $wtpl->output();
        }
    }
    $tpl->set("withdrawals_list",$withdrawals_list);
    echo $tpl->output();
} elseif($b == "discount_system") {
    $tpl = new Template("app/templates/".$settings['default_template']."/account/discount_system.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $tpl->set("discount_level",(int)idinfo($_SESSION['ce_uid'],"discount_level"));
    $tpl->set("total_exchanged",(int)idinfo($_SESSION['ce_uid'],"exchanged_volume"));
    $discount_rows = '';
    $query = $db->query("SELECT * FROM ce_discount_system ORDER BY discount_level");
    if($query->num_rows>0) {
        while($row = $query->fetch_assoc()) {
            $dtpl = new Template("app/templates/".$settings['default_template']."/rows/account_discount_row.html",$lang);    
            $dtpl->set("discount_level",$row['discount_level']);
            $dtpl->set("from_value",$row['from_value']);
            $dtpl->set("to_value",$row['to_value']);
            $dtpl->set("currency",$row['currency']);
            $dtpl->set("discount_percentage",$row['discount_percentage']);
            $discount_rows .= $dtpl->output();
        }   
    }
    $tpl->set("discount_rows",$discount_rows);
    echo $tpl->output();
} elseif($b == "settings") {
    $c = protect($_GET['c']);
    if($c == "profile") {
        $tpl = new Template("app/templates/".$settings['default_template']."/account/settings_profile.html",$lang);
        $tpl->set("url",$settings['url']);
        $tpl->set("name",$settings['name']);
        $results = '';
        if(idinfo($_SESSION['ce_uid'],"status") == "16") {
            $results = info($lang['info_1']);
        }
        $ce_form = protect($_POST['ce_save']);
        if($ce_form == "changes") {
            $first_name = protect($_POST['first_name']);
            $last_name = protect($_POST['last_name']);
            $country = protect($_POST['country']);
            $city = protect($_POST['city']);
            $zip_code = protect($_POST['zip_code']);
            $address = protect($_POST['address']);
            $birthday_date = protect($_POST['birth_date']);
            $mobile_number = protect($_POST['mobile_number']);

            if(empty($first_name) or empty($last_name) or empty($country) or empty($city) or empty($zip_code) or empty($address) or empty($birthday_date) or empty($mobile_number)) {
                $results = error($lang['error_11']);
            } elseif($country !== "United Kingdom" && !is_numeric($zip_code)) {
                $results = error($lang['error_12']);
            } elseif($country == "United Kingdom" && postcode_check($zip_code) == false) {
                $results = error($lang['error_12']);    
            } else {
                $update = $db->query("UPDATE ce_users SET first_name='$first_name',last_name='$last_name',country='$country',city='$city',zip_code='$zip_code',address='$address',birthday_date='$birthday_date',mobile_number='$mobile_number',status='1' WHERE id='$_SESSION[ce_uid]'");
                $results = success($lang['success_5']);
            }
        }
        $tpl->set("results",$results);
        $tpl->set("u_first_name",idinfo($_SESSION['ce_uid'],"first_name"));
        $tpl->set("u_last_name",idinfo($_SESSION['ce_uid'],"last_name"));
        $countries = getCountries();
        $country_list = '';
        foreach($countries as $ck=>$cv) {
            if(idinfo($_SESSION['ce_uid'],"country") == $cv) { $sel = 'selected'; } else { $sel = ''; }
            $country_list .= '<option value="'.$cv.'" '.$sel.'>'.$cv.'</option>';
        }
        $tpl->set("u_country",$country_list);
        $tpl->set("u_city",idinfo($_SESSION['ce_uid'],"city"));
        $tpl->set("u_address",idinfo($_SESSION['ce_uid'],"address"));
        $tpl->set("u_zip_code",idinfo($_SESSION['ce_uid'],"zip_code"));
        $tpl->set("u_birthday_date",idinfo($_SESSION['ce_uid'],"birthday_date"));
        $tpl->set("u_mobile_number",idinfo($_SESSION['ce_uid'],"mobile_number"));
        echo $tpl->output();
    } elseif($c == "security") {
        $tpl = new Template("app/templates/".$settings['default_template']."/account/settings_security.html",$lang);
        $tpl->set("url",$settings['url']);
        $tpl->set("name",$settings['name']);
        $results = '';
        $results_2fa = '';
        $ce_form = protect($_POST['ce_save']);
        if($ce_form == "2fa") {
                $twoFA_enable = protect($_POST['2fa_enable']);
                if($twoFA_enable == "1") {
                    $update = $db->query("UPDATE ce_users SET twoFA='1' WHERE id='$_SESSION[ce_uid]'");
                } else {
                    $update = $db->query("UPDATE ce_users SET twoFA='0' WHERE id='$_SESSION[ce_uid]'");
                }
                $results_2fa = success($lang['success_5']);
        }
        if($ce_form == "password") {
            $cpassword = protect($_POST['cpassword']);
            $npassword = protect($_POST['npassword']);
            $cnpassword = protect($_POST['cnpassword']);
            if(empty($cpassword)) {
                $results = error($lang['error_13']);
            } elseif(!password_verify($cpassword,idinfo($_SESSION['ce_uid'],"password"))) {
                $results = error($lang['error_14']);
            } elseif(empty($npassword)) {
                $results = error($lang['error_15']);
            } elseif(strlen($npassword)<6) {
                $results = error($lang['error_16']);
            } elseif($npassword !== $cnpassword) {
                $results = error($lang['error_17']);
            } else {
                $password = password_hash($npassword, PASSWORD_DEFAULT);
                $update = $db->query("UPDATE ce_users SET password='$password' WHERE id='$_SESSION[ce_uid]'");
                $results = success($lang['success_6']);
            }
        }
        if(idinfo($_SESSION['ce_uid'],"twoFA") == "1") { $twoFA_status = 'checked'; } else { $twoFA_status = ''; }
        $tpl->set("u_2fa_status",$twoFA_status);
        $tpl->set("results",$results);
        $tpl->set("results_2fa",$results_2fa);
        echo $tpl->output();
    } elseif($c == "verification") {
        $tpl = new Template("app/templates/".$settings['default_template']."/account/settings_verification.html",$lang);
        $tpl->set("url",$settings['url']);
        $tpl->set("name",$settings['name']);
        $eresults = '';
        $dresults = '';
        $EmailVerificationForm = '';
        $email = idinfo($_SESSION['ce_uid'],"email");
        $CEAction = protect($_POST['ce_btn']);
        if(idinfo($_SESSION['ce_uid'],"email_verified") == "1") {
            $etpl = new Template("app/templates/".$settings['default_template']."/rows/email_verification_success.html",$lang);
            $etpl->set("email",$email);
            $EmailVerificationForm = $etpl->output();
        } else {
            if(isset($CEAction) && $CEAction == "resend_email") {
                CE_Send_VE($email);
                $eresults = success($lang['success_7']);
            }
            $etpl = new Template("app/templates/".$settings['default_template']."/rows/email_verification_form.html",$lang);
            $etpl->set("email",$email);
            $EmailVerificationForm = $etpl->output();
        }
        $tpl->set("EmailVerificationForm",$EmailVerificationForm);
        $tpl->set("eresults",$eresults);
        if(isset($CEAction) && $CEAction == "upload_doc") {
            $document_type = protect($_POST['document_type']);
            $document_number = protect($_POST['document_number']);
            $additional_information = protect($_POST['additional_information']);
            $filename = $_FILES['uploadFile']['name'];
            $filesize = $_FILES['uploadFile']['size'];
            $fileextension = end(explode('.',$_FILES['uploadFile']['name'])); 
            $fileextension = strtolower($fileextension); 
            $extensions = array('jpg','jpeg','png','pdf'); 
            if(empty($document_type)) { 
                $dresults = error($lang['error_18']);
            } elseif(empty($document_number)) {
                $dresults = error($lang['error_19']);
            } elseif(empty($filename)) {
                $dresults = error($lang['error_20']);
            } elseif($filename > 10240) {
                $dresults = error($lang['error_21']);
            } elseif(!in_array($fileextension,$extensions)) { 
                $dresults = error($lang['error_22']); 
            } else {
                $secure_directory = CE_secure_directory();
                if(!is_dir("./".$secure_directory)) {
                    mkdir("./".$secure_directory,0777);
                    $file_htaccess = 'order deny,allow
deny from all
allow from 127.0.0.1';
                    file_put_contents("./".$secure_directory."/.htaccess",$file_htaccess);
                }
                $upload_file = $secure_directory.'/'.$_SESSION[ce_uid];
                if(!is_dir($upload_file)) {
                    mkdir("./".$upload_file,0777);
                }
                $upload_file = $upload_file.'/'.randomHash(20).'.'.$fileextension;
                @move_uploaded_file($_FILES['uploadFile']['tmp_name'],$upload_file);
                $time = time();
                $insert = $db->query("INSERT ce_users_documents (uid,document_type,document_path,uploaded,status,u_field_1,u_field_2,u_field_3,u_field_4,u_field_5) VALUES ('$_SESSION[ce_uid]','$document_type','$upload_file','$time','1','$document_number','$additional_information','$filename','','')");
                $update = $db->query("UPDATE ce_users SET documents_pending='1' WHERE id='$_SESSION[ce_uid]'");
                $dresults = success($lang['success_8']);
            }
        }
        $dfiles = '';
        $GetDocuments = $db->query("SELECT * FROM ce_users_documents WHERE uid='$_SESSION[ce_uid]' ORDER BY id");
        if($GetDocuments->num_rows>0) {
            while($doc = $GetDocuments->fetch_assoc()) {
                if($doc['status'] == "1") {
                    $status = '<span class="badge badge-primary">'.$lang[status_doc_1].'</span>';
                } elseif($doc['status'] == "2") {
                    $status = '<span class="badge badge-danger">'.$lang[status_doc_2].'</span>';
                } elseif($doc['status'] == "3") {
                    $status = '<span class="badge badge-success">'.$lang[status_doc_3].'</span>';
                } else {
                    $status = '<span class="badge badge-default">'.$lang[status_unknown].'</span>';
                }
                if($doc['document_type'] == "1") {
                    $document_type = $lang['doc_type_1'];
                } elseif($doc['document_type'] == "2") {
                    $document_type = $lang['doc_type_2'];
                } elseif($doc['document_type'] == "3") {
                    $document_type = $lang['doc_type_3'];
                } elseif($doc['document_type'] == "4") {
                    $document_type = $lang['doc_type_4'];
                } elseif($doc['document_type'] == "5") {
                    $document_type = $lang['doc_type_5'];
                } else {
                    $document_type = $lang['unknown'];
                }
                if($doc['u_field_5']) {
                    $comment = $doc['u_field_5'];
                } else {
                    $comment = 'n/a';
                }
                $dtpl = new Template("app/templates/".$settings['default_template']."/rows/document_row.html",$lang);
                $dtpl->set("document_type",$document_type);
                $dtpl->set("document_number",$doc['u_field_1']);
                $dtpl->set("status",$status);
                $dtpl->set("additional_information",$doc['u_field_2']);
                $dtpl->set("filename",$doc['u_field_3']);
                $dtpl->set("comment",$comment);
                $dfiles .= $dtpl->output();
            }
        } else {
            $dtpl = new Template("app/templates/".$settings['default_template']."/rows/no_documents.html",$lang);
            $dfiles = $dtpl->output();
        }
        $tpl->set("dfiles",$dfiles);
        $tpl->set("dresults",$dresults);
        echo $tpl->output();
    } else {
        $redirect = $settings['url']."account/settings/profile";
        header("Location: $redirect");
    }
} elseif($b == "close") {
    $tpl = new Template("app/templates/".$settings['default_template']."/account/close.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $results = '';
    $CEAction = protect($_POST['ce_btn']);
    if(isset($CEAction) && $CEAction == "delacc") {
        $deloption = protect($_POST['deloption']);
        if($deloption == "yes") {
            $update = $db->query("UPDATE ce_users SET close_request='1' WHERE id='$_SESSION[ce_uid]'");
            $results = success($lang['success_9']);
        }
    }
    $tpl->set("results",$results);
    echo $tpl->output();
} elseif($b == "logout") {
    unset($_SESSION['ce_uid']);
    unset($_COOKIE['cryptoexchanger_uid']);
    setcookie("cryptoexchanger_uid", "", time() - (86400 * 30), '/'); // 86400 = 1 day
    session_unset();
    session_destroy();
    header("Location: $settings[url]");
} else {
    $redirect = $settings['url']."account/dashboard";
    header("Location: $redirect");
}
$tpl = new Template("app/templates/".$settings['default_template']."/account/footer.html",$lang);
$tpl->set("url",$settings['url']);
$tpl->set("name",$settings['name']);
echo $tpl->output();
?>