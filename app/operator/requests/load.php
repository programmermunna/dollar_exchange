<?php
define('CryptExchanger_INSTALLED',TRUE);
ob_start();
session_start();
error_reporting(0);
include("../../configs/bootstrap.php");
include("../../includes/bootstrap.php");
$a = protect($_GET['a']);
$data = array();
if($a == "rate") {
    $gateway_send = protect($_GET['from']);
    $gateway_receive = protect($_GET['to']);
    $fee = protect($_GET['fee']);
    if(empty($gateway_send) or empty($gateway_receive) or empty($fee)) {
        $data['status'] = 'error';
        $data['msg'] = 'All fields are required.';
    } elseif(!is_numeric($fee)) {
        $data['status'] = 'error';
        $data['msg'] = 'Please enter fee with numbers.'; 
    } else {
            $gateway_sendname = gatewayinfo($gateway_send,"name");
            $gateway_receivename = gatewayinfo($gateway_receive,"name");
            $data['status'] = 'success';
            $rate_from = 0;
            $rate_to = 0;
            $currency_from = gatewayinfo($gateway_send,"currency");
            $currency_to = gatewayinfo($gateway_receive,"currency");
              
                    
                    if($currency_from == $currency_to) {
                        $fee = str_ireplace("-","",$fee);
                        $calculate1 = (1 * $fee) / 100;
                        $calculate2 = 1 - $calculate1;
                        $data['status'] = 'success';
                        $rate_from = 1;
                        $rate_to = $calculate2;
                    } else {
                        if(gatewayinfo($gateway_send,"is_crypto") == "1" && gatewayinfo($gateway_receive,"is_crypto") == "1") {
                            $price = getCrypto2CryptoPrice($currency_from,$currency_to);
                            $calculate1 = ($price * $fee) / 100;
                            $calculate2 = $price - $calculate1;
                            $calculate2 = number_format($calculate2, 6, '.', '');
                            $data['status'] = 'success';
                            $rate_from = 1;
                            $rate_to = $calculate2;
                        } elseif(gatewayinfo($gateway_send,"is_crypto") == "1" && gatewayinfo($gateway_receive,"is_crypto") == "0") {
                            $price = getCryptoPrice($currency_from);
                            if($currency_to == "USD" && gatewayinfo($gateway_send,"is_crypto") == "1") {
                                $price = $price;
                            } else {
                                $price = currencyConvertor($price,"USD",$currency_to);
                            }
                            $calculate1 = ($price * $fee) / 100;
                            $calculate2 = $price - $calculate1;
                            $calculate2 = number_format($calculate2, 2, '.', '');
                            $data['status'] = 'success';
                            $rate_to = $calculate2;
                            $rate_from = 1;
                        } elseif(gatewayinfo($gateway_send,"is_crypto") == "0" && gatewayinfo($gateway_receive,"is_crypto") == "1") {
                            $price = getCryptoPrice($currency_to);
                            $fee = '-'.$fee;
                            if($currency_from == "USD" && gatewayinfo($gateway_receive,"is_crypto") == "1") {
                                $price = $price;
                            } else {
                                $price = currencyConvertor($price,"USD",$currency_from);
                            }
                            $calculate1 = ($price * $fee) / 100;
                            $calculate2 = $price - $calculate1;
                            $calculate2 = number_format($calculate2, 2, '.', '');
                            $data['status'] = 'success';
                            $rate_to = 1;
                            $rate_from = $calculate2;
                        } else {
                            $fee = '-'.$fee;
                            $rate_from = 1;
                            $calculate = currencyConvertor($rate_from,$currency_from,$currency_to);
                            $calculate1 = ($calculate * $fee) / 100;
                            $calculate2 = $calculate - $calculate1;
                            if($calculate2 < 1) { 
                                $calculate = currencyConvertor($rate_from,$currency_to,$currency_from);
                                $calculate1 = ($calculate * $fee) / 100;
                                $calculate2 = $calculate - $calculate1;
                                $rate_from = number_format($calculate2, 2, '.', '');
                                $rate_to = 1;
                            } else {
                                $rate_to = number_format($calculate2, 2, '.', '');
                            }
                        }
                    }
                 
                
            $data['rate_from'] = $rate_from; 
            $data['rate_to'] = $rate_to;
            $data['currency_from'] = $currency_from;
            $data['currency_to'] = $currency_to;
    }
} else {
    $data['status'] = 'error';
    $data['msg'] = 'Error loading request.';
}
echo json_encode($data);
?>