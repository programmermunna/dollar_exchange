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
if(isset($b) && $b == "2fa") {
    if(!$_SESSION['ce_twoFA_uid']) {
        $redirect = $settings['url']."login";
        header("Location: $redirect");
    }
    $tpl = new Template("app/templates/".$settings['default_template']."/login_2fa.html",$lang);
    $tpl->set("url",$settings['url']);
    $tpl->set("u_email",idinfo($_SESSION['ce_twoFA_uid'],"email"));
    $results = success($lang['success_11']);
    $CEAction = protect($_POST['ce_btn']);
    if(isset($CEAction) && $CEAction == "authorize") {
        $acode = protect($_POST['acode']);
        if(empty($acode)) {
            $results = error($lang['error_33']);
        } elseif($acode !== idinfo($_SESSION['ce_twoFA_uid'],"email_hash")) {
            $results = error($lang['error_34']);
        } else {
            $_SESSION['ce_uid'] = $_SESSION['ce_twoFA_uid'];
            $_SESSION['çe_twoFA_uid'] = false;
            if(isset($_SESSION['ce_redirect'])) {
                $redirect = $_SESSION['ce_redirect'];
                $_SESSION['ce_redirect'] = false;
                header("Location: $redirect");
            } else {
                $redirect = $settings['url']."account/dashboard";
                header("Location:$redirect");
            }
        }
    }
    if(isset($CEAction) && $CEAction == "resend") {
        CE_Send_ACE(idinfo($_SESSION['ce_twoFA_uid'],"email"));
        $results = success($lang['success_11']);   
    }
    $tpl->set("results",$results);
    echo $tpl->output();
} else {
    $tpl = new Template("app/templates/".$settings['default_template']."/login.html",$lang);
    $tpl->set("url",$settings['url']);
    $results = '';
    $CEAction = protect($_POST['ce_btn']);
    if(isset($CEAction) && $CEAction == "login") {
        $email = protect($_POST['email']);
        $password = protect($_POST['password']);
        $recaptcha_response = protect($_POST['g-recaptcha-response']);
        $check = $db->query("SELECT * FROM ce_users WHERE email='$email'");
        if(empty($email) or empty($password)) {
            $results = error($lang['error_35']);
        } elseif($check->num_rows==0) {
            $results = error($lang['error_36']);
        }  elseif($settings['enable_recaptcha'] == "1" && !VerifyGoogleRecaptcha($recaptcha_response)) {
            $results = error($lang['error_58']);  
        } else {
            $login = $check->fetch_assoc();
            if(password_verify($password, $login['password'])) {
                if($login['status'] == "2") {
                    $results = error($lang['error_37']);
                } else {
                    if($login['twoFA'] == "1") {
                        $_SESSION['ce_twoFA_uid'] = $login['id'];
                        CE_Send_ACE($login['email']);
                        $redirect = $settings['url']."login/2fa";
                        header("Location: $redirect");
                    } else {
                        $time = time();
                        $_SESSION['ce_uid'] = $login['id'];
                        $update = $db->query("UPDATE ce_users SET last_login='$time' WHERE id='$login[id]'");
                        if(isset($_SESSION['ce_redirect'])) {
                            $redirect = $_SESSION['ce_redirect'];
                            $_SESSION['ce_redirect'] = false;
                            header("Location: $redirect");
                        } else {
                            if($login['status'] == "16") {
                                $redirect = $settings['url']."account/settings/profile";
                            } else {
                                $redirect = $settings['url']."account/dashboard";
                            }
                            header("Location: $redirect");
                        }
                    }
                }
            } else {
                $results = error($lang['error_38']);
            }
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
}
?>