<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

if(checkSession()) {
    $redirect = $settings['url']."account/dashboard";
    header("Location: $redirect");
}


$b = protect($_GET['b']);
if($b == "reset") {
    $tpl = new Template("app/templates/".$settings['default_template']."/password_reset.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $results = '';
    $ce_form = protect($_POST['ce_btn']);
    if($ce_form == "reset") {
        $email = protect($_POST['email']);
        $recaptcha_response = protect($_POST['g-recaptcha-response']);
        $check = $db->query("SELECT * FROM ce_users WHERE email='$email'");
        if(empty($email)) { 
            $results = error($lang['error_39']);
        } elseif(!isValidEmail($email)) {
            $results = error($lang['error_23']);
        } elseif($check->num_rows==0) {
            $results = error($lang['error_40']);
        } elseif($settings['enable_recaptcha'] == "1" && !VerifyGoogleRecaptcha($recaptcha_response)) {
            $results = error($lang['error_58']);  
        } else {
            CE_Send_RPE($email);
            $success = str_ireplace("%email%",$email,$lang['success_12']);
            $results = success($success);
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
} elseif($b == "change") {
    $hash = protect($_GET['hash']);
    $query = $db->query("SELECT * FROM ce_users WHERE password_hash='$hash'");
    if($query->num_rows==0) {
        $redirect = $settings['url']."password/reset";
        header("Location: $redirect");
    }
    $row = $query->fetch_assoc();
    $tpl = new Template("app/templates/".$settings['default_template']."/password_change.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("name",$settings['name']);
    $results = '';
    $tpl->set("email",$row['email']);
    $ce_form = protect($_POST['ce_btn']);
    if($ce_form == "change") {
        $password = protect($_POST['password']);
        $cpassword = protect($_POST['cpassword']);
        if(empty($password)) {
            $results = error($lang['error_15']);
        } elseif(strlen($password)<6) {
            $results = error($lang['error_41']);
        } elseif($password !== $cpassword) {
            $results = error($lang['error_42']);
        } else {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $update = $db->query("UPDATE ce_users SET password='$password',password_hash='' WHERE id='$row[id]'");
            $results = success($lang['success_13']);
        }
    }
    $tpl->set("results",$results);
    
    echo $tpl->output();
} else {
    header("Location: $settings[url]");
}
?>