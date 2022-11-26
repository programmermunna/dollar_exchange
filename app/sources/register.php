<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if(checkSession()) {
    $redirect = $settings['url']."account/dashboard";
    header("Location: $redirect");
}

$tpl = new Template("app/templates/".$settings['default_template']."/register.html",$lang);
$tpl->set("url",$settings['url']);
$results = '';
$CEAction = protect($_POST['ce_btn']);
if(isset($CEAction) && $CEAction == "register") {
    $email = protect($_POST['email']);
	$password = protect($_POST['password']);
	$cpassword = protect($_POST['cpassword']);
	$p1= protect($_POST['p1']);
    $recaptcha_response = protect($_POST['g-recaptcha-response']);
	$check_email = $db->query("SELECT * FROM ce_users WHERE email='$email'");
	if(empty($email) or empty($password) or empty($cpassword)) {
		$results = error($lang['error_1']);
	} elseif(!isValidEmail($email)) {
		$results = error($lang['error_23']);
	} elseif($check_email->num_rows>0) {
		$results = error($lang['error_47']); 
	} elseif(strlen($password)<6) {
		$results = error($lang['error_48']);
	} elseif($password !== $cpassword) {
		$results = error($lang['error_49']);
	} elseif($p1 !== "ok") {
		$results = error($lang['error_50']);
	} elseif($settings['enable_recaptcha'] == "1" && !VerifyGoogleRecaptcha($recaptcha_response)) {
        $results = error($lang['error_58']);  
    }  else {
		$time = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		$password = password_hash($password, PASSWORD_DEFAULT);
		$hash = randomHash(25);
		CE_Send_VE($email);
		$insert = $db->query("INSERT ce_users (email,password,password_hash,email_hash,email_verified,status,ip,twoFA,registered_on,last_login,first_name,last_name,birthday_date,country,city,zip_code,address,mobile_number,mobile_verified,document_verified,discount_level,exchanged_volume) VALUES ('$email','$password','','$hash','0','16','$ip','0','$time','','','','','','','','','','0','0','0','0')");
		$results = success($lang['success_14']);
	}
}
$tpl->set("results",$results);
$insert_recaptcha = '';
if($settings['enable_recaptcha'] == "1") {
    $captcha_tpl = new Template("app/templates/".$settings['default_template']."/rows/recaptcha.html",$lang);
    $captcha_tpl->set("recaptcha_publickey",$settings['recaptcha_publickey']);
    $insert_recaptcha = $captcha_tpl->output();   
}
$tpl->set("insert_recaptcha",$insert_recaptcha);
echo $tpl->output();
?>