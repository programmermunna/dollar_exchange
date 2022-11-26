<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

$b = protect($_GET['b']);
if($b == "verify") {
    $hash = protect($_GET['hash']);
    if(empty($hash)) {
        header("Location: $settings[url]");
    } else {
        $CheckUser = $db->query("SELECT * FROM ce_users WHERE email_hash='$hash'");
        if($CheckUser->num_rows>0) {
            $user = $CheckUser->fetch_assoc();
            $update = $db->query("UPDATE ce_users SET email_verified='1',email_hash='' WHERE id='$user[id]'");
            $tpl = new Template("app/templates/".$settings['default_template']."/email_verification.html",$lang);
            $tpl->set("url",$settings['url']);
            $tpl->set("name",$settings['name']);
            echo $tpl->output();
        } else {
            header("Location: $settings[url]");
        }
    }
} else {
    header("Location: $settings[url]");
}
?> 