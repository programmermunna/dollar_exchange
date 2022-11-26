<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}


function gatewayinfo($gid,$value) {
	global $db;
	$query = $db->query("SELECT * FROM ce_gateways WHERE id='$gid'");
	$row = $query->fetch_assoc();
	return $row[$value];
}	


function gatewayicon($name) {
	global $db, $settings;
	$path = "app/templates/CryptoExchanger/assets/icons/";
	$external_icon = 0;
	if($name == "PayPal") { $icon = 'PayPal.png'; }
	elseif($name == "Skrill") { $icon = 'Skrill.png'; }
	elseif($name == "WebMoney") { $icon = 'WebMoney.png'; }
	elseif($name == "Payeer") { $icon = 'Payeer.png'; }
	elseif($name == "Perfect Money") { $icon = 'PerfectMoney.png'; }
	elseif($name == "AdvCash") { $icon = 'AdvCash.png'; }
	elseif($name == "OKPay") { $icon = 'OKPay.png'; }
	elseif($name == "Entromoney") { $icon = 'Entromoney.png'; }
	elseif($name == "SolidTrust Pay") { $icon = 'SolidTrustPay.png'; }
	elseif($name == "Stripe") { $icon = 'CreditCard.png'; }
	elseif($name == "Paytm") { $icon = 'Paytm.png'; }
	elseif($name == "2checkout") { $icon = 'CreditCard.png'; }
	elseif($name == "Litecoin") { $icon = 'Litecoin.png'; }
	elseif($name == "Neteller") { $icon = 'Neteller.png'; }
	elseif($name == "UQUID") { $icon = 'UQUID.png'; }
	elseif($name == "Dash") { $icon = 'Dash.png'; }
	elseif($name == "Dogecoin") { $icon = 'Dogecoin.png'; }
	elseif($name == "BTC-e") { $icon = 'BTCe.png'; }
	elseif($name == "Ethereum") { $icon = 'Ethereum.png'; }
	elseif($name == "Peercoin") { $icon = 'Peercoin.png'; }
	elseif($name == "Yandex Money") { $icon = 'YandexMoney.png'; }
	elseif($name == "QIWI") { $icon = 'QIWI.png'; }
	elseif($name == "Payza") { $icon = 'Payza.png'; }
	elseif($name == "Bitcoin") { $icon = 'Bitcoin.png'; }
	elseif($name == "Bank Transfer") { $icon = 'BankTransfer.png'; }
	elseif($name == "Western Union") { $icon = 'Westernunion.png'; }
	elseif($name == "Moneygram") { $icon = 'Moneygram.png'; }
	elseif($name == "TheBillioncoin") { $icon = 'TheBillioncoin.png'; }
	elseif($name == "Edinarcoin") { $icon = 'Edinarcoin.png'; }
	elseif($name == "Mollie") { $icon = 'Mollie.png'; }
	else { 
		$check = $db->query("SELECT * FROM ce_gateways WHERE name='$name' and external_gateway='1'");
		if($check->num_rows>0) {
			$r = $check->fetch_assoc();
			$icon = $settings['url'].$r['external_icon'];
			$external_icon = 1;
		} else {
			$icon = "Missing.png";
		}
	}
	if($external_icon == "1") {
		return $settings[url].$icon;
	} else {
		return $settings[url].$path.$icon;
	}
}

function gticon($gateway) {
	global $db, $settings;
	$check = $db->query("SELECT * FROM ce_gateways WHERE id='$gateway' and external_icon!=''");
	if($check->num_rows>0) {
		$r = $check->fetch_assoc();
		return $settings['url'].$r['external_icon'];
	} else {
		if(gatewayinfo($gateway,"is_crypto") == "1") {
			return $settings[url].'app/templates/CryptoExchanger/assets/icons/crypto/'.GetCryptoCurrency(gatewayinfo($gateway,"name")).'.png';
		} else {
			return gatewayicon(gatewayinfo($gateway,"name"));
		}
	}
}

function currencyConvertor($amount,$from_Currency,$to_Currency) {
	global $db, $settings;
	$am = urlencode($amount);
	$prefix = $from_Currency.'_'.$to_Currency;
	$ch = curl_init();
	$url = "https://api.currconv.com/api/v7/convert?q=$prefix&compact=ultra&apiKey=$settings[curcnv_api]";
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL,$url);
	// Execute
	$result=curl_exec($ch);
	// Closing
	curl_close($ch);
	$json = json_decode($result, true);
	//echo $json[$prefix]['val'];
	$converted_amount = $json[$prefix];
	if($amount>1 && $from_Currency != "USD") {
		$converted_amount = $amount * $converted_amount;
		return number_format($converted_amount, 2, '.', '');
	} elseif($amount>1 && $to_Currency != "USD") {
		$converted_amount = $amount * $converted_amount;
		return number_format($converted_amount, 2, '.', '');
	} else {
		return number_format($converted_amount, 2, '.', '');
	}
}

function get_rates($gateway_send,$gateway_receive) {
	global $db, $settings;
	$gateway_sendname = gatewayinfo($gateway_send,"name");
	$gateway_receivename = gatewayinfo($gateway_receive,"name");
	if(empty($gateway_send) or empty($gateway_receive)) {
		$data['status'] = 'error';
		$data['msg'] = '-';
	} else {
		$data['status'] = 'success';
		$rate_from = 0;
		$rate_to = 0;
		$currency_from = gatewayinfo($gateway_send,"currency");
		$currency_to = gatewayinfo($gateway_receive,"currency");
		$query = $db->query("SELECT * FROM ce_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
		if($query->num_rows>0) {
			$row = $query->fetch_assoc();
			if($row['percentage_rate'] == "1") {
				$fee = $row['fee'];
				if(checkSession()) {
					$discount_level = idinfo($_SESSION['ce_uid'],"discount_level");
					$DiscountQuery = $db->query("SELECT * FROM ce_discount_system WHERE discount_level='$discount_level'");
					if($DiscountQuery->num_rows>0) {
						$discount = $DiscountQuery->fetch_assoc();
						if($fee>1) {
							$fee = $fee.".".$discount['discount_percentage'];
						} else {
							$fee2 = ".".$discount['discount_percentage'];
							$fee = $fee.$fee2;
						}
					}
				}
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
						$fee = '-'.$fee;
						$price = getCryptoPrice($currency_to);
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
			} else {
				$data['status'] = 'success';
				$rate_from = $row['rate_from'];
				$rate_to = $row['rate_to'];
			}
		} else {
			$data['status'] = 'error';
			$data['msg'] = '-';
		}
		$data['rate_from'] = $rate_from; 
		$data['rate_to'] = $rate_to;
		$data['currency_from'] = $currency_from;
		$data['currency_to'] = $currency_to;
	}
	return $data;
}

function getCrypto2CryptoPrice($from,$to) {
	$ch = curl_init();
	$url = "https://min-api.cryptocompare.com/data/price?fsym=$from&tsyms=$to";
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL,$url);
	// Execute
	$result=curl_exec($ch);
	// Closing
	curl_close($ch);
	$json = json_decode($result, true);
	if($json[$to]) {
		return $json[$to];
	} else {
		return '0';
	}
}

function getCryptoPrice($coin) {
	$ch = curl_init();
	$url = "https://min-api.cryptocompare.com/data/price?fsym=$coin&tsyms=USD";
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL,$url);
	// Execute
	$result=curl_exec($ch);
	// Closing
	curl_close($ch);
	$json = json_decode($result, true);
	if($json['USD']) {
		return $json['USD'];
	} else {
		return '0';
	}
}

function checkCryptoExchange($gateway_send,$gateway_receive) {
    global $db, $settings;
	$isCrypto_1 = gatewayinfo($gateway_send,"is_crypto");
	$isCrypto_2 = gatewayinfo($gateway_receive,"is_crypto");
	if($isCrypto_1 == "1" && $isCrypto_2 == "1") {
		return true;
	} else {
		return false;
	}
}

function ce_ExCalculator($from,$to,$amount) {
	global $db;
	$rt = get_rates($from,$to);
	if($rt['status'] == "error") {
		$rate_from = 0;
		$rate_to = 0;
	} else {
		$rate_from = $rt['rate_from'];
		$rate_to = $rt['rate_to'];
	}
	if(is_numeric($amount)) {
		if(gatewayinfo($from,"is_crypto") == "1" && gatewayinfo($to,"is_crypto") == "1") {
			if($rate_from<1) {
				$calculate = $amount / $rate_from;
				$converted = number_format($calculate,8,'.','');
			} else {
				$calculate = $amount * $rate_to;
				$converted = number_format($calculate,8,'.','');
			}
		} elseif(gatewayinfo($to,"is_crypto") == "1") {
			$calculate = $amount / $rate_from;
			$converted = number_format($calculate,8,'.','');
		} elseif($rate_from>1) {
			$calculate = $amount / $rate_from;
			$converted = number_format($calculate,2,'.','');
		} else { 	
			$calculate = $amount * $rate_to;
			$converted = number_format($calculate,2,'.','');
		}
	} else {	
		$converted = 0;
	}
	return $converted;
}

function CryptoSupport($merchant) {
	if($merchant == "block.io") {
		$supported_coins = array("Bitcoin","Litecoin","Dogecoin");
		return $supported_coins;
	} elseif($merchant == "coinpayments.net") {
		$supported_coins = array("Bitcoin","Litecoin","Dogecoin","CPS Coin","Bitcoin Cash","Bytecoin","BitBean","BlackCoin","Breakout","CloakCoin","ClubCoin","Crown","CureCoin","Dash","Decred","DigiByte","eBoost","Ether Classic","Ether","Goldcoin","Groestlcoin","Komodo","LISK","MonetaryUnit","NAV Coin","NEO","Namecoin","NXT","Pinkcoin","PoSW Coin","Potcoin","Peercoin","ProCurrency","Pura","Qtum","SmartCash","Stratis","Syscoin","TokenPay","Triggers","Ubiq","Vertcoin","Waves","Counterparty","NEM","Monero","VERGE","ZCoin","ZCash","ZenCash");
		return $supported_coins;
	} else {
		return false;
	}
}

function GetCryptoCurrency($gateway) {
	if($gateway == "Bitcoin") { $currency = 'BTC'; }
	elseif($gateway == "Litecoin") { $currency = 'LTC'; }
	elseif($gateway == "Dogecoin") { $currency = 'DOGE'; }
	elseif($gateway == "CPS Coin") { $currency = 'CPS'; }
	elseif($gateway == "Bitcoin Cash") { $currency = 'BCH'; }
	elseif($gateway == "Bytecoin") { $currency = 'BCN'; } 
	elseif($gateway == "BitBean") { $currency = 'BITB'; }
	elseif($gateway == "BlackCoin") { $currency = 'BLK'; }
	elseif($gateway == "Breakout") { $currency = 'BRK'; }
	elseif($gateway == "CloakCoin") { $currency = 'CLOAK'; } 
	elseif($gateway == "ClubCoin") { $currency = 'CLUB'; }
	elseif($gateway == "Crown") { $currency = 'CRW'; } 
	elseif($gateway == "CureCoin") { $currency = 'CURE'; }
	elseif($gateway == "Dash") { $currency = 'DASH'; } 
	elseif($gateway == "Decred") { $currency = 'DCR'; }
	elseif($gateway == "DigiByte") { $currency = 'DGB'; }
	elseif($gateway == "eBoost") { $currency = 'EBST'; }
	elseif($gateway == "Ether Classic") { $currency = 'ETC'; }
	elseif($gateway == "Ether") { $currency = 'ETH'; }
	elseif($gateway == "Goldcoin") { $currency = 'GLD'; }
	elseif($gateway == "Groestlcoin") { $currency = 'GRS'; } 
	elseif($gateway == "Komodo") { $currency = 'KMD'; }
	elseif($gateway == "LISK") { $currency = 'LSK'; }
	elseif($gateway == "MonetaryUnit") { $currency = 'MUE'; }
	elseif($gateway == "NAV Coin") { $currency = 'NAV'; } 
	elseif($gateway == "NEO") { $currency = 'NEO'; }
	elseif($gateway == "Namecoin") { $currency = 'NMC'; }
	elseif($gateway == "NXT") { $currency = 'NXT'; }
	elseif($gateway == "PinkCoin") { $currency = 'PINK'; }
	elseif($gateway == "Potcoin") { $currency = 'POT'; } 
	elseif($gateway == "Peercoin") { $currency = 'PPC'; }
	elseif($gateway == "ProCurrency") { $currency = 'PROC'; }
	elseif($gateway == "Pura") { $currency = 'PURA'; }
	elseif($gateway == "Qtum") { $currency = 'QTUM'; }
	elseif($gateway == "Smart Dollars") { $currency = 'SBD'; } 
	elseif($gateway == "SmartCash") { $currency = 'SMART'; }
	elseif($gateway == "SOXAX") { $currency = 'SOXAX'; }
	elseif($gateway == "STEEM") { $currency = 'STEEM'; } 
	elseif($gateway == "Stratis") { $currency = 'STRAT'; }
	elseif($gateway == "Syscoin") { $currency = 'SYS'; }
	elseif($gateway == "TokenPay") { $currency = 'TPAY'; }
	elseif($gateway == "Triggers") { $currency = 'TRIG'; }
	elseif($gateway == "Ubiq") { $currency = 'UBQ'; }
	elseif($gateway == "UniversalCurrency") { $currency = 'UNIT'; }
	elseif($gateway == "Vertcoin") { $currency = 'VTC'; }
	elseif($gateway == "Waves") { $currency = 'WAVES'; }
	elseif($gateway == "Counterparty") { $currency = 'XCP'; }
	elseif($gateway == "NEM") { $currency = 'XEM'; }
	elseif($gateway == "Monero") { $currency = 'XMR'; }
	elseif($gateway == "Stakenet") { $currency = 'XSN'; }
	elseif($gateway == "VERGE") { $currency = 'XVG'; }
	elseif($gateway == "ZCoin") { $currency = 'XZC'; } 
	elseif($gateway == "ZCash") { $currency = 'ZEC'; }
	elseif($gateway == "ZenCash") { $currency = 'ZEN'; }
	else { $currency = 'Unknown'; }
	return $currency;
}
?>