<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

function protect($string) {
	$protection = htmlspecialchars(trim($string), ENT_QUOTES);
	return $protection;
}

function randomHash($lenght = 7) {
	$random = substr(md5(rand()),0,$lenght);
	return $random;
}

function isValidURL($url) {
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

function CE_secure_directory() {
	global $db, $settings;
	$secure_directory = md5($settings['name']);
	$secure_directory = substr($secure_directory,0,15);
	$secure_directory = md5($secure_directory);
	$secure_directory = substr($secure_directory,0,15);
	$secure_directory = md5($secure_directory);
	$secure_directory = substr($secure_directory,0,20);
	return $secure_directory;
}


function croptext2($text,$chars) {
	$string = $text;
	if(strlen($string) > $chars) $string = substr($string, 0, $chars).'***************';
	echo $string;
}

function decodeTitle($a,$b,$c,$from,$to,$id,$prefix) {
	global $db, $settings, $lang;
	if($a == "login") {
		return $lang['title_login']." - ".$settings['name'];
	} elseif($a == "password") {
		if($b == "reset") {
			return $lang['title_reset_password']." - ".$settings['name'];
		} elseif($b == "change") {
			return $lang['title_change_password']." - ".$settings['name'];
		} else {
			return $settings['title'];
		}
	} elseif($a == "reserve_request") {
		return $lang['title_reserve_request']." - ".$settings['name'];
	} elseif($a == "order") {
		$query = $db->query("SELECT * FROM ce_orders WHERE order_hash='$id'");
		$row = $query->fetch_assoc();
		return $lang['order']." #".$row['id']." ".$lang['overview']." - ".$settings['name'];
	} elseif($a == "pay") {
		$query = $db->query("SELECT * FROM ce_orders WHERE order_hash='$id'");
		$row = $query->fetch_assoc();
		return $lang['pay_order']." #".$row['id']." - ".$settings['name'];	
	} elseif($a == "contacts") {
		return $lang['title_contacts']." - ".$settings['name'];
	} elseif($a == "exchange") {
		if(gatewayinfo($from,"merchant_source") == "stripe" or gatewayinfo($from,"merchant_source") == "2checkout") {
			$gateway_send = 'VISA/MasterCard '.gatewayinfo($from,"currency");
		} else { 
			$gateway_send = gatewayinfo($from,"name").' '.gatewayinfo($from,"currency");
		}
		if(gatewayinfo($to,"merchant_source") == "stripe" or gatewayinfo($to,"merchant_source") == "2checkout") {
			$gateway_receive = 'VISA/MasterCard '.gatewayinfo($to,"currency");
		} else {
			$gateway_receive = gatewayinfo($to,"name").' '.gatewayinfo($to,"currency");
		}
		return $lang['exchange']." ".$gateway_send." ".$lang['to']." ".$gateway_receive." - ".$settings['name'];
	} elseif($a == "reviews") {
		return $lang['title_customer_reviews']." - ".$settings['name'];	
	} elseif($a == "page") { 
		if($prefix == "discount_system") {
			return $lang['title_discount_system']." - ".$settings['name'];
		} elseif($prefix == "affiliate_program") {
			return $lang['title_affiliate_program']." - ".$settings['name'];
		} elseif($prefix == "faq") {
			return $lang['title_faq']." - ".$settings['name'];
		} else {
			$query = $db->query("SELECT * FROM ce_pages WHERE prefix='$prefix'");
			$row = $query->fetch_assoc();
			return $row['title']." - ".$settings['name'];	
		}
	} elseif($a == "news") {
		if($b == "view") {
			$query = $db->query("SELECT * FROM ce_news WHERE id='$id'");
			$row = $query->fetch_assoc();
			return $row['title']." - ".$lang['title_news']." - ".$settings['name'];
		} else {
			return $lang['title_news']." - ".$settings['name'];
		}
	} elseif($a == "sitemap") {
		return $lang['title_sitemap']." ".$settings['name'];
	} elseif($a == "payment") {
		if($b == "success") {
			return $lang['payment_success']." - ".$settings['name'];
		} elseif($b == "fail") {
			return $lang['payment_fail']." - ".$settings['name'];
		} else {
			return $settings['name'];
		}
	} elseif($a == "register") {
		return $lang['title_create_account']." - ".$settings['name'];
	} elseif($a == "account") {
		if($b == "dashboard") {
			return $lang['title_account']." ".$lang['menu_dashboard']." - ".$settings['name'];
		} elseif($b == "exchanges") {
			return $lang['title_account']." ".$lang['menu_exchanges']." - ".$settings['name'];
		} elseif($b == "reviews") {
			return $lang['title_account']." ".$lang['menu_reviews']." - ".$settings['name'];
		} elseif($b == "tickets") {
			return $lang['title_account']." ".$lang['menu_support_tickets']." - ".$settings['name'];
		} elseif($b == "referrals") {
			return $lang['title_account']." ".$lang['menu_referrals']." - ".$settings['name'];
		} elseif($b == "discount_system") {
			return $lang['title_account']." ".$lang['menu_discount_system']." - ".$settings['name'];
		} elseif($b == "settings") {
			if($c == "profile") {
				return $lang['title_account']." ".$lang['menu_title_settings']." - ".$lang['menu_profile']." - ".$settings['name'];
			} elseif($c == "security") {
				return $lang['title_account']." ".$lang['menu_title_settings']." - ".$lang['menu_security']." - ".$settings['name'];
			} elseif($c == "verification") {
				return $lang['title_account']." ".$lang['menu_title_settings']." - ".$lang['menu_verification']." - ".$settings['name'];
			} else {	
				return $lang['title_account']." ".$lang['menu_title_settings']." - ".$settings['name'];
			}
		} elseif($b == "close") {
			return $lang['title_close_account']." - ".$settings['name'];	
		} else {
			return $settings['title'];
		}
	} else {
		return $settings['title'];
	}
}


function getCountries() {
	$countries = array('AL' => 'Albania',
	'DZ' => 'Algeria',
	'AD' => 'Andorra',
	'AO' => 'Angola',
	'AI' => 'Anguilla',
	'AG' => 'Antigua &amp; Barbuda',
	'AR' => 'Argentina',
	'AM' => 'Armenia',
	'AW' => 'Aruba',
	'AU' => 'Australia',
	'AT' => 'Austria',
	'AZ' => 'Azerbaijan',
	'BS' => 'Bahamas',
	'BH' => 'Bahrain',
	'BB' => 'Barbados',
	'BY' => 'Belarus',
	'BE' => 'Belgium',
	'BZ' => 'Belize',
	'BJ' => 'Benin',
	'BM' => 'Bermuda',
	'BT' => 'Bhutan',
	'BO' => 'Bolivia',
	'BA' => 'Bosnia &amp; Herzegovina',
	'BW' => 'Botswana',
	'BR' => 'Brazil',
	'VG' => 'British Virgin Islands',
	'BN' => 'Brunei',
	'BG' => 'Bulgaria',
	'BF' => 'Burkina Faso',
	'BI' => 'Burundi',
	'KH' => 'Cambodia',
	'CM' => 'Cameroon',
	'CA' => 'Canada',
	'CV' => 'Cape Verde',
	'KY' => 'Cayman Islands',
	'TD' => 'Chad',
	'CL' => 'Chile',
	'C2' => 'China',
	'CO' => 'Colombia',
	'KM' => 'Comoros',
	'CG' => 'Congo - Brazzaville',
	'CD' => 'Congo - Kinshasa',
	'CK' => 'Cook Islands',
	'CR' => 'Costa Rica',
	'CI' => 'Cote d’Ivoire',
	'HR' => 'Croatia',
	'CY' => 'Cyprus',
	'CZ' => 'Czech Republic',
	'DK' => 'Denmark',
	'DJ' => 'Djibouti',
	'DM' => 'Dominica',
	'DO' => 'Dominican Republic',
	'EC' => 'Ecuador',
	'EG' => 'Egypt',
	'SV' => 'El Salvador',
	'ER' => 'Eritrea',
	'EE' => 'Estonia',
	'ET' => 'Ethiopia',
	'FK' => 'Falkland Islands',
	'FO' => 'Faroe Islands',
	'FJ' => 'Fiji',
	'FI' => 'Finland',
	'FR' => 'France',
	'GF' => 'French Guiana',
	'PF' => 'French Polynesia',
	'GA' => 'Gabon',
	'GM' => 'Gambia',
	'GE' => 'Georgia',
	'DE' => 'Germany',
	'GI' => 'Gibraltar',
	'GR' => 'Greece',
	'GL' => 'Greenland',
	'GD' => 'Grenada',
	'GP' => 'Guadeloupe',
	'GT' => 'Guatemala',
	'GN' => 'Guinea',
	'GW' => 'Guinea-Bissau',
	'GY' => 'Guyana',
	'HN' => 'Honduras',
	'HK' => 'Hong Kong SAR China',
	'HU' => 'Hungary',
	'IS' => 'Iceland',
	'IN' => 'India',
	'ID' => 'Indonesia',
	'IE' => 'Ireland',
	'IL' => 'Israel',
	'IT' => 'Italy',
	'JM' => 'Jamaica',
	'JP' => 'Japan',
	'JO' => 'Jordan',
	'KZ' => 'Kazakhstan',
	'KE' => 'Kenya',
	'KI' => 'Kiribati',
	'KW' => 'Kuwait',
	'KG' => 'Kyrgyzstan',
	'LA' => 'Laos',
	'LV' => 'Latvia',
	'LS' => 'Lesotho',
	'LI' => 'Liechtenstein',
	'LT' => 'Lithuania',
	'LU' => 'Luxembourg',
	'MK' => 'Macedonia',
	'MG' => 'Madagascar',
	'MW' => 'Malawi',
	'MY' => 'Malaysia',
	'MV' => 'Maldives',
	'ML' => 'Mali',
	'MT' => 'Malta',
	'MH' => 'Marshall Islands',
	'MQ' => 'Martinique',
	'MR' => 'Mauritania',
	'MU' => 'Mauritius',
	'YT' => 'Mayotte',
	'MX' => 'Mexico',
	'FM' => 'Micronesia',
	'MD' => 'Moldova',
	'MC' => 'Monaco',
	'MN' => 'Mongolia',
	'ME' => 'Montenegro',
	'MS' => 'Montserrat',
	'MA' => 'Morocco',
	'MZ' => 'Mozambique',
	'NA' => 'Namibia',
	'NR' => 'Nauru',
	'NP' => 'Nepal',
	'NL' => 'Netherlands',
	'AN' => 'Netherlands Antilles',
	'NC' => 'New Caledonia',
	'NZ' => 'New Zealand',
	'NI' => 'Nicaragua',
	'NE' => 'Niger',
	'NG' => 'Nigeria',
	'NU' => 'Niue',
	'NF' => 'Norfolk Island',
	'NO' => 'Norway',
	'OM' => 'Oman',
	'PW' => 'Palau',
	'PA' => 'Panama',
	'PG' => 'Papua New Guinea',
	'PY' => 'Paraguay',
	'PE' => 'Peru',
	'PH' => 'Philippines',
	'PN' => 'Pitcairn Islands',
	'PL' => 'Poland',
	'PT' => 'Portugal',
	'QA' => 'Qatar',
	'RE' => 'Reunion',
	'RO' => 'Romania',
	'RU' => 'Russia',
	'RW' => 'Rwanda',
	'WS' => 'Samoa',
	'SM' => 'San Marino',
	'ST' => 'Sao Tome &amp; Principe',
	'SA' => 'Saudi Arabia',
	'SN' => 'Senegal',
	'RS' => 'Serbia',
	'SC' => 'Seychelles',
	'SL' => 'Sierra Leone',
	'SG' => 'Singapore',
	'SK' => 'Slovakia',
	'SI' => 'Slovenia',
	'SB' => 'Solomon Islands',
	'SO' => 'Somalia',
	'ZA' => 'South Africa',
	'KR' => 'South Korea',
	'ES' => 'Spain',
	'LK' => 'Sri Lanka',
	'SH' => 'St. Helena',
	'KN' => 'St. Kitts &amp; Nevis',
	'LC' => 'St. Lucia',
	'PM' => 'St. Pierre &amp; Miquelon',
	'VC' => 'St. Vincent &amp; Grenadines',
	'SR' => 'Suriname',
	'SJ' => 'Svalbard &amp; Jan Mayen',
	'SZ' => 'Swaziland',
	'SE' => 'Sweden',
	'CH' => 'Switzerland',
	'TW' => 'Taiwan',
	'TJ' => 'Tajikistan',
	'TZ' => 'Tanzania',
	'TH' => 'Thailand',
	'TG' => 'Togo',
	'TO' => 'Tonga',
	'TT' => 'Trinidad &amp; Tobago',
	'TN' => 'Tunisia',
	'TM' => 'Turkmenistan',
	'TC' => 'Turks &amp; Caicos Islands',
	'TV' => 'Tuvalu',
	'UG' => 'Uganda',
	'UA' => 'Ukraine',
	'AE' => 'United Arab Emirates',
	'GB' => 'United Kingdom',
	'US' => 'United States',
	'UY' => 'Uruguay',
	'VU' => 'Vanuatu',
	'VA' => 'Vatican City',
	'VE' => 'Venezuela',
	'VN' => 'Vietnam',
	'WF' => 'Wallis &amp; Futuna',
	'YE' => 'Yemen',
	'ZM' => 'Zambia',
	'ZW' => 'Zimbabwe');
	return $countries;
}

 /**
     * Function to see if a string is a UK postcode or not. The postcode is also 
     * formatted so it contains no strings. Full or partial postcodes can be used.
     * 
     * @param string $toCheck
     * @return boolean 
     */
    function postcode_check(&$toCheck) {
		// Permitted letters depend upon their position in the postcode.
		$alpha1 = "[abcdefghijklmnoprstuwyz]";                          // Character 1
		$alpha2 = "[abcdefghklmnopqrstuvwxy]";                          // Character 2
		$alpha3 = "[abcdefghjkstuw]";                                   // Character 3
		$alpha4 = "[abehmnprvwxy]";                                     // Character 4
		$alpha5 = "[abdefghjlnpqrstuwxyz]";                             // Character 5
	   
		// Expression for postcodes: AN NAA, ANN NAA, AAN NAA, and AANN NAA with a space
		// Or AN, ANN, AAN, AANN with no whitespace
		$pcexp[0] = '^(' . $alpha1 . '{1}' . $alpha2 . '{0,1}[0-9]{1,2})([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})?$';
	   
		// Expression for postcodes: ANA NAA
		// Or ANA with no whitespace
		$pcexp[1] = '^(' . $alpha1 . '{1}[0-9]{1}' . $alpha3 . '{1})([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})?$';
	   
		// Expression for postcodes: AANA NAA
		// Or AANA With no whitespace
		$pcexp[2] = '^(' . $alpha1 . '{1}' . $alpha2 . '[0-9]{1}' . $alpha4 . ')([[:space:]]{0,})([0-9]{1}' . $alpha5 . '{2})?$';
	   
		// Exception for the special postcode GIR 0AA
		// Or just GIR
		$pcexp[3] = '^(gir)([[:space:]]{0,})?(0aa)?$';
	   
		// Standard BFPO numbers
		$pcexp[4] = '^(bfpo)([[:space:]]{0,})([0-9]{1,4})$';
	   
		// c/o BFPO numbers
		$pcexp[5] = '^(bfpo)([[:space:]]{0,})(c\/o([[:space:]]{0,})[0-9]{1,3})$';
	   
		// Overseas Territories
		$pcexp[6] = '^([a-z]{4})([[:space:]]{0,})(1zz)$';
	   
		// Anquilla
		$pcexp[7] = '^(ai\-2640)$';
	   
		// Load up the string to check, converting into lowercase
		$postcode = strtolower($toCheck);
	   
		// Assume we are not going to find a valid postcode
		$valid = false;
	   
		// Check the string against the six types of postcodes
		foreach ($pcexp as $regexp) {
		  if (preg_match('/' . $regexp . '/i', $postcode, $matches)) {
	   
			// Load new postcode back into the form element
			$postcode = strtoupper($matches[1]);
			if (isset($matches[3])) {
			  $postcode .= ' ' . strtoupper($matches[3]);
			}
	   
			// Take account of the special BFPO c/o format
			$postcode = preg_replace('/C\/O/', 'c/o ', $postcode);
	   
			// Remember that we have found that the code is valid and break from loop
			$valid = true;
			break;
		  }
		}
	   
		// Return with the reformatted valid postcode in uppercase if the postcode was 
		// valid
		if ($valid) {
		  $toCheck = $postcode;
		  return true;
		} else {
		  return false;
		}
	  }

	  function VerifyGoogleRecaptcha($response) {
		global $db, $settings;
		$secret = $settings['recaptcha_privatekey'];
		$ch = curl_init();
		$url = "https://www.google.com/recaptcha/api/siteverify";
		$data = "secret=$secret&response=$response";
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		// Execute
		$result=curl_exec($ch);
		// Closing
		curl_close($ch);
		$json = json_decode($result, true);
		if($json['success'] == true) {
			return true;
		} else {
			return false;
		}
	}

function CE_CheckExpiredOrders() {
	global $db, $settings;
	$expired_time = time()-$settings['expire_uncompleted_time'];
	$query = $db->query("SELECT * FROM ce_orders WHERE created < $expired_time and status < 3");
	if($query->num_rows>0) {
		$time = time();
		while($row = $query->fetch_assoc()) {
			$update = $db->query("UPDATE ce_orders SET status='6',expired='$time' WHERE id='$row[id]'");
		}
	}
}
?>