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
$order_id = protect($_GET['order_id']);
$timenow = time();
$created = orderinfo($order_id,"created")+1800;
if($timenow > $created) {
    $update = $db->query("UPDATE ce_orders SET status='6',expired='$timenow' WHERE id='$order_id'");
    $data['status'] = 'error';
    $data['timeout'] = '1';
    $data['html'] = '<i class="fa fa-times"></i> '.$lang[session_expired];
} else {
    if($a == "coinpayments") {
        $query = $db->query("SELECT * FROM ce_orders WHERE id='$order_id'");
        if($query->num_rows>0) {
            $row = $query->fetch_assoc();
                if($row['status'] == "2") {
                    $data['status'] = 'error';
                    $data['msg'] = '<i class="fa fa-check"></i> '.$lang[payment_received_but_awaiting].'<br/>'.$lang[when_payment_is_confirmed];
                } elseif($row['status'] == "3") {
                    $data['status'] = 'success';
                    $data['html'] = '<i class="fa fa-check"></i> '.$lang[payment_received_and_confirmed].'<br/>'.$lang[order_in_process];
                } else {
                    $data['status'] = 'error';
                    $data['msg'] = '<i class="fa fa-spin fa-spinner"></i> '.$lang[awaiting_payment].'...';
                }
            }
    } elseif($a == "blockchain") {
        $query = $db->query("SELECT * FROM ce_orders WHERE id='$order_id'");
        if($query->num_rows>0) {
            $row = $query->fetch_assoc();
                if($row['status'] == "2") {
                    $data['status'] = 'error';
                    $data['msg'] = '<i class="fa fa-check"></i> '.$lang[payment_received_but_awaiting].'<br/>'.$lang[when_payment_is_confirmed];
                } elseif($row['status'] == "3") {
                    $data['status'] = 'success';
                    $data['html'] = '<i class="fa fa-check"></i> '.$lang[payment_received_and_confirmed].'<br/>'.$lang[order_in_process];
                } else {
                    $data['status'] = 'error';
                    $data['msg'] = '<i class="fa fa-spin fa-spinner"></i> '.$lang[awaiting_payment].'...';
                }
            }
    } elseif($a == "blockio") {
                $blockio_api_key = gatewayinfo(orderinfo($order_id,"gateway_send"),"g_field_1");
                $blockio_secret = gatewayinfo(orderinfo($order_id,"gateway_send"),"g_field_2");	
                $option = gatewayinfo(orderinfo($order_id,"gateway_send"),"g_field_3");
                if($option == "1") {
                    $address = gatewayinfo(orderinfo($order_id,"gateway_send"),"g_field_4");
                    $apiKey = $blockio_api_key;
                    $pin = $blockio_secret;
                    $version = 2; // the API version
                    $block_io = new BlockIo($apiKey, $pin, $version);
                    $transactions = $block_io->get_transactions(array('type' => 'received', 'addresses' => $address));
                    $tx = $transactions->data->txs;
                    $tx = json_decode(json_encode($tx), true);
                    $searched_amount = orderinfo($order_id,"amount_send");
                    $before_confirms = '5';
                    $famount = '';
                    $ftxid = '';
                    $fconfs = '';
                    foreach($tx as $k => $v) {
                        $txs['id'] = $k;
                        foreach($v as $a => $b) {
                            $txs[$k][$a] = $b;
                            if($a == 'amounts_received') {
                                foreach($b[0] as $c => $d) {
                                    $txs[$k][$c] = $d;
                                    if($c == "amount") {
                                        if($d == $searched_amount) {
                                            $famount = $d;
                                            $ftxid = $txs[$k]['txid'];
                                            $fconfs = $txs[$k]['confirmations'];
                                        }
                                    }
                                }	
                            }
                        }
                    }
                    if(!empty($famount) && !empty($ftxid)) {
                        if($fconfs < $before_confirms) {
                            //echo 'Transaction found!<br/>TXID: '.$ftxid.'<br/>Amount: '.$famount.'<br/>Confirmations: '.$fconfs;
                            $data['status'] = 'success';
                            $data['html'] = '<i class="fa fa-check"></i> '.$lang[payment_received_and_confirmed].'<br/>'.$lang[order_in_process];
                            $time = time();
                            $update = $db->query("UPDATE ce_orders SET status='3',updated='$time',transaction_send='$ftxid' WHERE id='$order_id'");
                        }
                    } else {
                        $data['status'] = 'error';
                        $data['msg'] = '<i class="fa fa-spin fa-spinner"></i> '.$lang[awaiting_payment].'...';
                    }
                } elseif($option == "2") {
                    $address = orderinfo($order_id,"u_field_10");
                    $apiKey = $blockio_api_key;
                    $pin = $blockio_secret;
                    $version = 2; // the API version
                    $block_io = new BlockIo($apiKey, $pin, $version);
                    $transactions = $block_io->get_transactions(array('type' => 'received', 'addresses' => $address));
                    $tx = $transactions->data->txs;
                    $tx = $tx[0];
                    $txid = $tx->txid;
                    $amount = $tx->amounts_received;
                    $amount = $amount[0];
                    $amount = $amount->amount;
                    if(orderinfo($order_id,"amount_send") == $amount) {
                        $data['status'] = 'success';
                            $data['html'] = '<i class="fa fa-check"></i> '.$lang[payment_received_and_confirmed].'<br/>'.$lang[order_in_process];
                            $time = time();
                            $update = $db->query("UPDATE ce_orders SET status='3',updated='$time',transaction_send='$ftxid' WHERE id='$order_id'");
                    } else {
                        $data['status'] = 'error';
                        $data['msg'] = '<i class="fa fa-spin fa-spinner"></i> '.$lang[awaiting_payment].'...';
                    }
                } else {
                    $data['status'] = 'error';
                    $data['msg'] = 'Error load information'; 
                }
    }
}
echo json_encode($data);
?>