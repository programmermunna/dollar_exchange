<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

function ce_PaymentForm($order_id) {
    global $db;
    $gname = gatewayinfo(orderinfo($order_id,"gateway_send"),"name");
    if($gname == "PayPal") { return ce_PF_PayPal($order_id); }
    elseif($gname == "Stripe") { return ce_PF_Stripe($order_id); }
    elseif($gname == "2checkout") { return ce_PF_2checkout($order_id); }
    elseif($gname == "Paytm") { return ce_PF_Paytm($order_id); }
    elseif($gname == "Paysera") { return ce_PF_Paysera($order_id); }
    elseif($gname == "Yandex Money") { return ce_PF_YandexMoney($order_id); }
    elseif($gname == "AdvCash") { return ce_PF_AdvCash($order_id); }
    elseif($gname == "Entromoney") { return ce_PF_Entromoney($order_id); }
    elseif($gname == "Payeer") { return ce_PF_Payeer($order_id); } 
    elseif($gname == "Perfect Money") { return ce_PF_PerfectMoney($order_id); }
    elseif($gname == "Skrill") { return ce_PF_Skrill($order_id); } 
    elseif($gname == "SolidTrust Pay") { return ce_PF_SolidTrustPay($order_id); }
    elseif($gname == "WebMoney") { return ce_PF_WebMoney($order_id); }
    else {
        return 'Unsupported automatic payment gateway.';
    }
} 

function ce_PF_PayPal($order_id) {
    global $db, $settings;
    require("payment_src/paypal_class.php");
    $exchange_id = $order_id;
	define('EMAIL_ADD', gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1")); // For system notification.
	define('PAYPAL_EMAIL_ADD', gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1"));
	
	// Setup class
	$p = new paypal_class( ); 				 // initiate an instance of the class.
	$p -> admin_mail = EMAIL_ADD; 
	$amount = orderinfo($exchange_id,"amount_send");
	if(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"include_fee") == "1") {
		if (strpos(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
		} else {
			$amount = $amount + gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee");
		}
	}
	$currency = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"currency");
	$amount_r = orderinfo($exchange_id,"amount_receive");
	$currency_r = gatewayinfo(orderinfo($exchange_id,"gateway_receive"),"currency");
	$user = orderinfo($exchange_id,"u_field_2");
	$id = orderinfo($exchange_id,"id");
	$payment_note = 'Order #'.$exchange_id;
	$p->add_field('business', PAYPAL_EMAIL_ADD); //don't need add this item. if y set the $p -> paypal_mail.
	$p->add_field('return', $url.'payment/'.$id.'/success');
	$p->add_field('cancel_return', $url.'payment/'.$id.'/fail');
	$p->add_field('notify_url', $url.'callbacks/PayPal_IPN.php');
	$p->add_field('item_name', $payment_note);
	$p->add_field('item_number', $id);
	$p->add_field('amount', $amount);
	$p->add_field('currency_code', $currency);
	$p->add_field('cmd', '_xclick');
	$p->add_field('rm', '2');	// Return method = POST
						 
	$return = $p->submit_paypal_post(); // submit the fields to paypal
	$return .= '<script type="text/javascript" src="'.$settings[url].'app/templates/CryptoExchanger/assets/js/jquery-1.10.2.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#paypal_form").submit(); });</script>';
	return $return;
}

function ce_PF_Stripe($order_id) {
	global $db, $settings;
	$exchange_id = $order_id;
    define('STRIPE_API_KEY', gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_2")); 
	define('STRIPE_PUBLISHABLE_KEY', gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1")); 
	$amount = orderinfo($exchange_id,"amount_send");
	if(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"include_fee") == "1") {
		if (strpos(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
		} else {
			$amount = $amount + gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee");
		}
	}
	$currency = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"currency");
	$amount_r = orderinfo($exchange_id,"amount_receive");
	$currency_r = gatewayinfo(orderinfo($exchange_id,"gateway_receive"),"currency");
	$user = orderinfo($exchange_id,"u_field_2");
	$id = orderinfo($exchange_id,"id");
	$payment_note = 'Order #'.$exchange_id;
	$productName = $payment_note; 
	$productNumber = $id; 
	$productPrice = $amount;
	$currency = strtolower($currency);
		// Convert product price to cent
	$stripeAmount = round($productPrice*100, 2);
	$_SESSION['ce_stripe_productName'] = $productName;
	$_SESSION['ce_stripe_productNumber'] = $productNumber;
	$_SESSION['ce_stripe_productPrice'] = $productPrice;
	$_SESSION['ce_stripe_currency'] = $currency;
	$_SESSION['ce_stripe_stripeAmount'] = $stripeAmount;
	$return .= '<script type="text/javascript" src="'.$settings[url].'app/templates/CryptoExchanger/assets/js/jquery-1.10.2.js"></script>';
	$return .= '<script src="https://checkout.stripe.com/checkout.js"></script>';
	$return .= '<div id="buynow">
	<button class="stripe-button" id="payButton">Pay Now</button>
	<input type="hidden" id="payProcess" value="0"/>
</div>';
	$return .= '<script>
	var handler = StripeCheckout.configure({
		key: "'.STRIPE_PUBLISHABLE_KEY.'",
		image: "'.$settings[url].'app/templates/CryptoExchanger/assets/img/site_logo_2.png",
		locale: "auto",
		token: function(token) {
			// You can access the token ID with `token.id`.
			// Get the token ID to your server-side code for use.
			
			$("#paymentDetails").hide();
			$("#payProcess").val(1);
			$.ajax({
				url: "'.$settings[url].'callbacks/Stripe_IPN.php",
				type: "POST",
				data: {stripeToken: token.id, stripeEmail: token.email},
				dataType: "json",
				beforeSend: function(){
					$("body").prepend("<div class=\'overlay\'></div>");
					$("#payButton").prop("disabled", true);
					$("#payButton").html("Please wait...");
				},
				success: function(data){
					$(".overlay").remove();
					$("#payProcess").val(0);
					if(data.status == 1){
						var paidAmount = (data.txnData.amount/100);
						$("#buynow").hide();
						$(location).attr("href", "'.$settings[url].'payment/success");
					}else{
						$("#payButton").prop("disabled", false);
						$("#payButton").html("Pay Now");
						alert("Some problem occurred, please try again.");
					}
				},
				error: function() {
					$("#payProcess").val(0);
					$("#payButton").prop("disabled", false);
					$("#payButton").html("Pay Now");
					alert("Some problem occurred, please try again.");
				}
			});
		}
	});
	
	var stripe_closed = function(){
		var processing = $("#payProcess").val();
		if (processing == 0){
			$("#payButton").prop("disabled", false);
			$("#payButton").html("Buy Now");
		}
	};
	
	var eventTggr = document.getElementById("payButton");
	if(eventTggr){
		eventTggr.addEventListener("click", function(e) {
			$("#payButton").prop("disabled", true);
			$("#payButton").html("Please wait...");
			
			// Open Checkout with further options:
			handler.open({
				name: "'.$settings[name].'",
				description: "'.$productName.'",
				amount: "'.$stripeAmount.'",
				currency: "'.$currency.'",
				closed:	stripe_closed
			});
			e.preventDefault();
		});
	}
	
	// Close Checkout on page navigation:
	window.addEventListener("popstate", function() {
		handler.close();
	});
	</script>';
	return $return;
}

function ce_PF_Paytm($order_id) {
	global $db, $settings;
	include("payment_src/encdec_paytm.php");
    $exchange_id = $order_id;
	define('PAYTM_ENVIRONMENT', 'PROD'); // PROD
    define('PAYTM_MERCHANT_KEY',  gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1")); //Change this constant's value with Merchant key received from Paytm.
    define('PAYTM_MERCHANT_MID', gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_2")); //Change this constant's value with MID (Merchant ID) received from Paytm.
    define('PAYTM_MERCHANT_WEBSITE',  gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_3")); //Change this constant's value with Website name received from Paytm.
    $PAYTM_STATUS_QUERY_NEW_URL='https://securegw-stage.paytm.in/order/status';
    $PAYTM_TXN_URL='https://securegw-stage.paytm.in/order/process';
    if (PAYTM_ENVIRONMENT == 'PROD') {
    	$PAYTM_STATUS_QUERY_NEW_URL='https://securegw.paytm.in/order/status';
    	$PAYTM_TXN_URL='https://securegw.paytm.in/order/process';
    }
    define('PAYTM_REFUND_URL', '');
    define('PAYTM_STATUS_QUERY_URL', $PAYTM_STATUS_QUERY_NEW_URL);
    define('PAYTM_STATUS_QUERY_NEW_URL', $PAYTM_STATUS_QUERY_NEW_URL);
    define('PAYTM_TXN_URL', $PAYTM_TXN_URL);
	$amount = orderinfo($exchange_id,"amount_send");
	if(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"include_fee") == "1") {
		if (strpos(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
		} else {
			$amount = $amount + gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee");
		}
	}
	$currency = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"currency");
	$amount_r = orderinfo($exchange_id,"amount_receive");
	$currency_r = gatewayinfo(orderinfo($exchange_id,"gateway_receive"),"currency");
	$user = orderinfo($exchange_id,"u_field_2");
	$id = orderinfo($exchange_id,"id");
	$payment_note = 'Order #'.$exchange_id;
	$checkSum = "";
    $paramList = array();
    $ORDER_ID = $exchange_id;
    $CUST_ID = orderinfo($exchange_id,"uid");
    $INDUSTRY_TYPE_ID = 'Retail';
    $CHANNEL_ID = 'WEB';
    $TXN_AMOUNT = $amount;
    
    // Create an array having all required parameters for creating checksum.
    $paramList["MID"] = PAYTM_MERCHANT_MID;
    $paramList["ORDER_ID"] = (int) $ORDER_ID;
    $paramList["CUST_ID"] = (int) $CUST_ID;
    $paramList["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID;
    $paramList["CHANNEL_ID"] = $CHANNEL_ID;
    $paramList["TXN_AMOUNT"] = (int) $TXN_AMOUNT;
    $paramList["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;
    $paramList["CALLBACK_URL"] = $settings['url']."callbacks/Paytm_IPN.php";
    $return = '<form method="post" action="'.PAYTM_TXN_URL.'" name="f1" id="paytm_form">';
    foreach($paramList as $name => $value) {
				$return .= '<input type="hidden" name="' . $name .'" value="' . $value . '">';
			}
 	$checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);
 	$return .= '<input type="hidden" name="CHECKSUMHASH" value="'.$checkSum.'"></form>';
    //Here checksum string will return by getChecksumFromArray() function.
	$return .= '<script type="text/javascript" src="'.$settings[url].'app/templates/CryptoExchanger/assets/js/jquery-1.10.2.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#paytm_form").submit(); });</script>';
	return $return;
}

function ce_PF_AdvCash($order_id) {
	global $db, $settings;
	$exchange_id = $order_id;
	$merchant = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1");
	$secret = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_2");
	$amount = orderinfo($exchange_id,"amount_send");
	if(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"include_fee") == "1") {
		if (strpos(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
		} else {
			$amount = $amount + gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee");
		}
	}
	$currency = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"currency");
	$amount_r = orderinfo($exchange_id,"amount_receive");
	$currency_r = gatewayinfo(orderinfo($exchange_id,"gateway_receive"),"currency");
	$user = orderinfo($exchange_id,"u_field_2");
	$id = orderinfo($exchange_id,"id");
	$payment_note = 'Order #'.$exchange_id;
	$arHash = array(
			$merchant,
			$settings[name],
			$amount,
			$currency,
			$secret,
			$id
		);
	$sign = strtoupper(hash('sha256', implode(':', $arHash)));
	$return = '<div style="display:none;">
					<form method="GET" id="advcash_form" action="https://wallet.advcash.com/sci/">
					<input type="hidden" name="ac_account_email" value="'.$merchant.'">
					<input type="hidden" name="ac_sci_name" value="'.$settings[name].'">
					<input type="hidden" name="ac_amount" value="'.$amount.'">
					<input type="hidden" name="ac_currency" value="'.$currency.'">
					<input type="hidden" name="ac_order_id" value="'.$id.'">
					<input type="hidden" name="ac_sign" value="'.$sign.'">
					<input type="hidden" name="ac_success_url" value="'.$settings[url].'payment/'.$id.'/success" />
					 <input type="hidden" name="ac_success_url_method" value="GET" />
					 <input type="hidden" name="ac_fail_url" value="'.$settings[url].'payment/'.$id.'/fail" />
					 <input type="hidden" name="ac_fail_url_method" value="GET" />
					 <input type="hidden" name="ac_status_url" value="'.$settings[url].'callbacks/AdvCash_IPN.php" />
					 <input type="hidden" name="ac_status_url_method" value="GET" />
					<input type="hidden" name="ac_comments" value="'.$payment_note.'">
					</form>
					</div>';
	$return .= '<script type="text/javascript" src="'.$settings[url].'app/templates/CryptoExchanger/assets/js/jquery-1.10.2.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#advcash_form").submit(); });</script>';
	return $return;
}

function ce_PF_Entromoney($order_id) {
	global $db, $settings;
	$exchange_id = $order_id;
	$merchant = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1");
	$secret = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_2");
	$amount = orderinfo($exchange_id,"amount_send");
	if(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"include_fee") == "1") {
		if (strpos(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
		} else {
			$amount = $amount + gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee");
		}
	}
	$currency = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"currency");
	$amount_r = orderinfo($exchange_id,"amount_receive");
	$currency_r = gatewayinfo(orderinfo($exchange_id,"gateway_receive"),"currency");
	$user = orderinfo($exchange_id,"u_field_2");
	$id = orderinfo($exchange_id,"id");
	$payment_note = 'Order #'.$exchange_id;
	require("payment_src/entromoney.php");
	$config = array();
	$config['sci_user'] = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1");
	$config['sci_id'] 	= gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_2");
	$config['sci_pass'] = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_3");
	$config['receiver'] = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_4");

	// Call lib sci
	try {
		$sci = new Paygate_Sci($config);
	}
	catch (Paygate_Exception $e) {
		exit($e->getMessage());
	}
	$return = '';
	$input = array();
	$input['sci_user'] 		= $config['sci_user'];
	$input['sci_id'] 		= $config['sci_id'];
	$input['receiver'] 		= $config['receiver'];
	$input['amount'] 		= $amount;
	$input['desc'] 			= $payment_note;
	$input['payment_id'] 	= $id;
	$input['up_1'] 			= 'user_param_1';
	$input['up_2'] 			= 'user_param_2';
	$input['up_3'] 			= 'user_param_3';
	$input['up_4'] 			= 'user_param_4';
	$input['up_5'] 			= 'user_param_5';
	$input['url_status'] 	= $settings[url].'callbacks/Entromoney_IPN.php';
	$input['url_success'] 	= $settings[url].'payment/'.$id.'/success';
	$input['url_fail'] 		= $settings[url].'payment/'.$id.'/fail';

	// Create hash
	$input['hash']			= $sci->create_hash($input);
	$return = '<form action="'.Paygate_Sci::URL_SCI.'" id="entromoney_form" method="post">';
	foreach ($input as $p => $v) {
		$return .= '<input type="hidden" name="'.$p.'" value="'.$v.'">';
	}
	$return .= '</form>';
	$return .= '<script type="text/javascript" src="'.$settings[url].'app/templates/CryptoExchanger/assets/js/jquery-1.10.2.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#entromoney_form").submit(); });</script>';
	return $return;
}

function ce_PF_Payeer($order_id) {
	global $db, $settings;
	$exchange_id = $order_id;
	$merchant = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1");
	$secret = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_2");
	$amount = orderinfo($exchange_id,"amount_send");
	if(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"include_fee") == "1") {
		if (strpos(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
		} else {
			$amount = $amount + gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee");
		}
	}
	$currency = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"currency");
	$amount_r = orderinfo($exchange_id,"amount_receive");
	$currency_r = gatewayinfo(orderinfo($exchange_id,"gateway_receive"),"currency");
	$user = orderinfo($exchange_id,"u_field_2");
	$id = orderinfo($exchange_id,"id");
	$payment_note = 'Order #'.$exchange_id;
	$m_shop = $merchant;
	$m_orderid = $id;
	$m_amount = number_format($amount, 2, '.', '');
	$m_curr = $currency;
	$desc = $payment_note;
	$m_desc = base64_encode($desc);
	$m_key = $secret;

	$arHash = array(
			$m_shop,
			$m_orderid,
			$m_amount,
			$m_curr,
			$m_desc,
			$m_key
		);
	$sign = strtoupper(hash('sha256', implode(':', $arHash)));
	$return = '<div style="display:none;"><form method="GET" id="payeer_form" action="https://payeer.com/merchant/">
		<input type="hidden" name="m_shop" value="'.$m_shop.'">
		<input type="hidden" name="m_orderid" value="'.$m_orderid.'">
		<input type="hidden" name="m_amount" value="'.$m_amount.'">
		<input type="hidden" name="m_curr" value="'.$m_curr.'">
		<input type="hidden" name="m_desc" value="'.$m_desc.'">
		<input type="hidden" name="m_sign" value="'.$sign.'">
		<!--
		<input type="hidden" name="form[ps]" value="2609">
		<input type="hidden" name="form[curr[2609]]" value="USD">
		-->
		<input type="submit" name="m_process" value="Pay with Payeer" />
		</form></div>';
	$return .= '<script type="text/javascript" src="'.$settings[url].'app/templates/CryptoExchanger/assets/js/jquery-1.10.2.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#payeer_form").submit(); });</script>';
	return $return;
}

function ce_PF_PerfectMoney($order_id) {
	global $db, $settings;
	$exchange_id = $order_id;
	$merchant = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1");
	$amount = orderinfo($exchange_id,"amount_send");
	if(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"include_fee") == "1") {
		if (strpos(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
		} else {
			$amount = $amount + gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee");
		}
	}
	$currency = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"currency");
	$amount_r = orderinfo($exchange_id,"amount_receive");
	$currency_r = gatewayinfo(orderinfo($exchange_id,"gateway_receive"),"currency");
	$user = orderinfo($exchange_id,"u_field_2");
	$id = orderinfo($exchange_id,"id");
	$payment_note = 'Order #'.$exchange_id;
	$return = '<div style="display:none;">
				<form action="https://perfectmoney.is/api/step1.asp" id="pm_form" method="POST">
					<input type="hidden" name="PAYEE_ACCOUNT" value="'.$merchant.'">
					<input type="hidden" name="PAYEE_NAME" value="'.$settings[name].'">
					<input type="hidden" name="PAYMENT_ID" value="'.$id.'">
					<input type="text"   name="PAYMENT_AMOUNT" value="'.$amount.'"><BR>
					<input type="hidden" name="PAYMENT_UNITS" value="'.$currency.'">
					<input type="hidden" name="STATUS_URL" value="'.$settings[url].'callbacks/PerfectMoney_IPN.php">
					<input type="hidden" name="PAYMENT_URL" value="'.$settings[url].'payment/'.$id.'/success">
					<input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
					<input type="hidden" name="NOPAYMENT_URL" value="'.$settings[url].'payment/'.$id.'/fail">
					<input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST">
					<input type="hidden" name="SUGGESTED_MEMO" value="'.$payment_note.'">
					<input type="hidden" name="BAGGAGE_FIELDS" value="IDENT"><br>
					<input type="submit" name="PAYMENT_METHOD" value="Pay Now!" class="tabeladugme"><br><br>
					</form></div>';
	$return .= '<script type="text/javascript" src="'.$settings[url].'app/templates/CryptoExchanger/assets/js/jquery-1.10.2.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#pm_form").submit(); });</script>';
	return $return;	
}

function ce_PF_Skrill($order_id) {
	global $db, $settings;
	$exchange_id = $order_id;
	$merchant = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1");
	$amount = orderinfo($exchange_id,"amount_send");
	if(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"include_fee") == "1") {
		if (strpos(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
		} else {
			$amount = $amount + gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee");
		}
	}
	$currency = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"currency");
	$amount_r = orderinfo($exchange_id,"amount_receive");
	$currency_r = gatewayinfo(orderinfo($exchange_id,"gateway_receive"),"currency");
	$user = orderinfo($exchange_id,"u_field_2");
	$id = orderinfo($exchange_id,"id");
	$payment_note = 'Order #'.$exchange_id;
	$return = '<div style="display:none;"><form action="https://www.moneybookers.com/app/payment.pl" method="post" id="skrill_form">
					  <input type="hidden" name="pay_to_email" value="'.$merchant.'"/>
					  <input type="hidden" name="status_url" value="'.$settings[url].'callbacks/Skrill_IPN.php"/> 
					  <input type="hidden" name="language" value="EN"/>
					  <input type="hidden" name="amount" value="'.$amount.'"/>
					  <input type="hidden" name="currency" value="'.$currency.'"/>
					  <input type="hidden" name="detail1_description" value="'.$payment_note.'"/>
					  <input type="hidden" name="detail1_text" value="'.$id.'"/>
					  <input type="submit" class="btn btn-primary" value="Click to pay."/>
					</form></div>';
	$return .= '<script type="text/javascript" src="'.$settings[url].'app/templates/CryptoExchanger/assets/js/jquery-1.10.2.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#skrill_form").submit(); });</script>';
	return $return;
}

function ce_PF_SolidTrustPay($order_id) {
	global $db, $settings;
	$exchange_id = $order_id;
	$merchant = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1");
	$sci_name = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_2");
	$amount = orderinfo($exchange_id,"amount_send");
	if(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"include_fee") == "1") {
		if (strpos(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
		} else {
			$amount = $amount + gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee");
		}
	}
	$currency = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"currency");
	$amount_r = orderinfo($exchange_id,"amount_receive");
	$currency_r = gatewayinfo(orderinfo($exchange_id,"gateway_receive"),"currency");
	$user = orderinfo($exchange_id,"u_field_2");
	$id = orderinfo($exchange_id,"id");
	$payment_note = 'Order #'.$exchange_id;
	$return = ' <form action="https://solidtrustpay.com/handle.php" method="post" id="solid_form">
						<input type=hidden name="merchantAccount" value="'.$merchant.'" />
						<input type="hidden" name="sci_name" value="'.$sci_name.'">
						<input type="hidden" name="amount" value="'.$amount.'">
						<input type=hidden name="currency" value="'.$currency.'" />
						 <input type="hidden" name="notify_url" value="'.$settings[url].'callbacks/SolidTrustPay_IPN.php">
						  <input type="hidden" name="confirm_url" value="'.$settings[url].'callbacks/SolidTrustPay_IPN.php>
						   <input type="hidden" name="return_url" value="'.$settings[url].'payment/'.$id.'/success">
						<input type=hidden name="item_id" value="'.$payment_note.'" />
						<input type=hidden name="user1" value="'.$id.'" />
					  </form>';
	$return .= '<script type="text/javascript" src="'.$settings[url].'app/templates/CryptoExchanger/assets/js/jquery-1.10.2.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#solid_form").submit(); });</script>';
	return $return;
}

function ce_PF_WebMoney($order_id) {
	global $db, $settings;
	$exchange_id = $order_id;
	require("payment_src/webmoney.inc.php");
	$merchant = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"g_field_1");
	$amount = orderinfo($exchange_id,"amount_send");
	if(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"include_fee") == "1") {
		if (strpos(gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
		} else {
			$amount = $amount + gatewayinfo(orderinfo($exchange_id,"gateway_send"),"extra_fee");
		}
	}
	$currency = gatewayinfo(orderinfo($exchange_id,"gateway_send"),"currency");
	$amount_r = orderinfo($exchange_id,"amount_receive");
	$currency_r = gatewayinfo(orderinfo($exchange_id,"gateway_receive"),"currency");
	$user = orderinfo($exchange_id,"u_field_2");
	$id = orderinfo($exchange_id,"id");
	$payment_note = 'Order #'.$exchange_id;
	$paymentno = intval($id);
	$wm_request = new WM_Request();
	$wm_request->payment_amount = $amount;
	$wm_request->payment_desc = $payment_note;
	$wm_request->payment_no = $paymentno;
	$wm_request->payee_purse = $merchant;
	$wm_request->sim_mode = WM_ALL_SUCCESS;
	$wm_request->result_url = $settings['url']."callbacks/WebMoney_IPN.php";
	$wm_request->success_url = $settings['url']."payment/".$id."/success";
	$wm_request->success_method = WM_POST;
	$wm_request->fail_url = $settings['url']."payment/".$id."/fail";
	$wm_request->fail_method = WM_POST;
	$wm_request->extra_fields = array('FIELD1'=>'VALUE 1', 'FIELD2'=>'VALUE 2');
	$wm_action = 'https://merchant.wmtransfer.com/lmi/payment.asp';
	$wm_btn_label = 'Pay Webmoney';
	$return = $wm_request->SetForm();
	$return .= '<script type="text/javascript" src="'.$settings[url].'app/templates/CryptoExchanger/assets/js/jquery-1.10.2.js"></script>';
	$return .= '<script type="text/javascript">$(document).ready(function() { $("#webmoney_form").submit(); });</script>';
	return $return;
}

function ce_PF_CoinPayments($order_id) {
    global $db, $settings;
}

function CP_CreateOrder($gateway_id,$order_id) {
    global $db, $settings;
    require_once("payment_src/coinpayments.inc.php");
    $cps = new CoinPaymentsAPI();
	$cps->Setup(gatewayinfo($gateway_id,"g_field_2"), gatewayinfo($gateway_id,"g_field_1"));
    $data = array();
    $item_name = 'Order: '.$order_id;
    $callback_url = $settings['url']."callbacks/CoinPayments_IPN.php?order_id=".$order_id;
	$req = array(
		'amount' => orderinfo($order_id,"amount_send"),
		'currency1' => orderinfo($order_id,"currency_from"),
		'currency2' => orderinfo($order_id,"currency_from"),
		'buyer_email' => orderinfo($order_id,"u_field_1"),
		'item_name' => $item_name,
		'address' => '', // leave blank send to follow your settings on the Coin Settings page
		'ipn_url' => $callback_url,
	);
	// See https://www.coinpayments.net/apidoc-create-transaction for all of the available fields
			
	$result = $cps->CreateTransaction($req);
	if ($result['error'] == 'ok') {
        $data['status'] = 'success';
		$le = php_sapi_name() == 'cli' ? "\n" : '<br />';
		//print 'Transaction created with ID: '.$result['result']['txn_id'].$le;
		//print 'Buyer should send '.sprintf('%.08f', $result['result']['amount']).' BTC'.$le;
        //print 'Status URL: '.$result['result']['status_url'].$le;
        $data['cp_txid'] = $result['result']['txn_id'];
        $data['qrcode'] = $result['result']['qrcode_url'];
        $data['address'] = $result['result']['address'];
        $data['confirms_need'] = $result['result']['confirms_needed'];
	} else {
        $data['status'] = 'error';
		$data['msg'] = 'Error: '.$result['error']."\n";
    }
    return $data;
}

function getManualForm($exchange_id) {
	global $db, $settings, $lang;
	$gateway_id = orderinfo($exchange_id,"gateway_send");
	$gateway = gatewayinfo($gateway_id,"name");
	$amount = orderinfo($exchange_id,"amount_send");
	$currency = gatewayinfo($gateway_id,"currency");
	if(gatewayinfo($gateway_id,"include_fee") == "1") {
		if (strpos(gatewayinfo($gateway_id,"extra_fee"),'%') !== false) { 
			$amount = $amount;
			$explode = explode("%",gatewayinfo($gateway_id,"extra_fee"));
			$fee_percent = 100+$explode[0];
			$new_amount = ($amount * 100) / $fee_percent;
			$new_amount = round($new_amount,2);
			$fee_amount = $amount-$new_amount;
			$amount = $amount+$fee_amount;
			$fee_text = gatewayinfo($gateway_id,"extra_fee");
		} else {
			$amount = $amount + gatewayinfo($gateway_id,"extra_fee");
			$fee_text = gatewayinfo($gateway_id,"extra_fee")." ".gatewayinfo($gateway_id,"currency");
		}
		$currency = gatewayinfo($gateway_id,"currency");
	} else {
		$amount = $amount;
		$currency = gatewayinfo($gateway_id,"currency");
		$fee_text = '0';
	}
	if(gatewayinfo($gateway_id,"is_crypto")) { $acc_addr = $lang['address']; } else { $acc_addr = $lang['account']; }
	if(gatewayinfo($gateway_id,"external_gateway") == "1" or gatewayinfo($gateway_id,"manual_payment") == "1") {
		$form = $lang[send].' <b>'.$amount.' '.$currency.'</b> '.$lang[to].' '.$gateway.' to:<br/>';
		$check = $db->query("SELECT * FROM ce_gateways WHERE name='$gateway' and external_gateway='1' or name='$gateway' and manual_payment='1'");
		if($check->num_rows>0) {
			$r = $check->fetch_assoc();
			$fieldsquery = $db->query("SELECT * FROM ce_gateways_fields WHERE gateway_id='$r[id]' ORDER BY id");
			if($fieldsquery->num_rows>0) {
				while($field = $fieldsquery->fetch_assoc()) {
					$field_number = $field['field_number'];
					$field_id = 'g_field_'.$field_number;
					$field_value = gatewayinfo($r['id'],$field_id);
					$form .= $field['field_name'].": ".$field_value."<br/>";
				}
			} else {
				$form = $lang[send].' <b>'.$amount.' '.$currency.'</b> '.$lang[to].' '.$gateway.' '.$acc_addr.': <b>'.gatewayinfo($gateway_id,"g_field_1").'</b>';
			}
		}
		return $form;
	} else {
		$form = $lang[send].' <b>'.$amount.' '.$currency.'</b> '.$lang[to].' '.$gateway.' '.$acc_addr.' <b>'.gatewayinfo($gateway_id,"g_field_1").'</b>';
		return $form;
	}
}
?>